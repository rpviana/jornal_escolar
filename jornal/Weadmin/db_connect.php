<?php
// db_connect.php

$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "school_journal";  // Nome da db

// Criação da conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificação de erro na conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
