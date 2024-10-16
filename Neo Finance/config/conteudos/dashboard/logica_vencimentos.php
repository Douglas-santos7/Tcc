<?php
include("../../config/database/conexao.php");

// Função para obter o próximo vencimento pendente
function obterProximoVencimento($conn, $userId)
{
  $queryVencimentos = "SELECT id, descricao, data_vencimento, valor, categoria, status, tipo_transacao
                         FROM vencimentos
                         WHERE usuario_id = ? AND status = 'Pendente' AND data_vencimento >= CURDATE()
                         ORDER BY data_vencimento ASC
                         LIMIT 1";

  $stmt = $conn->prepare($queryVencimentos);
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $resultVencimentos = $stmt->get_result();

  // Verifica se existe um vencimento
  if ($vencimento = $resultVencimentos->fetch_assoc()) {
    return $vencimento; // Retorna o vencimento encontrado
  } else {
    return [
      'id' => null,
      'descricao' => "Sem vencimentos pendentes",
      'data_vencimento' => "",
      'valor' => 0,
      'categoria' => "",
      'status' => "Nenhum",
      'tipo_transacao' => ""
    ];
  }
}

function confirmarPagamento($conn, $vencimentoId)
{
  // Inicia uma transação
  mysqli_begin_transaction($conn);

  // Busca os detalhes do vencimento
  $stmt = $conn->prepare("SELECT v.*, c.id AS categoria_id, c.icone
                            FROM vencimentos v
                            JOIN categorias c ON v.categoria = c.nome
                            WHERE v.id = ?");
  $stmt->bind_param("i", $vencimentoId);
  $stmt->execute();
  $vencimento = $stmt->get_result()->fetch_assoc();

  if ($vencimento) {
    // Insere a transação na tabela de transações
    $stmt = $conn->prepare("INSERT INTO transacoes (usuario_id, tipo, categoria_id, nome, valor, data, criado_em, icone)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $usuario_id = $vencimento['usuario_id']; // Certifique-se que esta chave está correta
    $tipo_transacao = $vencimento['tipo_transacao']; // Certifique-se que esta chave está correta
    $categoria_id = $vencimento['categoria_id']; // Certifique-se que esta chave está correta
    $nome = $vencimento['descricao'];
    $valor = $vencimento['valor'];
    $data = $vencimento['data_vencimento'];
    $criado_em = date('Y-m-d H:i:s');
    $icone = $vencimento['icone']; // Certifique-se que esta chave está correta

    // Use 'isssssss' se estiver vinculando 8 parâmetros
    $stmt->bind_param("isssssss", $usuario_id, $tipo_transacao, $categoria_id, $nome, $valor, $data, $criado_em, $icone);

    if ($stmt->execute()) {
      // Marca o vencimento como pago
      $stmt = $conn->prepare("UPDATE vencimentos SET status = 'Pago' WHERE id = ?");
      $stmt->bind_param("i", $vencimentoId);
      $stmt->execute();

      // Confirma a transação
      mysqli_commit($conn);
      return true;
    } else {
      // Se a inserção falhar, reverte a transação
      mysqli_rollback($conn);
      return false;
    }
  }

  // Se o vencimento não existir, reverte a transação
  mysqli_rollback($conn);
  return false;
}

// Verifica se o formulário de confirmação foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_pagamento'])) {
  $vencimentoId = $_POST['vencimento_id']; // ID do vencimento a ser atualizado
  if (confirmarPagamento($conn, $vencimentoId)) {
    echo "<script>alert('Pagamento confirmado com sucesso!');</script>";
  } else {
    echo "<script>alert('Erro ao confirmar o pagamento.');</script>";
  }
}

// Obtém o ID do usuário logado
$userId = $_SESSION['user_id'];
$proximoVencimento = obterProximoVencimento($conn, $userId);

