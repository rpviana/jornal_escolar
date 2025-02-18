<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['username'])) {
    header("Location: login/login.php");
    exit();
}

// Conectar à base de dados
$conn = new mysqli('localhost', 'root', '', 'school_journal');
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id, username, email, created_at, profile_pic FROM admins WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$default_profile_pic = "https://voxnews.com.br/wp-content/uploads/2017/04/unnamed.png";
$profile_pic = $user['profile_pic'] ? $user['profile_pic'] : $default_profile_pic;

// Atualizar email
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p>Email inválido.</p>";
    } else {
        $token = bin2hex(random_bytes(16));
        
        $stmt = $conn->prepare("UPDATE admins SET email = ?, email_token = ? WHERE username = ?");
        $stmt->bind_param("sss", $email, $token, $username);
        if ($stmt->execute()) {
            $confirm_link = "http://localhost/confirm_email.php?token=$token";
            $subject = "Confirme seu e-mail";
            $message = "Clique no link para confirmar seu e-mail: $confirm_link";
            $headers = "From: no-reply@schooljournal.com\r\n";
            
            if (mail($email, $subject, $message, $headers)) {
                echo "<p>Um email de confirmação foi enviado para $email.</p>";
            } else {
                echo "<p>Falha ao enviar o email.</p>";
            }
        }
        $stmt->close();
    }
}

// Upload da foto de perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_pic'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);

    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("UPDATE admins SET profile_pic = ? WHERE username = ?");
        $stmt->bind_param("ss", $target_file, $username);
        $stmt->execute();
        $stmt->close();
        
        header("Location: perfil.php");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Perfil</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; text-align: center; }
        .container { max-width: 400px; margin: auto; background: #f9f9f9; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .profile-pic { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #007BFF; }
        .info { margin-bottom: 10px; }
        .email-form, .profile-form { display: none; margin-top: 10px; }
        button { padding: 10px; background-color: #007BFF; color: white; border: none; cursor: pointer; margin-top: 10px; border-radius: 5px; }
        input { padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
    </style>
    <script>
        function showForm(className) {
            document.querySelector('.' + className).style.display = 'block';
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Perfil</h1>
        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Foto de Perfil" class="profile-pic">
        <form method="POST" enctype="multipart/form-data" class="profile-form">
            <input type="file" name="profile_pic" accept="image/*" required>
            <button type="submit">Alterar Foto</button>
        </form>
        <button onclick="showForm('profile-form')">Mudar Foto</button>
        <p class="info"><strong>Nome de usuário:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p class="info"><strong>Data de criação:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
        <p class="info"><strong>Email:</strong> <?php echo $user['email'] ? htmlspecialchars($user['email']) : 'Nenhum email associado'; ?></p>
        <button onclick="showForm('email-form')">Adicionar/Alterar E-mail</button>
        <form method="POST" class="email-form">
            <input type="email" name="email" placeholder="Digite seu e-mail" required>
            <button type="submit">Confirmar</button>
        </form>
    </div>
</body>
</html>
