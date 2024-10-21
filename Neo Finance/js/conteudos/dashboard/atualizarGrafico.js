function animateNumber(element, start, end, duration) {
  const range = end - start;
  const stepTime = Math.abs(Math.floor(duration / range));

  let startTime = performance.now();

  function updateNumber(currentTime) {
    const elapsedTime = currentTime - startTime;
    const progress = Math.min(elapsedTime / duration, 1);
    const currentValue = Math.floor(start + range * progress);

    element.textContent = `R$ ${currentValue.toFixed(2).replace(".", ",")}`;

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
      const receitasFiltro = document.querySelector(".receitas--filtro span:last-child");
      const despesasFiltro = document.querySelector(".despesas--filtro span:last-child");
      const saldoFiltro = document.querySelector(".saldo--filtro span:last-child");

      graficoReceitas.style.width = `${data.proporcaoReceitas}px`;
      graficoDespesas.style.width = `${data.proporcaoDespesas}px`;

      const newReceitas = parseFloat(data.receitas);
      const newDespesas = parseFloat(data.despesas);
      const newSaldo = parseFloat(data.balanco);

      animateNumber(
        receitasFiltro,
        parseFloat(receitasFiltro.textContent.replace("R$ ", "").replace(",", ".")),
        newReceitas,
        500
      );
      animateNumber(
        despesasFiltro,
        parseFloat(despesasFiltro.textContent.replace("R$ ", "").replace(",", ".")),
        newDespesas,
        500
      );
      animateNumber(saldoFiltro, parseFloat(saldoFiltro.textContent.replace("R$ ", "").replace(",", ".")), newSaldo, 500);
    })
    .catch((error) => console.error("Erro ao buscar dados:", error));
}
