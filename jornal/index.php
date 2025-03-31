<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Notícias Recentes</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>
<!-- BEGIN wrapper -->
<div id="wrapper">
    <!-- BEGIN header -->
    <div id="header">
        <ul id="nav">
            <li><a href="index.php">HOME</a></li>
            <li><a href="#">ABOUT US</a></li>
            <li><a href="#">DEMO PAGE</a></li>
            <li><a href="#">CONTACT PAGE</a></li>
        </ul>
    </div>
    <!-- END header -->

    <!-- BEGIN navigation -->
    <div id="menu">
        <ul>
            <li><a href="#">Advertising</a></li>
            <li><a href="#">Entertainment</a></li>
            <li><a href="#">Fashion</a></li>
            <li><a href="#">Lifestyle</a></li>
            <li><a href="#">Pictures</a></li>
            <li><a href="#">Videos</a></li>
        </ul>
    </div>
    <!-- END navigation -->

    <!-- BEGIN content -->
    <div id="content">
        <div id="main">
            <?php
            // Conectar ao banco de dados
            $conn = new mysqli('localhost', 'root', '', 'school_journal');
            if ($conn->connect_error) {
                die("Falha na conexão: " . $conn->connect_error);
            }

            // Obter notícias
            $query = "SELECT id, title, created_at, main_image, content FROM news ORDER BY created_at DESC";
            $result = $conn->query($query);

            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()): ?>
                    <div class="post">
                        <img src="<?php echo htmlspecialchars($row['main_image']); ?>" alt="Imagem da notícia">
                        <h2 class="post-title"><a href="news.php?id=<?php echo $row['id']; ?>">
                            <?php echo htmlspecialchars($row['title']); ?></a></h2>
                        <p class="post-date">Publicado em: <?php echo htmlspecialchars($row['created_at']); ?></p>
                        <p><?php echo htmlspecialchars(substr($row['content'], 0, 100)); ?>...</p>
                        <a href="news.php?id=<?php echo $row['id']; ?>" class="read-more">Continue Reading</a>
                    </div>
                <?php endwhile;
            else: ?>
                <p>Não há notícias disponíveis.</p>
            <?php endif; ?>
        </div>

        <div id="sidebar">
            <h3>Notícias Populares</h3>
            <ul>
                <?php
                $popular = $conn->query("SELECT id, title FROM news ORDER BY created_at DESC LIMIT 5");
                while ($pop = $popular->fetch_assoc()): ?>
                    <li><a href="news.php?id=<?php echo $pop['id']; ?>">
                        <?php echo htmlspecialchars($pop['title']); ?></a></li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
    <!-- END content -->

    <!-- BEGIN footer -->
    <div id="footer">
        <p>&copy; 2025 The Web News. Todos os direitos reservados.</p>
    </div>
    <!-- END footer -->
</div>
<!-- END wrapper -->
</body>
</html>
