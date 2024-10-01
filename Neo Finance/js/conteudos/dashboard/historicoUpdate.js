// Função para formatar o valor para o formato monetário
// Função para formatar o valor para o formato monetário
function formatarMoeda(valor) {
  // Certifique-se de que o valor é um número
  valor = parseFloat(valor);
  if (isNaN(valor)) {
    return "R$ 0,00"; // Retorna 0 se o valor não for numérico
  }

  return valor.toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
    minimumFractionDigits: 2, // Garante duas casas decimais
    maximumFractionDigits: 2,
  });
}

// Função para buscar o histórico de transações
function buscarHistorico() {
  fetch("../../../config/conteudos/dashboard/get_historico.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      const historicoList = document.getElementById("historicoList");
      historicoList.innerHTML = ""; // Limpa a lista existente

      // Verifica se há dados
      if (data.length > 0) {
        data.forEach((item) => {
          const listItem = document.createElement("li");
          listItem.innerHTML = `
                        <div class="parte--um-info">
                            <div class="img--categoria">
                                <i class="${item.icone}"></i> <!-- Aqui adiciona o ícone -->
                            </div>
                            <div class="info--detalhada">
                                <span class="categoria--historico">${item.categoria}</span> <!-- Exibe a categoria -->
                                <span class="nome--historico">${item.nome}</span>
                            </div>
                        </div>
                        <div class="parte--dois-info">
                            <span class="data--historico">${new Date(item.data).toLocaleDateString("pt-BR")}</span>
                            <span class="valor--historico" style="color: ${item.tipo === "Receita" ? "green" : "red"};">
                                ${formatarMoeda(item.valor)}
                            </span>
                        </div>
                    `;
          historicoList.appendChild(listItem);
        });
      } else {
        historicoList.innerHTML = "<li>Nenhuma transação encontrada.</li>";
      }
    })
    .catch((error) => {
      console.error("Erro ao buscar histórico:", error);
    });
}

// Chamar a função ao carregar a página
document.addEventListener("DOMContentLoaded", buscarHistorico);
