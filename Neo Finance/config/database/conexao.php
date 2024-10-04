<?php

$servername = "localhost"; // Nome do servidor (ou IP)
$username = "root"; // Nome de usuário do banco de dados
$password = ""; // Senha do banco de dados
$dbname = "finance"; // Nome do banco de dados

// Tenta criar a conexão com o banco de dados
try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica se houve algum erro de conexão
    if ($conn->connect_error) {
        // Ambiente de desenvolvimento: exibe erro real
        if ($_SERVER['SERVER_NAME'] === 'localhost') {
            throw new Exception("Erro de conexão com o banco de dados: " . $conn->connect_error);
        } else {
            throw new Exception("Erro de conexão com o banco de dados.");
        }
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
        <style>
            /* IMPORT DA FONTE PRINCIPAL */
            @import url("https://fonts.googleapis.com/css2?family=Kodchasan:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700&display=swap");

            :root {
                /* CORES */
                --cor-primaria: #cfffb5;
                --cor-secundaria: #003115;
                --cor-terciaria: #003617;
                --cor--destaque-verde: #00e060;
                --cor--background--animação: rgba(217, 217, 217, 0.3);
                --background: rgba(184, 239, 203, 1);
                --background--cards: #f1eeff;
                --background--icon: rgba(2, 84, 45, 0.49);
                --hover: rgba(11, 170, 77, 0.3);
                --verde--vidro: rgba(0, 54, 23, 0.2);
                --verde--datas: rgba(0, 54, 23, 0.7);
                --card: rgba(139, 205, 167, 0.7);
                --card-categorias: rgba(0, 60, 25, 0.78);

                /* COR DE FONTES */

                --fonte-branco-50: rgba(255, 255, 255, 0.5);
                --fonte-branco-100: rgba(255, 255, 255, 1);
                --fonte-cards: #003617;

                /* FONTES */

                --fonte-principal: "Kodchasan", sans-serif;
            }

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

            .mensagem {
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
        <div class="mensagem">
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
    // Log para desenvolvimento (opcional)
    error_log($e->getMessage());

    // Finaliza o script
    exit();
}
