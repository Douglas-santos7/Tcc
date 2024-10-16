<?php
session_start();
include("../config/database/conexao.php");

// Verifica se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: ./login/login.php");
  exit();
}

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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $saldo_inicial_adicionado == 0) {
  if (isset($_POST['saldo_inicial'])) {
    $saldo_inicial = $_POST['saldo_inicial'];

    // Atualiza o saldo do usuário
    $query = "UPDATE users SET saldo = saldo + ?, saldo_inicial_adicionado = 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('di', $saldo_inicial, $user_id);  // 'd' para decimal, 'i' para integer
    $stmt->execute();

    // Atualiza a sessão com o novo saldo (opcional)
    $_SESSION['saldo'] = $user['saldo'] + $saldo_inicial;

    // Redireciona após adicionar o saldo para evitar a repetição do envio do formulário
    header("Location: ./home.php");
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
  <style>
    /* Estilo do modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background-color: white;
      padding: 20px;
      border-radius: 5px;
      width: 400px;
      text-align: center;
    }

    .close {
      color: red;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }

    .close:hover,
    .close:focus {
      color: #000;
      text-decoration: none;
      cursor: pointer;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="sidebar" id="sidebar">
      <div class="sidebar-content" id="bloquear-selecao">
        <div class="logo--sidebar" <!-- id="logo--sidebar" -->>
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
  <div class="conteudo" id="conteudo">
    <!-- Conteúdo da página -->
    <iframe id="mainIframe" src="./conteudos/(1) dashboard.php" width="100%" height="100%"></iframe>
    <button class="toggle-button">></button>

    <!-- Modal de Boas-Vindas -->
    <div id="welcomeModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Bem-vindo ao Neo Finance!</h2>
        <p>Gostaria de adicionar um saldo inicial?</p>
        <button id="addSaldoButton">Sim</button>
        <form method="POST" action="">
          <input type="hidden" name="skip_saldo" value="1">
          <button type="submit" id="skipSaldoButton">Não</button>
        </form>
      </div>
    </div>

    <!-- Modal de Saldo -->
    <div id="saldoModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Adicionar Saldo Inicial</h2>
        <form method="POST" action="">
          <label for="saldo_inicial">Digite o saldo inicial:</label>
          <input type="number" name="saldo_inicial" step="0.01" required>
          <button type="submit">Adicionar Saldo</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    var welcomeModal = document.getElementById("welcomeModal");
    var saldoModal = document.getElementById("saldoModal");
    var span = document.getElementsByClassName("close");

    // Função para abrir o modal de boas-vindas
    function abrirWelcomeModal() {
      welcomeModal.style.display = "flex";
    }

    // Função para abrir o modal de saldo
    function abrirSaldoModal() {
      welcomeModal.style.display = "none";
      saldoModal.style.display = "flex";
    }

    // Função para fechar o modal
    function fecharModal() {
      welcomeModal.style.display = "none";
      saldoModal.style.display = "none";
    }

    // Fecha o modal quando clicar fora dele
    window.onclick = function(event) {
      if (event.target == welcomeModal || event.target == saldoModal) {
        fecharModal();
      }
    }

    // Fecha o modal ao clicar no botão de fechar
    for (var i = 0; i < span.length; i++) {
      span[i].onclick = function() {
        fecharModal();
      }
    }

    // Abre o modal de boas-vindas automaticamente se o saldo inicial ainda não foi adicionado
    <?php if ($saldo_inicial_adicionado == 0): ?>
      abrirWelcomeModal();
    <?php endif; ?>

    // Eventos para os botões do modal de boas-vindas
    document.getElementById("addSaldoButton").onclick = function() {
      abrirSaldoModal();
    }
  </script>
</body>

</html>
