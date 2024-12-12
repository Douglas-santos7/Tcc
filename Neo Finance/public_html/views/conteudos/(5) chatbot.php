<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Financeiro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #b8e4c9;
            /* Fundo do corpo */
            margin: 0;
            padding: 20px;
        }

        .container--header {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 10px 20px;
        }

        .titulo--banner {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .titulo--banner img {
            width: 50px;
            height: auto;
            margin-right: 20px;
        }

        .titulo--banner h1 {
            font-size: 2rem;
            color: var(--fonte-cards);
        }

        #chat {
            border-radius: 15px;
            border: none;
            padding: 20px;
            height: calc(93vh - 200px);
            /* Altura responsiva */
            overflow-y: auto;
            background-color: #013220;
            /* Verde escuro */
            margin-bottom: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            /* As mensagens são exibidas em coluna */
            gap: 10px;
            /* Espaçamento entre as mensagens */
        }

        #message {
            width: 100%;
            /* Ocupa toda a largura disponível */
            padding: 15px;
            /* Aumenta o padding para uma área maior */
            border-radius: 20px;
            border: 1px solid #ccc;
            margin-right: 10px;
            font-size: 1rem;
            box-sizing: border-box;
            /* Para incluir o padding na largura total */
        }

        button {
            background-color: #4caf50;
            /* Verde claro */
            color: white;
            border: none;
            padding: 15px 25px;
            /* Aumenta o tamanho do botão */
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 1rem;
        }

        button:hover {
            background-color: #45a049;
        }

        .user,
        .bot {
            text-wrap: wrap;
            font-size: 1.1rem;
            margin: 10px 0;
            display: inline-block;
            max-width: 70%;
            padding: 15px;
            border-radius: 15px;
            position: relative;
            background-color: #ffffff;
            /* Fundo branco do balão */
            color: #013220;
            /* Texto verde escuro */
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            /* Sombra leve */
        }

        .user {
            margin-left: auto;
            /* Alinha à direita */
            text-align: right;
        }

        .bot {
            margin-right: auto;
            /* Alinha à esquerda */
            text-align: left;
        }

        .chat-buttons {
            display: flex;
            justify-content: center;
            /* Centraliza os botões horizontalmente */
            gap: 10px;
            position: relative;
            margin-bottom: 15px;
        }

        .chat-buttons button {
            background-color: #3b8d7f;
            /* Cor de fundo dos botões */
            padding: 10px 15px;
            border-radius: 12px;
            color: white;
            border: none;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .chat-buttons button:hover {
            background-color: #2e7059;
            /* Cor de fundo quando o botão é hover */
        }

        .send-area {
            display: flex;
            flex-direction: row;
            gap: 15px;
            /* Aumentando o espaçamento entre o input e o botão */
            position: fixed;
            bottom: 10px;
            /* Distância da parte inferior */
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            justify-content: space-between;
            background-color: #f9f9f9;
            /* Cor de fundo suave para o campo de entrada */
            padding: 15px;
            /* Aumentei o padding para maior conforto */
            border-radius: 30px;
            /* Bordas mais arredondadas */
            box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.1);
            /* Sombra mais suave */
            box-sizing: border-box;
            /* Inclui o padding no cálculo da largura */
            transition: background-color 0.3s ease;
            z-index: 1000;
        }

        .send-area:hover {
            background-color: #f1f1f1;
            /* Fundo ligeiramente mais escuro ao passar o mouse */
        }

        .send-area input {
            width: 80%;
            /* O campo de mensagem ocupa 80% da largura */
            padding: 12px;
            /* Maior espaço interno para facilitar a digitação */
            font-size: 1rem;
            border-radius: 25px;
            /* Bordas mais arredondadas */
            border: 1px solid #ccc;
            outline: none;
            /* Remove o contorno padrão do navegador */
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .send-area input:focus {
            border-color: #4CAF50;
            /* Destaca o campo com cor verde quando selecionado */
            box-shadow: 0px 0px 5px rgba(76, 175, 80, 0.5);
            /* Suaviza a sombra ao focar */
        }

        .send-area img {
            width: 50px;
            /* Aumenta um pouco o tamanho da imagem */
            height: 50px;
            cursor: pointer;
            border-radius: 50%;
            /* Tornar a imagem redonda */
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .send-area img:hover {
            opacity: 0.8;
            transform: scale(1.1);
            /* Leve aumento de tamanho ao passar o mouse */
        }

        /* Animação de 3 pontos - Simulando digitação */
        .loading {
            font-size: 1.5rem;
            color: black;
            display: inline-block;
            font-family: "Courier New", monospace;
            /* Estilo de fonte de máquina de escrever */
            letter-spacing: 0.1em;
            /* Espaço entre os pontos */
        }

        .dot {
            opacity: 0;
            display: inline-block;
            animation: typing 1.5s steps(3) infinite;
        }

        .dot:nth-child(1) {
            animation-delay: 0s;
            /* O primeiro ponto aparece primeiro */
        }

        .dot:nth-child(2) {
            animation-delay: 0.5s;
            /* O segundo ponto aparece depois de meio segundo */
        }

        .dot:nth-child(3) {
            animation-delay: 1s;
            /* O terceiro ponto aparece depois de um segundo */
        }

        @keyframes typing {
            0% {
                opacity: 0;
            }

            33% {
                opacity: 1;
            }

            66% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }

        /* Estilos para a barra de rolagem */
        #chat::-webkit-scrollbar {
            width: 8px;
            /* Largura da barra de rolagem */
        }

        #chat::-webkit-scrollbar-track {
            background: #f1f1f1;
            /* Cor de fundo da trilha da barra de rolagem */
        }

        #chat::-webkit-scrollbar-thumb {
            background: #fff;
            /* Cor da barra de rolagem */
            border-radius: 15px;
            /* Bordas arredondadas */
        }

        #chat::-webkit-scrollbar-thumb:hover {
            background: #555;
            /* Cor da barra de rolagem ao passar o mouse */
        }
    </style>
</head>

<body>
    <div class="container--header">
        <header class="banner">
            <div class="titulo--banner">
                <img src="../../assets/icons/home--sidebar/graficos--icon.svg" alt="calendario--icon" />
                <h1>Gráficos</h1>
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
            <img src="https://img.freepik.com/psd-premium/iconico-de-carregamento-isolado-em-fundo-transparente_943194-9237.jpg?w=826" alt="Enviar" onclick="sendMessage()" />
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
            chat.innerHTML += `<div class="user">${message}</div>`;

            // Exibir o indicador de carregamento do bot
            const loadingDiv = document.createElement("div");
            loadingDiv.classList.add("bot");
            loadingDiv.innerHTML = '<span class="loading"><span class="dot">.</span><span class="dot">.</span><span class="dot">.</span></span>';
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
                // Enviar a mensagem para o bot (simulado com uma resposta fixa por agora)
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
                        chat.innerHTML += `<div class="bot">${data}</div>`;
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
    </script>

</body>

</html>