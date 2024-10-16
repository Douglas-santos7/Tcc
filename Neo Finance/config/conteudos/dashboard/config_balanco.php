<?php
include("../../config/database/conexao.php");


function calcularBalanco($conn, $userId, $saldoInicial = 0)
{
    // Consultar receitas e despesas
    $queryReceitas = "SELECT SUM(valor) AS totalReceitas FROM transacoes WHERE tipo = 'receita' AND usuario_id = ?";
    $queryDespesas = "SELECT SUM(valor) AS totalDespesas FROM transacoes WHERE tipo = 'despesa' AND usuario_id = ?";

    $stmtReceitas = $conn->prepare($queryReceitas);
    $stmtReceitas->bind_param("i", $userId);
    $stmtReceitas->execute();
    $resultReceitas = $stmtReceitas->get_result();
    $receitas = $resultReceitas->fetch_assoc()['totalReceitas'] ?? 0;

    $stmtDespesas = $conn->prepare($queryDespesas);
    $stmtDespesas->bind_param("i", $userId);
    $stmtDespesas->execute();
    $resultDespesas = $stmtDespesas->get_result();
    $despesas = $resultDespesas->fetch_assoc()['totalDespesas'] ?? 0;

    // Calcular Balanço Total
    $balanco = $saldoInicial + $receitas - $despesas;

    // Calcular proporções
    $total = $receitas + $despesas;
    $proporcaoReceitas = ($total > 0) ? ($receitas / $total) * 800 : 0; // Largura em pixels
    $proporcaoDespesas = ($total > 0) ? ($despesas / $total) * 800 : 0; // Largura em pixels

    return [
        'receitas' => $receitas,
        'despesas' => $despesas,
        'balanco' => $balanco,
        'proporcaoReceitas' => $proporcaoReceitas,
        'proporcaoDespesas' => $proporcaoDespesas
    ];
}

function obterSaldoInicial($conn, $userId)
{
    $query = "SELECT SUM(valor) AS total_saldo_inicial FROM transacoes WHERE usuario_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total_saldo_inicial'] ?? 0; // Retorna saldo inicial
}
?>
