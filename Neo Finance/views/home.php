<?php
session_start(); // Inicia a sessão
include("../config/database/conexao.php");

// Verifica se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: ../../views/login/login.php"); // Redireciona para a página de login
  exit();
}
?>
<!DOCTYPE html>a
<html lang="pt-br">



<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Neo Finance - Home</title>
  <link rel="stylesheet" href="../css/home.css" />
</head>

<body>
  <div class="container">
    <div class="sidebar" id="sidebar">
      <div class="sidebar-content">
        <div class="logo--sidebar" id="logo--sidebar">
          <img src="../assets/img/neofinance--logo.svg" />
        </div>
        <ul>
          <li class="Item1" data-src="./conteudos/(1) dashboard.php">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/home--icon.svg" />
            <span>Home</span>
          </li>
          <div class="espacamentoLi"></div>
          <li class="Item2" data-src="./conteudos/(2) teste.html">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/graficos--icon.svg" />
            <span>Gráficos</span>
          </li>
          <li class="Item3" data-src="./conteudos/(3) teste.html">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/historico--icon.svg" />
            <span>Histórico</span>
          </li>
          <li class="Item4" data-src="./conteudos/(4) calendario.php">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/calendario--icon.svg" />
            <span>Calendário</span>
          </li>
          <li class="Item5" data-src="./conteudos/(5) teste.html">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/chatbot--icon.svg" />
            <span>ChatBot</span>
          </li>
          <li class="Item6" data-src="./conteudos/(6) Categorias.html">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/categorias--icon.svg" />
            <span>Categorias</span>
          </li>
          <li class="Item7" data-src="./conteudos/(7) teste.html">
            <div class="barra--icon"></div>
            <img class="icon--li" src="../assets/icons/home--sidebar/metas--icon.svg" />
            <span>Metas</span>
          </li>
          <div class="espacamentoLi"></div>
          <li class="Item8" data-src="./conteudos/(8) teste.html">
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
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const sidebar = document.getElementById("sidebar");
      const sidebar_logo = document.getElementById("logo--sidebar");
      const conteudo = document.getElementById("conteudo");
      const toggleButton = document.querySelector(".toggle-button");
      const iframe = document.getElementById("mainIframe");

      // Garante que o item "Home" ou outro padrão esteja selecionado ao carregar a página
      const defaultItem = document.querySelector('.sidebar ul li[data-src="./conteudos/(1) dashboard.php"]');
      if (defaultItem) {
        defaultItem.classList.add("clicado");
        iframe.src = defaultItem.getAttribute("data-src"); // Define o src do iframe como o do item padrão
      }

      // Alternância de sidebar pelo botão e logo
      toggleButton.addEventListener("click", function() {
        sidebar.classList.toggle("fechado");
        conteudo.classList.toggle("alterado");
      });

      sidebar_logo.addEventListener("click", function() {
        sidebar.classList.toggle("fechado");
        conteudo.classList.toggle("alterado");
      });

      // Adiciona funcionalidade de clique nos itens do menu
      document.querySelectorAll(".sidebar ul li").forEach(function(item) {
        item.addEventListener("click", function() {
          document.querySelectorAll(".sidebar ul li").forEach(function(el) {
            el.classList.remove("clicado");
          });
          item.classList.toggle("clicado");

          // Atualiza o src do iframe com base no item clicado
          const src = item.getAttribute("data-src");
          iframe.src = src;
        });
      });

      // Adiciona funcionalidade de logout
      document.getElementById("logout-button").addEventListener("click", function() {
        // Redireciona para o script de logout
        window.location.href = "../config/conteudos/login/logout.php"; // Atualize este caminho se necessário
      });
    });
  </script>
</body>

</html>