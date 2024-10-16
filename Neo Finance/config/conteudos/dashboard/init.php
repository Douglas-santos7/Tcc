<?php
// Inclua as dependências uma vez
require_once("../../config/database/conexao.php");
require_once("../../config/conteudos/dashboard/config_balanco.php");

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../views/login/login.php");
    exit();
}

// Obter o ID do usuário logado
$userId = $_SESSION['user_id'];
?>
