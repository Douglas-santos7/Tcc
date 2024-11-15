<?php
// Conexão do banco de dados
require_once '../../config/database/conexao.php';
session_start();

// Verificando sessão
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
  echo "Erro: Usuário não autenticado.";
  exit();
}

$userId = $_SESSION['user_id']; // Puxando ID do user

// Verificar se o usuário existe na tabela 'users'
$sql_check_user = "SELECT id FROM users WHERE id = ?";
$stmt_check_user = $conn->prepare($sql_check_user);
$stmt_check_user->bind_param("i", $userId);
$stmt_check_user->execute();
$stmt_check_user->store_result();

// Se não encontrar o usuário, interrompa a execução
if ($stmt_check_user->num_rows == 0) {
  echo "Erro: O usuário não existe.";
  exit();
}
$stmt_check_user->close();

/* ================================
ENVIO DE FORMULARIO DA META CRIADA
===================================*/
// Verifique se o formulário foi enviado para criação de meta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['goal_name']) && isset($_POST['target_amount']) && isset($_POST['deadline'])) {
  $goalName = $_POST['goal_name'];
  $targetAmount = $_POST['target_amount'];
  $deadline = $_POST['deadline'];

  // Inserção de dados na tabela 'metas_usuario'
  $sql = "INSERT INTO metas_usuario (id_usuario, nome_meta, valor_alvo, prazo) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("isds", $userId, $goalName, $targetAmount, $deadline);

  if ($stmt->execute()) {
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
  } else {
    echo "Erro: " . $sql . "<br>" . $conn->error;
  }
  $stmt->close();
}

/* ================================
LÓGICA DE DEPÓSITO
===================================*/
// Processar o depósito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['depositar'])) {
  $depositValue = $_POST['deposit_value'];
  $goalId = $_POST['goal_id'];

  if ($depositValue > 0) {
    // Atualizar o valor atual da meta
    $sql = "UPDATE metas_usuario SET valor_atual = valor_atual + ? WHERE id_meta = ? AND id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dii", $depositValue, $goalId, $userId);

    if ($stmt->execute()) {
      // Registrar a transação no histórico
      $sql_history = "INSERT INTO historico_transacoes (id_meta, id_usuario, tipo_transacao, valor) VALUES (?, ?, 'deposito', ?)";
      $stmt_history = $conn->prepare($sql_history);
      $stmt_history->bind_param("iid", $goalId, $userId, $depositValue);
      $stmt_history->execute();
      
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
    } else {
      echo "Erro: " . $stmt->error;
    }
    $stmt->close();
  } else {
    echo "Erro: O valor do depósito não pode ser negativo.";
  }
}

/* ================================
LÓGICA DE RESGATE
===================================*/
// Processar o resgatar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resgatar'])) {
  $withdrawValue = $_POST['withdraw_value'];
  $goalId = $_POST['goal_id'];

  if ($withdrawValue > 0) {
    // Buscar o valor atual da meta
    $sql = "SELECT valor_atual FROM metas_usuario WHERE id_meta = ? AND id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $goalId, $userId);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($currentValue);
    $stmt->fetch();
    $stmt->close();

    if ($currentValue >= $withdrawValue) {
      // Atualizar o valor atual da meta
      $sql = "UPDATE metas_usuario SET valor_atual = valor_atual - ? WHERE id_meta = ? AND id_usuario = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("dii", $withdrawValue, $goalId, $userId);

      if ($stmt->execute()) {
        // Registrar a transação no histórico
        $sql_history = "INSERT INTO historico_transacoes (id_meta, id_usuario, tipo_transacao, valor) VALUES (?, ?, 'resgato', ?)";
        $stmt_history = $conn->prepare($sql_history);
        $stmt_history->bind_param("iid", $goalId, $userId, $withdrawValue);
        $stmt_history->execute();
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
      } else {
        echo "Erro: " . $stmt->error;
      }
      $stmt->close();
    } else {
      echo "Erro: O valor a ser resgatado não pode ser maior que o valor atual da meta.";
    }
  } else {
    echo "Erro: O valor do resgate não pode ser negativo.";
  }
}

