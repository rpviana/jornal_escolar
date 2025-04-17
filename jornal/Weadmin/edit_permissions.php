<?php
session_start();
include("db_connect.php");

// Bloquear acesso direto sem ID
if (!isset($_GET['id'])) {
    header("Location: manage_admins.php");
    exit();
}

$admin_id = intval($_GET['id']);

// Processar atualização
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role_id = intval($_POST['role_id']);

    $update = "UPDATE admins SET username = '$nome', email = '$email', role_id = '$role_id' WHERE id = '$admin_id'";
    if (mysqli_query($conn, $update)) {
        $_SESSION['success_message'] = "Administrador atualizado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar administrador.";
    }

    header("Location: manage_admins.php");
    exit();
}

// Buscar dados do admin selecionado
$query = "SELECT * FROM admins WHERE id = '$admin_id'";
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);

if (!$admin) {
    header("Location: manage_admins.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 600px; margin-top: 60px; }
        h2 { color: #343a40; }
        .btn-primary { margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Editar Administrador</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="username" class="form-control" value="<?php echo $admin['username']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $admin['email']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Cargo</label>
                <select name="role_id" class="form-select" required>
                    <option value="1" <?php if ($admin['role_id'] == 1) echo 'selected'; ?>>Superadmin</option>
                    <option value="2" <?php if ($admin['role_id'] == 2) echo 'selected'; ?>>Editor</option>
                    <option value="3" <?php if ($admin['role_id'] == 3) echo 'selected'; ?>>Moderador</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Alterações</button>
        </form>
    </div>
</body>
</html>
