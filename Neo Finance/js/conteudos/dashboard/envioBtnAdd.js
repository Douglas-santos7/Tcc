// Variáveis globais
let iconeSelecionado = "";

// Função para abrir o seletor de ícones
document.getElementById("abrir--selecao--icones").addEventListener("click", function () {
  document.getElementById("id--selecao--icones").style.display = "block";
});

// Função para fechar o seletor de ícones
document.getElementById("id--fecha--icone").addEventListener("click", function () {
  document.getElementById("id--selecao--icones").style.display = "none";
});

// Função para selecionar um ícone
document.querySelectorAll(".icon").forEach(function (icon) {
  icon.addEventListener("click", function () {
    iconeSelecionado = this.getAttribute("data-icon");
    document.getElementById("selecao--icone--container").setAttribute("data-icon", iconeSelecionado);
    document.getElementById("selecao--icone--container").innerHTML = `<i class="${iconeSelecionado}"></i>`; // Exibe o ícone selecionado
    document.getElementById("id--selecao--icones").style.display = "none"; // Fecha o seletor após a seleção
  });
});

// Função para enviar os dados
function subValores() {
  var inputValor = document.getElementById("id--valor");
  var valor = parseFloat(inputValor.value.trim());
  var tipo = document.getElementById("id--tipo").value;
  var categoria = document.getElementById("categoria--select").value;
  var nome = document.getElementById("id--nome").value;

  if (!isNaN(valor) && nome.trim() !== "" && categoria.trim() !== "" && iconeSelecionado) {
    var dados = {
      tipo: tipo,
      categoria: categoria,
      nome: nome,
      valor: valor,
      icone: iconeSelecionado, // Incluindo o ícone
    };

    console.log(dados); // Verifique se os dados estão corretos

    fetch("../../../config/conteudos/dashboard/salvar_transacao.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(dados),
    })
      .then((response) => {
        console.log(response); // Log a resposta do servidor

        return response.text().then((text) => {
          console.log("Conteúdo da resposta:", text); // Verifique o conteúdo da resposta
          return text; // Retorna o texto da resposta
        });
      })
      .then((text) => {
        try {
          const data = JSON.parse(text); // Tenta converter o texto para JSON
          if (data.status === "success") {
            alert("Transação salva com sucesso!");
            window.location.reload();
          } else {
            alert("Erro ao salvar transação: " + data.message);
            console.log(data);
          }
        } catch (error) {
          console.error("Erro ao analisar JSON:", error);
          alert("Erro ao processar a resposta do servidor.");
        }
      })
      .catch((error) => {
        console.error("Erro:", error);
        alert("Erro ao enviar os dados.");
      });
  } else {
    alert("Por favor, preencha todos os campos obrigatórios.");
  }
}
