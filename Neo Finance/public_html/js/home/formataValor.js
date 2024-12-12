// Função para formatar o valor como moeda
function formatarMoeda(valor) {
  valor = valor.replace(/\D/g, "");
  if (valor.length > 8) {
    valor = valor.slice(0, 8);
  }
  let valorFormatado = (valor / 100)
    .toFixed(2)
    .replace(".", ",")
    .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  return "R$ " + valorFormatado;
}
