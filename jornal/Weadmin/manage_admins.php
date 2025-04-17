<?php
session_start();

// Conexão com a base de dados
$conn = new mysqli('localhost', 'root', '', 'school_journal');
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Só superadmins podem aceder
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    echo "<p>Acesso negado. Apenas Superadmins podem aceder esta página.</p>";
    exit();
}

// Pesquisa (por ID ou username)
$search = '';
$whereClause = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $whereClause = "WHERE a.id LIKE '%$search%' OR a.username LIKE '%$search%'";
}

// Buscar admins com as respetivas permissões
$result = $conn->query("SELECT a.id, a.username, r.role_name as role 
                        FROM admins a 
                        JOIN roles r ON a.role_id = r.id 
                        $whereClause");

// Eliminar admin (exceto superadmins)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $admin_id = intval($_POST['admin_id']);
    $current_admin_id = $_SESSION['id'];

    // Verificar se o admin a apagar é superadmin
    $stmt = $conn->prepare("SELECT a.username, r.role_name FROM admins a JOIN roles r ON a.role_id = r.id WHERE a.id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->bind_result($admin_username, $role_name);
    $stmt->fetch();
    $stmt->close();

    if ($role_name === 'superadmin') {
        echo "<p>Erro: Não podes apagar um superadmin.</p>";
    } else {
        $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $stmt->close();

        $log_stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action) VALUES (?, ?)");
        $action = "Eliminou admin '$admin_username' (ID: $admin_id)";
        $log_stmt->bind_param("is", $current_admin_id, $action);
        $log_stmt->execute();
        $log_stmt->close();

        header("Location: manage_admins.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Administradores</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f9f9f9; }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
        }
        h1 { color: #333; margin: 0; }
        .add-admin-btn {
            background: linear-gradient(135deg, #00b894, #00cec9);
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        .add-admin-btn:hover {
            background: linear-gradient(135deg, #00cec9, #00b894);
            transform: scale(1.05);
        }
        .admin-box { background: white; padding: 15px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ccc; }
        .admin-box p { margin: 8px 0; }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .edit-btn { background-color: #3498db; color: white; }
        .edit-btn:hover { background-color: #2980b9; }
        .delete-btn { background-color: #e74c3c; color: white; }
        .delete-btn:hover { background-color: #c0392b; }
        .search-box { margin-bottom: 20px; }
        .search-input { padding: 8px; width: 250px; border-radius: 4px; border: 1px solid #ccc; }
        .search-button { padding: 8px 12px; background-color: #2ecc71; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .search-button:hover { background-color: #27ae60; }
        .back-btn { display: inline-block; margin-top: 30px; padding: 10px 20px; background: #8e44ad; color: white; text-decoration: none; border-radius: 5px; }
        .back-btn:hover { background: #71368a; }
    </style>
</head>
<body>

    <div class="top-bar">
        <h1>Gestão de Administradores</h1>
        <a href="add_admin.php" class="add-admin-btn">Criar Admin</a>
    </div>

    <div class="search-box">
        <form method="GET" action="manage_admins.php">
            <input type="text" name="search" class="search-input" placeholder="Procurar por ID ou Username..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="search-button">Pesquisar</button>
        </form>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($admin = $result->fetch_assoc()): ?>
            <div class="admin-box">
            <p><strong>Id:</strong> <span style="color: red;"><?php echo htmlspecialchars($admin['id']); ?></span></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($admin['username']); ?></p>
                <p><strong>Permissão:</strong> <?php echo htmlspecialchars($admin['role']); ?></p>

                <?php if ($admin['role'] !== 'superadmin'): ?>
                    <!-- Botão Editar Permissões -->
                    <form method="GET" action="edit_permissions.php" style="display:inline;">
                        <button type="submit" name="id" value="<?php echo $admin['id']; ?>" class="btn edit-btn">Editar Permissões</button>
                    </form>

                    <!-- Botão Eliminar -->
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Tens a certeza que queres eliminar este admin?');">
                        <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                        <button type="submit" name="delete" class="btn delete-btn">Eliminar</button>
                    </form>
                <?php else: ?>
                    <p><strong>Superadmin</strong> - Não é possível editar ou eliminar.</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Nenhum administrador encontrado para o termo de pesquisa "<?php echo htmlspecialchars($search); ?>".</p>
    <?php endif; ?>

    <a href="admin_dashboard.php" class="back-btn">Voltar ao Painel</a>
</body>
</html>

<?php
$conn->close();
?>
