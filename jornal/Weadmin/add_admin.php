<?php
session_start();
include("db_connect.php");

// Mensagens de sucesso ou erro
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role_id = $_POST['role_id'];

    $stmt = $conn->prepare("INSERT INTO admins (username, email, password, role_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $email, $password, $role_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Administrador adicionado com sucesso!";
        header("Location: add_admin.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Erro ao adicionar administrador.";
        header("Location: add_admin.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Administrador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 650px;
            margin-top: 50px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #343a40;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-primary {
            background-color: #007bff;
            font-weight: 600;
            border: none;
            border-radius: 30px;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            font-weight: 600;
            border: none;
            border-radius: 30px;
            transition: background-color 0.3s;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .alert {
            border-radius: 5px;
            padding: 15px;
        }
        .alert-success {
            background-color: #28a745;
            color: white;
        }
        .alert-danger {
            background-color: #dc3545;
            color: white;
        }
        .form-control, .form-select {
            border-radius: 8px;
        }
        .text-center {
            margin-top: 20px;
        }
        .message {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Adicionar Novo Administrador</h2>

    <?php if ($success_message): ?>
        <div class="alert alert-success message"><?= $success_message ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger message"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST" action="add_admin.php">
        <div class="mb-3">
            <label for="username" class="form-label">Nome de Utilizador</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Palavra-passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="mb-3">
            <label for="role_id" class="form-label">Função</label>
            <select class="form-select" id="role_id" name="role_id" required>
                <option value="1">Superadmin</option>
                <option value="2">Editor</option>
                <option value="3">Moderador</option>
            </select>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Criar Administrador</button>
            <a href="manage_admins.php" class="btn btn-secondary">Voltar</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
