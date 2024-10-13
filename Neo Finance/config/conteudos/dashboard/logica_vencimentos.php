<?php
include("../../config/database/conexao.php");

function obterProximoVencimento($conn, $userId)
{
  $queryVencimentos = "SELECT descricao, data_vencimento, valor, categoria
                     FROM vencimentos
                     WHERE usuario_id = $userId
                     AND status = 'Pendente'
                     AND data_vencimento >= CURDATE()
                     ORDER BY data_vencimento ASC
                     LIMIT 1";
  $resultVencimentos = mysqli_query($conn, $queryVencimentos);

  // Extrair o próximo vencimento
  $vencimento = mysqli_fetch_assoc($resultVencimentos);

  // Verifica se existe um vencimento
  if ($vencimento) {
    // Se existir, armazene as informações
    $descricao = $vencimento['descricao'];
    $data_vencimento = $vencimento['data_vencimento'];
    $valor = $vencimento['valor'];
    $categoria = $vencimento['categoria'];
  } else {
    // Caso não exista, defina valores padrão
    $descricao = "Sem vencimentos pendentes";
    $data_vencimento = "";
    $valor = 0;
    $categoria = "";
  }
  return [
    'descricao' => $descricao,
    'data_vencimento' => $data_vencimento,
    'valor' => $valor,
    'categoria' => $categoria
  ];
}
