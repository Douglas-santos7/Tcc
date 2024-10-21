<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /Neo%20Finance/views/login/login.php");
    exit();
}
