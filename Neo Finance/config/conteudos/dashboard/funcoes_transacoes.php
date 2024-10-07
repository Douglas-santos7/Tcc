<?php
function consultarReceitasDespesas($conn, $userId)
{
    // Consultar receitas e despesas
    $queryReceitas = "SELECT SUM(valor) AS totalReceitas FROM transacoes WHERE tipo = 'receita' AND usuario_id = $userId";
    $queryDespesas = "SELECT SUM(valor) AS totalDespesas FROM transacoes WHERE tipo = 'despesa' AND usuario_id = $userId";

    $resultReceitas = mysqli_query($conn, $queryReceitas);
    $resultDespesas = mysqli_query($conn, $queryDespesas);

    // Extrair valores
    $receitas = mysqli_fetch_assoc($resultReceitas)['totalReceitas'] ?? 0;
    $despesas = mysqli_fetch_assoc($resultDespesas)['totalDespesas'] ?? 0;

    return [
        'receitas' => $receitas,
        'despesas' => $despesas,
        'total' => $receitas + $despesas
    ];
}

function consultarHistorico($conn, $userId)
{
    // Consultar os 5 últimos lançamentos
    $queryHistorico = "SELECT tipo, nome, valor, data FROM transacoes WHERE usuario_id = $userId ORDER BY data DESC LIMIT 5";
    return mysqli_query($conn, $queryHistorico);
}
?>