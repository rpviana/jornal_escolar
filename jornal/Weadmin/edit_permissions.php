<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'school_journal');

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Verificar se o utilizador está autenticado e é superadmin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    die("Acesso negado. Apenas Superadmins podem acessar esta página.");
}

// Buscar informações do admin para edição
if (isset($_GET['admin_id'])) {
    $admin_id = intval($_GET['admin_id']);

    $stmt = $conn->prepare("SELECT username, role FROM admins WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->bind_result($username, $role);
    $stmt->fetch();
    $stmt->close();

    if (!$username) {
        die("Administrador não encontrado.");
    }
}

// Atualizar permissões
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_id'], $_POST['new_role'])) {
    $admin_id = intval($_POST['admin_id']);
    $new_role = $_POST['new_role'];
    $current_admin_id = $_SESSION['id']; // ID do superadmin atual

    // Buscar o username do superadmin atual
    $stmt = $conn->prepare("SELECT username FROM admins WHERE id = ?");
    $stmt->bind_param("i", $current_admin_id);
    $stmt->execute();
    $stmt->bind_result($current_admin_username);
    $stmt->fetch();
    $stmt->close();

    if (!$current_admin_username) {
        echo "<p>Erro: Superadmin não encontrado.</p>";
        exit();
    }

    // Buscar o username do adm que está a ser editado
    $stmt = $conn->prepare("SELECT username FROM admins WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->bind_result($target_admin_username);
    $stmt->fetch();
    $stmt->close();

    if (!$target_admin_username) {
        echo "<p>Erro: Admin não encontrado.</p>";
        exit();
    }

    // Atualizar permissões
    $stmt = $conn->prepare("UPDATE admins SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $admin_id);

    if ($stmt->execute()) {
        // Log da ação
        $log_stmt = $conn->prepare("
            INSERT INTO admin_logs (admin_id, admin_username, action) 
            VALUES (?, ?, ?)
        ");
        $action = "Alterou permissões do admin '$target_admin_username' (ID: $admin_id) para '$new_role'";
        $log_stmt->bind_param("iss", $current_admin_id, $current_admin_username, $action);
        $log_stmt->execute();
        $log_stmt->close();

        echo "<p>Permissões atualizadas com sucesso!</p>";
        header("Location: manage_admins.php");
        exit();
    } else {
        echo "<p>Erro ao atualizar permissões.</p>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Permissões</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { margin-bottom: 20px; }
        form { display: flex; flex-direction: column; max-width: 400px; }
        label { margin-bottom: 5px; font-weight: bold; }
        select, button { margin-bottom: 10px; padding: 8px; }
        button { background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #45a049; }
        a { text-decoration: none; color: #fff; background: #008CBA; padding: 8px 12px; border-radius: 5px; display: inline-block; margin-top: 10px; }
        a:hover { background: #007bb5; }
    </style>
</head>
<body>
    <h1>Editar Permissões do Administrador</h1>

    <form method="POST" action="edit_permissions.php">
        <input type="hidden" name="admin_id" value="<?php echo htmlspecialchars($admin_id); ?>">

        <label>Nome de Utilizador:</label>
        <p><strong><?php echo htmlspecialchars($username); ?></strong></p>

        <label>Permissão Atual:</label>
        <p><strong><?php echo htmlspecialchars($role); ?></strong></p>

        <label>Nova Permissão:</label>
        <select name="new_role" required>
            <option value="superadmin" <?php echo ($role === 'superadmin') ? 'selected' : ''; ?>>Superadmin</option>
            <option value="editor" <?php echo ($role === 'editor') ? 'selected' : ''; ?>>Editor</option>
            <option value="moderator" <?php echo ($role === 'moderator') ? 'selected' : ''; ?>>Moderator</option>
        </select>

        <button type="submit">Salvar Alterações</button>
    </form>

    <a href="manage_admins.php">Voltar para Controlar Admins</a>
</body>
</html>
