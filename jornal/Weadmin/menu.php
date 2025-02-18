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
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(#051f30, #000000);
            margin: 0;
        }

        .menu {
            position: fixed;
            top: 0;
            left: -260px;
            width: 260px;
            height: 100%;
            transition: .3s;
            background: #ffffff12;
            backdrop-filter: blur(5px);
            box-shadow: 8px 0px 9px 0px #00000014;
            padding: 20px;
        }

        .menu.active {
            left: 0;
        }

        .menu-content li {
            list-style: none;
            border-radius: 0px 50px 50px 0;
            transition: .3s;
            margin-bottom: 20px;
            padding-left: 20px;
        }

        .menu-content li:hover {
            background: #0c0c0c;
        }

        a {
            text-decoration: none;
            color: rgb(213, 213, 213);
            display: flex;
            align-items: center;
            font-family: 'calibri';
        }

        .material-symbols-outlined {
            padding: 10px;
            font-size: 25px;
            margin-right: 10px;
            border-radius: 50%;
            background: #0c0c0c;
        }

        .menu-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            background: none;
            border: none;
            color: white;
            font-size: 30px;
            cursor: pointer;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <button class="menu-toggle" onclick="toggleMenu()">
        <i class="bi bi-list"></i>
    </button>

    <div class="menu" id="sidebar">
        <ul class="menu-content">
            <li><a href="admin_dashboard.php"><span class="material-symbols-outlined">home</span><span>Home</span></a></li>
            <li><a href="#"><span class="material-symbols-outlined">dashboard</span><span>DashBoard</span></a></li>
            <li><a href="#"><span class="material-symbols-outlined">news</span><span>News</span></a></li>
            <li><a href="#"><span class="material-symbols-outlined">analytics</span><span>Analytics</span></a></li>
            <li><a href="#"><span class="material-symbols-outlined">settings</span><span>Settings</span></a></li>
            <li><a href="Account/account.php"><span class="material-symbols-outlined">person</span><span>Account</span></a></li>
            <li><a href="#"><span class="material-symbols-outlined">report</span><span>Report</span></a></li>
            <li><a href="#"><span class="material-symbols-outlined">email</span><span>Contact</span></a></li>
            <li><a href="logout/logout.php"><span class="material-symbols-outlined">logout</span><span>Logout</span></a></li>
        </ul>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById("sidebar").classList.toggle("active");
        }
    </script>
</body>
</html>

