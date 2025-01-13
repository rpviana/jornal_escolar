<?php
session_start();

// Conexão com a base de dados
$conn = new mysqli('localhost', 'root', '', 'school_journal');

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Verificar se o utilizador está autenticado e é superadmin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    die("Acesso negado. Apenas Superadmins podem acessar esta página.");
}

// Buscar logs diretamente da tabela admin_logs
$query = "
    SELECT id, admin_username, action, timestamp 
    FROM admin_logs
    ORDER BY timestamp DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Admins</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { border: 1px solid #ddd; text-align: center; padding: 8px; }
        table th { background-color: #4CAF50; color: white; }
        table tr:nth-child(even) { background-color: #f2f2f2; }
        table tr:hover { background-color: #ddd; }
        .back-btn { margin-top: 20px; display: inline-block; text-decoration: none; background: #008CBA; color: white; padding: 8px 15px; border-radius: 4px; }
        .back-btn:hover { background: #007bb5; }
    </style>
</head>
<body>
    <h1>Logs de Ações dos Administradores</h1>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Ação</th>
                    <th>Data/Hora</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['id']); ?></td>
                        <td><?php echo htmlspecialchars($log['admin_username']); ?></td>
                        <td><?php echo htmlspecialchars($log['action']); ?></td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($log['timestamp'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum registro de log encontrado.</p>
    <?php endif; ?>

    <a href="admin_dashboard.php" class="back-btn">Voltar ao Painel</a>
</body>
</html>

<?php
$conn->close();
?>
