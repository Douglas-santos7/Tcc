document.getElementById('send-btn').addEventListener('click', function() {
    const userInput = document.getElementById('user-input');
    const message = userInput.value;
    userInput.value = '';

    // Exibe a mensagem do usuário
    const chatBox = document.getElementById('chat-box');
    chatBox.innerHTML += `<div>Você: ${message}</div>`;

    // Envia a mensagem para o PHP
    fetch('chatbot.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'message=' + encodeURIComponent(message)
    })
    .then(response => response.text())
    .then(data => {
        chatBox.innerHTML += `<div>Bot: ${data}</div>`;
        chatBox.scrollTop = chatBox.scrollHeight; // Scroll para a parte inferior
    });
});