/* ================================
LÓGICA DE EXCLUSÃO DE META
===================================*/
// Processar a exclusão da meta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_goal_id'])) {
  $goalIdToDelete = $_POST['delete_goal_id'];

  // Excluir a meta
  $sql_delete = "DELETE FROM metas_usuario WHERE id_meta = ? AND id_usuario = ?";
  $stmt_delete = $conn->prepare($sql_delete);
  $stmt_delete->bind_param("ii", $goalIdToDelete, $userId);

  if ($stmt_delete->execute()) {
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
  } else {
    echo "Erro ao excluir a meta: " . $stmt_delete->error;
  }
  $stmt_delete->close();
}

// Seleção de metas para exibição
$sql = "SELECT * FROM metas_usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Minhas Metas - Neo Finance</title>
  <link rel="stylesheet" href="../../css/conteudos/metas/metas.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
  <h1>Minhas Metas</h1>
  <div class="container">
    <div class="menu-criar-meta">
      <!-- Formulário para criação de meta -->
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <label for="goal_name">Nome da Meta:</label>
        <input type="text" id="goal_name" name="goal_name" required><br><br>
        <label for="target_amount">Valor Alvo:</label>
        <input type="number" id="target_amount" name="target_amount" required><br><br>
        <label for="deadline">Prazo:</label>
        <input type="date" id="deadline" name="deadline" required><br><br>
        <button type="submit">Criar Meta</button>
      </form>
    </div>

    <div class="container-cards">
      <?php while ($row = $result->fetch_assoc()) : 
        $goalId = $row['id_meta'];  // Defina o ID da meta para o histórico
      ?>
        <div class="card-meta">
          <div class="titulo-card">
             <!-- Ícone de exclusão -->
             <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display:inline;">
              <input type="hidden" name="delete_goal_id" value="<?php echo $goalId; ?>">
              <button type="submit" style="background: none; border: none; color: red; cursor: pointer;">
                <i class="fas fa-trash"></i> Excluir
              </button>
            </form>
            <span><?php echo $row['nome_meta']; ?></span>
          </div>
          <div class="progresso-meta">
            <span>Progresso:</span>
            <div class="barra-progresso">
              <div class="barra-progresso-preenchida" style="width: <?php echo ($row['valor_atual'] / $row['valor_alvo']) * 100; ?>%;"></div>
            </div>
            <span>R$ <?php echo $row['valor_atual']; ?> de R$ <?php echo $row['valor_alvo']; ?></span>
            <?php if ($row['valor_atual'] >= $row['valor_alvo']) : ?>
              <div class="mensagem-meta-alcancada">Meta Alcançada!</div>
            <?php endif; ?>
          </div>

          <!-- Formulário de resgatar -->
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form-resgatar">
            <input type="hidden" name="goal_id" value="<?php echo $goalId; ?>">
            <label for="withdraw_value">Valor a Resgatar:</label>
            <input type="number" id="withdraw_value" name="withdraw_value" required>
            <button type="submit" name="resgatar">Resgatar</button>
          </form>

          <!-- Formulário de depósito -->
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form-depositar">
            <input type="hidden" name="goal_id" value="<?php echo $goalId; ?>">
            <label for="deposit_value">Valor a Depositar:</label>
            <input type="number" id="deposit_value" name="deposit_value" required>
            <button type="submit" name="depositar">Depositar</button>
          </form>

          <!-- Exibição do histórico -->
          <div class="historico-transacoes">
            <h3>Histórico</h3>
            <table>
              <tr>
                <th>Tipo</th>
                <th>Valor</th>
                <th>Data</th>
              </tr>
              <?php
                // Consulta de histórico para a meta específica
                $sql_history = "SELECT tipo_transacao, valor, data_transacao FROM historico_transacoes WHERE id_usuario = ? AND id_meta = ?";
                $stmt_history = $conn->prepare($sql_history);
                $stmt_history->bind_param("ii", $userId, $goalId);  // Passando o ID da meta
                $stmt_history->execute();
                $history_result = $stmt_history->get_result();
                
                while ($history = $history_result->fetch_assoc()) : 
              ?>
                <tr>
                  <td><?php echo ucfirst($history['tipo_transacao']); ?></td>
                  <td>R$ <?php echo number_format($history['valor'], 2, ',', '.'); ?></td>
                  <td><?php echo date('d/m/Y H:i', strtotime($history['data_transacao'])); ?></td>
                </tr>
              <?php endwhile; ?>
            </table>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</body>
</html>

<?php $conn->close(); ?>
