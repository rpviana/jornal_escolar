<?php
// Aqui podes adicionar lógica PHP no futuro, como verificar permissões ou carregar itens dinamicamente
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Lateral</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/style.css">

</head>
<body>

    <div class="menu" id="sidebar">
        <ul class="menu-content">
            <li><a href="../../Weadmin/admin_dashboard.php"><span class="material-symbols-outlined">home</span><span>Home</span></a></li>
            <li><a href="../admin_dashboard.php"><span class="material-symbols-outlined">dashboard</span><span>DashBoard</span></a></li>
            <li><a href="#"><span class="material-symbols-outlined">news</span><span>News</span></a></li>
            <li><a href="#"><span class="material-symbols-outlined">analytics</span><span>Analytics</span></a></li>
            <li><a href="#"><span class="material-symbols-outlined">settings</span><span>Settings</span></a></li>
            <li><a href="Account/account.php"><span class="material-symbols-outlined">person</span><span>Account</span></a></li>
            <li><a href="#"><span class="material-symbols-outlined">report</span><span>Report</span></a></li>
            <li><a href="#"><span class="material-symbols-outlined">email</span><span>Contact</span></a></li>
            <li><a href="logout/logout.php"><span class="material-symbols-outlined">logout</span><span>Logout</span></a></li>
        </ul>
    </div>

</body>
</html>
