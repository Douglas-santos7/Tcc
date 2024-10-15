/*======================
Script | Select categorias
========================*/
// Referências aos elementos
const btnSelecionarCategoria = document.getElementById("btn-selecionar-categoria");
const popupCategorias = document.getElementById("popup-categorias-unico");
const btnFecharPopup = document.getElementById("btn-fechar-popup-categorias");
const categoriaItems = document.querySelectorAll(".categoria-item-unico");
const categoriaInput = document.getElementById("categoria-id");

// Abrir o popup quando o botão for clicado
btnSelecionarCategoria.addEventListener("click", function () {
  popupCategorias.style.display = "flex";
});

// Fechar o popup ao clicar no botão de fechar
btnFecharPopup.addEventListener("click", function () {
  popupCategorias.style.display = "none";
});

// Fechar o popup ao clicar fora do conteúdo
window.addEventListener("click", function (event) {
  if (event.target === popupCategorias) {
    popupCategorias.style.display = "none";
  }
});

// Selecionar uma categoria e fechar o popup
categoriaItems.forEach(function (item) {
  item.addEventListener("click", function () {
    const categoriaId = this.getAttribute("data-id");
    const categoriaNome = this.innerText;

    // Atualiza o texto do botão e o input escondido
    btnSelecionarCategoria.innerText = "Categoria: " + categoriaNome;
    categoriaInput.value = categoriaId;

    // Fecha o popup
    popupCategorias.style.display = "none";
  });
});

let ordemAtual = "A-Z"; // Estado inicial

document.getElementById("botao-filtro-categorias").addEventListener("click", function () {
  const listaCategorias = document.getElementById("lista-categorias");
  const itens = Array.from(listaCategorias.querySelectorAll(".categoria-item-unico"));

  // Alternar o texto do botão
  if (ordemAtual === "A-Z") {
    ordemAtual = "Mais usadas";
    this.textContent = "Mais usadas";
    //  ordenar as categorias com base nas mais usadas.
    itens.sort((a, b) => {
      return Math.random() - 0.5; // Aqui, deve ser a lógica para ordenar as mais usadas
    });
  } else {
    ordemAtual = "A-Z";
    this.textContent = "A-Z";
    // Ordenar as categorias em ordem alfabética
    itens.sort((a, b) => {
      const nomeA = a.querySelector(".categoria-nome").textContent.toLowerCase();
      const nomeB = b.querySelector(".categoria-nome").textContent.toLowerCase();
      return nomeA.localeCompare(nomeB);
    });
  }

  // Atualizar a lista de categorias no DOM
  listaCategorias.innerHTML = ""; // Limpar a lista
  itens.forEach((item) => listaCategorias.appendChild(item)); // Adicionar novamente na nova ordem
});
