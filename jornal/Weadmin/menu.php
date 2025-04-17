<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="menu" id="sidebar">
    <ul class="menu-content">
        <li><a href="admin_dashboard.php" class="<?= $current_page == 'admin_dashboard.php' ? 'active' : '' ?>"><span class="material-symbols-outlined">home</span><span>Home</span></a></li>
        <li><a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><span class="material-symbols-outlined">dashboard</span><span>DashBoard</span></a></li>
        <li><a href="news.php" class="<?= $current_page == 'news.php' ? 'active' : '' ?>"><span class="material-symbols-outlined">news</span><span>News</span></a></li>
        <li><a href="analytics.php" class="<?= $current_page == 'analytics.php' ? 'active' : '' ?>"><span class="material-symbols-outlined">analytics</span><span>Analytics</span></a></li>
        <li><a href="settings.php" class="<?= $current_page == 'settings.php' ? 'active' : '' ?>"><span class="material-symbols-outlined">settings</span><span>Settings</span></a></li>
        <li><a href="account.php" class="<?= $current_page == 'account.php' ? 'active' : '' ?>"><span class="material-symbols-outlined">person</span><span>Account</span></a></li>
        <li><a href="report.php" class="<?= $current_page == 'report.php' ? 'active' : '' ?>"><span class="material-symbols-outlined">report</span><span>Report</span></a></li>
        <li><a href="contact.php" class="<?= $current_page == 'contact.php' ? 'active' : '' ?>"><span class="material-symbols-outlined">email</span><span>Contact</span></a></li>
        <li><a href="logout.php" class="<?= $current_page == 'logout.php' ? 'active' : '' ?>"><span class="material-symbols-outlined">logout</span><span>Logout</span></a></li>
    </ul>
</div>
