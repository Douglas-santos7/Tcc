<?php
session_start();
include("../../config/database/conexao.php");

// Verifica se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../views/login/login.php");
    exit();
}

$userId = $_SESSION['user_id']; // ID do usuário logado

// Verifica se um mês específico foi selecionado
$selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : null;

// Definindo o intervalo padrão (Mensal ou Diário)
$interval = isset($_GET['interval']) && $_GET['interval'] === 'diario' ? 'diario' : 'mensal';

// Consultar receitas e despesas de acordo com o intervalo selecionado
if ($interval === 'diario') {
    $queryReceitas = "SELECT DATE(data) as dia, DAYNAME(data) as dia_semana, SUM(valor) AS totalReceitas 
                      FROM transacoes WHERE tipo = 'receita' AND usuario_id = $userId 
                      AND DATE(data) = CURDATE() GROUP BY dia ORDER BY dia";
    $queryDespesas = "SELECT DATE(data) as dia, DAYNAME(data) as dia_semana, SUM(valor) AS totalDespesas 
                      FROM transacoes WHERE tipo = 'despesa' AND usuario_id = $userId 
                      AND DATE(data) = CURDATE() GROUP BY dia ORDER BY dia";
} else {
    if ($selectedMonth) {
        // Se um mês específico for selecionado
        $queryReceitas = "SELECT DAY(data) as dia, SUM(valor) AS totalReceitas 
                          FROM transacoes WHERE tipo = 'receita' AND usuario_id = $userId 
                          AND MONTH(data) = $selectedMonth AND YEAR(data) = YEAR(CURDATE()) 
                          GROUP BY dia ORDER BY dia";
        $queryDespesas = "SELECT DAY(data) as dia, SUM(valor) AS totalDespesas 
                          FROM transacoes WHERE tipo = 'despesa' AND usuario_id = $userId 
                          AND MONTH(data) = $selectedMonth AND YEAR(data) = YEAR(CURDATE()) 
                          GROUP BY dia ORDER BY dia";
    } else {
        // Se todos os meses forem selecionados
        $queryReceitas = "SELECT MONTH(data) as mes, SUM(valor) AS totalReceitas 
                          FROM transacoes WHERE tipo = 'receita' AND usuario_id = $userId 
                          AND YEAR(data) = YEAR(CURDATE()) GROUP BY mes ORDER BY mes";
        $queryDespesas = "SELECT MONTH(data) as mes, SUM(valor) AS totalDespesas 
                          FROM transacoes WHERE tipo = 'despesa' AND usuario_id = $userId 
                          AND YEAR(data) = YEAR(CURDATE()) GROUP BY mes ORDER BY mes";
    }
}

$resultReceitas = mysqli_query($conn, $queryReceitas);
$resultDespesas = mysqli_query($conn, $queryDespesas);

// Extrair valores
if ($interval === 'diario') {
    // Inicializa os arrays para 30 dias
    $receitas = array_fill(0, 30, 0);
    $despesas = array_fill(0, 30, 0);

    while ($row = mysqli_fetch_assoc($resultReceitas)) {
        $dia = intval(date('j', strtotime($row['dia']))); // Pega o dia do mês
        $receitas[$dia - 1] = $row['totalReceitas']; // -1 para índice de array
    }
    while ($row = mysqli_fetch_assoc($resultDespesas)) {
        $dia = intval(date('j', strtotime($row['dia']))); // Pega o dia do mês
        $despesas[$dia - 1] = $row['totalDespesas']; // -1 para índice de array
    }
} else {
    // Inicializa arrays para os dados mensais
    $receitas = array_fill(0, 12, 0);
    $despesas = array_fill(0, 12, 0);

    while ($row = mysqli_fetch_assoc($resultReceitas)) {
        $mes = intval($row['mes']) - 1; // Mês começa em 1, ajuste para índice de array
        $receitas[$mes] = $row['totalReceitas'];
    }
    while ($row = mysqli_fetch_assoc($resultDespesas)) {
        $mes = intval($row['mes']) - 1; // Mês começa em 1, ajuste para índice de array
        $despesas[$mes] = $row['totalDespesas'];
    }
}
// Consultar as despesas por categoria
$queryDespesasPorCategoria = "SELECT c.nome AS categoria, SUM(t.valor) AS total 
                               FROM transacoes t
                               JOIN categorias c ON t.categoria_id = c.id
                               WHERE t.tipo = 'despesa' AND t.usuario_id = $userId 
                               GROUP BY c.nome 
                               ORDER BY total DESC";

$resultDespesasPorCategoria = mysqli_query($conn, $queryDespesasPorCategoria);

$categoriasDespesas = [];
while ($row = mysqli_fetch_assoc($resultDespesasPorCategoria)) {
    $categoriasDespesas[] = $row;
}

// Fecha a conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Financeiro</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 20px;
}

.container {
    width: 90%;
    max-width: 1200px;
    margin: auto;
}

.card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.card-title {
    font-size: 22px;
    margin-bottom: 15px;
    color: #333;
}

