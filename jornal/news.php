<?php
$conn = new mysqli('localhost', 'root', '', 'school_journal');

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$news = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($news['title']); ?></title>
    <style>
        img { max-width: 600px; margin: 10px 0; }
        .date { font-size: 14px; color: gray; }
    </style>
</head>
<body>
    <h1><?php echo htmlspecialchars($news['title']); ?></h1>
    <p class="date"><?php echo $news['created_at']; ?></p>
    <img src="<?php echo htmlspecialchars($news['main_image']); ?>" alt="Imagem Principal">
    <p><?php echo nl2br(htmlspecialchars($news['content'])); ?></p>

    <?php 
    $additional_images = json_decode($news['additional_images'], true);
    if (!empty($additional_images)) {
        foreach ($additional_images as $image) {
            echo "<img src='" . htmlspecialchars($image) . "' alt='Imagem Adicional'>";
        }
    }
    ?>
    <a href="index.php">Voltar</a>
</body>
</html>
