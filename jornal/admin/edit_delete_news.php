<?php
session_start();

// Verificar se o admin está logado
if (!isset($_SESSION['username'])) {
    header("Location: login/login.php");
    exit();
}

// Conexão com a base de dados
$conn = new mysqli('localhost', 'root', '', 'school_journal');
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Inicializar variáveis
$edit_mode = false;
$edit_news = null;

// Excluir notícia
if (isset($_POST['delete'])) {
    $id = $_POST['news_id'];
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    echo "<p>Notícia excluída com sucesso!</p>";
}

// Buscar notícia para edição
if (isset($_POST['edit'])) {
    $id = $_POST['news_id'];
    $stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_news = $result->fetch_assoc();
    $edit_mode = true;
    $stmt->close();
}

// Salvar alterações na notícia
if (isset($_POST['update'])) {
    $id = $_POST['news_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $stmt = $conn->prepare("UPDATE news SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);
    if ($stmt->execute()) {
        echo "<p>Notícia atualizada com sucesso!</p>";
    } else {
        echo "<p>Erro: " . $stmt->error . "</p>";
    }
    $stmt->close();
    $edit_mode = false;
}

// Buscar todas as notícias
$stmt = $conn->prepare("SELECT * FROM news ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar/Excluir Notícias</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .news-container { margin-top: 20px; }
        .news-item { border: 1px solid #ddd; margin-bottom: 10px; padding: 10px; }
        .news-item h3 { margin: 0; }
        .news-item p { margin: 5px 0; }
        .btn { padding: 5px 10px; cursor: pointer; border: none; border-radius: 3px; margin-right: 5px; }
        .edit-btn { background-color: #4CAF50; color: white; }
        .delete-btn { background-color: #f44336; color: white; }
        .edit-btn:hover { background-color: #45a049; }
        .delete-btn:hover { background-color: #da190b; }
        .save-btn { background-color: #008CBA; color: white; }
        .save-btn:hover { background-color: #007bb5; }
        .form-container { margin-top: 20px; border: 1px solid #ddd; padding: 20px; }
    </style>
</head>
<body>
    <h1>Editar/Excluir Notícias</h1>

    <?php if ($edit_mode && $edit_news): ?>
        <!-- Formulário de Edição -->
        <div class="form-container">
            <h2>Editar Notícia</h2>
            <form method="POST">
                <input type="hidden" name="news_id" value="<?php echo $edit_news['id']; ?>">
                <label>Título:</label><br>
                <input type="text" name="title" value="<?php echo htmlspecialchars($edit_news['title']); ?>" required><br><br>
                <label>Conteúdo:</label><br>
                <textarea name="content" rows="5" required><?php echo htmlspecialchars($edit_news['content']); ?></textarea><br><br>
                <button type="submit" name="update" class="btn save-btn">Salvar Alterações</button>
                <a href="edit_delete_news.php" class="btn delete-btn">Cancelar</a>
            </form>
        </div>
    <?php else: ?>
        <!-- Lista de Notícias -->
        <div class="news-container">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($news = $result->fetch_assoc()): ?>
                    <div class="news-item">
                        <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                        <p><strong>Data:</strong> <?php echo htmlspecialchars($news['created_at']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($news['content'])); ?></p>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="news_id" value="<?php echo $news['id']; ?>">
                            <button type="submit" name="edit" class="btn edit-btn">Editar</button>
                            <button type="submit" name="delete" class="btn delete-btn" onclick="return confirm('Tem certeza que deseja excluir esta notícia?');">Excluir</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nenhuma notícia encontrada.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php
    $stmt->close();
    $conn->close();
    ?>
    <br>
    <a href="admin_dashboard.php">Voltar ao Painel</a>
</body>
</html>
