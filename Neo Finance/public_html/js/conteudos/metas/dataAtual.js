 // Função para definir a data mínima do campo de data
 function setMinDate() {
  var today = new Date();
  var dd = String(today.getDate()).padStart(2, '0');
  var mm = String(today.getMonth() + 1).padStart(2, '0'); // Janeiro é 0!
  var yyyy = today.getFullYear();

  today = yyyy + '-' + mm + '-' + dd;
  document.getElementById("deadline").setAttribute("min", today);
}

// Chamar a função quando a página carregar
window.onload = setMinDate;