<?php
// Iniciar a sessão
session_start();
include("db_connect.php"); // A ligação à base de dados

// Verificar se o utilizador tem permissão para adicionar admins
if ($_SESSION['role'] != 1) { // Apenas superadmin pode adicionar admins
    header("Location: manage_admin.php");
    exit();
}

// Processar o envio do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encriptar a password
    $role_id = $_POST['role_id'];

    // Inserir na base de dados
    $query = "INSERT INTO admins (username, email, password, role_id) VALUES ('$username', '$email', '$password', '$role_id')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Administrador adicionado com sucesso!";
        header("Location: manage_admin.php");
        exit(); // Garantir que o script pare após o redirecionamento
    } else {
        $_SESSION['error_message'] = "Erro ao adicionar administrador.";
        header("Location: add_admin.php"); // Voltar à página caso haja erro
        exit(); // Garantir que o script pare após o redirecionamento
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
        }

        h2 {
            font-family: 'Arial', sans-serif;
            color: #495057;
        }

        form {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-control, .form-select {
            border-radius: 5px;
        }

        .btn {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">Adicionar Novo Administrador</h2>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="add_admin.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nome de Utilizador</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Papel</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="1">Superadmin</option>
                            <option value="2">Editor</option>
                            <option value="3">Moderador</option>
                        </select>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Adicionar Administrador</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
