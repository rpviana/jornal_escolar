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

    $stmt = $conn->prepare("SELECT password, role FROM admins WHERE username = ?");
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;900&display=swap');

        * {
            font-family: 'Poppins', sans-serif;
        }

        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .wrapper {
            --input-focus: #f02df0;
            --font-color: #323232;
            --font-color-sub: #666;
            --bg-color: #fff;
            --main-color: #323232;
        }

        .flip-card__inner {
            width: 300px;
            height: 350px;
            text-align: center;
            background: lightgrey;
            border-radius: 5px;
            border: 2px solid var(--main-color);
            box-shadow: 4px 4px var(--main-color);
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .title {
            font-size: 25px;
            font-weight: 900;
            color: var(--main-color);
            margin-bottom: 20px;
        }

        .flip-card__form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .flip-card__input {
            width: 250px;
            height: 40px;
            border-radius: 5px;
            border: 2px solid var(--main-color);
            background-color: var(--bg-color);
            box-shadow: 4px 4px var(--main-color);
            font-size: 15px;
            font-weight: 600;
            color: var(--font-color);
            padding: 5px 10px;
            outline: none;
        }

        .username-wrapper,
        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        #username,
        #password {
            width: 250px;
            height: 40px;
            border-radius: 5px;
            border: 2px solid var(--main-color);
            background-color: var(--bg-color);
            box-shadow: 4px 4px var(--main-color);
            font-size: 15px;
            font-weight: 600;
            color: var(--font-color);
            padding: 5px 10px;
            outline: none;
        }

        #eye-icon {
            position: absolute;
            right: 15px;
            cursor: pointer;
            width: 20px;
            height: 20px;
        }

        .flip-card__btn {
            margin: 20px 0;
            width: 120px;
            height: 40px;
            border-radius: 5px;
            border: 2px solid var(--main-color);
            background-color: var(--bg-color);
            box-shadow: 4px 4px var(--main-color);
            font-size: 17px;
            font-weight: 600;
            color: var(--font-color);
            cursor: pointer;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
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
</head>
<body>
    <div class="container">
        <div class="wrapper">
            <div class="flip-card__inner">
                <div class="title">Log in</div>
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
</body>
</html>
