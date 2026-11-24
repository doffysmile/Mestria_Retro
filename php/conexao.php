<?php
$servername = "localhost";
$username = "mestria_app";
$password = 'DEFINA_UMA_SENHA';
$database = "mestria";

$connection = new mysqli(
    $servername,
    $username,
    $password,
    $database
);

if ($connection->connect_error) {
    die("Falha na conexão com o banco de dados.");
}

$connection->set_charset("utf8mb4");
?>
