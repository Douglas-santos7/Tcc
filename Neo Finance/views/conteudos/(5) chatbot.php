<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Financeiro</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 20px; }
        #chat { border: 1px solid #ccc; padding: 10px; height: 400px; overflow-y: scroll; background: #fff; margin-bottom: 20px; }
        #message { width: 80%; padding: 10px; }
        button { padding: 10px; }
        .user { text-align: right; color: blue; }
        .bot { text-align: left; color: green; }
        #historico { max-height: 400px; border: 1px solid #ccc; padding: 10px; overflow-y: auto; background: #fff; margin-bottom: 20px; }
    </style>
</head>
<body>

<h1>Chatbot Financeiro</h1>
<div id="chat"></div>

<input type="text" id="message" placeholder="Digite sua mensagem..." />
<button onclick="sendMessage()">Enviar</button>

<script>
    function sendMessage() {
        const messageInput = document.getElementById('message');
        const message = messageInput.value;

        if (message.trim() === "") return;

        // Exibir a mensagem do usuário
        const chat = document.getElementById('chat');
        chat.innerHTML += `<div class="user">${message}</div>`;

        // Enviar a mensagem para o bot
        fetch('../../config/conteudos/chat/chatbot.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'message=' + encodeURIComponent(message)
        })
        .then(response => response.text())
        .then(data => {
            chat.innerHTML += `<div class="bot">${data}</div>`;
            messageInput.value = '';


        });
    }

</script>
</body>
</html>
