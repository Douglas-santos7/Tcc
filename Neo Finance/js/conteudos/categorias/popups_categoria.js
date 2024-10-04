/* ==================
    FUNÇÕES DE ABRIR E FECHAR POPUP'S 
    ----------------------------------
    PopUp de ADICIONAR NOVA CATEGORIA 
    ================== */

// Seleciona os elementos do DOM
const popupCategoria = document.getElementById("id--popup--categoria");
const fechaBtn = document.getElementById("id--fecha--btn--categoria");
const selecioneIconeBtn = document.querySelector(".selecione-icone");
const iconeGrade = document.querySelector(".icone--grade");

// Função para abrir o popup
function abrirPopup() {
  popupCategoria.style.display = "flex"; // Altera a exibição do popup para flex
  carregarIcones(iconeGrade); // Carrega os ícones ao abrir o popup
}

// Função para fechar o popup
function fecharPopup() {
  popupCategoria.style.display = "none"; // Oculta o popup
}

// Função para mostrar ou ocultar a grade de ícones
function mostrarIcones() {
  iconeGrade.style.display = iconeGrade.style.display === "grid" ? "none" : "grid"; // Alterna a visibilidade da grade
}
function carregarIcones() {
  const iconeGrade = document.getElementById("icone-grade");
  // Adicione a lógica para carregar os ícones conforme necessário
  iconeGrade.style.display = "block"; // Exibir a grade de ícones
}

// Adiciona eventos aos botões
fechaBtn.addEventListener("click", fecharPopup);
selecioneIconeBtn.addEventListener("click", mostrarIcones);

/* ============
      Animação
  ===============*/

// Função para mostrar ou ocultar a grade de ícones
function mostrarIcones() {
  const iconeGrade = document.getElementById("icone-grade");

  if (iconeGrade.style.display === "grid") {
    iconeGrade.classList.add("fechar-icones"); // Adiciona a classe de fechamento
    setTimeout(() => {
      iconeGrade.style.display = "none"; // Oculta a grade após a animação
      iconeGrade.classList.remove("fechar-icones"); // Remove a classe de fechamento
    }, 300); // Tempo correspondente à duração da animação
  } else {
    iconeGrade.style.display = "grid"; // Mostra a grade
    iconeGrade.classList.add("abrir-icones"); // Adiciona a classe de abertura
    setTimeout(() => {
      iconeGrade.classList.remove("abrir-icones"); // Remove a classe de abertura após a animação
    }, 300); // Tempo correspondente à duração da animação
  }
}

// Evento para mostrar a grade de ícones
document.getElementById("selecione-icone").addEventListener("click", mostrarIcones);

/* =============================
      PopUp de EXCLUIR 
    ============================== */

// Seleciona o pop-up de exclusão e o botão de fechar
const popupExcluir = document.getElementById("id--pop--up--excluir");
const btnFecharExcluir = document.getElementById("id--fecha--popup--excluir");

// Seleciona todos os ícones de apagar nas categorias
const iconsApagar = document.querySelectorAll(".icon--apagar");

// Função para abrir o pop-up de exclusão
function abrirPopupExcluir() {
  popupExcluir.style.display = "flex"; // Define o display como 'flex' para abrir o pop-up
}

// Função para fechar o pop-up de exclusão
function fecharPopupExcluir() {
  popupExcluir.style.display = "none"; // Define o display como 'none' para fechar o pop-up
}

// Adiciona evento de clique a cada ícone de apagar
iconsApagar.forEach((icon) => {
  icon.addEventListener("click", abrirPopupExcluir);
});

// Evento para fechar o pop-up quando o botão de fechar é clicado
btnFecharExcluir.addEventListener("click", fecharPopupExcluir);

// Fechar o pop-up ao clicar fora do conteúdo do pop-up
window.addEventListener("click", function (event) {
  if (event.target === popupExcluir) {
    fecharPopupExcluir();
  }
});

/* =============================
      PopUp de EDIÇÃO 
    ============================= */

// Seleciona elementos do DOM
const popupEditar = document.getElementById("id--popup--editar");
const fecharPopupBtn = document.getElementById("id--fecha--popup--editar");
const editarIcon = document.querySelector(".icon--editar");

// Função para abrir o popup de edição
function abrirPopUpEdicao() {
  popupEditar.style.display = "flex"; // Exibe o popup
}

// Função para fechar o popup de edição
function fecharPopUpEdicao() {
  popupEditar.style.display = "none"; // Oculta o popup
}

// Adiciona eventos de clique
if (editarIcon) {
  editarIcon.addEventListener("click", abrirPopUpEdicao); // Abre o popup ao clicar no ícone de editar
}
fecharPopupBtn.addEventListener("click", fecharPopUpEdicao); // Fecha o popup ao clicar no botão de fechar

// Fecha o popup se o usuário clicar fora do conteúdo
popupEditar.addEventListener("click", (event) => {
  if (event.target === popupEditar) {
    fecharPopUpEdicao();
  }
});
