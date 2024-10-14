document.addEventListener("DOMContentLoaded", function () {
  const iframe = document.getElementById("mainIframe");
  const historicoItem = document.querySelector('.sidebar ul li[data-src="./conteudos/(3) historico.php"]');

  // Função para atualizar o iframe com o conteúdo do histórico
  function verTudoHistorico() {
    iframe.src = "./conteudos/(3) historico.php";
    document.title = "Neo Finance - Histórico"; // Atualiza o título da página

    // Remove a classe "clicado" de todos os itens
    document.querySelectorAll(".sidebar ul li").forEach(function (item) {
      item.classList.remove("clicado");
    });

    // Adiciona a classe "clicado" ao item "Histórico"
    historicoItem.classList.add("clicado");
  }

  // Adiciona um event listener para o botão "Ver tudo" dentro do iframe
  iframe.addEventListener("load", function () {
    const iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
    const verTudoBtn = iframeDocument.getElementById("ver-tudo-btn");

    if (verTudoBtn) {
      verTudoBtn.addEventListener("click", function (event) {
        event.preventDefault(); // Impede o comportamento padrão do botão
        verTudoHistorico(); // Atualiza o iframe com o histórico
      });
    }
  });
});
