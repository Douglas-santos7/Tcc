<?php
include("../../config/database/conexao.php");

function obterProximoVencimento($conn, $userId)
{
  // Consulta para obter o próximo vencimento pendente
  $queryVencimentos = "SELECT descricao, data_vencimento, valor, categoria, status, tipo_transacao
                       FROM vencimentos
                       WHERE usuario_id = ? 
                       AND status = 'Pendente'
                       AND data_vencimento >= CURDATE()
                       ORDER BY data_vencimento ASC
                       LIMIT 1";

  // Prepara a consulta para evitar SQL Injection
  $stmt = $conn->prepare($queryVencimentos);
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $resultVencimentos = $stmt->get_result();

  // Verifica se existe um vencimento
  if ($vencimento = $resultVencimentos->fetch_assoc()) {
    // Se existir, armazene as informações
    $descricao = $vencimento['descricao'];
    $data_vencimento = $vencimento['data_vencimento'];
    $valor = $vencimento['valor'];
    $categoria = $vencimento['categoria'];
    $status = $vencimento['status'];
    $tipo_transacao = $vencimento['tipo_transacao'];
  } else {
    // Caso não exista, defina valores padrão
    $descricao = "Sem vencimentos pendentes";
    $data_vencimento = "";
    $valor = 0;
    $categoria = "";
    $status = "Nenhum";
    $tipo_transacao = "";
  }

  // Retorna um array com os dados
  return [
    'descricao' => $descricao,
    'data_vencimento' => $data_vencimento,
    'valor' => $valor,
    'categoria' => $categoria,
    'status' => $status,
    'tipo_transacao' => $tipo_transacao
  ];
}
