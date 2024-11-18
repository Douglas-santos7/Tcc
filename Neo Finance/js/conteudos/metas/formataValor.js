function formatarMoeda(valor) {
  // Remove todos os caracteres que não são dígitos
  valor = valor.replace(/\D/g, "");

  // Limita o valor a 8 dígitos antes da vírgula (999.999,99)
  if (valor.length > 8) {
    valor = valor.slice(0, 8); // Limita a 8 caracteres (6 dígitos inteiros + 2 decimais)
  }

  // Formata para moeda
  let valorFormatado = (valor / 100)
    .toFixed(2) // Converte para decimal e fixa em 2 casas decimais
    .replace(".", ",") // Substitui o ponto decimal pela vírgula
    .replace(/\B(?=(\d{3})+(?!\d))/g, "."); // Adiciona pontos para os milhares

  return valorFormatado;
}

// Formata valor da meta
document.getElementById("valor_meta").addEventListener("input", function () {
  let valorAtual = this.value;

  // Remove formatação e formata novamente
  this.value = formatarMoeda(valorAtual.replace(/\D/g, ""));
});

// Formata valor resgatado
document.getElementById("valor_resgatar").addEventListener("input", function () {
  let valorAtual = this.value;

  // Remove formatação e formata novamente
  this.value = formatarMoeda(valorAtual.replace(/\D/g, ""));
});

// Formata Valor Deposito
document.getElementById("valor_deposito").addEventListener("input", function () {
  let valorAtual = this.value;

  // Remove formatação e formata novamente
  this.value = formatarMoeda(valorAtual.replace(/\D/g, ""));
});
