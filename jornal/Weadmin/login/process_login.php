<?php
session_start();
include_once('../includes/database.php');

// Configuração
$max_attempts = 3; // Número máximo de tentativas
$lockout_time = 300; // Tempo de bloqueio em segundos (5 minutos)

// Captura o IP do utilizador
$ip_address = $_SERVER['REMOTE_ADDR'];

// Verifica se o IP já está na tabela de tentativas
$stmt = $conn->prepare("SELECT attempts, last_attempt FROM failed_logins WHERE ip_address = ?");
$stmt->bind_param("s", $ip_address);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $attempts = $row['attempts'];
    $last_attempt = strtotime($row['last_attempt']);
    $current_time = time();

    // Se ultrapassar o número máximo de tentativas e estiver no tempo de bloqueio
    if ($attempts >= $max_attempts && ($current_time - $last_attempt) < $lockout_time) {
        die("A tua conta está temporariamente bloqueada. Tente novamente mais tarde.");
    }
} else {
    // Insere o IP se não existir
    $stmt = $conn->prepare("INSERT INTO failed_logins (ip_address, attempts, last_attempt) VALUES (?, 0, NOW())");
    $stmt->bind_param("s", $ip_address);
    $stmt->execute();
}

// Processamento do login
$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT a.id, a.password, a.role_id, r.role_name FROM admins a JOIN roles r ON a.role_id = r.id WHERE a.username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        $stmt = $conn->prepare("DELETE FROM failed_logins WHERE ip_address = ?");
        $stmt->bind_param("s", $ip_address);
        $stmt->execute();

        $_SESSION['id'] = $row['id'];  // Guarda o ID do administrador
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $row['role_id'];  // Agora guarda o role_id, não o role_name
        header("Location: ../front-page.php");
        exit();
    } else {
        // Senha incorreta
        $stmt = $conn->prepare("UPDATE failed_logins SET attempts = attempts + 1, last_attempt = NOW() WHERE ip_address = ?");
        $stmt->bind_param("s", $ip_address);
        $stmt->execute();
        die("A senha tá incorreta!");
    }
} else {
    // Utilizador não encontrado
    $stmt = $conn->prepare("UPDATE failed_logins SET attempts = attempts + 1, last_attempt = NOW() WHERE ip_address = ?");
    $stmt->bind_param("s", $ip_address);
    $stmt->execute();
    die("Utilizador não encontrado!");
}
?>
