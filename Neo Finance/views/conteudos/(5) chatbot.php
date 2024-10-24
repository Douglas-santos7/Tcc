<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Financeiro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        #chat {
            border: 1px solid #ccc;
            padding: 10px;
            height: 400px;
            overflow-y: scroll;
            background: #fff;
            margin-bottom: 20px;
        }

        #message {
            width: 80%;
            padding: 10px;
        }

        button {
            padding: 10px;
            margin-top: 10px;
        }

        .user {
            text-align: right;
            color: black;
        }

        .bot {
            text-align: left;
            color: red;
        }

        #historico {
            max-height: 200px;
            border: 1px solid #ccc;
            padding: 10px;
            overflow-y: auto;
            background: #fff;
            margin-bottom: 20px;
        }

        .limpar-historico {
            background-color: #ff4c4c;
            color: white;
            transition: background-color 0.3s, transform 0.2s;
        }

        .limpar-historico:hover {
            background-color: #ff1a1a;
            transform: scale(1.05);
        }

        .historico-topico {
            background-color: #e0e0e0;
            padding: 10px;
            margin-bottom: 5px;
            cursor: pointer;
        }

        .historico-mensagens {
            display: none;
            padding-left: 10px;
        }

        .expandido .historico-mensagens {
            display: block;
        }
    </style>
</head>

<body>

    <h1>Chatbot Financeiro</h1>
    <div id="chat"></div>

    <input type="text" id="message" placeholder="Digite sua mensagem..." />
    <button onclick="sendMessage()">Enviar</button>

    <h2>Histórico de Tópicos</h2>
    <div id="historico"></div>
    <button class="limpar-historico" onclick="clearHistorico()">Limpar Histórico</button>

    <script>
        let topicoAtual = null;

        // Função para carregar o histórico de tópicos do Local Storage
        function loadHistorico() {
            const historico = document.getElementById('historico');
            const topicos = JSON.parse(localStorage.getItem('historicoTopicos')) || [];

            historico.innerHTML = ''; // Limpar a lista de tópicos exibidos antes de carregar
            topicos.forEach((topico, index) => {
                historico.innerHTML += `
                <div class="historico-topico" onclick="toggleTopico(${index})">
                    ${topico.nome}
                </div>`;
            });
        }

        // Função para alternar a exibição do tópico
        function toggleTopico(index) {
            const topicos = document.querySelectorAll('.historico-topico');
            topicos[index].classList.toggle('expandido');
            restoreFromLocalStorage(index); // Carregar o histórico de um tópico anterior
        }

        // Função para enviar a mensagem
        function sendMessage() {
            const messageInput = document.getElementById('message');
            const message = messageInput.value;

            if (message.trim() === "") return;

            // Exibir a mensagem do usuário no chat
            const chat = document.getElementById('chat');
            chat.innerHTML += `<div class="user">${message}</div>`;

            // Verificar se existe um tópico em andamento
            if (!topicoAtual) {
                const now = new Date();
                const timestamp = now.toLocaleDateString() + ' ' + now.toLocaleTimeString();
                topicoAtual = {
                    nome: `Tópico - ${timestamp}`,
                    mensagens: []
                };
            }

            // Adicionar a mensagem ao tópico atual
            topicoAtual.mensagens.push(`<div class="user">${message}</div>`);

            // Enviar a mensagem para o bot
            fetch('../../config/conteudos/chat/chatbot.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'message=' + encodeURIComponent(message)
                })
                .then(response => response.text())
                .then(data => {
                    chat.innerHTML += `<div class="bot">${data}</div>`;
                    topicoAtual.mensagens.push(`<div class="bot">${data}</div>`);

                    saveToLocalStorage(topicoAtual); // Salvar o tópico no Local Storage
                    messageInput.value = '';
                    chat.scrollTop = chat.scrollHeight; // Rolar para baixo no chat
                });
        }

        // Função para salvar tópicos no Local Storage
        function saveToLocalStorage(topico) {
            const topicos = JSON.parse(localStorage.getItem('historicoTopicos')) || [];
            // Atualizar ou adicionar o tópico atual
            const topicoIndex = topicos.findIndex(t => t.nome === topico.nome);
            if (topicoIndex >= 0) {
                topicos[topicoIndex] = topico;
            } else {
                topicos.push(topico);
            }
            localStorage.setItem('historicoTopicos', JSON.stringify(topicos));
            loadHistorico(); // Recarregar a exibição do histórico
        }

        // Função para restaurar o histórico de um tópico selecionado
        function restoreFromLocalStorage(index) {
            const topicos = JSON.parse(localStorage.getItem('historicoTopicos')) || [];
            const chat = document.getElementById('chat');
            if (topicos[index]) {
                chat.innerHTML = topicos[index].mensagens.join('');
                topicoAtual = topicos[index];
            }
        }

        // Função para limpar o histórico de tópicos
        function clearHistorico() {
            localStorage.removeItem('historicoTopicos'); // Limpar o Local Storage
            document.getElementById('historico').innerHTML = ''; // Limpar a exibição do histórico
            document.getElementById('chat').innerHTML = ''; // Limpar o chat
            topicoAtual = null; // Resetar o tópico atual
        }

        // Carregar o histórico ao iniciar a página
        window.onload = loadHistorico;
    </script>
</body>

</html>