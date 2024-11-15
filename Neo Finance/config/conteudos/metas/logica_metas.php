<?php

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