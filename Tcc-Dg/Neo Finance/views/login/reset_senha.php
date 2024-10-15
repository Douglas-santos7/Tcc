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
    <link rel="stylesheet" href="../../css/login/telaCadastro.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.0/css/all.css">
</head>

<body>
    <div class="main-container">
        <div class="image-container">
            <div class="carousel">
                <div class="carousel-images" id="carousel-images">
                    <img src="../../assets/img/carrosel--logjn/1.jpg" alt="">
                    <img src="../../assets/img/carrosel--logjn/1.jpg" alt="">
                    <img src="../../assets/img/carrosel--logjn/1.jpg" alt="">
                    <video src="../../assets/img/carrosel--logjn/Iphon.mp4" loop muted autoplay></video>
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
                <img src="../../assets/img/neofinance--logo.svg" alt="Logo">
            </div>
            <div class="signup-form">
                <div class="title">Redefinir Senha</div>
                <form method="POST" action="./reset_senha.php">
                    <div class="field">
                        <input type="password" id="new-password" name="new-password" placeholder=" " required>
                        <label for="new-password">Nova Senha</label>
                        <i class="fa fa-lock"></i>
                        <i class="fa fa-eye toggle-password" onclick="togglePasswordVisibility('new-password', this)"></i>
                    </div>

                    <div class="field">
                        <input type="password" id="confirm-new-password" name="confirm-new-password" placeholder=" " required>
                        <label for="confirm-new-password">Confirme a Nova Senha</label>
                        <i class="fa fa-lock"></i>
                        <i class="fa fa-eye toggle-password" onclick="togglePasswordVisibility('confirm-new-password', this)"></i>
                    </div>

                    <button type="submit" class="login-btn">Redefinir Senha</button>
                </form>

                <div class="messages <?php echo $errorMessage ? 'show' : ''; ?>">
                    <?php if ($errorMessage): ?>
                        <p class="error"><?php echo $errorMessage; ?></p>
                    <?php endif; ?>
                </div>

                <div class="bottom">
                    <span>Já possui uma conta?&nbsp;<a href="./login.php">Login</a></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, eyeIcon) {
            const inputField = document.getElementById(inputId);
            const isPassword = inputField.type === 'password';

            inputField.type = isPassword ? 'text' : 'password';

            eyeIcon.classList.toggle('fa-eye', !isPassword);
            eyeIcon.classList.toggle('fa-eye-slash', isPassword);
        }

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