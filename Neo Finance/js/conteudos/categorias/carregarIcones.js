// carregarIcones.js
async function carregarIcones(iconeGrade) {
  try {
      const response = await fetch('../../assets/iconesJson/icones.json');
      const data = await response.json();
      iconeGrade.innerHTML = ''; // Limpa a grade de ícones antes de adicionar novos ícones

      data.icons.forEach(icon => {
          const iconDiv = document.createElement('div');
          iconDiv.classList.add('icon');
          iconDiv.setAttribute('data-icon', icon['data-icon']);
          iconDiv.innerHTML = `<i class="${icon.class}"></i>`;

          // Adiciona evento de clique para selecionar o ícone
          iconDiv.addEventListener('click', function() {
              // Remove a classe 'selecionado' de todos os ícones
              const allIcons = document.querySelectorAll('.icon');
              allIcons.forEach(i => i.classList.remove('selecionado'));

              // Adiciona a classe 'selecionado' ao ícone clicado
              iconDiv.classList.add('selecionado');
          });

          iconeGrade.appendChild(iconDiv); // Adiciona o novo ícone à grade
      });
  } catch (error) {
      console.error('Erro ao carregar ícones:', error);
  }
}


    // Evento para mostrar a grade de ícones
    document.getElementById('selecione-icone').addEventListener('click', () => {
      const iconeGrade = document.getElementById('icone-grade');
      iconeGrade.style.display = (iconeGrade.style.display === 'none') ? 'block' : 'none';
    });
