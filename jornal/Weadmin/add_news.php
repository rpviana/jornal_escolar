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

// Garantir que a pasta uploads existe
$upload_dir = __DIR__ . '/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Processar o formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $main_image = '';
    $additional_images = [];

    // Processar a imagem principal
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $main_image = 'uploads/' . basename($_FILES['main_image']['name']);
        move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_dir . basename($_FILES['main_image']['name']));
    }

    // Processar imagens adicionais
    if (isset($_FILES['additional_images'])) {
        foreach ($_FILES['additional_images']['tmp_name'] as $index => $tmp_name) {
            if ($_FILES['additional_images']['error'][$index] == 0) {
                $file_name = basename($_FILES['additional_images']['name'][$index]);
                $file_path = 'uploads/' . $file_name;
                move_uploaded_file($tmp_name, $upload_dir . $file_name);
                $additional_images[] = $file_path;
            }
        }
    }

    $additional_images_json = json_encode($additional_images);

    // Inserir na base de dados
    $stmt = $conn->prepare("INSERT INTO news (title, content, main_image, additional_images, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $title, $content, $main_image, $additional_images_json);

    if ($stmt->execute()) {
        echo "<p>✅ Notícia adicionada com sucesso!</p>";
    } else {
        echo "<p>❌ Erro: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Notícia</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { display: flex; flex-direction: column; max-width: 400px; }
        input, textarea { margin-bottom: 10px; padding: 8px; }
        button { padding: 10px; cursor: pointer; background-color: #4CAF50; color: white; }
        a { margin-top: 10px; display: inline-block; }
    </style>
</head>
<body>
    <h1>Adicionar Notícia</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Título" required>
        <textarea name="content" placeholder="Conteúdo da notícia" rows="5" required></textarea>
        <label>Imagem Principal:</label>
        <input type="file" name="main_image" accept="image/*" required>
        <label>Imagens Adicionais:</label>
        <input type="file" name="additional_images[]" accept="image/*" multiple>
        <button type="submit">Publicar</button>
    </form>
    <a href="admin_dashboard.php">⬅️ Voltar ao Painel</a>
</body>
</html>
