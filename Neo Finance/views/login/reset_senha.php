<?php
session_start();
require '../../vendor/autoload.php';
include '../../config/database/conexao.php'; // conexão com o banco de dados

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar se a sessão de redefinição de senha está definida
    if (!isset($_SESSION['reset_email'])) {
        die("Sessão de redefinição de senha não encontrada.");
    }

    $email = $_SESSION['reset_email'];
    $newPassword = $_POST['new-password'];
    $confirmNewPassword = $_POST['confirm-new-password'];

    // Verificar se as senhas coincidem
    if ($newPassword !== $confirmNewPassword) {
        $_SESSION['error'] = 'As senhas não coincidem!'; // Mensagem de erro se as senhas não coincidirem
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    // Hash da nova senha
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Atualizar a senha no banco de dados
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);

    if (!$stmt->execute()) {
        $_SESSION['error'] = "Erro ao atualizar a senha: " . $stmt->error; // Mensagem de erro
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    // Verificar se a senha foi atualizada
    if ($stmt->affected_rows === 0) {
        $_SESSION['error'] = "Nenhuma linha foi atualizada. Verifique se o email está correto."; // Mensagem de erro
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    // Remover o código de redefinição do banco de dados
    $user_query = "SELECT id FROM users WHERE email = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("s", $email);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if ($user_result->num_rows === 0) {
        $_SESSION['error'] = "Usuário não encontrado"; // Mensagem de erro se o usuário não for encontrado
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    $user_id = $user_result->fetch_assoc()['id'];

    // Remover o código de redefinição do banco de dados
    $delete_query = "DELETE FROM password_resets WHERE user_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $user_id);

    if (!$delete_stmt->execute()) {
        $_SESSION['error'] = "Erro ao remover o código de redefinição: " . $delete_stmt->error; // Mensagem de erro
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    // Fechar statements e conexão
    $stmt->close();
    $user_stmt->close();
    $delete_stmt->close();
    $conn->close();

    // Limpar a variável de sessão e redirecionar para a tela de login
    unset($_SESSION['reset_email']);
    header("Location: ./login.php");
    exit();
}

// Exibir mensagem de erro se existir
if (isset($_SESSION['error'])) {
    $errorMessage = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="../../css/login/resetSenha.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.0/css/all.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
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
                <div class="title">Redefinir Senha</div>
                <div class="message-container">
    <?php
    if (!empty($errorMessage)) {
        echo '<div class="message error">' . htmlspecialchars($errorMessage) . '</div>';
    }
    ?>
</div>
                <form method="POST" action="./reset_senha.php">
                    <div class="field">
                        <input type="password" id="new-password" name="new-password" required placeholder=" " autocomplete="on" oninput="checkPasswordStrength()">
                        <label for="new-password">Nova Senha</label>
                        <i class="fa fa-lock"></i>
                        <i class="fa fa-eye toggle-password" onclick="togglePasswordVisibility('new-password', this)"></i>

                    </div>
                    <div class="forca--senha">
                        <span id="password-strength"></span>
                    </div>
                    <div class="field">
                        <input type="password" id="confirm-new-password" name="confirm-new-password" placeholder=" " required>
                        <label for="confirm-new-password">Confirme a Nova Senha</label>
                        <i class="fa fa-lock"></i>
                        <i class="fa fa-eye toggle-password" onclick="togglePasswordVisibility('confirm-new-password', this)"></i>
                    </div>

                    <button type="submit" class="login-btn">Redefinir Senha</button>
                </form>
                <div class="bottom">
                    <span>Já possui uma conta?&nbsp;<a href="./login.php">Login</a></span>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
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
                        video.currentTime = 0; // Reseta o vídeo
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

        function togglePasswordVisibility(inputId, icon) {
            const input = document.getElementById(inputId);
            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';

            // Troca as classes do ícone
            icon.classList.toggle('fa-eye', isPassword);
            icon.classList.toggle('fa-eye-slash', !isPassword);
        }

        function checkPasswordStrength() {
            const password = document.getElementById('new-password').value;
            const strengthText = document.getElementById('password-strength');

            // Se a senha estiver vazia, esconder a força da senha
            if (password === '') {
                strengthText.innerHTML = ''; // Limpa o texto
                return; // Sai da função
            }

            let strength = 'Fraca';
            let color = 'red'; // Cor padrão para "Fraca"

            if (password.length >= 8) {
                if (/[A-Z]/.test(password) && /[0-9]/.test(password) && /[^\w]/.test(password)) {
                    strength = 'Forte';
                    color = 'green'; // Cor para "Forte"
                } else {
                    strength = 'Média';
                    color = 'orange'; // Cor para "Média"
                }
            }

            strengthText.innerHTML = `Força da senha: <span style="color: ${color}; font-weight: bold;">${strength}</span>`;
        }
    </script>
</body>

</html>