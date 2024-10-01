<?php

$servername = "localhost"; // Nome do servidor (ou IP)
$username = "root"; // Nome de usuário do banco de dados
$password = ""; // Senha do banco de dados
$dbname = "finance"; // Nome do banco de dados

// Tenta criar a conexão com o banco de dados
try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica se houve algum erro de conexão
    if ($conn->connect_error) {
        throw new Exception("Erro de conexão com o banco de dados.");
    }
} catch (Exception $e) {
    // Redireciona para uma página de manutenção
    header("Location:../../../config/database/maintence.php ");
    exit(); // Encerra o script após o redirecionamento
}
