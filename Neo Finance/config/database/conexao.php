<?php

$servername = "localhost"; // Nome do servidor (ou IP)
$username = "root"; // Nome de usuário do banco de dados
$password = ""; // Senha do banco de dados
$dbname = "finance"; // Nome do banco de dados
define('BASE_URL', '/config/database/');

// Tenta criar a conexão com o banco de dados
try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica se houve algum erro de conexão
    if ($conn->connect_error) {
        throw new Exception("Erro de conexão com o banco de dados.");
    }
} catch (Exception $e) {
    // Se houver erro, exibe a página de manutenção diretamente
?>
    <!DOCTYPE html>
    <html lang="pt-BR">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Neo Finance - Manutenção :c</title>
        <link rel="stylesheet" href="path_to_your_css_file.css"> <!-- Caminho correto do seu arquivo CSS -->

        <style>
            @import url(../../css/root.css);

            body {
                background-color: var(--background);
                font-family: var(--fonte-principal);
                color: var(--cor-secundaria);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                height: 100vh;
                text-align: center;
                margin: 0;
                padding: 0;
            }

            h1 {
                color: var(--cor-terciaria);
                font-size: 4rem;
            }

            .spinner {
                width: 100px;
                height: 100px;
                border: 20px solid black;
                border-radius: 50%;
                border-color: var(--cor--destaque-verde) black black black;
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

            .messagem {
                background-color: var(--card);
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                margin-top: 20px;
                font-size: 4rem;
            }
        </style>
    </head>

    <body>
        <h1>Neo Finance - Sistema em Manutenção</h1>
        <div class="spinner"></div>
        <div class="messagem">
            <p>Nosso sistema está passando por manutenção no momento. Por favor, tente novamente mais tarde.</p>
        </div>
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
    exit(); 
}
