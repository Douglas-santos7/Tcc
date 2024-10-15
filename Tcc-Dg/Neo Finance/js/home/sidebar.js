document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.getElementById("sidebar");
  const sidebar_logo = document.getElementById("logo--sidebar");
  const conteudo = document.getElementById("conteudo");
  const toggleButton = document.querySelector(".toggle-button");
  const iframe = document.getElementById("mainIframe");

  // Garante que o item "Home" ou outro padrão esteja selecionado ao carregar a página
  const defaultItem = document.querySelector('.sidebar ul li[data-src="../../views/conteudos/(1) dashboard.php"]');
  if (defaultItem) {
    defaultItem.classList.add("clicado");
    iframe.src = defaultItem.getAttribute("data-src"); // Define o src do iframe como o do item padrão
    // Atualiza o título com base no título do iframe
    iframe.onload = function () {
      document.title = iframe.contentDocument.title; // Define o título da página
    };
  }

  // Alternância de sidebar pelo botão e logo
  toggleButton.addEventListener("click", function () {
    sidebar.classList.toggle("fechado");
    conteudo.classList.toggle("alterado");
  });

  sidebar_logo.addEventListener("click", function () {
    sidebar.classList.toggle("fechado");
    conteudo.classList.toggle("alterado");
  });

  // Adiciona funcionalidade de clique nos itens do menu
  document.querySelectorAll(".sidebar ul li").forEach(function (item) {
    item.addEventListener("click", function () {
      document.querySelectorAll(".sidebar ul li").forEach(function (el) {
        el.classList.remove("clicado");
      });
      item.classList.toggle("clicado");

      // Atualiza o src do iframe com base no item clicado
      const src = item.getAttribute("data-src");
      iframe.src = src;

      // Atualiza o título da página após o iframe ser carregado
      iframe.onload = function () {
        document.title = iframe.contentDocument.title; // Define o título da página
      };
    });
  });

  // Adiciona funcionalidade de logout
  document.getElementById("logout-button").addEventListener("click", function () {
    // Redireciona para o script de logout
    window.location.href = "../config/conteudos/login/logout.php"; // Atualize este caminho se necessário
  });
});
