<?php
include("../../config/database/conexao.php");

function calcularBalanco($conn, $userId)
{
  // Consultar receitas e despesas
  $queryReceitas = "SELECT SUM(valor) AS totalReceitas FROM transacoes WHERE tipo = 'receita' AND usuario_id = $userId";
  $queryDespesas = "SELECT SUM(valor) AS totalDespesas FROM transacoes WHERE tipo = 'despesa' AND usuario_id = $userId";

  $resultReceitas = mysqli_query($conn, $queryReceitas);
  $resultDespesas = mysqli_query($conn, $queryDespesas);

  // Extrair valores
  $receitas = mysqli_fetch_assoc($resultReceitas)['totalReceitas'] ?? 0;
  $despesas = mysqli_fetch_assoc($resultDespesas)['totalDespesas'] ?? 0;
  $total = $receitas + $despesas;
  $proporcaoReceitas = ($total > 0) ? ($receitas / $total) * 800 : 0; // Largura em pixels
  $proporcaoDespesas = ($total > 0) ? ($despesas / $total) * 800 : 0; // Largura em pixels

  // Calcular Balanço Total
  $balanco = $receitas - $despesas;

  return [
    'receitas' => $receitas,
    'despesas' => $despesas,
    'balanco' => $balanco,
    'proporcaoReceitas' => $proporcaoReceitas,
    'proporcaoDespesas' => $proporcaoDespesas
  ];
}
