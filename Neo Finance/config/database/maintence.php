<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "finance";

try {
    // Tenta criar a conexão com o banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica se houve algum erro de conexão
    if ($conn->connect_error) {
        throw new Exception("Erro de conexão com o banco de dados: ");
    }

    // Se a conexão for bem-sucedida, redireciona para a página de login
    header("Location: ../../views/login/login.php");
    exit();
} catch (Exception $e) {
    // Se houver um erro, exibe a página de manutenção
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Neo Finance - Manutenção :c</title>
        <style>
            body {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                height: 100vh;
                text-align: center;
                font-size: 2rem;
            }

            .spinner {
                width: 60px;
                height: 60px;
                border: 10px solid black;
                border-radius: 50%;
                border-color: green black black black;
                animation: spin 2s infinite;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }
        </style>
    </head>

    <body>
        <h1>Sistema em manutenção no momento. Por favor, tente novamente mais tarde.</h1>
        <div class="spinner"></div>
        <script>
            // Monitora se o navegador volta a ter conexão com a internet
            window.addEventListener('online', () => {
                console.log('Conexão restaurada. Recarregando a página...');
                window.location.reload(); // Recarrega a página quando a conexão for restaurada
            });
        </script>
    </body>

    </html>
<?php
    exit(); // Termina o script para não executar o resto do código
}
?>