// Função para alternar a frase lembrete
function alternarFraseLembrete() {
  const frases = ["Tudo certo por aqui!", "Você está em dia!"];
  let indiceFrase = 0;

  // Alterna a frase a cada 15 segundos
  setInterval(function () {
    const elemento = document.getElementById("fraseLembrete");

    // Remove a animação existente
    elemento.classList.remove("fade-in");

    // Força o reflow para "resetar" a animação
    void elemento.offsetWidth;

    // Alterna a frase
    indiceFrase = (indiceFrase + 1) % frases.length; // Alterna entre 0 e 1
    elemento.textContent = frases[indiceFrase];

    // Adiciona a classe de animação novamente
    elemento.classList.add("fade-in");
  }, 15000); // Troca a frase a cada 15 segundos
}
