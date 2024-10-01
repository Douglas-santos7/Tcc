// Função que é chamada ao adicionar receita/despesa
function subValores() {
  // Captura o valor da nova receita/despesa
  const valor = parseFloat(document.getElementById("id--valor").value);
  const tipo = document.getElementById("id--tipo").value;

  // Simulação de atualização (você deve substituir isso pela lógica que adiciona ao banco)
  if (tipo === "Receita") {
    receitas += valor;
  } else if (tipo === "Despesa") {
    despesas += valor;
  }

  // Chama a função para atualizar as barras
  atualizarBarras(receitas, despesas);
}
