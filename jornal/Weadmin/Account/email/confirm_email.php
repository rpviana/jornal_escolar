<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Certifica-te de que o PHPMailer está instalado corretamente

session_start();

$conn = new mysqli('localhost', 'root', '', 'school_journal');

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id, username, created_at, email, email_verified FROM admins WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($id, $username, $created_at, $email, $email_verified);
$stmt->fetch();
$stmt->close();

// Processar adição de email
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_email'])) {
    $new_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        echo "<p>Email inválido.</p>";
    } else {
        $token = bin2hex(random_bytes(32));

        $stmt = $conn->prepare("UPDATE admins SET email = ?, email_token = ?, email_verified = 0 WHERE id = ?");
        $stmt->bind_param("ssi", $new_email, $token, $id);
        if ($stmt->execute()) {
            // Enviar email de verificação
            $verification_link = "http://yourwebsite.com/confirm_email.php?token=$token";
            
            $mail = new PHPMailer(true);
            try {
                // Configurações do servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.seuprovedor.com'; // Substituir pelo SMTP do teu provedor
                $mail->SMTPAuth = true;
                $mail->Username = 'teuemail@seuprovedor.com'; // Teu email SMTP
                $mail->Password = 'tuapalavra-passe'; // Senha do email SMTP
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587; // Porta do SMTP
                
                // Configuração do email
                $mail->setFrom('teuemail@seuprovedor.com', 'School Journal');
                $mail->addAddress($new_email);
                $mail->Subject = 'Confirme seu Email';
                $mail->Body = "Clique no link para confirmar seu email: $verification_link";
                
                $mail->send();
                echo "<p>Um email de confirmação foi enviado para $new_email</p>";
            } catch (Exception $e) {
                echo "<p>Erro ao enviar email: {$mail->ErrorInfo}</p>";
            }
        }
        $stmt->close();
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
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 400px; margin: auto; }
        input, button { margin: 10px 0; padding: 10px; width: 100%; }
        button { background-color: #4CAF50; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Perfil</h1>
        <p><strong>Usuário:</strong> <?php echo htmlspecialchars($username); ?></p>
        <p><strong>Conta criada em:</strong> <?php echo $created_at; ?></p>
        <p><strong>Email:</strong> <?php echo $email ? htmlspecialchars($email) : "Não adicionado"; ?></p>

        <?php if (!$email_verified): ?>
            <form method="POST">
                <input type="email" name="email" placeholder="Digite seu email" required>
                <button type="submit" name="add_email">Adicionar Email</button>
            </form>
        <?php else: ?>
            <p>Email verificado ✅</p>
        <?php endif; ?>

        <a href="logout.php">Sair</a>
    </div>
</body>
</html>
