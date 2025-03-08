<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <!-- Chamadas ao CSS e JS -->
    <link rel="stylesheet" href="css/style.css">
    <script src="script/script.js" defer></script>
</head>
<body>

<!-- Div Container -->
<div class="container-xl px-4 mt-4">
    <!-- Account page navigation-->
    <nav class="nav nav-borders">
        <a class="nav-link active ms-0" href="https://www.bootdey.com/snippets/view/bs5-edit-profile-account-details" target="__blank">Profile</a>
        <a class="nav-link" href="https://www.bootdey.com/snippets/view/bs5-profile-billing-page" target="__blank">Billing</a>
        <a class="nav-link" href="https://www.bootdey.com/snippets/view/bs5-profile-security-page" target="__blank">Security</a>
        <a class="nav-link" href="https://www.bootdey.com/snippets/view/bs5-edit-notifications-page"  target="__blank">Notifications</a>
    </nav>
    <hr class="mt-0 mb-4">
    <div class="row">
        <div class="col-xl-4">
            <!-- Profile picture card-->
            <div class="card mb-4 mb-xl-0">
                <div class="card-header">Profile Picture</div>
                <div class="card-body text-center">
                    <!-- Profile picture image-->
                    <img class="img-account-profile rounded-circle mb-2" src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Foto de Perfil">
                    <!-- Profile picture help block-->
                    <div class="small font-italic text-muted mb-4">JPG ou PNG não maior que 5 MB</div>
                    <!-- Profile picture upload button-->
                    <button class="btn btn-primary" type="button">Upload nova imagem</button>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <!-- Account details card-->
            <div class="card mb-4">
                <div class="card-header">Detalhes da Conta</div>
                <div class="card-body">
                    <form>
                        <!-- Form Group (username)-->
                        <div class="mb-3">
                            <label class="small mb-1" for="inputUsername">Nome de usuário (como o nome será exibido para outros usuários)</label>
                            <input class="form-control" id="inputUsername" type="text" placeholder="Digite seu nome de usuário" value="<?php echo htmlspecialchars($user['username']); ?>">
                        </div>
                        <!-- Form Row-->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (first name)-->
                            <div class="col-md-6">
                                <label class="small mb-1" for="inputFirstName">Primeiro nome</label>
                                <input class="form-control" id="inputFirstName" type="text" placeholder="Digite seu primeiro nome" value="Valerie">
                            </div>
                            <!-- Form Group (last name)-->
                            <div class="col-md-6">
                                <label class="small mb-1" for="inputLastName">Último nome</label>
                                <input class="form-control" id="inputLastName" type="text" placeholder="Digite seu último nome" value="Luna">
                            </div>
                        </div>
                        <!-- Form Group (email address)-->
                        <div class="mb-3">
                            <label class="small mb-1" for="inputEmailAddress">Endereço de e-mail</label>
                            <input class="form-control" id="inputEmailAddress" type="email" placeholder="Digite seu endereço de e-mail" value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        <!-- Save changes button-->
                        <button class="btn btn-primary" type="button">Salvar alterações</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
