document.addEventListener("DOMContentLoaded", function () {
  const iframe = document.getElementById("mainIframe");
  const dashboardItem = document.querySelector('.sidebar ul li[data-src="./conteudos/(1) dashboard.php"]');
  const graficoItem = document.querySelector('.sidebar ul li[data-src="./conteudos/(2) graficos.php"]');
  const historicoItem = document.querySelector('.sidebar ul li[data-src="./conteudos/(3) historico.php"]');
  const chatbotItem = document.querySelector('.sidebar ul li[data-src="./conteudos/(5) chatbot.php"]');

  // Função para remover a classe "clicado" de todos os itens da barra lateral
  function removerClasseClicado() {
    document.querySelectorAll(".sidebar ul li").forEach(function (item) {
      item.classList.remove("clicado");
    });
  }

  // Função para marcar o item do Painel de Controle como clicado
  function marcarDashboardClicado() {
    removerClasseClicado();
    dashboardItem.classList.add("clicado");
  }

  // Função para carregar o Painel de Controle no iframe e atualizar o título
  function carregarDashboard() {
    iframe.src = "./conteudos/(1) dashboard.php";
    document.title = "Neo Finance - Painel de Controle";
    marcarDashboardClicado();
  }

  // Função para carregar o Gráfico no iframe e atualizar o título
  function verTudoGráficos() {
    iframe.src = "./conteudos/(2) graficos.php";
    document.title = "Neo Finance - Gráfico";
    removerClasseClicado();
    graficoItem.classList.add("clicado");
  }

  // Função para carregar o Histórico no iframe e atualizar o título
  function verTudoHistorico() {
    iframe.src = "./conteudos/(3) historico.php";
    document.title = "Neo Finance - Histórico";
    removerClasseClicado();
    historicoItem.classList.add("clicado");
  }

  // Função para carregar o Chatbot no iframe e atualizar o título
  function verTudoChatBot() {
    iframe.src = "./conteudos/(5) chatbot.php";
    document.title = "Neo Finance - ChatBot";
    removerClasseClicado();
    chatbotItem.classList.add("clicado");
  }

  // Adiciona ouvintes de eventos aos itens da barra lateral
  dashboardItem.addEventListener("click", carregarDashboard);
  graficoItem.addEventListener("click", verTudoGráficos);
  historicoItem.addEventListener("click", verTudoHistorico);
  chatbotItem.addEventListener("click", verTudoChatBot);

  // Carrega o Painel de Controle inicialmente
  carregarDashboard();

  // Evento para o carregamento do conteúdo dentro do iframe
  iframe.addEventListener("load", function () {
    const iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
    const verTudoBtn = iframeDocument.getElementById("ver-tudo-btn");
    const cardReceitasDespesas = iframeDocument.getElementById("cardReceitasDespesas");
    const chatBotLink = iframeDocument.getElementById("chatbotLink");

    // Se o botão "Ver tudo" for encontrado, adiciona evento para carregar o Histórico
    if (verTudoBtn) {
      verTudoBtn.addEventListener("click", function (event) {
        event.preventDefault();
        verTudoHistorico();
      });
    }

    // Se o link do Chatbot for encontrado, adiciona evento para carregar o Chatbot
    if (chatBotLink) {
      chatBotLink.addEventListener("click", function (event) {
        event.preventDefault();
        verTudoChatBot();
      });
    }

    // Se o cartão "Receitas e Despesas" for encontrado, adiciona evento para carregar os Gráficos
    if (cardReceitasDespesas) {
      cardReceitasDespesas.addEventListener("click", function (event) {
        event.preventDefault();
        verTudoGráficos();
      });
    }
  });
});
