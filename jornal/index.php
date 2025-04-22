<?php
$conn = new mysqli('localhost', 'root', '', 'school_journal');
if ($conn->connect_error) {
    die("Erro na ligação à base de dados: " . $conn->connect_error);
}

// Buscar todas as notícias ordenadas por data de criação
$newsQuery = "SELECT id, title, created_at, main_image, content FROM news ORDER BY created_at DESC";
$newsResult = $conn->query($newsQuery);

// Buscar as mais populares (aqui ainda estamos a ordenar por data, podes adaptar para views no futuro)
$popularQuery = "SELECT id, title FROM news ORDER BY created_at DESC LIMIT 5";
$popularResult = $conn->query($popularQuery);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jornal Escolar - Notícias Recentes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div id="wrapper">

    <!-- HEADER -->
    <div id="header">
        <ul id="top-nav">
            <li><a href="index.php">Início</a></li>
            <li><a href="#">Sobre Nós</a></li>
            <li><a href="#">Demonstração</a></li>
            <li><a href="#">Contactos</a></li>
        </ul>
    </div>

    <!-- MENU DE CATEGORIAS -->
    <div id="menu">
        <ul>
            <li><a href="#">Publicidade</a></li>
            <li><a href="#">Entretenimento</a></li>
            <li><a href="#">Moda</a></li>
            <li><a href="#">Estilo de Vida</a></li>
            <li><a href="#">Fotografias</a></li>
            <li><a href="#">Vídeos</a></li>
        </ul>
    </div>

    <!-- CONTEÚDO -->
    <div id="content">
        <div id="main">
            <?php if ($newsResult->num_rows > 0): ?>
                <?php while ($row = $newsResult->fetch_assoc()): ?>
                    <div class="post">
                        <img src="<?= htmlspecialchars($row['main_image']) ?>" alt="Imagem da notícia">
                        <h2 class="post-title">
                            <a href="news.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a>
                        </h2>
                        <p class="post-date">Publicado em: <?= htmlspecialchars(date('d/m/Y', strtotime($row['created_at']))) ?></p>
                        <p><?= htmlspecialchars(mb_strimwidth($row['content'], 0, 120, "...")) ?></p>
                        <a href="news.php?id=<?= $row['id'] ?>" class="read-more">Ler Mais</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Não há notícias disponíveis no momento.</p>
            <?php endif; ?>
        </div>

        <!-- SIDEBAR -->
        <div id="sidebar">
            <h3>Notícias Populares</h3>
            <ul>
                <?php if ($popularResult->num_rows > 0): ?>
                    <?php while ($pop = $popularResult->fetch_assoc()): ?>
                        <li><a href="news.php?id=<?= $pop['id'] ?>"><?= htmlspecialchars($pop['title']) ?></a></li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>Sem dados disponíveis.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- FOOTER -->
    <div id="footer">
        <p>&copy; <?= date("Y") ?> Jornal Escolar. Todos os direitos reservados.</p>
    </div>

</div>
</body>
</html>
