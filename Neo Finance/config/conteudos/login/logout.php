<?php
// Inicia a sessão
session_start();

$_SESSION = array();
session_destroy();

if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, "/");
}
header("Location: ../../../views/login/login.php")
?>
