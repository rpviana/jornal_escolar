<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Verificar se o admin está logado
if (!isset($_SESSION['username'])) {
    header("Location: login/login.php");
    exit();
}

// Conexão com a base de dados
$conn = new mysqli('localhost', 'root', '', 'school_journal');
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Buscar o cargo (role) do admin através de join entre admins e roles
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT r.role_name FROM admins a JOIN roles r ON a.role_id = r.id WHERE a.username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$role = $admin['role_name'];
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .btn { display: inline-block; padding: 10px 15px; margin: 5px; text-decoration: none; color: white; border-radius: 5px; }
        .btn-add { background-color: #4CAF50; }
        .btn-edit { background-color: #2196F3; }
        .btn-logout { background-color: #f44336; }
        .btn-admins { background-color: #ff9800; }
        .btn-logs { background-color: #D2B48C; }
    </style>
</head>
<body>
    <h1>Bem-vindo, <?php echo htmlspecialchars($username); ?>!</h1>
    <a href="add_news.php" class="btn btn-add">Adicionar Notícia</a>
    <a href="edit_delete_news.php" class="btn btn-edit">Editar/Excluir Notícia</a>
    <a href="logout.php" class="btn btn-logout">Logout</a>
    
    <?php if ($role === 'superadmin'): ?>
        <a href="manage_admins.php" class="btn btn-admins">Controlar Admins</a>
        <a href="logs.php" class="btn btn-logs">Ver logs</a>
    <?php endif; ?>
</body>
</html>
