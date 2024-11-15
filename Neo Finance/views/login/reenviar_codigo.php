<?php
session_start();
require '../../vendor/autoload.php';
include '../../config/database/conexao.php'; // Conexão com o banco de dados

// Verifica se o e-mail de redefinição está salvo na sessão
if (!isset($_SESSION['reset_email'])) {
    $_SESSION['error_message'] = 'Nenhum e-mail para enviar o código. Tente novamente.';
    header("Location: ./codigo_verificacao.php");
    exit();
}

$email = $_SESSION['reset_email'];

// Gerar um novo código de redefinição aleatório
$resetCode = random_int(100000, 999999); // Código de 6 dígitos

// Atualizar o código na tabela password_resets
$stmt = $conn->prepare("UPDATE password_resets SET reset_code = ? WHERE user_id = (SELECT id FROM users WHERE email = ?)");
$stmt->bind_param("ss", $resetCode, $email);
$stmt->execute();

// Configuração do PHPMailer para enviar o e-mail
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Configurações do servidor de e-mail
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Substitua pelo seu servidor SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'neo.finance.contato@gmail.com'; // Substitua pelo seu e-mail
    $mail->Password = 'txht dkee nmdi msvz'; // Substitua pela sua senha
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Configuração do e-mail de envio
    $mail->setFrom('neo.finance.contato@gmail.com', 'Neo Finance');
    $mail->addAddress($email);

    // Conteúdo do e-mail
    $mail->isHTML(true);
    $mail->Subject = 'Código de Verificação';
    $mail->Body = "Olá, <br><br> Seu código de verificação é: <b>$resetCode</b>.<br> Utilize este código para redefinir sua senha.<br><br>Atenciosamente,<br>Neo Finance";

    $mail->send();
    $_SESSION['success_message'] = 'Código de verificação reenviado com sucesso!';
} catch (Exception $e) {
    $_SESSION['error_message'] = 'Erro ao enviar o código. Tente novamente.';
}

// Redireciona de volta para a página de verificação
header("Location: ./codigo_verificacao.php");
exit();

?>
