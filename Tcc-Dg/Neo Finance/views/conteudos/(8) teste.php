<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // Caminho para o autoload do Composer

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coletar dados do formulário e sanitizar
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $supportType = htmlspecialchars(trim($_POST['support_type']));
    $messageContent = htmlspecialchars(trim($_POST['message']));

    // Validação de e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Endereço de e-mail inválido.");
    }

    // Configuração do PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'neo.finance.contato@gmail.com'; 
        $mail->Password = 'xvrj xmua cepf vojy'; // Sua senha de e-mail ou senha de app
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Usar TLS
        $mail->Port = 587; // 587 para TLS

        // Destinatários
        $mail->setFrom($email, $name); // E-mail do usuário e seu nome
        $mail->addAddress('neo.finance.contato@gmail.com', 'Neo Finance'); // E-mail do destinatário

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = 'Novo Ticket de Suporte de ' . $name;
        $mail->Body    = "Nome: $name<br>E-mail: $email<br>Telefone: $phone<br>Tipo de Suporte: $supportType<br><br>Mensagem:<br>$messageContent";

        // Enviar o e-mail
        $mail->send();
        $message = 'Seu ticket foi enviado com sucesso!';
    } catch (Exception $e) {
        $message = "Erro ao enviar o ticket. Tente novamente. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central de Suporte</title>
    <link rel="stylesheet" href="styles.css">
    <style>
body {
    font-family: 'Arial', sans-serif;
    background-color: #f7f9fc; /* Cor de fundo mais suave */
    margin: 0;
    padding: 20px;
}

header {
    text-align: center;
    margin-bottom: 40px; /* Aumentado para destacar o cabeçalho */
}

h1 {
    color: #333;
    font-size: 2.5em; /* Tamanho adequado para o título */
    margin-bottom: 10px; /* Espaçamento inferior */
}

#search-bar {
    padding: 12px;
    width: 400px; /* Largura aumentada */
    border: 1px solid #ccc;
    border-radius: 5px;
    transition: border-color 0.3s, box-shadow 0.3s; /* Transição suave */
    font-size: 1em; /* Tamanho da fonte do campo de pesquisa */
}

#search-bar:focus {
    border-color: #28a745; /* Cor verde ao focar */
    box-shadow: 0 0 5px rgba(40, 167, 69, 0.5); /* Sombra ao focar */
    outline: none; /* Remove o contorno padrão */
}

main {
    max-width: 900px; /* Largura máxima aumentada */
    margin: auto;
    background: #fff;
    padding: 40px; /* Aumentado o padding */
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

section {
    margin-bottom: 40px; /* Aumentado o espaço entre seções */
}

h2 {
    color: #28a745;
    margin-bottom: 20px; /* Aumentado o espaço inferior */
    font-size: 2em; /* Tamanho maior para o subtítulo */
    border-bottom: 2px solid #e9ecef; /* Linha abaixo do título */
    padding-bottom: 10px; /* Espaçamento inferior */
}

.faq-item {
    margin-bottom: 25px; /* Aumentado o espaço entre itens */
    padding: 15px;
    border: 1px solid #e9ecef; /* Borda leve */
    border-radius: 5px; /* Bordas arredondadas */
    background-color: #f8f9fa; /* Fundo mais claro para FAQ */
}

label {
    display: block;
    margin: 15px 0 5px;
    font-weight: bold; /* Negrito para melhor legibilidade */
}

input[type="text"],
input[type="email"],
input[type="tel"],
textarea,
select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    transition: border-color 0.3s, box-shadow 0.3s; /* Transição suave */
    font-size: 1em; /* Tamanho da fonte dos campos */
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="tel"]:focus,
textarea:focus,
select:focus {
    border-color: #28a745; /* Cor verde ao focar */
    box-shadow: 0 0 5px rgba(40, 167, 69, 0.5); /* Sombra ao focar */
    outline: none; /* Remove o contorno padrão */
}

button {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 12px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 18px;
    margin-top: 15px;
    width: 100%;
    transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s; /* Efeitos de transição */
}

button:hover {
    background-color: #218838; /* Cor mais escura ao passar o mouse */
    transform: translateY(-2px); /* Leve elevação ao passar o mouse */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Sombra ao passar o mouse */
}

p {
    text-align: center;
    color: #d9534f; /* cor do texto de erro */
    font-size: 1.1em;
    margin-top: 15px; /* Espaçamento superior */
}

footer {
    text-align: center;
    margin-top: 40px; /* Espaçamento superior */
    font-size: 14px;
    color: #777;
}

footer a {
    color: #007bff;
    text-decoration: none;
}

footer a:hover {
    text-decoration: underline;
}
    </style>
</head>
<body>

    <header>
        <h1>Central de Suporte</h1>
        <input type="text" placeholder="Pesquisar no suporte..." id="search-bar">
    </header>

    <main>
        <!-- Perguntas Frequentes (FAQ) -->
        <section id="faq">
            <h2>Perguntas Frequentes</h2>
            <div class="faq-item">
                <h3>Como redefinir minha senha?</h3>
                <p>Para redefinir sua senha, vá para a seção "Configurações da Conta" e clique em "Redefinir Senha".</p>
            </div>
            <div class="faq-item">
                <h3>Como adicionar uma nova despesa?</h3>
                <p>Para adicionar uma despesa, clique no botão "Adicionar Despesa" no painel de controle e preencha os detalhes da transação.</p>
            </div>
            <div class="faq-item">
                <h3>Como exportar relatórios?</h3>
                <p>Na aba "Relatórios", clique em "Exportar" para gerar um relatório em PDF ou Excel.</p>
            </div>
        </section>

        <!-- Artigos e Tutoriais -->
        <section id="articles">
            <h2>Artigos e Tutoriais</h2>
            <ul>
                <li><a href="#">Como criar um orçamento mensal</a></li>
                <li><a href="#">Como categorizar suas despesas</a></li>
                <li><a href="#">Dicas para controlar suas finanças pessoais</a></li>
            </ul>
        </section>

        <!-- Formulário de Contato -->
        <section id="contact-form">
            <h2>Entre em Contato</h2>
            <form action="" method="post">
                <label for="name">Nome:</label>
                <input type="text" id="name" name="name" required>

                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>

                <label for="phone">Telefone:</label>
                <input type="tel" id="phone" name="phone" required>

                <label for="support_type">Tipo de Suporte:</label>
                <select id="support_type" name="support_type" required>
                    <option value="duvida">Dúvida</option>
                    <option value="problema_tecnico">Problema Técnico</option>
                    <option value="sugestao">Sugestão</option>
                </select>

                <label for="message">Descrição do Problema:</label>
                <textarea id="message" name="message" rows="5" required></textarea>

                <button type="submit">Enviar Ticket</button>
            </form>
            <?php if ($message) { echo "<p>$message</p>"; } ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Controle de Finanças. Todos os direitos reservados.</p>
        <p><a href="#">Termos de Serviço</a> | <a href="#">Política de Privacidade</a></p>
    </footer>

</body>
</html>
