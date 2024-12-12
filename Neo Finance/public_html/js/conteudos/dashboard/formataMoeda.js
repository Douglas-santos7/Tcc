document.addEventListener("DOMContentLoaded", function () {
  function formatarMoeda(valor) {
    valor = valor.replace(/\D/g, "");

    if (valor.length > 8) {
      valor = valor.slice(0, 8);
    }

    let valorFormatado = (valor / 100)
      .toFixed(2)
      .replace(".", ",")
      .replace(/\B(?=(\d{3})+(?!\d))/g, ".");

    return valorFormatado;
  }

  // Função para adicionar o listener ao campo de valor
  function adicionarListener() {
    const inputValor = document.getElementById("valor");

    if (inputValor) {
      // Verifica se o elemento existe
      inputValor.addEventListener("input", function () {
        let valorAtual = this.value;
        this.value = formatarMoeda(valorAtual.replace(/\D/g, ""));
      });
    } else {
      console.warn("Elemento com ID 'valor' não encontrado. O listener não será adicionado.");
    }
  }

  // Chame a função para adicionar o listener
  adicionarListener();

  // Se o pop-up for aberto, adicione o listener novamente (se necessário)
  const btnOpenPopup = document.getElementById("btn-open-popup");
  const popupContainer = document.getElementById("popup-container");

  if (btnOpenPopup) {
    btnOpenPopup.addEventListener("click", function () {
      popupContainer.style.display = "block";
      adicionarListener(); // Adiciona o listener novamente ao abrir o pop-up
    });
  }

  const closeBtn = document.getElementById("close-btn");
  if (closeBtn) {
    closeBtn.addEventListener("click", function () {
      popupContainer.style.display = "none";
    });
  }
});
