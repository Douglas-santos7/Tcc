function abrirModalAdicionar() {
  document.getElementById("modalAdicionar").style.display = "flex";
}

function fecharModalAdicionar() {
  document.getElementById("modalAdicionar").style.display = "none";
}

function fecharModalSucesso() {
  document.getElementById("modalSucesso").style.display = "none";
}

function fecharModalSucessoPagamento() {
  document.getElementById("modalSucessoPagamento").style.display = "none";
}

function validarFormulario(event) {
  const valorInput = document.querySelector('input[name="valor"]');
  const dataInput = document.getElementById("data_vencimento");
  if (valorInput.value < 0) {
    alert("O valor não pode ser negativo.");
    return false;
  }
  const dataSelecionada = new Date(dataInput.value);
  const hoje = new Date();
  hoje.setHours(0, 0, 0, 0);
  if (dataSelecionada < hoje) {
    document.getElementById("modalConfirmacao").style.display = "flex";
    return false;
  }
  return true;
}

function formatarValor(input) {
  let valor = input.value.replace(/\D/g, "");
  valor = (valor / 100).toFixed(2);
  let partes = valor.split(".");
  let inteiro = partes[0];
  let decimal = partes[1];

  inteiro = inteiro.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  valor = inteiro + "," + decimal;

  input.value = valor;
}

document.getElementById("btn-ok").onclick = function () {
  document.getElementById("modalConfirmacao").style.display = "none";
};

function abrirModalConfirmarPagamento(vencimentoId) {
  document.getElementById("vencimento_id").value = vencimentoId;
  document.getElementById("modalConfirmarPagamento").style.display = "flex";
}

function fecharModalConfirmarPagamento() {
  document.getElementById("modalConfirmarPagamento").style.display = "none";
}
