<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login/login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'school_journal');
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- CSSs -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php include 'menu.php'; ?>

    <div class="main-content">
        <h1>Bem-vindo, <?php echo htmlspecialchars($username); ?>!</h1>

        <a href="add_news.php" class="btn btn-warning mb-2">Adicionar Notícia</a>
        <a href="edit_delete_news.php" class="btn btn-primary mb-2">Editar/Excluir Notícia</a>
        <a href="logout.php" class="btn btn-danger mb-2">Logout</a>

        <?php if ($role === 'superadmin'): ?>
            <a href="manage_admins.php" class="btn btn-dark mb-2">Controlar Admins</a>
            <a href="logs.php" class="btn btn-secondary mb-2">Ver Logs</a>
        <?php endif; ?>
    </div>

</body>
</html>
