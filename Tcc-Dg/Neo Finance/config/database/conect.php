<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "finance";

// Cria conexão
$mysqli = new mysqli($servername, $username, $password, $database);

// Verifica a conexão
if ($mysqli->connect_error) {
    die("Conexão falhou: " . $mysqli->connect_error);
}

// Define o conjunto de caracteres
$mysqli->set_charset("utf8mb4");

// Define a linguagem para os nomes dos meses
$mysqli->query("SET lc_time_names = 'pt_BR'");

// A partir daqui, você pode executar suas consultas
?>
