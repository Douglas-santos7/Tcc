<?php
include("../../config/database/conexao.php");

session_start();
$userId = $_SESSION['user_id']; // Garantir que o id do usuário esteja na sessão

// Consultar o nome do usuário
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($userName);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Financeiro</title>
    <link rel="stylesheet" href="../../css/conteudos/chat/chat.css">
</head>

<body>
    <h1>Chatbot Financeiro</h1>
    <div id="chat"></div>

    <script>
    const chat = document.getElementById('chat');
    let currentState = 'main'; // Variável de controle de estado
    let previousState = null; // Armazena o estado anterior

    // Puxando o nome do usuário do PHP
const userName = "<?php echo htmlspecialchars($userName); ?>"; // Nome do usuário vindo do banco de dados

// Adicionando a imagem e a mensagem
chat.innerHTML = `
    <div class="bot"><img src="../../assets/img/fin.png" alt="Avatar" class="avatar">
        Olá, ${userName}! Sou a Neo, sua assistente virtual dedicada a ajudar você com suas finanças. Estou aqui para oferecer suporte na gestão de seus recursos, na criação de estratégias de investimento e no planejamento para alcançar  seus objetivos financeiros e sucesso econômico. Vamos juntos construir um futuro mais próspero!
    </div>
`;



    // Função para enviar uma mensagem e exibir animação de "pensando"
    function sendMessage(mensagem) {
        if (!mensagem) return;

        // Exibir mensagem do usuário no chat
        chat.innerHTML += `<div class="user">${mensagem}</div>`;
        chat.scrollTop = chat.scrollHeight;

        // Adicionar animação de "pensando"
        const thinkingMessage = document.createElement('div');
        thinkingMessage.className = 'bot thinking';
        thinkingMessage.textContent = "Pensando...";
        chat.appendChild(thinkingMessage);

        // Simular atraso para resposta do bot
        setTimeout(() => {
            thinkingMessage.remove(); // Remover o texto "Pensando..."
            fetch('../../config/conteudos/chat/chatbot.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'message=' + encodeURIComponent(mensagem)
            })
                .then(response => response.text())
                .then(data => {
                    chat.innerHTML += `<div class="bot">${data}</div>`;
                    chat.scrollTop = chat.scrollHeight; // Rolar para baixo no chat
                    askIfNeedHelp();
                });
        }, 2000); // 2 segundos para a simulação de "pensando"
    }

    // Função para gerar botões dinamicamente
    function generateButtons(options, callback) {
        let buttonsHtml = '';
        options.forEach(option => {
            buttonsHtml += `<button onclick="sendMessage('${option}')">${option}</button>`;
        });
        buttonsHtml += `<button class="back-button" onclick="backToPrevious()">Voltar</button>`;
        callback(buttonsHtml);
    }

    // Perguntar se o usuário precisa de mais ajuda
    function askIfNeedHelp() {
        const askMessage = `
            <div class="bot">Posso te ajudar com mais alguma coisa?</div>
            <div class="options">
                <button onclick="showOptions()">Sim, mostrar mais opções</button>
                <button onclick="endChat()">Não, encerrar o chat</button>
            </div>
        `;
        chat.innerHTML += askMessage;
        chat.scrollTop = chat.scrollHeight;
    }

    // Encerrar o chat e redirecionar
    function endChat() {
        chat.innerHTML += `<div class="bot"><img src="../../assets/img/fin.png" alt="Avatar" class="avatar">O chat está sendo encerrado. Até logo!</div>`;
        setTimeout(() => {
            window.location.href = "./(1)dashboard.php"; 
        }, 3000);
    }

    // Função para exibir as opções principais
    function showOptions() {
        previousState = currentState; // Armazenar o estado atual como anterior
        currentState = 'main'; // Atualiza o estado para 'main'
        const opcoes = ` 
            <div class="bot">Aqui estão algumas opções para você:</div>
            <div class="options">
                <button onclick="showConsultas()">Consultas</button>
                <button onclick="showDicas()">Dicas</button>
                <button onclick="showPlanejamento()">Planejamento</button>
                <button onclick="showRelatorios()">Relatórios</button>
            </div>
        `;
        chat.innerHTML += opcoes;
        chat.scrollTop = chat.scrollHeight;
    }

    // Exibir opções de Consultas
    function showConsultas() {
        previousState = currentState; // Armazenar o estado atual como anterior
        currentState = 'consultas'; // Atualiza o estado para 'consultas'
        const consultas = [
            "Consultar Saldo",
            "Resumo Mensal",
            "Resumo Diário",
            "Histórico de transações"
        ];
        generateButtons(consultas, (buttonsHtml) => {
            chat.innerHTML = `<div class="bot"><img src="../../assets/img/fin.png" alt="Avatar" class="avatar">Você escolheu Consultas</div>` + buttonsHtml;

            chat.scrollTop = chat.scrollHeight;
        });
    }

    // Exibir opções de Dicas
    function showDicas() {
        previousState = currentState; // Armazenar o estado atual como anterior
        currentState = 'dicas'; // Atualiza o estado para 'dicas'
        const dicas = [
            "Dicas de Economia",
            "Dicas de Investimento"
        ];
        generateButtons(dicas, (buttonsHtml) => {
            chat.innerHTML = `<div class="bot"><img src="../../assets/img/fin.png" alt="Avatar" class="avatar">Você escolheu dicas</div>` + buttonsHtml;
            chat.scrollTop = chat.scrollHeight;
        });
    }
    
    // Exibir opções de Planejamento
    function showPlanejamento() {
        previousState = currentState; // Armazenar o estado atual como anterior
        currentState = 'planejamento'; // Atualiza o estado para 'planejamento'
        const planejamento = [
            "Análise de Gastos",
            "Comparação de Gastos",
            "Desafios Financeiros"
        ];
        generateButtons(planejamento, (buttonsHtml) => {
            chat.innerHTML = `<div class="bot"><img src="../../assets/img/fin.png" alt="Avatar" class="avatar">Você escolheu Planejamento</div>` + buttonsHtml;
            chat.scrollTop = chat.scrollHeight;
        });
    }

    // Exibir opções de Relatórios
    function showRelatorios() {
        previousState = currentState; // Armazenar o estado atual como anterior
        currentState = 'relatorios'; // Atualiza o estado para 'relatorios'
        const relatorios = [
            "Relatório de Gastos"
        ];
        generateButtons(relatorios, (buttonsHtml) => {
            chat.innerHTML = `<div class="bot"><img src="../../assets/img/fin.png" alt="Avatar" class="avatar">Você escolheu Relatórios</div>` + buttonsHtml;
            chat.scrollTop = chat.scrollHeight;
        });
    }

    // Voltar ao estado anterior
    function backToPrevious() {
        if (previousState) {
            currentState = previousState;
            if (currentState === 'main') {
                showOptions();
            } else {
                // Voltar para o estado anterior e exibir as opções correspondentes
                window[currentState]();
            }
        }
    }

    // Inicializar com as opções principais
    showOptions();
</script>

</body>

</html>
