<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login/login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'school_journal');
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$edit_mode = false;
$edit_news = null;
$search_query = "";

if (isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
    $stmt = $conn->prepare("SELECT * FROM news WHERE id LIKE ? OR title LIKE ?");
    $search_param = "%" . $search_query . "%";  // Para procurar por ID ou título com base no valor fornecido
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt = $conn->prepare("SELECT * FROM news ORDER BY created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();
}

if (isset($_POST['delete'])) {
    $id = $_POST['news_id'];
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    echo "<p class='edit-news-message edit-news-success'>✅ Notícia eliminada com sucesso!</p>";
}

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

if (isset($_POST['update'])) {
    $id = $_POST['news_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $stmt = $conn->prepare("UPDATE news SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);
    if ($stmt->execute()) {
        echo "<p class='edit-news-message edit-news-success'>✅ Notícia atualizada com sucesso!</p>";
    } else {
        echo "<p class='edit-news-message edit-news-error'>❌ Erro: " . $stmt->error . "</p>";
    }
    $stmt->close();
    $edit_mode = false;
}

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerir Notícias</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="edit-news-wrapper">
        <h1 class="edit-news-title">📰 Gerir Notícias</h1>

        <!-- Barra de pesquisa -->
        <form method="POST" class="edit-news-search-form">
            <input type="text" name="search_query" placeholder="Procurar por ID ou Título..." value="<?= htmlspecialchars($search_query) ?>" class="edit-news-search-input">
            <button type="submit" name="search" class="edit-news-btn search">🔍 Procurar</button>
        </form>

        <?php if ($edit_mode && $edit_news): ?>
            <div class="edit-news-form-container">
                <h2 class="edit-news-subtitle">✏️ Editar Notícia</h2>
                <form method="POST" class="edit-news-form">
                    <input type="hidden" name="news_id" value="<?= $edit_news['id'] ?>">
                    <input type="text" name="title" value="<?= htmlspecialchars($edit_news['title']) ?>" required class="edit-news-input">
                    <textarea name="content" rows="5" required class="edit-news-textarea"><?= htmlspecialchars($edit_news['content']) ?></textarea>
                    <div class="edit-news-buttons">
                        <button type="submit" name="update" class="edit-news-btn save">💾 Guardar</button>
                        <a href="edit_delete_news.php" class="edit-news-btn cancel">❌ Cancelar</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="edit-news-list">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($news = $result->fetch_assoc()): ?>
                        <div class="edit-news-card">
                            <h3 class="edit-news-card-title"><?= htmlspecialchars($news['title']) ?></h3>
                            <p class="edit-news-card-date">🗓️ <?= htmlspecialchars($news['created_at']) ?></p>
                            <p class="edit-news-card-content"><?= nl2br(htmlspecialchars($news['content'])) ?></p>
                            <form method="POST" class="edit-news-actions">
                                <input type="hidden" name="news_id" value="<?= $news['id'] ?>">
                                <button type="submit" name="edit" class="edit-news-btn edit">✏️ Editar</button>
                                <button type="submit" name="delete" class="edit-news-btn delete" onclick="return confirm('Tens a certeza que queres eliminar esta notícia?');">🗑️ Eliminar</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="edit-news-message">📭 Nenhuma notícia encontrada.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <a href="admin_dashboard.php" class="edit-news-back">⬅️ Voltar ao Painel</a>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
