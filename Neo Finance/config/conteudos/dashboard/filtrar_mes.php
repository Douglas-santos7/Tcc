<?php
// Incluindo conexão com o banco de dados
include("../../database/conexao.php");

// Iniciando a sessão
session_start();
// Obtendo o ID do usuário da sessão
$userId = $_SESSION['user_id'] ?? null;

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
            $dataInicial = date('Y-m-d H:i:s', strtotime('last Monday')); // Última segunda-feira
            break;
        case 'diario':
            $dataInicial = date('Y-m-d 00:00:00'); // Primeiro dia do dia atual
            break;
        default:
            return ['error' => 'Período inválido.'];
    }

    // Consultar receitas e despesas com base no período
    $queryReceitas = "SELECT SUM(valor) AS totalReceitas FROM transacoes WHERE tipo = 'receita' AND usuario_id = ? AND data >= ? AND data <= ?";
    $queryDespesas = "SELECT SUM(valor) AS totalDespesas FROM transacoes WHERE tipo = 'despesa' AND usuario_id = ? AND data >= ? AND data <= ?";
    $querySaldo = "SELECT saldo FROM users WHERE id = ?";

    // Preparar e executar a consulta para receitas
    $stmt = $conn->prepare($queryReceitas);
    $stmt->bind_param("iss", $userId, $dataInicial, $dataFinal);
    $stmt->execute();
    $resultReceitas = $stmt->get_result();
    $receitas = $resultReceitas->fetch_assoc()['totalReceitas'] ?? 0;

    // Preparar e executar a consulta para despesas
    $stmt = $conn->prepare($queryDespesas);
    $stmt->bind_param("iss", $userId, $dataInicial, $dataFinal);
    $stmt->execute();
    $resultDespesas = $stmt->get_result();
    $despesas = $resultDespesas->fetch_assoc()['totalDespesas'] ?? 0;

    // Preparar e executar a consulta para saldo
    $stmt = $conn->prepare($querySaldo);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $resultSaldo = $stmt->get_result();
    $saldo = $resultSaldo->fetch_assoc()['saldo'] ?? 0;

    // Calcular o balanço
    $balanco = $receitas - $despesas + $saldo;

    // Calcular o total de receitas e despesas para a proporção
    $total = $receitas + $despesas + $saldo;

    // Calcular as proporções em pixels (600 é a largura total do gráfico)
    $proporcaoReceitas = ($total > 0) ? ($receitas / $total) * 600 : 0;
    $proporcaoDespesas = ($total > 0) ? ($despesas / $total) * 600 : 0;
    $proporcaoBalanco = ($total > 0) ? (abs($balanco) / $total) * 600 : 0;

    return [
        'receitas' => number_format($receitas, 2, ',', '.'),
        'despesas' => number_format($despesas, 2, ',', '.'),
        'saldo' => number_format($saldo, 2, ',', '.'),
        'balanco' => number_format($balanco, 2, ',', '.'),
        'proporcaoReceitas' => $proporcaoReceitas,
        'proporcaoDespesas' => $proporcaoDespesas,
        'proporcaoBalanco' => $proporcaoBalanco,
    ];
}

// Retornando os dados em formato JSON
header('Content-Type: application/json');
echo json_encode($dadosBalancoFiltrado);
