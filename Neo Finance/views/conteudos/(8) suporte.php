<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../config/database/conexao.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se o usuário está logado e o ID está na sessão 
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        // Busca o e-mail e o nome do usuário no banco de dados 
        $stmt = $conn->prepare("SELECT email, username FROM users WHERE id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $email = $user['email'];
            $name = $user['username'];
        } else {
            echo "<script>alert('Erro: Usuário não encontrado.');</script>";
            exit;
        }
    } else {
        echo "<script>alert('Erro: Usuário não autenticado.');</script>";
        exit;
    }

    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    $mail = new PHPMailer(true);

    try {
        // Configurações SMTP 
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'neo.finance.contato@gmail.com';
        $mail->Password = 'dkdq fcrd ojkf vbxd';  // Insira a senha do app aqui 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Define o remetente como o e-mail do usuário logado 
        $mail->setFrom($email, $name);
        $mail->addAddress('neo.finance.contato@gmail.com'); // E-mail de suporte da Neo Finance 

        // Título do suporte 
        $mail->Subject = "Novo Ticket de Suporte de $name";

        // Formata a mensagem 
        $mail->Body = "Nome: $name\nE-mail: $email\nTipo de Suporte: $subject\n\nMensagem: $message";

        // Verifica se há arquivos anexados 
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            // Obtém o conteúdo do arquivo 
            $fileContent = file_get_contents($_FILES['attachment']['tmp_name']);
            // Gera um nome único para o arquivo para evitar conflitos 
            $uniqueName = uniqid() . '_' . basename($_FILES['attachment']['name']);
            // Adiciona o anexo usando o conteúdo do arquivo 
            $mail->addStringAttachment($fileContent, $uniqueName);
        }

        // Envia o e-mail 
        $mail->send();
        echo "<script>alert('Sua dúvida foi enviada com sucesso!');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Erro ao enviar e-mail: {$mail->ErrorInfo}');</script>";
    }
} ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neo Finance - Suporte</title>
    <link rel="stylesheet" href="../../css/suporte/suporte.css">
</head>

<body>

    <div class="container">
        <div class="container--intro">
            <div class="titulo">
                <h1>Suporte Neo</h1>
            </div>
            <div class="texto--intro">
                <h2>Precisa de ajuda? Nossa FAQ tem as respostas que você procura – basta digitar!</h2>
            </div>
        </div>
        <!-- Barra de Pesquisa -->
        <div class="search-bar">
            <!-- <img src="../../assets/icons/icon--conversa--neo.svg" alt="icon--conversa"> -->
            <input type="text" id="faq-search" placeholder="Digite sua dúvida...">
        </div>

        <!-- Seção de FAQ -->
        <div class="faq">
            <h2>Perguntas Frequentes (FAQ)</h2>
            <div class="faq-item">
                <h3>1. Como posso recuperar minha senha?</h3>
                <p>Para recuperar sua senha, clique na opção "Esqueci minha senha" na tela de login e siga as instruções enviadas para o seu e-mail.</p>
            </div>

            <div class="faq-item">
                <h3>2. O que faço se encontrei um erro no meu saldo?</h3>
                <p>Se você encontrou um erro no seu saldo, entre em contato com nosso suporte imediatamente através deste formulário, informando os detalhes do erro.</p>
            </div>

            <div class="faq-item">
                <h3>3. Posso anexar documentos ao suporte?</h3>
                <p>Sim, você pode anexar documentos relacionados à sua dúvida ou reclamação usando o campo de anexos abaixo.</p>
            </div>

            <div class="faq-item">
                <h3>4. Como funciona o planejamento financeiro?</h3>
                <p>Nosso planejamento financeiro analisa suas despesas e receitas e gera um planejamento personalizado para ajudar você a atingir suas metas financeiras.</p>
            </div>
        </div>

        <!-- Link para entrar em contato -->
        <div class="contact-link" id="contact-link">
            Não encontrou o que procurava? <strong><a href="#">Entre em contato conosco</a></strong>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" id="support-form" style="display:none;">
            <h1>Entre em Contato</h1>
            <select name="subject" required>
                <option value="" disabled selected>Escolha o assunto</option>
                <option value="Dúvida">Dúvida</option>
                <option value="Reclamação">Reclamação</option>
                <option value="Outro">Outro</option>
            </select>

            <textarea name="message" rows="5" placeholder="Escreva sua dúvida..." required></textarea>
            <input type="file" name="attachment">
            <button type="submit">Enviar</button>
        </form>
    </div>
    <script>
        const searchInput = document.getElementById("faq-search");
        const faqItems = document.querySelectorAll(".faq-item");
        const contactLink = document.getElementById("contact-link");
        const supportForm = document.getElementById("support-form");

        // Função para atualizar a visibilidade das perguntas de acordo com a pesquisa
        function updateFAQVisibility(query) {
            let found = false;

            faqItems.forEach(item => {
                const question = item.querySelector("h3").innerText.toLowerCase();
                const answer = item.querySelector("p").innerText.toLowerCase();

                if (query === "") {
                    // Se a pesquisa estiver vazia, mostramos todas as perguntas.
                    item.style.display = "none"; // Esconde todas as perguntas
                } else {
                    if (question.includes(query) || answer.includes(query)) {
                        // Se houver correspondência com a pesquisa, mostramos a pergunta
                        item.style.display = "block";
                        found = true;
                    } else {
                        // Caso contrário, escondemos a pergunta
                        item.style.display = "none";
                    }
                }
            });

            // O link de contato sempre deve aparecer
            contactLink.style.display = "block";
        }

        // Atualiza a visibilidade do FAQ sempre que a pesquisa mudar
        searchInput.addEventListener("input", function() {
            const query = searchInput.value.toLowerCase().trim();

            // Verifica se o formulário de suporte está visível
            if (supportForm.style.display === "block") {
                // Se estiver visível, escondemos o formulário e mostramos as perguntas novamente
                supportForm.style.display = "none";
                contactLink.style.display = "block"; // Reexibe o link de contato
            }

            // Atualiza as perguntas conforme a pesquisa
            updateFAQVisibility(query);
        });

        // Mostrar o formulário de contato quando o link for clicado
        contactLink.addEventListener("click", function() {
            // Limpa o campo de pesquisa
            searchInput.value = "";

            // Oculta todas as perguntas do FAQ quando clicar em "Entrar em contato"
            faqItems.forEach(item => {
                item.style.display = "none"; // Esconde todas as perguntas
            });

            // Exibe o formulário de contato
            supportForm.style.display = "block";

            // (Opcional) Esconde o link de contato após clicar
            contactLink.style.display = "none";
        });
    </script>







</body>

</html>