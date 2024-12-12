document.getElementById('adicionarBtn').addEventListener('click', function () {
    document.getElementById('menuCriarMeta').style.display = 'flex';
  });

  document.querySelector('.close-btn').addEventListener('click', function () {
    document.getElementById('menuCriarMeta').style.display = 'none';
  });

  // Função para exibir o formulário de resgate ou depósito
  function showForm(action, card) {
    const formResgatar = card.querySelector('.form-resgatar');
    const formDepositar = card.querySelector('.form-depositar');
    const historico = card.querySelector('.historico-transacoes');

    if (action === 'resgatar') {
      formResgatar.style.display = 'block';
      formDepositar.style.display = 'none';
      historico.style.display = 'none';
    } else if (action === 'depositar') {
      formResgatar.style.display = 'none';
      formDepositar.style.display = 'block';
      historico.style.display = 'none';
    } else if (action === 'verHistorico') {
      formResgatar.style.display = 'none';
      formDepositar.style.display = 'none';
      historico.style.display = 'block';
    }
  }

  // Função para voltar às opções de selecionar ação
  function goBack(card) {
    const actionButtons = card.querySelector('.actionButtons');
    actionButtons.style.display = 'flex';
    card.querySelector('.form-resgatar').style.display = 'none';
    card.querySelector('.form-depositar').style.display = 'none';
    card.querySelector('.historico-transacoes').style.display = 'none';
  }

  // Adicionar evento de clique ao botão de selecionar ação
  document.querySelectorAll('.selectActionBtn').forEach(button => {
    button.addEventListener('click', function () {
      const card = this.closest('.card-meta');
      const actionButtons = card.querySelector('.actionButtons');
      actionButtons.style.display = 'flex';
      this.style.display = 'none';
    });
  });

  // Adicionar evento de clique aos botões de ação
  document.querySelectorAll('.actionBtn').forEach(button => {
    button.addEventListener('click', function () {
      const card = this.closest('.card-meta');
      const action = this.getAttribute('data-action');
      showForm(action, card);
      card.querySelector('.actionButtons').style.display = 'none';
    });
  });

  // Adicionar evento de clique aos botões de voltar
  document.querySelectorAll('.back-btn').forEach(button => {
    button.addEventListener('click', function () {
      const card = this.closest('.card-meta');
      goBack(card);
    });
  });