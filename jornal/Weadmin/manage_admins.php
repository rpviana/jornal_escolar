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

        header("Location: manage_admin.php");
        exit();
    }
}

// Editar permissões (redirigir e logar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $admin_id = intval($_POST['admin_id']);
    $current_admin_id = $_SESSION['id'];

    $stmt = $conn->prepare("SELECT username FROM admins WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->bind_result($admin_username);
    $stmt->fetch();
    $stmt->close();

    $log_stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, admin_username, action) VALUES (?, ?, ?)");
    $action = "Acedeu à edição de permissões de '$admin_username' (ID: $admin_id)";
    $log_stmt->bind_param("iss", $current_admin_id, $_SESSION['username'], $action);
    $log_stmt->execute();
    $log_stmt->close();

    header("Location: edit_permissions.php?admin_id=$admin_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Administradores</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f9f9f9; }
        h1 { color: #333; }
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
        /* Novo botão no topo direito */
        .new-admin-btn { 
            position: absolute; 
            top: 30px; 
            right: 30px; 
            background-color: #2ecc71; 
            color: white; 
            padding: 10px 20px; 
            border-radius: 5px; 
            font-weight: bold; 
            text-decoration: none; 
        }
        .new-admin-btn:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <h1>Gestão de Administradores</h1>

    <!-- Botão "Novo Admin" no topo direito -->
    <a href="../Weadmin/add_admin.php" class="new-admin-btn">Novo Admin</a>

    <div class="search-box">
        <form method="GET" action="manage_admin.php">
            <input type="text" name="search" class="search-input" placeholder="Procurar por ID ou Username..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="search-button">Pesquisar</button>
        </form>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($admin = $result->fetch_assoc()): ?>
            <div class="admin-box">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($admin['username']); ?></p>
                <p><strong>Permissão:</strong> <?php echo htmlspecialchars($admin['role']); ?></p>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                    <?php if ($admin['role'] !== 'superadmin'): ?>
                        <button type="submit" name="edit" class="btn edit-btn">Editar Permissões</button>
                        <button type="submit" name="delete" class="btn delete-btn" onclick="return confirm('Tens a certeza que queres eliminar este admin?');">Eliminar</button>
                    <?php else: ?>
                        <p><strong>Superadmin</strong> - Não é possível editar ou eliminar.</p>
                    <?php endif; ?>
                </form>
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
