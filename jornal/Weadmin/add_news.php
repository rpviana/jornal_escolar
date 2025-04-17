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
        echo "<p class='alert success'>✅ Notícia adicionada com sucesso!</p>";
    } else {
        echo "<p class='alert error'>❌ Erro: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Notícia</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="main-wrapper">
        <header class="header-container">
            <h1>Adicionar Nova Notícia</h1>
        </header>

        <div class="form-wrapper">
            <form method="POST" enctype="multipart/form-data" class="news-form">
                <label for="title" class="input-label">Título da Notícia:</label>
                <input type="text" name="title" id="title" class="input-field" placeholder="Insira o título da notícia" required>

                <label for="content" class="input-label">Conteúdo:</label>
                <textarea name="content" id="content" class="input-textarea" placeholder="Digite o conteúdo da notícia" rows="6" required></textarea>

                <label for="main_image" class="input-label">Imagem Principal:</label>
                <div class="file-upload-box">
                    <input type="file" name="main_image" id="main_image" class="file-input" accept="image/*" required>
                    <div class="file-input-text">Arraste ou clique para selecionar a imagem principal</div>
                </div>

                <label for="additional_images" class="input-label">Imagens Adicionais:</label>
                <div class="file-upload-box">
                    <input type="file" name="additional_images[]" id="additional_images" class="file-input" accept="image/*" multiple>
                    <div class="file-input-text">Arraste ou clique para selecionar imagens adicionais</div>
                </div>

                <button type="submit" class="submit-button">Publicar Notícia</button>
            </form>

            <a href="admin_dashboard.php" class="back-button">Voltar ao Painel</a>
        </div>
    </div>
</body>
<script>
document.querySelectorAll('.file-upload-box').forEach(box => {
    const input = box.querySelector('input');
    const text = box.querySelector('.file-input-text');

    // Clica na caixa → ativa o input real
    box.addEventListener('click', () => input.click());

    // Atualiza texto com o nome do ficheiro
    input.addEventListener('change', () => {
        if (input.files.length === 1) {
            text.textContent = input.files[0].name;
        } else if (input.files.length > 1) {
            text.textContent = input.files.length + " ficheiros selecionados";
        }
    });

    // Impede que o browser abra a imagem ao arrastar
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        box.addEventListener(eventName, e => {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    // Visual feedback
    box.addEventListener('dragover', () => box.classList.add('drag-over'));
    box.addEventListener('dragleave', () => box.classList.remove('drag-over'));
    box.addEventListener('drop', e => {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
        box.classList.remove('drag-over');
    });
});
</script>

</html>
