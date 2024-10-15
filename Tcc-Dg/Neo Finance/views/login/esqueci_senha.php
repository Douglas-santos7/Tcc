<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../../config/database/conexao.php'; // conexão com o banco de dados

require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['forgot-email'];

    // Verificação se o email existe no banco de dados
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        $username = $user['username']; // Capturar o nome de usuário

        // Gerar um código de redefinição de senha
        $resetCode = mt_rand(100000, 999999);

        // Salvar o código de redefinição no banco de dados
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, reset_code, created_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE reset_code = ?, created_at = NOW()");
        $stmt->bind_param("iss", $user_id, $resetCode, $resetCode);
        $stmt->execute();

        // Configuração do PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'neo.finance.contato@gmail.com';
            $mail->Password = 'txht dkee nmdi msvz'; // Use a senha de aplicativo aqui
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->CharSet = 'UTF-8';

            // Remetente e destinatário
            $mail->setFrom('neo.finance.contato@gmail.com', 'Neo Finance');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Código de Redefinição de Senha';

            // Corpo do email com o nome de usuário
            $mail->Body    = 'Olá ' . htmlspecialchars($username) . ',<br><br>Você solicitou a redefinição de sua senha. Aqui está o código de redefinição que você precisará para criar uma nova senha:<br><br><b>' . $resetCode . '</b><br><br>Se você não solicitou esta mudança, por favor, ignore esta mensagem.<br><br>Atenciosamente,<br>Neo Finance';
            $mail->AltBody = 'Olá ' . htmlspecialchars($username) . ",\n\nVocê solicitou a redefinição de sua senha. Aqui está o código de redefinição que você precisará para criar uma nova senha:\n\n" . $resetCode . "\n\nSe você não solicitou esta mudança, por favor, ignore esta mensagem.\n\nAtenciosamente,\nNeo Finance";

            // Enviar o email
            $mail->send();

            // Armazenar mensagem de sucesso na sessão
            $_SESSION['reset_message'] = 'Código de redefinição enviado para o seu email.';

            // Redirecionar para a página de verificação de código
            $_SESSION['reset_email'] = $email;
            header("Location: ./codigo_verificacao.php");
            exit();
        } catch (Exception $e) {
            echo "Erro ao enviar email: {$mail->ErrorInfo}";
        }
    } else {
        // Set error message in session
        $_SESSION['error_message'] = 'Email não encontrado!';
        // Redirect to the same page to display the error
        header("Location: ./esqueci_senha.php");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>
    <link rel="stylesheet" href="../../css/login/telaCadastro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="main-container">
        <div class="image-container">
            <!-- Carrossel de Imagens -->
            <div class="carousel">
                <div class="carousel-images">
                    <img src="../../assets/img/carrosel--logjn/1.jpg" alt="">
                    <img src="../../assets/img/carrosel--logjn/2.jpg" alt="">
                    <img src="../../assets/img/carrosel--logjn/1.jpg" alt="">
                    <video src="../../assets/img/carrosel--logjn/iphon.mp4" loop muted autoplay></video>
                </div>
                <div class="carousel-dots">
                    <span class="dot" onclick="currentSlide(1)"></span>
                    <span class="dot" onclick="currentSlide(2)"></span>
                    <span class="dot" onclick="currentSlide(3)"></span>
                    <span class="dot" onclick="currentSlide(4)"></span>
                </div>
            </div>
        </div>
        <div class="form-container">
            <div class="logo-container">
                <img src="../../assets/img/neofinance--logo.svg" alt="Logo"> <!-- Insira o caminho para a sua logo -->
            </div>
            <div class="signup-form">
                <div class="title">Recuperar Senha</div>
                <form id="forgot-password-form" method="post" action="./esqueci_senha.php">
                    <div class="field">
                        <input type="email" id="forgot-email" name="forgot-email" placeholder=" " required autocomplete="off">
                        <label for="forgot-email">Email</label>
                        <i class="fa fa-envelope"></i>
                    </div>
                    <button type="submit" class="login-btn">Enviar código de redefinição</button>
                </form>
                <div class="back-to-login">
                    <p><a href="login.php">Voltar para Login</a></p>
                </div>
            </div>
            <?php
            if (isset($_SESSION['reset_message'])) {
                echo '<div class="messages show">
            <p class="success">' . htmlspecialchars($_SESSION['reset_message']) . '</p>
          </div>';
                unset($_SESSION['reset_message']);
            }

            if (isset($_SESSION['error_message'])) {
                echo '<div class="messages show">
            <p class="error">' . htmlspecialchars($_SESSION['error_message']) . '</p>
          </div>';
                unset($_SESSION['error_message']);
            }
            ?>
        </div>
    </div>

    <script>
        let slideIndex = 1;
        showSlides(slideIndex);

        // Mudar o slide a cada 3 segundos
        setInterval(() => {
            showSlides(slideIndex += 1);
        }, 10000); // 3000 milissegundos = 3 segundos

        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            const slides = document.querySelectorAll('.carousel-images img, .carousel-images video');
            const dots = document.querySelectorAll('.dot');

            if (n > slides.length) {
                slideIndex = 1
            }
            if (n < 1) {
                slideIndex = slides.length
            }

            slides.forEach((slide, index) => {
                slide.style.display = (index + 1 === slideIndex) ? 'block' : 'none';
                // Reproduzir o vídeo ativo
                if (index + 1 === slideIndex && slide.tagName === 'VIDEO') {
                    slide.play();
                } else if (slide.tagName === 'VIDEO') {
                    slide.pause();
                }
            });

            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index + 1 === slideIndex);
            });
        }
    </script>
</body>

</html>