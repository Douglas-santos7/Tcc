/*======================
Script | Btn PopUp Principal
========================*/
// Captura o novo botão de abrir popup com o ícone
const openPopupIcon = document.getElementById("btn--abrir--popup");
const closePopupBtn = document.getElementById("close-btn");
const popupContainer = document.getElementById("popup-container");

// Abrir o popup ao clicar no ícone de adicionar
openPopupIcon.addEventListener("click", function () {
  popupContainer.style.display = "flex"; // Mostrar o popup
});

// Fechar o popup ao clicar no botão fechar
closePopupBtn.addEventListener("click", function () {
  popupContainer.style.display = "none"; // Esconder o popup
});

// Fechar o popup ao clicar fora dele
window.addEventListener("click", function (event) {
  if (event.target === popupContainer) {
    popupContainer.style.display = "none"; // Esconder o popup
  }
});

window.onload = function () {
  // Seleciona os elementos de receitas e despesas
  var receitas = document.querySelector(".grafico--receitas");
  var despesas = document.querySelector(".grafico--despesas");

  // Obtém o valor da largura a partir dos atributos de dados
  var larguraReceitas = receitas.getAttribute("data-largura");
  var larguraDespesas = despesas.getAttribute("data-largura");

  // Define a largura final, ativando a animação
  receitas.style.width = larguraReceitas + "px";
  despesas.style.width = larguraDespesas + "px";
};
