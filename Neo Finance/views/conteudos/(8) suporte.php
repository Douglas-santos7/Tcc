<?php
if (session_status() === PHP_SESSION_NONE) {
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
}
?>



<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suporte - Controle de Finanças</title>
    <style>
        :root {
            --primary-color: #4caf50;
            --secondary-color: #f5f5f5;
            --text-color: #333;
            --font: 'Kodchasan', sans-serif;
        }

        body {
            font-family: var(--font);
            background-color: var(--secondary-color);
            color: var(--text-color);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input,
        textarea,
        button,
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #388e3c;
        }

        .faq {
            margin-top: 40px;
        }

        .faq h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .faq-item {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .faq-item h3 {
            margin: 0;
            font-size: 1.2em;
        }

        .faq-item p {
            margin: 5px 0 0 0;
        }
    </style>
</head>

<body>
    <div class="container">
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

        <h1>Suporte - Controle de Finanças</h1>
        <form action="" method="POST" enctype="multipart/form-data">

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
</body>

</html>