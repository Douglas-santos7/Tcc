// Aguarde o DOM ser totalmente carregado
document.addEventListener("DOMContentLoaded", function () {
  // Obtenha referências para o iframe e os itens da barra lateral
  const iframe = document.getElementById("mainIframe");
  const dashboardItem = document.querySelector('.sidebar ul li[data-src="./conteudos/(1) dashboard.php"]');
  const graficoItem = document.querySelector('.sidebar ul li[data-src="./conteudos/(2) graficos.php"]');
  const historicoItem = document.querySelector('.sidebar ul li[data-src="./conteudos/(3) historico.php"]');

  // Função para marcar o item do Painel de Controle como clicado
  function marcarDashboardClicado() {
    // Remova a classe "clicado" de todos os itens da barra lateral
    document.querySelectorAll(".sidebar ul li").forEach(function (item) {
      item.classList.remove("clicado");
    });

    // Adicione a classe "clicado" ao item do Painel de Controle
    dashboardItem.classList.add("clicado");
  }

  // Função para carregar o Painel de Controle no iframe e marcar o item do Painel de Controle como clicado
  function carregarDashboard() {
    iframe.src = "./conteudos/(1) dashboard.php"; // Carregue o Painel de Controle no iframe
    document.title = "Neo Finance - Painel de Controle"; // Atualize o título da página
    marcarDashboardClicado(); // Marque o item do Painel de Controle como clicado
  }

  // Função para atualizar o iframe com o conteúdo do Gráficos
  function verTudoGráficos() {
    iframe.src = "./conteudos/(2) graficos.php"; // Carregue o Gráficos no iframe
    document.title = "Neo Finance - Gráfico"; // Atualize o título da página

    // Remova a classe "clicado" de todos os itens da barra lateral
    document.querySelectorAll(".sidebar ul li").forEach(function (item) {
      item.classList.remove("clicado");
    });

    // Adicione a classe "clicado" ao item do Gráficos
    graficoItem.classList.add("clicado");
  }

  // Função para atualizar o iframe com o conteúdo do Histórico
  function verTudoHistorico() {
    iframe.src = "./conteudos/(3) historico.php"; // Carregue o Histórico no iframe
    document.title = "Neo Finance - Histórico"; // Atualize o título da página

    // Remova a classe "clicado" de todos os itens da barra lateral
    document.querySelectorAll(".sidebar ul li").forEach(function (item) {
      item.classList.remove("clicado");
    });

    // Adicione a classe "clicado" ao item do Histórico
    historicoItem.classList.add("clicado");
  }

  // Adicione ouvintes de eventos aos itens da barra lateral
  dashboardItem.addEventListener("click", carregarDashboard);
  graficoItem.addEventListener("click", verTudoGráficos);
  historicoItem.addEventListener("click", verTudoHistorico);

  // Carregue o Painel de Controle quando home.php for carregado
  carregarDashboard();

  // Adicione ouvintes de eventos para o botão "Ver tudo" e o cartão "Receitas e Despesas" dentro do iframe
  iframe.addEventListener("load", function () {
    const iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
    const verTudoBtn = iframeDocument.getElementById("ver-tudo-btn");
    const cardReceitasDespesas = iframeDocument.getElementById("cardReceitasDespesas");

    // Se o botão "Ver tudo" for encontrado, adicione um ouvinte de eventos de clique para atualizar o iframe com o conteúdo do Histórico
    if (verTudoBtn) {
      verTudoBtn.addEventListener("click", function (event) {
        event.preventDefault(); // Impedir o comportamento padrão do botão
        verTudoHistorico(); // Atualize o iframe com o conteúdo do Histórico
      });
    }

    // Se o cartão "Receitas e Despesas" for encontrado, adicione um ouvinte de eventos de clique para atualizar o iframe com o conteúdo do Gráficos
    if (cardReceitasDespesas) {
      cardReceitasDespesas.addEventListener("click", function (event) {
        event.preventDefault(); // Impedir o comportamento padrão do botão
        verTudoGráficos(); // Atualize o iframe com o conteúdo do Gráficos
      });
    }
  });
});