.row {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.chart-card {
    flex: 2; 
}

.options-card {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.button {
    background-color: #007BFF;
    color: white;
    text-align: center;
    padding: 10px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    margin-bottom: 10px;
    transition: background-color 0.3s;
}

.button:hover {
    background-color: #0056b3;
}

h3 {
    margin-top: 20px;
    color: #555;
    border-bottom: 2px solid #007BFF; /* Linha abaixo do título */
    padding-bottom: 10px;
}

ul {
    list-style-type: none;
    padding: 0;
}

ul li {
    background: #e9ecef;
    padding: 15px; /* Aumentado para melhor visualização */
    margin: 10px 0; /* Aumentado o espaçamento entre itens */
    border-radius: 5px;
    transition: background-color 0.3s, transform 0.2s; /* Adiciona efeito ao passar o mouse */
}

ul li:hover {
    background: #d4d4d4; /* Muda a cor de fundo ao passar o mouse */
    transform: translateY(-2px); /* Leve movimento para cima ao passar o mouse */
}

ul li::before {
    content: "✔️"; /* Ícone de verificação antes de cada dica */
    margin-right: 10px;
    color: #007BFF; /* Cor do ícone */
}

.cricri-card {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    margin-top: 20px; 
}

.cricri-card img {
    max-width: 80%; 
    height: auto;
    margin-top: 10px;
}

#chart {
    border-radius: 8px; 
    overflow: hidden; 
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); 
}

    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="card chart-card">
                <h2 class="card-title">Visão Financeira - <?= ucfirst($interval) ?></h2>
                <div id="chart"></div>
            </div>

            <div class="card options-card">
                <h2 class="card-title">Selecionar Intervalo</h2>
                <a href="?interval=mensal" class="button">Mensal</a>
                <a href="?interval=diario" class="button">Diário</a>

                <div class="cricri-card">
                    <h2 class="card-title">Dúvidas? Fale com Cristopher</h2>
                    <img src="../../assets/img/download.jpg" alt="icon-notificacao" />
                </div>
            </div>
        </div>

        <!-- Análise dos dados -->
        <div class="card">
            <h2 class="card-title">Análise dos Dados</h2>
            <p style="color: green;">Receitas: R$<?= number_format(array_sum($receitas), 2, ',', '.') ?></p>
            <p style="color: red;">Despesas: R$<?= number_format(array_sum($despesas), 2, ',', '.') ?></p>

            <h3>Dicas:</h3>
<ul>
    <?php
    $totalReceitas = array_sum($receitas);
    $totalDespesas = array_sum($despesas);
    $porcentagemReceitas = ($totalReceitas > 0) ? (($totalReceitas - $totalDespesas) / $totalReceitas) * 100 : 0;
    $porcentagemDespesas = ($totalDespesas > 0) ? (($totalDespesas - $totalReceitas) / $totalDespesas) * 100 : 0;

    // Defina limites para a personalização das dicas
    $limiteAltoReceitas = 5000; // Exemplo de limite de receita alta
    $limiteBaixoDespesas = 2000; // Exemplo de limite de despesa baixa

    // Condição se receitas são superiores às despesas
    if ($totalReceitas > $totalDespesas): ?>
        <li>Ótimo trabalho! Suas receitas estão superando suas despesas em <?= number_format($porcentagemReceitas, 2) ?>%. Considere aumentar sua reserva ou investir.</li>
        <li>Você está em uma boa situação financeira. Avalie se você pode aumentar suas contribuições para um fundo de emergência.</li>
        <li>Considere diversificar seus investimentos para reduzir riscos.</li>
        <li>Acompanhe suas receitas regularmente para identificar fontes adicionais.</li>
        <?php if ($totalReceitas > $limiteAltoReceitas): ?>
            <li>Com uma receita tão alta, considere investir em opções de renda fixa ou ações para potencializar seu capital.</li>
        <?php endif; ?>
    <?php // Condição se despesas são superiores às receitas
    elseif ($totalDespesas > $totalReceitas): ?>
        <li>Atenção! Suas despesas estão excedendo suas receitas em <?= number_format(abs($porcentagemDespesas), 2) ?>%. É fundamental tomar medidas para evitar problemas financeiros.</li>
        <li>Faça uma lista de suas despesas fixas e variáveis para identificar áreas onde você pode cortar gastos.</li>
        <li>Considere estabelecer um limite mensal para despesas variáveis.</li>
        <li>Se possível, busque formas de aumentar sua renda, como um trabalho extra.</li>
        <?php if ($totalDespesas > $limiteBaixoDespesas): ?>
            <li>Suas despesas estão bastante elevadas. Considere reduzir gastos com lazer e alimentação para equilibrar seu orçamento.</li>
        <?php endif; ?>
    <?php // Condição se receitas e despesas estão equilibradas
    else: ?>
        <li>Suas receitas e despesas estão equilibradas, o que é um sinal positivo de controle financeiro!</li>
        <li>Continue monitorando suas despesas e considere criar um fundo de emergência para lidar com imprevistos.</li>
        <li>Pense em formas de aumentar suas receitas sem aumentar suas despesas.</li>
        <li>Considere investir em educação financeira para aprimorar suas habilidades.</li>
    <?php endif; ?>
    
    <li>Recomenda-se revisar seu planejamento financeiro mensalmente para ajustar suas metas.</li>
</ul>
        </div>

        <script>
    var options = {
        chart: {
            type: 'area'
        },
        series: [{
            name: 'Receitas',
            data: <?= json_encode($receitas) ?>  
        }, {
            name: 'Despesas',
            data: <?= json_encode($despesas) ?>
        }],
        xaxis: {
            categories: <?= json_encode($interval === 'diario' 
                ? array_map(function($i) { return "Dia " . ($i + 1); }, range(0, 29)) 
                : ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro']) 
            ?>
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
</script>

    </div>
</body>
</html>