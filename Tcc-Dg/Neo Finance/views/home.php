<?php
session_start(); // Inicia a sessão
include("../config/database/conexao.php");

// Verifica se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: ./login/login.php"); // Redireciona para a página de login
  exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Neo Finance - Home</title>
  <link rel="stylesheet" href="../css/home.css" />
  <script src="../js/home/sidebar.js" defer></script>
  <script src="../js/home/iframe.js" defer></script>
</head>

<body>
  <div class="container">
    <div class="sidebar" id="sidebar">
      <div class="sidebar-content">
        <div class="logo--sidebar" id="logo--sidebar">
          <img src="../assets/img/neofinance--logo.svg" />
        </div>
        <ul>
          <li class="Item1" data-src="./conteudos/(1) dashboard.php" data-title="Dashboard">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/home--icon.svg" />
            <span>Home</span>
          </li>
          <div class="espacamentoLi"></div>
          <li class="Item2" data-src="./conteudos/(2) graficos.php" data-title="Gráficos">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/graficos--icon.svg" />
            <span>Gráficos</span>
          </li>
          <li class="Item3" data-src="./conteudos/(3) historico.php" data-title="Histórico">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/historico--icon.svg" />
            <span>Histórico</span>
          </li>
          <li class="Item4" data-src="./conteudos/(4) calendario.php" data-title="Calendário">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/calendario--icon.svg" />
            <span>Calendário</span>
          </li>
          <li class="Item5" data-src="./conteudos/(5) teste.php" data-title="ChatBot">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/chatbot--icon.svg" />
            <span>ChatBot</span>
          </li>
          <li class="Item6" data-src="./conteudos/(6) categorias.php" data-title="Categorias">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/categorias--icon.svg" />
            <span>Categorias</span>
          </li>
          <li class="Item7" data-src="./conteudos/(7) teste.html" data-title="Metas">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/metas--icon.svg" />
            <span>Metas</span>
          </li>
          <div class="espacamentoLi"></div>
          <li class="Item8" data-src="./conteudos/(8) teste.php" data-title="Suporte">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/suport--icon.svg" />
            <span>Suporte</span>
          </li>
          <li class="Item9" id="logout-button">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/sair--icon.svg" />
            <span>Sair</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="conteudo" id="conteudo">
    <!-- Conteúdo da página -->
    <iframe id="mainIframe" src="./conteudos/(1) dashboard.php" width="100%" height="100%"></iframe>
    <button class="toggle-button">></button>
  </div>
</body>

</html>