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
    <!-- Conexão com o CSS externo -->
    <link rel="stylesheet" href="http://localhost/jornal/css/style.css">
</head>
<body>
    <h1>Editar/Excluir Notícias</h1>

    <?php if ($edit_mode && $edit_news): ?>
        <!-- Formulário de Edição -->
        <div class="form-container-editdelete">
            <h2>Editar Notícia</h2>
            <form method="POST">
                <input type="hidden" name="news_id" value="<?php echo $edit_news['id']; ?>">
                <label>Título:</label><br>
                <input type="text" name="title" value="<?php echo htmlspecialchars($edit_news['title']); ?>" required><br><br>
                <label>Conteúdo:</label><br>
                <textarea name="content" rows="5" required><?php echo htmlspecialchars($edit_news['content']); ?></textarea><br><br>
                <button type="submit" name="update" class="btn-editdelete save-btn-editdelete">Salvar Alterações</button>
                <a href="edit_delete_news.php" class="btn-editdelete delete-btn-editdelete">Cancelar</a>
            </form>
        </div>
    <?php else: ?>
        <!-- Lista de Notícias -->
        <div class="news-container-editdelete">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($news = $result->fetch_assoc()): ?>
                    <div class="news-item-editdelete">
                        <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                        <p><strong>Data:</strong> <?php echo htmlspecialchars($news['created_at']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($news['content'])); ?></p>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="news_id" value="<?php echo $news['id']; ?>">
                            <button type="submit" name="edit" class="btn-editdelete edit-btn-editdelete">Editar</button>
                            <button type="submit" name="delete" class="btn-editdelete delete-btn-editdelete" onclick="return confirm('Tem certeza que deseja excluir esta notícia?');">Excluir</button>
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

    <!-- Conexão com o JS externo -->
    <script src="../js/script.js"></script>
</body>
</html>
