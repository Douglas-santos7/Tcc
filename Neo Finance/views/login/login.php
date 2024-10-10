<?php
session_start();
include '../../config/database/conexao.php';

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if (isset($_COOKIE['remember_token']) && !isset($_SESSION['logged_in'])) {
    $token = $_COOKIE['remember_token'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: ../../views/home.php", true, 302);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $stored_password_hash = $user['password_hash'];

        if (password_verify($password, $stored_password_hash)) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_attempts'] = 0;

            if (isset($_POST['remember_me'])) {
                $token = bin2hex(random_bytes(32));
                $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $stmt->bind_param("si", $token, $user['id']);
                $stmt->execute();
                setcookie('remember_token', $token, time() + (86400 * 30), "/", "", false, true);
            } else {
                setcookie('remember_token', '', time() - 3600, "/");
            }

            header("Location: ../../views/home.php", true, 302);
            exit();
        } else {
            $_SESSION['login_attempts']++;
            if ($_SESSION['login_attempts'] >= 3) {
                $_SESSION['reset_message'] = 'Você errou a senha 3 vezes. Redefina sua senha.';
                header("Location: ../../views/login/esqueci_senha.php", true, 302);
                exit();
            } else {
                $_SESSION['login_message'] = 'Senha incorreta! Tentativa ' . $_SESSION['login_attempts'] . ' de 3.';
            }
        }
    } else {
        $_SESSION['login_message'] = 'Email não encontrado!';
    }

    $stmt->close();
    header("Location: ../../views/login/login.php", true, 302);
    exit();
}

$email_value = isset($_COOKIE['user_email']) ? htmlspecialchars($_COOKIE['user_email']) : '';
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../css/login/telaLogin.css">
    <link href="https://fonts.googleapis.com/css2?family=Kodchasan&display=swap" rel="stylesheet">
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
            <div class="login-form">
                <div class="logo-container">
                    <img src="../../assets/img/neofinance--logo.svg" alt="Logo">
                </div>
                <div class="title">LOGIN</div>
                <p class="cadastro-text">Seja bem-vindo a Neo finance, Efetue o Login</p>

                <div class="message-container">
                    <?php
                    if (isset($_SESSION['login_message'])) {
                        echo '<div class="message error">' . htmlspecialchars($_SESSION['login_message']) . '</div>';
                        unset($_SESSION['login_message']);
                    }
                    if (isset($_SESSION['reset_message'])) {
                        echo '<div class="messages error">' . htmlspecialchars($_SESSION['reset_message']) . '</div>';
                        unset($_SESSION['reset_message']);
                    }
                    ?>
                </div>

                <form action="login.php" method="POST">
                    <div class="field">
                        <input type="email" name="email" id="email-address" placeholder=" " required autocomplete="on" aria-label="Email" value="<?php echo $email_value; ?>">
                        <label for="email-address">Email</label>
                        <i class="fa fa-envelope"></i>
                    </div>

                    <div class="field">
                        <input type="password" name="password" id="login-pass" required placeholder=" " autocomplete="off" aria-label="Senha">
                        <label for="login-pass">Senha</label>
                        <i class="fa fa-lock"></i>
                        <i class="fa fa-eye toggle-password" onclick="togglePasswordVisibility('login-pass', this)" aria-label="Mostrar/Ocultar Senha"></i>
                    </div>

                    <section>
                        <label for="remember"><input type="checkbox" id="remember_me" name="remember_me"> Lembrar-me</label>
                        <a href="../../views/login/esqueci_senha.php">Esqueceu a senha?</a>
                    </section>

                    <button type="submit" class="login-btn">Login</button>
                </form>

                <div class="bottom">
                    <div>Não tem conta?&nbsp;<a href="./cadastro.php">Se inscreva</a></div>
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
                    // Pausar todos os vídeos
                    const videos = document.querySelectorAll('.swiper-slide video');
                    videos.forEach(video => {
                        video.pause();
                        video.currentTime = 0; // Reseta o vídeo
                    });
                    // Reproduzir o vídeo do slide ativo, se existir
                    const activeSlide = this.slides[this.activeIndex].querySelector('video');
                    if (activeSlide) {
                        activeSlide.play();
                    }
                },
            },
        });

        // Inicia a reprodução do vídeo no slide inicial, se houver
        const initialVideo = swiper.slides[swiper.activeIndex].querySelector('video');
        if (initialVideo) {
            initialVideo.play();
        }

        function togglePasswordVisibility(inputId, eyeIcon) {
            const inputField = document.getElementById(inputId);
            const isPassword = inputField.type === 'password';

            inputField.type = isPassword ? 'text' : 'password';
            eyeIcon.classList.toggle('fa-eye', isPassword);
            eyeIcon.classList.toggle('fa-eye-slash', !isPassword);
        }
    </script>
</body>

</html>