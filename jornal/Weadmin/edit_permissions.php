<?php
// Iniciar a sessão
session_start();
include("db_connect.php"); // A ligação à base de dados

// Verificar se o utilizador tem permissão para editar permissões
if ($_SESSION['role'] != 1) { // Apenas superadmin pode editar permissões
    header("Location: manage_admins.php");
    exit();
}

// Processar o envio do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_id = $_POST['admin_id'];
    $role_id = $_POST['role_id'];

    // Atualizar permissões
    $query = "UPDATE admins SET role_id = '$role_id' WHERE id = '$admin_id'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Permissões atualizadas com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar permissões.";
    }
}

// Buscar todos os administradores
$query = "SELECT * FROM admins";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Permissões</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 900px;
            margin-top: 50px;
        }

        h2 {
            font-family: 'Arial', sans-serif;
            color: #495057;
        }

        table {
            margin-top: 20px;
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
    <div class="container">
        <h2 class="text-center mb-4">Editar Permissões dos Administradores</h2>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Papel Atual</th>
                    <th>Alterar Permissões</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php
                            $role_query = "SELECT * FROM roles WHERE id = " . $row['role_id'];
                            $role_result = mysqli_query($conn, $role_query);
                            $role = mysqli_fetch_assoc($role_result);
                            echo $role['role_name'];
                        ?></td>
                        <td>
                            <form method="POST" action="edit_permissions.php">
                                <input type="hidden" name="admin_id" value="<?php echo $row['id']; ?>">
                                <select class="form-select" name="role_id" required>
                                    <option value="1" <?php echo $row['role_id'] == 1 ? 'selected' : ''; ?>>Superadmin</option>
                                    <option value="2" <?php echo $row['role_id'] == 2 ? 'selected' : ''; ?>>Editor</option>
                                    <option value="3" <?php echo $row['role_id'] == 3 ? 'selected' : ''; ?>>Moderador</option>
                                </select>
                                <button type="submit" class="btn btn-primary mt-2">Atualizar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
