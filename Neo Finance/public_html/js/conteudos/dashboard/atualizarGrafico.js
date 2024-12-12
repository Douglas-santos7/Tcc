function formatCurrency(value) {
  return `R$ ${value.toLocaleString("pt-BR", { minimumFractionDigits: 2 })}`;
}

function animateNumber(element, start, end, duration) {
  const range = end - start;
  const startTime = performance.now();

  function updateNumber(currentTime) {
    const elapsedTime = currentTime - startTime;
    const progress = Math.min(elapsedTime / duration, 1);
    const currentValue = start + range * progress;

    // Update the text of the element using the formatted currency
    element.textContent = formatCurrency(currentValue);

    if (progress < 1) {
      requestAnimationFrame(updateNumber);
    }
  }

  requestAnimationFrame(updateNumber);
}

function atualizarGrafico() {
  const periodo = document.getElementById("Filtro--mes").value;

  fetch(`../../config/conteudos/dashboard/filtrar_mes.php?periodo=${periodo}`)
    .then((response) => response.json())
    .then((data) => {
      const graficoReceitas = document.querySelector(".grafico--receitas");
      const graficoDespesas = document.querySelector(".grafico--despesas");
      const graficoBalanco = document.querySelector(".grafico--balanco"); // Gráfico de balanço
      const receitasFiltro = document.querySelector(".receitas--filtro span:last-child");
      const despesasFiltro = document.querySelector(".despesas--filtro span:last-child");
      const saldoFiltro = document.querySelector(".saldo--filtro span:last-child");

      // Update the widths of the charts
      graficoReceitas.style.width = `${data.proporcaoReceitas}px`;
      graficoDespesas.style.width = `${data.proporcaoDespesas}px`;
      graficoBalanco.style.width = `${data.proporcaoBalanco}px`; // Update the balance chart

      // Convert the revenues, expenses, and balance to numbers
      const newReceitas = parseFloat(data.receitas.replace(".", "").replace(",", "."));
      const newDespesas = parseFloat(data.despesas.replace(".", "").replace(",", "."));
      const newSaldo = parseFloat(data.balanco.replace(".", "").replace(",", "."));

      // Animate the numbers
      animateNumber(
        receitasFiltro,
        parseFloat(receitasFiltro.textContent.replace("R$ ", "").replace(".", "").replace(",", ".")),
        newReceitas,
        500
      );
      animateNumber(
        despesasFiltro,
        parseFloat(despesasFiltro.textContent.replace("R$ ", "").replace(".", "").replace(",", ".")),
        newDespesas,
        500
      );
      animateNumber(
        saldoFiltro,
        parseFloat(saldoFiltro.textContent.replace("R$ ", "").replace(".", "").replace(",", ".")),
        newSaldo,
        500
      );
    })
    .catch((error) => console.error("Erro ao buscar dados:", error));
}
