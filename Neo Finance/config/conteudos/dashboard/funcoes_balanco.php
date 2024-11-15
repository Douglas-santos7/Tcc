<?php
include("../../config/database/conexao.php");

function calcularBalanco($conn, $userId)
{
  // Consultar receitas e despesas
  $queryReceitas = "SELECT SUM(valor) AS totalReceitas FROM transacoes WHERE tipo = 'receita' AND usuario_id = $userId";
  $queryDespesas = "SELECT SUM(valor) AS totalDespesas FROM transacoes WHERE tipo = 'despesa' AND usuario_id = $userId";

  $resultReceitas = mysqli_query($conn, $queryReceitas);
  $resultDespesas = mysqli_query($conn, $queryDespesas);

  // Extrair valores de receitas e despesas
  $receitas = mysqli_fetch_assoc($resultReceitas)['totalReceitas'] ?? 0;
  $despesas = mysqli_fetch_assoc($resultDespesas)['totalDespesas'] ?? 0;

  // Consultar saldo do usuário
  $querySaldo = "SELECT saldo FROM users WHERE id = $userId";
  $resultSaldo = mysqli_query($conn, $querySaldo);
  $saldo = mysqli_fetch_assoc($resultSaldo)['saldo'] ?? 0;



  // Calcular Balanço Total (incluindo o saldo)
  $balanco = ($receitas - $despesas) + $saldo;

  return [
    'receitas' => $receitas + $saldo,
    'despesas' => $despesas,
    'saldo' => $saldo, // Retornar saldo para exibição, se necessário
    'balanco' => $balanco, // Balanço inclui o saldo agora
  ];
}
