<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Financeiro</title>
    <link rel="stylesheet" href="../../css/conteudos/chat/chat.css">
</head>

<body>
    <div class="container--header">
        <header class="banner">
            <div class="titulo--banner">
                <img src="../../assets/icons/home--sidebar/graficos--icon.svg" alt="calendario--icon" />
                <h1>Chat</h1>
            </div>
            <div class="notificacao--usuario">
                <img src="../../assets/icons/sino--icon.svg" alt="icon-notificacao" />
            </div>
        </header>
    </div>
    <div id="chat">
        <!-- Botões pré-definidos dentro do chat -->
        <div class="chat-buttons" id="predefined-buttons">
            <button onclick="sendPredefinedMessage('Saldo')">Saldo</button>
            <button onclick="sendPredefinedMessage('Investimento')">Investir</button>
            <button onclick="sendPredefinedMessage('Previsão financeira')">Previsão</button>
        </div>

        <!-- Área onde as mensagens serão exibidas -->

        <!-- Área de envio de mensagens dentro do chat -->
        <div class="send-area">
            <input type="text" id="message" placeholder="Digite sua mensagem..." />
            <!-- Imagem de enviar -->
            <img src="" alt="Enviar" onclick="sendMessage()" />
        </div>
    </div>

    <script>
        let topicoAtual = null;

        // Função para enviar a mensagem
        function sendMessage() {
            const messageInput = document.getElementById('message');
            const message = messageInput.value;

            if (message.trim() === "") return;

            // Exibir a mensagem do usuário no chat
            const chat = document.getElementById('chat');
            chat.innerHTML += `
                <div class="message user">
                    <img src="../../assets/img/user.png" alt="User" class="message-icon" />
                    <div class="message-text">${message}</div>
                </div>
            `;

            // Exibir o indicador de carregamento do bot
            const loadingDiv = document.createElement("div");
            loadingDiv.classList.add("message", "bot");
            loadingDiv.innerHTML = `
                <img src="../../assets/img/neo.png" alt="Bot" class="message-icon" />
                <div class="loading"><span class="dot">.</span><span class="dot">.</span><span class="dot">.</span></div>
            `;
            chat.appendChild(loadingDiv);

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

            // Simular atraso de 2 segundos antes de enviar a resposta do bot
            setTimeout(() => {
                fetch('../../config/conteudos/chat/chatbot.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'message=' + encodeURIComponent(message)
                    })
                    .then(response => response.text())
                    .then(data => {
                        // Remover o indicador de carregamento
                        loadingDiv.remove();

                        // Exibir a resposta do bot
                        chat.innerHTML += `
                            <div class="message bot">
                                 <img src="../../assets/img/neo.png" alt="Bot" class="message-icon" />
                                <div class="message-text">${data}</div>
                            </div>
                        `;
                        topicoAtual.mensagens.push(`<div class="bot">${data}</div>`);

                        saveToLocalStorage(topicoAtual); // Salvar o tópico no Local Storage
                        messageInput.value = '';
                        chat.scrollTop = chat.scrollHeight; // Rolar para baixo no chat
                    });
            }, 2000); // 2000 ms = 2 segundos de atraso
            const buttons = document.getElementById('predefined-buttons');
            buttons.style.display = 'none';
        }

        // Função para enviar uma mensagem pré-definida
        function sendPredefinedMessage(message) {
            const messageInput = document.getElementById('message');
            messageInput.value = message;
            sendMessage();

            // Esconder os botões após o clique
            const buttons = document.getElementById('predefined-buttons');
            buttons.style.display = 'none';
        }

        function saveToLocalStorage(data) {
            localStorage.setItem('historicoTopicos', JSON.stringify(data));
        }

        // Adicionar evento de escuta para o campo de entrada de mensagem
        document.getElementById('chat').addEventListener('keydown', function(event) {
            if (event.target.id === 'message' && event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault(); // Previne o comportamento padrão de quebra de linha
                sendMessage();
            }
        });

        // Função para enviar a primeira mensagem ao carregar a página
        window.onload = function() {
            const firstMessage = `Olá! Como posso ajudá-lo hoje?<br>
                1. Saldo<br>
                2. Dicas de Economia<br>
                3. Dicas de Investimento<br>
                4. Resumo Mensal<br>
                5. Resumo Diário<br>
                6. Histórico de Transações<br>
                7. Análise de Gastos<br>
                8. Exportar Relatório<br>
                9. Previsão Financeira com Base no Histórico<br>
                10. Comparação de Gastos Mensais<br>
                11. Desafios<br>
                12. Planejamento Mensal<br>`; // Primeira mensagem do bot com lista
            
            const chat = document.getElementById('chat');
            
            // Exibir a mensagem inicial do bot
            const loadingDiv = document.createElement("div");
            loadingDiv.classList.add("message", "bot");
            loadingDiv.innerHTML = `
                <img src="../../assets/img/neo.png" alt="Bot" class="message-icon" />
                <div class="loading"><span class="dot">.</span><span class="dot">.</span><span class="dot">.</span></div>
            `;
            chat.appendChild(loadingDiv);

            // Simula a resposta do bot após 2 segundos
            setTimeout(() => {
                // Remover o indicador de carregamento
                loadingDiv.remove();

                // Exibir a mensagem inicial do bot
                chat.innerHTML += `
                    <div class="message bot">
                         <img src="../../assets/img/neo.png" alt="Bot" class="message-icon" />
                        <div class="message-text">${firstMessage}</div>
                    </div>
                `;

                // Adicionar a mensagem ao histórico
                if (!topicoAtual) {
                    const now = new Date();
                    const timestamp = now.toLocaleDateString() + ' ' + now.toLocaleTimeString();
                    topicoAtual = {
                        nome: `Tópico - ${timestamp}`,
                        mensagens: []
                    };
                }
                topicoAtual.mensagens.push(`<div class="bot">${firstMessage}</div>`);
            }, 2000); // 2000 ms = 2 segundos de atraso
        };
    </script>

</body>

</html>