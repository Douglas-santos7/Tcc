document.querySelector('.enviar--nova--categoria').addEventListener('click', function() {
    const nomeCategoria = document.getElementById('id--nova--categoria').value;
    const iconeCategoria = document.querySelector('.icon.selecionado')?.getAttribute('data-icon'); // Captura o ícone selecionado

    // Verifica se todos os campos estão preenchidos
    if (!nomeCategoria || !iconeCategoria) {
        alert('Por favor, preencha todos os campos!');
        return;
    }

    fetch('../../../../config/conteudos/categorias/adicionar_categoria.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'nome_categoria': nomeCategoria,
            'icone_categoria': iconeCategoria
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // Exibir mensagem de sucesso ou erro
        if (data.status === 'success') {
            // Aqui você pode adicionar lógica para atualizar a interface do usuário
            // Exemplo: fechar o popup, limpar campos, ou atualizar uma lista de categorias
        }
    })
    .catch(error => {
        console.error('Erro:', error);
    });
});

// Evento para selecionar o ícone
document.querySelectorAll('.icon').forEach(icon => {
    icon.addEventListener('click', function() {
        // Remove a classe "selecionado" de todos os ícones
        document.querySelectorAll('.icon').forEach(i => i.classList.remove('selecionado'));
        
        // Adiciona a classe "selecionado" ao ícone clicado
        this.classList.add('selecionado');
    });
});
