<?php
// Incluindo conexão com o banco de dados
include("../../database/conexao.php");

// Iniciando a sessão
session_start();
// Obtendo o ID do usuário da sessão
$userId = $_SESSION['user_id']; // Usar um valor padrão se o usuário não estiver autenticado

// Obtendo o período da requisição (GET)
$periodo = $_GET['periodo'] ?? 'diario'; // Define 'diario' como padrão

// Chamando a função e obtendo os dados filtrados
$dadosBalancoFiltrado = calcularBalancoFiltrado($conn, $userId, $periodo);

// Função para calcular o balanço com base no período
function calcularBalancoFiltrado($conn, $userId, $periodo)
{
    // Definindo as datas de início e fim com base no período selecionado
    $dataInicial = '';
    $dataFinal = date('Y-m-d H:i:s', strtotime('tomorrow')); // Data atual mais 24 horas

    switch ($periodo) {
        case 'mensal':
            $dataInicial = date('Y-m-01 00:00:00'); // Primeiro dia do mês atual
            break;
        case 'semanal':
            $dataInicial = date('Y-m-d H:i:s', strtotime('last Sunday')); // Último domingo
            break;
        case 'diario':
            $dataInicial = date('Y-m-d 00:00:00'); // Hoje
            break;
        default:
            return ['error' => 'Período inválido.'];
    }

    // Consultar receitas e despesas com base no período
    $queryReceitas = "SELECT SUM(valor) AS totalReceitas FROM transacoes WHERE tipo = 'receita' AND usuario_id = $userId AND data >= '$dataInicial' AND data <= '$dataFinal'";
    $queryDespesas = "SELECT SUM(valor) AS totalDespesas FROM transacoes WHERE tipo = 'despesa' AND usuario_id = $userId AND data >= '$dataInicial' AND data <= '$dataFinal'";

    // Executar consultas
    $resultReceitas = mysqli_query($conn, $queryReceitas);
    $resultDespesas = mysqli_query($conn, $queryDespesas);

    // Consultar saldo do usuário
    $querySaldo = "SELECT saldo FROM users WHERE id = $userId";
    $resultSaldo = mysqli_query($conn, $querySaldo);
    $saldo = mysqli_fetch_assoc($resultSaldo)['saldo'] ?? 0;

    // Extrair valores de receitas e despesas
    $receitas = mysqli_fetch_assoc($resultReceitas)['totalReceitas'] ?? 0;
    $despesas = mysqli_fetch_assoc($resultDespesas)['totalDespesas'] ?? 0;

    // Calcular o total de receitas e despesas para a proporção
    $total = $receitas + $despesas + $saldo;

    // Calcular as proporções em pixels (800 é a largura total do gráfico)
    $proporcaoReceitas = ($total > 0) ? (($receitas + $saldo) / $total) * 800 : 0; // Largura da barra de receitas
    $proporcaoDespesas = ($total > 0) ? ($despesas / $total) * 800 : 0; // Largura da barra de despesas

    // Calcular o balanço
    $balanco = $receitas - $despesas + $saldo;

    return [
        'receitas' => $receitas + $saldo,  // Receitas totais incluindo saldo
        'despesas' => $despesas,           // Despesas totais
        'saldo' => $saldo,                 // Saldo atual do usuário
        'balanco' => $balanco,             // Balanço inclui o saldo
        'proporcaoReceitas' => $proporcaoReceitas,  // Proporção de receitas no gráfico
        'proporcaoDespesas' => $proporcaoDespesas,  // Proporção de despesas no gráfico
    
        // Novas variáveis filtradas com base no período selecionado
        'receitas_filtradas' => $receitas,     // Receitas filtradas pelo período
        'despesas_filtradas' => $despesas,     // Despesas filtradas pelo período
        'saldo_filtrado' => $saldo,            // Saldo filtrado pelo período (se aplicável)
        'total_filtrado' => $total             // Total filtrado (receitas + despesas + saldo)
    ];
    
}

// Retornando os dados em formato JSON
header('Content-Type: application/json');
echo json_encode($dadosBalancoFiltrado);
