<?php
include("../config/database/conexao.php");
include("../config/conteudos/login/verifica_login.php");


// Obtém o saldo inicial do usuário
$user_id = $_SESSION['user_id'];
$query = "SELECT saldo_inicial_adicionado, saldo FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$saldo_inicial_adicionado = $user['saldo_inicial_adicionado'];

// Processa o formulário de saldo inicial somente se o saldo não foi adicionado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['saldo_inicial'])) {
    // Remove o "R$ " e formata o valor
    $saldo_inicial = str_replace(['R$', ' ', '.'], '', $_POST['saldo_inicial']);
    $saldo_inicial = str_replace(',', '.', $saldo_inicial); // Troca vírgula por ponto

    // Converte para float
    $saldo_inicial = (float)$saldo_inicial;

    // Atualiza o saldo do usuário
    $query = "UPDATE users SET saldo = saldo + ?, saldo_inicial_adicionado = 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('di', $saldo_inicial, $user_id);  // 'd' para decimal, 'i' para integer
    $stmt->execute();

    // Atualiza a sessão com o novo saldo (opcional)
    $_SESSION['saldo'] = $user['saldo'] + $saldo_inicial;

    // Redireciona após adicionar o saldo para evitar a repetição do envio do formulário
    header("Location: ./home.php?saldo_adicionado=1");
    exit();
  } elseif (isset($_POST['skip_saldo'])) {
    // Atualiza o campo saldo_inicial_adicionado para 1
    $query = "UPDATE users SET saldo_inicial_adicionado = 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();

    // Redireciona após atualizar o campo
    header("Location: ./home.php");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Neo Finance - Home</title>
    <link rel="stylesheet" href="../css/home.css" />
    <script src="../js/home/sidebar.js" defer></script>
    <script src="../js/home/iframe.js" defer></script>
    <link rel="stylesheet" href="../css/home/modal--bemvindo.css" />
    <link rel="stylesheet" href="../css/home/modal--add_valor.css" />
  </head>

  <body>
    <div class="container">
      <!-- Sidebar -->
      <div class="sidebar" id="sidebar">
        <div class="sidebar-content" id="bloquear-selecao">
          <div class="logo--sidebar" id="logo--sidebar">
            <img src="../assets/img/neofinance--logo.svg" />
          </div>
          <ul>
            <li class="Item1" data-src="./conteudos/(1) dashboard.php" data-title="Dashboard">
              <div class="barra--icon"></div>
              <img class="icon--li" src="../assets/icons/home--sidebar/home--icon.svg" />
              <span>Home</span>
            </li>
            <div class="espacamentoLi"></div>
            <li class="Item2" data-src="./conteudos/(2) graficos.php" data-title="Gráficos">
              <div class="barra--icon"></div>
              <img class="icon--li" src="../assets/icons/home--sidebar/graficos--icon.svg" />
              <span>Gráficos</span>
            </li>
            <li class="Item3" data-src="./conteudos/(3) historico.php" data-title="Histórico">
              <div class="barra--icon"></div>
              <img class="icon--li" src="../assets/icons/home--sidebar/historico--icon.svg" />
              <span>Histórico</span>
            </li>
            <li class="Item4" data-src="./conteudos/(4) calendario.php" data-title="Calendário">
              <div class="barra--icon"></div>
              <img class="icon--li" src="../assets/icons/home--sidebar/calendario--icon.svg" />
              <span>Calendário</span>
            </li>
            <li class="Item5" data-src="./conteudos/(5) teste.html" data-title="ChatBot">
              <div class="barra--icon"></div>
              <img class="icon--li" src="../assets/icons/home--sidebar/chatbot--icon.svg" />
              <span>ChatBot</span>
            </li>
            <li class="Item6" data-src="./conteudos/(6) categorias.php" data-title="Categorias">
              <div class="barra--icon"></div>
              <img class="icon--li" src="../assets/icons/home--sidebar/categorias--icon.svg" />
              <span>Categorias</span>
            </li>
            <li class="Item7" data-src="./conteudos/(7) teste.html" data-title="Metas">
              <div class="barra--icon"></div>
              <img class="icon--li" src="../assets/icons/home--sidebar/metas--icon.svg" />
              <span>Metas</span>
            </li>
            <div class="espacamentoLi"></div>
            <li class="Item8" data-src="./conteudos/(8) teste.html" data-title="Suporte">
              <div class="barra--icon"></div>
              <img class="icon--li" src="../assets/icons/home--sidebar/suport--icon.svg" />
              <span>Suporte</span>
            </li>
            <li class="Item9" id="logout-button">
              <div class="barra--icon"></div>
              <img class="icon--li" src="../assets/icons/home--sidebar/sair--icon.svg" />
              <span>Sair</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="conteudo" id="conteudo">
      <iframe id="mainIframe" src="./conteudos/(1) dashboard.php" width="100%" height="100%"></iframe>
      <button class="toggle-button">></button>

      <!-- Modal de Boas-Vindas -->
      <div id="welcomeModal" class="welcome-modal">
        <div class="welcome-modal-content">
          <h2 id="modalMessage"></h2>
          <div id="welcomeButtonContainer">
            <button id="addSaldoButton" class="welcome-button">Sim</button>
            <form method="POST" action="">
              <input type="hidden" name="skip_saldo" value="1" />
              <button type="submit" id="skipSaldoButton" class="welcome-button">Não</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Modal de Saldo -->
      <div id="saldoModal" class="modal">
        <div class="modal-content">
          <h2 id="saldoModalMessage">Digite o saldo inicial</h2>
          <form id="saldoForm" method="POST" action="">
            <input class="valor_saldo" id="valor_saldo" type="text" name="saldo_inicial" placeholder="R$ 0,00" required />
            <button type="submit">Adicionar Saldo</button>
          </form>
        </div>
      </div>
      <script src="../js/conteudos/dashboard/formataMoeda.js"></script>
      <script>
        // Evento de digitação para formatar o valor do saldo inicial
        document.getElementById("valor_saldo").addEventListener("input", function() {
          let valorAtual = this.value;
          this.value = formatarMoeda(valorAtual.replace(/\D/g, ""));
        });

        // Variáveis para os modais
        var welcomeModal = document.getElementById("welcomeModal");
        var saldoModal = document.getElementById("saldoModal");
        var saldoModalMessage = document.getElementById("saldoModalMessage");

        // Função para abrir o modal de boas-vindas
        function abrirWelcomeModal() {
          welcomeModal.classList.add("show");
          welcomeModal.style.display = 'flex';

          const h2 = document.getElementById("modalMessage");
          const messages = [
            "Olá",
            "Bem-vindo ao Neo Finance!",
            "Antes de começarmos, deseja definir um saldo inicial?"
          ];
          const welcomeButtonContainer = document.getElementById("welcomeButtonContainer");
          let currentIndex = 0;

          function changeMessage() {
            if (currentIndex < messages.length) {
              h2.classList.add("hidden");
              setTimeout(() => {
                h2.textContent = messages[currentIndex];
                h2.classList.remove("hidden");

                if (currentIndex === 2) {
                  setTimeout(() => {
                    welcomeButtonContainer.classList.add("visible");
                  }, 500);
                }

                currentIndex++;
                setTimeout(changeMessage, 4000);
              }, 500);
            }
          }

          document.getElementById("skipSaldoButton").addEventListener("click", (event) => {
            event.preventDefault();
            h2.classList.add("hidden");
            welcomeButtonContainer.classList.remove("visible");
            setTimeout(() => {
              h2.textContent = "Ok, então vamos lá";
              h2.classList.remove("hidden");
              setTimeout(() => {
                fecharWelcomeModal();
              }, 2000);
            }, 500);

            const form = new FormData();
            form.append('skip_saldo', '1');
            fetch('', {
              method: 'POST',
              body: form
            });
          });

          setTimeout(changeMessage, 100);
        }

        // Evento de clique no botão "Adicionar Saldo"
        document.getElementById("addSaldoButton").onclick = function() {
          fecharWelcomeModal();
          abrirSaldoModal();
        };

        // Função para fechar o modal de boas-vindas
        function fecharWelcomeModal() {
          welcomeModal.classList.add("hide");
          welcomeModal.addEventListener('transitionend', () => {
            if (!welcomeModal.classList.contains("show")) {
              welcomeModal.classList.remove("show");
              welcomeModal.style.display = 'none';
            }
          }, {
            once: true
          });
        }

        // Função para abrir o modal de saldo
        function abrirSaldoModal() {
          saldoModal.classList.add("show");
          saldoModal.style.display = 'flex';
          setTimeout(() => {
            const modalContent = saldoModal.querySelector(".modal-content");
            modalContent.classList.add("show");
          }, 500);
        }

        // Verifica se o saldo inicial foi adicionado e abre o modal de boas-vindas se não foi
        <?php if ($saldo_inicial_adicionado == 0): ?>
          abrirWelcomeModal();
        <?php endif; ?>

        // Evento de envio do formulário de saldo
        document.getElementById("saldoForm").addEventListener("submit", function(event) {
          event.preventDefault();

          saldoModalMessage.textContent = "Ótimo, já podemos começar";

          const form = this;
          form.classList.add("hidden");

          saldoModalMessage.classList.remove("hidden");

          const formData = new FormData(form);
          fetch('', {
              method: 'POST',
              body: formData
            })
            .then(response => {
              if (response.ok) {
                document.getElementById("mainIframe").contentWindow.location.reload();
                setTimeout(() => {
                  saldoModal.classList.remove("show");
                  saldoModal.classList.add("hide");
                  saldoModal.addEventListener('transitionend', () => {
                    if (!saldoModal.classList.contains("show")) {
                      saldoModal.style.display = 'none';
                    }
                  }, {
                    once: true
                  });
                }, 2000);
              }
            })
            .catch(error => {
              console.error('Erro:', error);
            });
        });
      </script>
    </div>
  </body>
</html>
