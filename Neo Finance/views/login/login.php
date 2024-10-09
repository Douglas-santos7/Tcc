    <?php
    // Inicia a sessão no início do script
    session_start();

    // Inclui o arquivo de conexão com o banco de dados
    include '../../config/database/conexao.php';

    // Verifica se ocorreu algum erro na conexão com o banco de dados
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    // Inicializa o contador de tentativas de login na sessão se ainda não estiver definido
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

    // Verifica se o cookie 'remember_token' está definido para tentar login automático
    if (isset($_COOKIE['remember_token']) && !isset($_SESSION['logged_in'])) {
        $token = $_COOKIE['remember_token'];

        // Prepara a consulta para verificar o token no banco de dados
        $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Login automático com sucesso, configura a sessão
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redireciona para a página inicial
            header("Location: ../../views/home.php", true, 302);
            exit(); // Encerra o script após o redirecionamento
        }
    }

    // Verifica se o método da requisição é POST (o formulário foi enviado)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepara a consulta para verificar o e-mail no banco de dados
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Se o e-mail for encontrado no banco, verifica a senha
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $stored_password_hash = $user['password_hash'];

            // Verifica se a senha fornecida corresponde ao hash armazenado
            if (password_verify($password, $stored_password_hash)) {
                // Configura a sessão com os dados do usuário
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['login_attempts'] = 0; // Reseta as tentativas após o sucesso do login

                // Verifica se o usuário marcou a opção "Lembrar-me"
                if (isset($_POST['remember_me'])) {
                    $token = bin2hex(random_bytes(32)); // Gera um token aleatório

                    // Armazena o token no banco de dados
                    $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                    $stmt->bind_param("si", $token, $user['id']);
                    $stmt->execute();

                    // Define o cookie com o token, com validade de 30 dias
                    setcookie('remember_token', $token, time() + (86400 * 30), "/", "", false, true);
                } else {
                    // Remove o cookie se o usuário não marcou a opção
                    setcookie('remember_token', '', time() - 3600, "/");
                }

                // Redireciona para a página inicial
                header("Location: ../../views/home.php", true, 302);
                exit();
            } else {
                // Senha incorreta, incrementa o contador de tentativas
                $_SESSION['login_attempts']++;

                // Se o usuário errou a senha 3 vezes, redireciona para redefinição de senha
                if ($_SESSION['login_attempts'] >= 3) {
                    $_SESSION['reset_message'] = 'Você errou a senha 3 vezes. Redefina sua senha.';
                    header("Location: ../../views/login/esqueci_senha.php", true, 302);
                    exit();
                } else {
                    $_SESSION['login_message'] = 'Senha incorreta! Tentativa ' . $_SESSION['login_attempts'] . ' de 3.';
                }
            }
        } else {
            // E-mail não encontrado
            $_SESSION['login_message'] = 'Email não encontrado!';
        }

        // Fecha a consulta
        $stmt->close();

        // Redireciona de volta para a página de login
        header("Location: ../../views/login/login.php", true, 302);
        exit();
    }

    // Preenche o campo de e-mail com o valor do cookie se ele estiver definido
    $email_value = isset($_COOKIE['user_email']) ? htmlspecialchars($_COOKIE['user_email']) : '';

    // Fecha a conexão com o banco de dados
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
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.0/css/all.css">
    </head>

    <body>
        <div class="main-container">
            <div class="image-container">
                <div class="carousel">
                    <div class="carousel-images">
                        <img src="../../assets/img/carrosel--logjn/1.jpg" alt="">
                        <img src="../../assets/img/carrosel--logjn/2.jpg" alt="">
                        <img src="../../assets/img/carrosel--logjn/1.jpg" alt="">
                        <video src="../../assets/img/carrosel--logjn/Iphon.mp4" loop muted autoplay></video>
                    </div>
                    <!-- <div class="carousel-dots">
                        <span class="dot" onclick="currentSlide(1)"></span>
                        <span class="dot" onclick="currentSlide(2)"></span>
                        <span class="dot" onclick="currentSlide(3)"></span>
                        <span class="dot" onclick="currentSlide(4)"></span>
                    </div> -->
                </div>
            </div>

            <div class="form-container">
                <div class="login-form">
                    <div class="logo-container">
                        <img src="../../assets/img/neofinance--logo.svg" alt="Logo">
                    </div>
                    <div class="title">LOGIN</div>
                    <p class="cadastro-text">Seja bem-vindo a Neo finance, Efetue o Login</p>

                    <!-- Message container for error/success messages -->
                    <div class="message-container">
                        <?php
                        // Exibe mensagens de erro, se existirem
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

        <script>
            let slideIndex = 1;
            showSlides(slideIndex);
            setInterval(() => {
                showSlides(slideIndex += 1);
            }, 10000);

            function currentSlide(n) {
                showSlides(slideIndex = n);
            }

            function showSlides(n) {
                const slides = document.querySelectorAll('.carousel-images img, .carousel-images video');
                const dots = document.querySelectorAll('.dot');

                if (n > slides.length) {
                    slideIndex = 1;
                }
                if (n < 1) {
                    slideIndex = slides.length;
                }

                slides.forEach((slide, index) => {
                    slide.style.display = (index + 1 === slideIndex) ? 'block' : 'none';
                });

                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index + 1 === slideIndex);
                });
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