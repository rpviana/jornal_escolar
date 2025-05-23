<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'school_journal');

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// IP do utilizador
$user_ip = $_SERVER['REMOTE_ADDR'];

// Verificar tentativas anteriores
$stmt = $conn->prepare("SELECT attempts, last_attempt FROM failed_logins WHERE ip_address = ?");
$stmt->bind_param("s", $user_ip);
$stmt->execute();
$stmt->store_result();

$attempts = 0;
$last_attempt = null;

if ($stmt->num_rows > 0) {
    $stmt->bind_result($attempts, $last_attempt);
    $stmt->fetch();

    if ($attempts >= 3 && (time() - strtotime($last_attempt)) < 300) {
        die("Muitas tentativas falhas. Tente novamente mais tarde.");
    }
}

$stmt->close();

// Processar login
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Atualizado: obter a senha e o role_name através de JOIN com a tabela roles
    $stmt = $conn->prepare("SELECT a.password, r.role_name FROM admins a JOIN roles r ON a.role_id = r.id WHERE a.username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            $stmt = $conn->prepare("DELETE FROM failed_logins WHERE ip_address = ?");
            $stmt->bind_param("s", $user_ip);
            $stmt->execute();
            $stmt->close();

            header("Location: ../front-page.php");
            exit();
        } else {
            $error_message = "Senha incorreta.";
        }
    } else {
        $error_message = "Utilizador não encontrado.";
    }

    if (isset($error_message)) {
        if ($attempts == 0) {
            $stmt = $conn->prepare("INSERT INTO failed_logins (ip_address, attempts, last_attempt) VALUES (?, 1, NOW())");
        } else {
            $stmt = $conn->prepare("UPDATE failed_logins SET attempts = attempts + 1, last_attempt = NOW() WHERE ip_address = ?");
        }
        $stmt->bind_param("s", $user_ip);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Conexão com o CSS -->
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="containerlogin">
        <div class="wrapperlogin">
            <div class="flip-card__inner">
                <div class="titlelogin">Log in</div>
                <form class="flip-card__form" action="login.php" method="POST">
                    <input class="flip-card__input" name="username" placeholder="Username" type="text" required>
                    <div class="password-wrapper">
                        <input id="password" class="flip-card__input" name="password" placeholder="Password" type="password" required>
                        <img id="eye-icon" src="eyes/eye-slash.svg" onclick="togglePasswordVisibility()" alt="Toggle Password">
                    </div>
                    <button class="flip-card__btn" type="submit">Let's go!</button>
                </form>
                <?php if (!empty($error_message)): ?>
                    <div class="error"><?php echo $error_message; ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.src = 'eyes/eye.svg';
    } else {
        passwordInput.type = 'password';
        eyeIcon.src = 'eyes/eye-slash.svg';
    }
}
    </script>
</body>
</html>
