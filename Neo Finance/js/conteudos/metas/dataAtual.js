//Função - input data - obter data atual
  // Obtém a data atual
  const today = new Date();
  
  // Formata a data como 'AAAA-MM-DD' para ser compatível com o valor do input de tipo date
  const year = today.getFullYear();
  const month = String(today.getMonth() + 1).padStart(2, '0'); // meses começam em 0
  const day = String(today.getDate()).padStart(2, '0');
  
  // Define o valor mínimo no input para a data atual
  const minDate = `${year}-${month}-${day}`;
  document.getElementById('data_meta').setAttribute('min', minDate);