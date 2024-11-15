<?php
session_start();
require '../../vendor/autoload.php';
include '../../config/database/conexao.php'; // conexão com o banco de dados

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Inicializa a mensagem de erro
$error_message = '';

// Verifica se a requisição foi feita via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se a variável de sessão está definida
    if (isset($_SESSION['reset_email'])) {
        $email = $_SESSION['reset_email'];
        $resetCode = $_POST['verification_code']; // Nome da chave corrigido

        // Verifica se o código de redefinição é válido
        $stmt = $conn->prepare("
            SELECT pr.user_id
            FROM password_resets pr
            JOIN users u ON pr.user_id = u.id
            WHERE u.email = ? AND pr.reset_code = ?
        ");
        $stmt->bind_param("ss", $email, $resetCode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Código válido, redirecionar para a página de redefinição de senha
            $_SESSION['reset_email'] = $email;
            header("Location: ./reset_senha.php");
            exit();
        } else {
            // Código inválido, definir mensagem de erro
            $_SESSION['error_message'] = 'Código de redefinição inválido!';
        }

        $stmt->close();
    } else {
        // Se a variável de sessão não estiver definida, redirecionar para a página de esqueci a senha
        header("Location: ./esqueci_senha.php");
        exit();
    }
}

// Fecha a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Código</title>
    <link rel="stylesheet" href="../../css/login/telaCadastro.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.0/css/all.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
</head>

<body>
    <div class="main-container">
        <div class="image-container">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="../../assets/img/carrosel--logjn/notenook--1.png" alt="">
                    </div>
                    <div class="swiper-slide">
                        <video src="../../assets/img/carrosel--logjn/Iphon.mp4" muted autoplay></video>
                    </div>
                    <div class="swiper-slide">
                        <img src="../../assets/img/carrosel--logjn/notenook--1.png" alt="">
                    </div>
                </div>
            </div>
            
        </div>
        
        <div class="form-container">
            <div class="logo-container">
                <img src="../../assets/img/neofinance--logo.svg" alt="Logo">
            </div>
            <div class="signup-form">
                
                <div class="title">Verificar Código</div>
                <!-- Mensagem de erro exibida acima do campo de código -->
    <div class="message-container">
        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<div class="message error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']); // Limpa a mensagem após exibir
        }
        ?>
    </div>
                <form method="POST" action="./codigo_verificacao.php">
                    <div class="field">
                        <input type="text" id="verification-code" name="verification_code" placeholder=" " required>
                        <label for="verification-code">Código de Verificação</label>
                        <i class="fa fa-key"></i>
                    </div>
                    <button type="submit" class="login-btn">Verificar Código</button>
                </form>

                <div class="bottom">
                    <span>Não recebeu o código?&nbsp;<a href="./reenviar_codigo.php">Reenviar Código</a></span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        // Inicialização do Swiper
        const swiper = new Swiper('.swiper-container', {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: false,
            },
            autoplay: {
                delay: 5000,
            },
            on: {
                slideChange: function() {
                    const videos = document.querySelectorAll('.swiper-slide video');
                    videos.forEach(video => {
                        video.pause();
                        video.currentTime = 0;
                    });
                    const activeSlide = this.slides[this.activeIndex].querySelector('video');
                    if (activeSlide) {
                        activeSlide.play();
                    }
                },
            },
        });

        const initialVideo = swiper.slides[swiper.activeIndex].querySelector('video');
        if (initialVideo) {
            initialVideo.play();
        }

        document.getElementById('resend-code').addEventListener('click', function(e) {
            e.preventDefault();
            fetch('./reenviar_codigo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
            })
            .catch(error => {
                console.error('Erro:', error);
            });
        });
    </script>
</body>

</html>
