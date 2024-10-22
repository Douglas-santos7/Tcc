document.addEventListener("DOMContentLoaded", function () {
  const iframe = document.getElementById("mainIframe");
  const dashboardItem = document.querySelector('.sidebar ul li[data-src="./conteudos/(1) dashboard.php"]');
  const historicoItem = document.querySelector('.sidebar ul li[data-src="./conteudos/(3) historico.php"]');

  // Função para marcar o dashboard como clicado
  function marcarDashboardClicado() {
    // Remove a classe "clicado" de todos os itens
    document.querySelectorAll(".sidebar ul li").forEach(function (item) {
      item.classList.remove("clicado");
    });

    // Adiciona a classe "clicado" ao item "Dashboard"
    dashboardItem.classList.add("clicado");
  }

  // Função para carregar o dashboard no iframe e marcar o item "Dashboard" como clicado
  function carregarDashboard() {
    iframe.src = "./conteudos/(1) dashboard.php";  // Carrega o dashboard no iframe
    document.title = "Neo Finance - Dashboard";    // Atualiza o título da página
    marcarDashboardClicado();                      // Marca o dashboard como clicado
  }

  // Função para atualizar o iframe com o conteúdo do histórico
  function verTudoHistorico() {
    iframe.src = "./conteudos/(3) historico.php";  // Carrega o histórico no iframe
    document.title = "Neo Finance - Histórico";    // Atualiza o título da página

    // Remove a classe "clicado" de todos os itens
    document.querySelectorAll(".sidebar ul li").forEach(function (item) {
      item.classList.remove("clicado");
    });

    // Adiciona a classe "clicado" ao item "Histórico"
    historicoItem.classList.add("clicado");
  }

  // Adiciona event listeners para as funções de clique
  dashboardItem.addEventListener("click", carregarDashboard);
  historicoItem.addEventListener("click", verTudoHistorico);

  // Marca automaticamente o "Dashboard" como ativo e carrega o dashboard ao carregar home.php
  carregarDashboard();

  // Adiciona um event listener para o botão "Ver tudo" dentro do iframe
  iframe.addEventListener("load", function () {
    const iframeDocument = iframe.contentDocument || iframe.contentWindow.document;

    // Verifica se há o botão "Ver tudo" no histórico
    const verTudoBtn = iframeDocument.getElementById("ver-tudo-btn");
    if (verTudoBtn) {
      verTudoBtn.addEventListener("click", function (event) {
        event.preventDefault(); // Impede o comportamento padrão do botão
        verTudoHistorico();     // Atualiza o iframe com o histórico
      });
    }
  });
});
