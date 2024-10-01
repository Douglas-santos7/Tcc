// Variável para controlar o próximo ID de categoria
let nextCategoryId = 1;

// Função para adicionar uma nova categoria
function addCategory() {
  var categoriaInput = document.getElementById("id--categoria");
  var categoria = categoriaInput.value.trim();

  // Obtenha a classe do ícone selecionado
  var iconeSelecionado = document.querySelector("#selecao--icone--container").dataset.icon;

  if (categoria !== "" && iconeSelecionado) {
    // Envia a nova categoria e o ícone para o servidor
    fetch("../../../config/conteudos/dashboard/salvar_categoria.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ nome: categoria, icone: iconeSelecionado }), // Envia o nome e o ícone
    })
      .then((response) => {
        // Verifica se a resposta foi ok
        if (!response.ok) {
          throw new Error("Erro na resposta do servidor");
        }
        return response.json();
      })
      .then((data) => {
        if (data.status === "success") {
          var option = document.createElement("option");
          option.value = data.id; // ID retornado pelo servidor
          option.text = categoria; // O texto da opção é o nome da categoria

          var select = document.getElementById("categoria--select");
          select.add(option);

          categoriaInput.value = ""; // Limpa o campo de entrada
        } else {
          alert("Erro ao adicionar categoria: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Erro:", error);
        alert("Erro ao se comunicar com o servidor: " + error.message);
      });
  } else {
    alert("Por favor, insira uma categoria e selecione um ícone.");
  }
}

// Adiciona a categoria ao clicar no botão
document.getElementById("add--tipo--categoria").addEventListener("click", addCategory);

// Adiciona a categoria ao pressionar Enter
document.getElementById("id--categoria").addEventListener("keypress", function (e) {
  if (e.key === "Enter") {
    e.preventDefault(); // Evita o comportamento padrão do Enter
    addCategory();
  }
});
