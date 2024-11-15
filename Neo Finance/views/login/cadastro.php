<?php
session_start();
include '../../config/database/conexao.php'; // Conexão com o banco de dados

// Verificar se o token CSRF já existe, se não, gerar um novo
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$showPopup = false; // Variável para controle do pop-up

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitização dos dados de entrada
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    // Verificação CSRF
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['signup_message'] = "Falha de segurança.";
        header('Location: ./cadastro.php');
        exit();
    }

    // Verificar se o nome de usuário já existe no banco de dados
    $query_username = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query_username);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['signup_message'] = "O nome de usuário já está em uso.";
        header('Location: ./cadastro.php');
        exit();
    }

    // Verificar se o email já existe no banco de dados
    $query_email = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['signup_message'] = "O email já está em uso.";
        header('Location: ./cadastro.php');
        exit();
    }

    // Verificar se a senha e a confirmação coincidem
    if ($password !== $confirm_pass) {
        $_SESSION['signup_message'] = "As senhas não coincidem.";
        header('Location: ./cadastro.php');
        exit();
    }

    // Verificar se a senha atende aos critérios de segurança
    if (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/[^\w]/", $password)) {
        $_SESSION['signup_message'] = "A senha deve ter no mínimo 8 caracteres, incluindo uma letra maiúscula, um número e um símbolo.";
        header('Location: ./cadastro.php');
        exit();
    }

    // Inserção no banco de dados
    $password_hash = password_hash($password, PASSWORD_BCRYPT); // Criptografa a senha
    $insert_query = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sss", $username, $email, $password_hash);

    if ($stmt->execute()) {
        $showPopup = true; // Definir a variável para mostrar o pop-up
    } else {
        $_SESSION['signup_message'] = "Erro ao cadastrar: " . $stmt->error;
        header('Location: ./cadastro.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="../../css/login/telaCadastro.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.0/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Kodchasan&display=swap" rel="stylesheet">
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
                <div class="title">CADASTRO</div>
                <p class="cadastro-text">Seja bem-vindo à Neo finance, efetue o cadastro</p>
                <div class="message-container">
        <?php
        if (isset($_SESSION['signup_message'])) {
            echo '<div class="message error">' . htmlspecialchars($_SESSION['signup_message']) . '</div>';
            unset($_SESSION['signup_message']); // Limpa a mensagem após exibir
        }
        ?>
    </div>
                <form method="POST" action="cadastro.php">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="field">
                        <input type="text" id="name" name="username" placeholder=" " required>
                        <label for="name">Nome</label>
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="field">
                        <input type="email" id="user-email" name="email" placeholder=" " required>
                        <label for="user-email">Email</label>
                        <i class="fa fa-envelope"></i>
                    </div>
                    <div class="field">
                        <input type="password" id="signup-pass" name="password" required placeholder=" " autocomplete="on" oninput="checkPasswordStrength()">
                        <label for="signup-pass">Senha</label>
                        <i class="fa fa-eye toggle-password" onclick="togglePasswordVisibility('signup-pass', this)"></i>
                        <i class="fa fa-lock"></i>
                    </div>
                    <div class="forca--senha">
                        <span id="password-strength"></span>
                    </div>
                    <div class="field">
                        <input type="password" id="confirm-pass" name="confirm_password" required placeholder=" " autocomplete="off">
                        <label for="confirm-pass">Confirme a Senha</label>
                        <i class="fa fa-lock"></i>
                        <i class="fa fa-eye toggle-password" onclick="togglePasswordVisibility('confirm-pass', this)"></i>
                    </div>
                    <section>
                        <label for="agree">
                            <input type="checkbox" id="agree" required> Eu concordo com &nbsp;<a href="#" id="terms-link">Termos e Condições</a>
                        </label>
                    </section>
                    <button type="submit" class="login-btn">Registrar</button>
                </form>

                <div class="message-container">
    <?php
    if (isset($_SESSION['signup_message'])) {
        echo '<div class="message error">' . htmlspecialchars($_SESSION['signup_message']) . '</div>';
        unset($_SESSION['signup_message']); // Limpa a mensagem após exibir
    }
    ?>
</div>


                <div class="bottom">
                    <span>Já Registrado?&nbsp;<a href="login.php">Login</a></span>
                </div>
            </div>
        </div>

        <!-- Pop-up de Agradecimento -->
        <div id="thankYouPopup" class="thank-you-popup" style="display: <?php echo $showPopup ? 'block' : 'none'; ?>;">
            <div class="popup-content">
                <h2>Obrigado por se cadastrar!</h2>
                <p>Você pode fazer login agora.</p>
                <button onclick="closeThankYouPopup()">Fechar</button>
            </div>
        </div>
    </div>

    <!-- Modal para Termos e Condições -->
    <div id="termsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Termos e Condições</h2>
            <p>1. Aceitação dos Termos</p>
            <p>Ao acessar e usar o site <strong>Neo finance</strong>, você concorda em cumprir e estar sujeito a estes Termos e Condições. Se você não concordar com estes termos, não deve usar o Serviço.</p>
            <p>2. Descrição do Serviço</p>
            <p><strong>Neo finance</strong> oferece um serviço de controle de finanças pessoais e pode incluir outros serviços relacionados.</p>
            <p>3. Modificações</p>
            <p>Reservamo-nos o direito de modificar ou interromper o Serviço a qualquer momento, com ou sem aviso prévio.</p>
            <p>4. Limitação de Responsabilidade</p>
            <p>Não seremos responsáveis por quaisquer danos decorrentes do uso ou incapacidade de usar o Serviço.</p>
            <button onclick="closeModal()">Fechar</button>
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

        function togglePasswordVisibility(inputId, icon) {
            const input = document.getElementById(inputId);
            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';

            // Troca as classes do ícone
            icon.classList.toggle('fa-eye', isPassword);
            icon.classList.toggle('fa-eye-slash', !isPassword);
        }

        function checkPasswordStrength() {
            const password = document.getElementById('signup-pass').value;
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

        function closeThankYouPopup() {
            document.getElementById('thankYouPopup').style.display = 'none';
            window.location.href = "login.php"; // Redireciona para a página de login após fechar o pop-up
        }

        function closeModal() {
            document.getElementById('termsModal').style.display = 'none';
        }

        document.getElementById('terms-link').onclick = function() {
            document.getElementById('termsModal').style.display = 'block';
        }
    </script>
</body>

</html>