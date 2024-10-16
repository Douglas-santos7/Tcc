<?php
session_start();
include("../../database/conexao.php");

// Verifica se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ./login/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $saldo_inicial = $_POST['saldo_inicial'];
    $user_id = $_SESSION['user_id'];

    // Atualiza o saldo do usuário
    $query = "UPDATE users SET saldo = saldo + ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('di', $saldo_inicial, $user_id); // 'd' para decimal, 'i' para integer
    $stmt->execute();

    // Marca que o saldo inicial foi adicionado
    $query = "UPDATE users SET saldo_inicial_adicionado = 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();

    // Redireciona para a página principal após adicionar o saldo
    header("Location: ../../../views/home.php");
    exit();
}
