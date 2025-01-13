<?php
session_start();

// Conexão com a base de dados
$conn = new mysqli('localhost', 'root', '', 'school_journal');

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Verificar se o usuário é superadmin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    echo "<p>Acesso negado. Apenas Superadmins podem acessar esta página.</p>";
    exit();
}

// Buscar lista de admins
$result = $conn->query("SELECT id, username, role FROM admins");

// Deletar admin (se solicitado)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $admin_id = intval($_POST['admin_id']);
    $current_admin_id = $_SESSION['id']; // ID do superadmin atual

    // Buscar informações do admin alvo
    $stmt = $conn->prepare("SELECT username, role FROM admins WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->bind_result($admin_username, $role);
    $stmt->fetch();
    $stmt->close();

    if ($role === 'superadmin') {
        echo "<p>Erro: Não é possível excluir um superadmin.</p>";
    } else {
        // Excluir admin
        $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $stmt->close();

        // Log da exclusão
        $log_stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action) VALUES (?, ?)");
        $action = "Excluiu o admin '$admin_username' (ID: $admin_id)";
        $log_stmt->bind_param("is", $current_admin_id, $action);
        $log_stmt->execute();
        $log_stmt->close();

        echo "<p>Administrador excluído com sucesso.</p>";
        header("Refresh:0"); // Recarregar a página
        exit();
    }
}

// Logar acesso à edição de permissões
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $admin_id = intval($_POST['admin_id']);
    $current_admin_id = $_SESSION['id']; // ID do superadmin atual

    // Buscar informações do superadmin atual
    $stmt = $conn->prepare("SELECT username FROM admins WHERE id = ?");
    $stmt->bind_param("i", $current_admin_id);
    $stmt->execute();
    $stmt->bind_result($current_admin_username);
    $stmt->fetch();
    $stmt->close();
    
    // Buscar informações do admin alvo
    $stmt = $conn->prepare("SELECT username FROM admins WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->bind_result($admin_username);
    $stmt->fetch();
    $stmt->close();
    
    // Log da edição
    $log_stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, admin_username, action) VALUES (?, ?, ?)");
    $action = "Acessou edição de permissões do admin '$admin_username' (ID: $admin_id)";
    $log_stmt->bind_param("iss", $current_admin_id, $current_admin_username, $action);
    $log_stmt->execute();
    $log_stmt->close();
    
    header("Location: edit_permissions.php?admin_id=$admin_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Controlar Admins</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { margin-bottom: 20px; }
        .admin-item { border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; }
        .admin-item p { margin: 5px 0; }
        .btn { padding: 5px 10px; cursor: pointer; border: none; border-radius: 3px; }
        .edit-btn { background-color: #4CAF50; color: white; }
        .delete-btn { background-color: #f44336; color: white; }
        .edit-btn:hover { background-color: #45a049; }
        .delete-btn:hover { background-color: #da190b; }
        .back-btn { margin-top: 20px; display: inline-block; text-decoration: none; background: #008CBA; color: white; padding: 8px 15px; border-radius: 4px; }
        .back-btn:hover { background: #007bb5; }
    </style>
</head>
<body>
    <h1>Controlars Admins</h1>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($admin = $result->fetch_assoc()): ?>
            <div class="admin-item">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($admin['username']); ?></p>
                <p><strong>Permissão:</strong> <?php echo htmlspecialchars($admin['role']); ?></p>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                    <button type="submit" name="edit" class="btn edit-btn">Editar Permissões</button>
                    <?php if ($admin['role'] !== 'superadmin'): ?>
                        <button type="submit" name="delete" class="btn delete-btn" onclick="return confirm('Tem a certeza que deseja excluir este admin?');">Excluir</button>
                    <?php endif; ?>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Nenhum administrador encontrado.</p>
    <?php endif; ?>

    <a href="admin_dashboard.php" class="back-btn">Voltar ao Painel</a>
</body>
</html>

<?php
$conn->close();
?>
