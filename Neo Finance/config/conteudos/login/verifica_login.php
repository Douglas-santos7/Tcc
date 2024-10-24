<?php
session_start();

define('BASE_URL', '/Tcc/Neo%20Finance/');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: " . BASE_URL . "views/login/login.php");
    exit();
}
