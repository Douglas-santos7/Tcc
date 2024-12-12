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
    $queryReceitas = "
    SELECT DAY(data) AS dia, SUM(valor) AS totalReceitas
    FROM transacoes 
    WHERE tipo = 'receita' AND usuario_id = $userId 
    AND MONTH(data) = MONTH(CURDATE()) AND YEAR(data) = YEAR(CURDATE())
    GROUP BY dia
    ORDER BY dia
    ";
    $queryDespesas = "
    SELECT DAY(data) AS dia, SUM(valor) AS totalDespesas
    FROM transacoes 
    WHERE tipo = 'despesa' AND usuario_id = $userId 
    AND MONTH(data) = MONTH(CURDATE()) AND YEAR(data) = YEAR(CURDATE())
    GROUP BY dia
    ORDER BY dia
    ";
} else {
    if ($selectedMonth) {
        // Se um mês específico for selecionado
        $queryReceitas = "
            SELECT DAY(data) AS dia, SUM(valor) AS totalReceitas
            FROM transacoes 
            WHERE tipo = 'receita' AND usuario_id = $userId 
            AND MONTH(data) = $selectedMonth AND YEAR(data) = YEAR(CURDATE())
            GROUP BY dia
            ORDER BY dia
        ";
        $queryDespesas = "
            SELECT DAY(data) AS dia, SUM(valor) AS totalDespesas
            FROM transacoes 
            WHERE tipo = 'despesa' AND usuario_id = $userId 
            AND MONTH(data) = $selectedMonth AND YEAR(data) = YEAR(CURDATE())
            GROUP BY dia
            ORDER BY dia
        ";
    } else {
        // Se todos os meses forem selecionados
        $queryReceitas = "
            SELECT MONTH(data) AS mes, SUM(valor) AS totalReceitas
            FROM transacoes 
            WHERE tipo = 'receita' AND usuario_id = $userId 
            AND YEAR(data) = YEAR(CURDATE())
            GROUP BY mes
            ORDER BY mes
        ";
        $queryDespesas = "
            SELECT MONTH(data) AS mes, SUM(valor) AS totalDespesas
            FROM transacoes 
            WHERE tipo = 'despesa' AND usuario_id = $userId 
            AND YEAR(data) = YEAR(CURDATE())
            GROUP BY mes
            ORDER BY mes
        ";
    }
}

$resultReceitas = mysqli_query($conn, $queryReceitas);
$resultDespesas = mysqli_query($conn, $queryDespesas);

// Extrair valores
if ($interval === 'diario') {
    // Inicializa os arrays para 31 dias (alguns meses tem 31 dias)
    $receitas = array_fill(0, 31, 0);
    $despesas = array_fill(0, 31, 0);

    // Preenche as receitas diárias
    while ($row = mysqli_fetch_assoc($resultReceitas)) {
        $dia = intval($row['dia']); // Pega o dia do mês
        $receitas[$dia - 1] = $row['totalReceitas']; // Preenche o valor correto no array
    }

    // Preenche as despesas diárias
    while ($row = mysqli_fetch_assoc($resultDespesas)) {
        $dia = intval($row['dia']); // Pega o dia do mês
        $despesas[$dia - 1] = $row['totalDespesas']; // Preenche o valor correto no array
    }
} else {
    // Inicializa os arrays para os dados mensais
    $receitas = array_fill(0, 12, 0);
    $despesas = array_fill(0, 12, 0);

    // Preenche as receitas mensais
    while ($row = mysqli_fetch_assoc($resultReceitas)) {
        $mes = intval($row['mes']) - 1; // Ajusta o índice do mês
        $receitas[$mes] = $row['totalReceitas']; // Preenche o valor correto no array
    }

    // Preenche as despesas mensais
    while ($row = mysqli_fetch_assoc($resultDespesas)) {
        $mes = intval($row['mes']) - 1; // Ajusta o índice do mês
        $despesas[$mes] = $row['totalDespesas']; // Preenche o valor correto no array
    }
}

// Consultar as despesas por categoria
$queryDespesasPorCategoria = "
    SELECT c.nome AS categoria, SUM(t.valor) AS total
    FROM transacoes t 
    JOIN categorias c ON t.categoria_id = c.id 
    WHERE t.tipo = 'despesa' AND t.usuario_id = $userId
    GROUP BY c.nome
    ORDER BY total DESC
";
$resultDespesasPorCategoria = mysqli_query($conn, $queryDespesasPorCategoria);

$categoriasDespesas = [];
$categoriaMaiorGasto = '';
$valorMaiorGasto = 0;
while ($row = mysqli_fetch_assoc($resultDespesasPorCategoria)) {
    $categoriasDespesas[] = $row;
    // Verifica a categoria com o maior gasto
    if ($row['total'] > $valorMaiorGasto) {
        $valorMaiorGasto = $row['total'];
        $categoriaMaiorGasto = $row['categoria'];
    }
}

// Calcular variação das receitas e despesas
$variacaoReceitas = [];
$variacaoDespesas = [];

for ($i = 1; $i < count($receitas); $i++) {
    $variacaoReceitas[] = $receitas[$i] - $receitas[$i - 1]; // Diferença das receitas
    $variacaoDespesas[] = $despesas[$i] - $despesas[$i - 1]; // Diferença das despesas
}

// Verificar tendências
$descricaoTendenciaReceitas = (end($variacaoReceitas) > 0) ? 'Tendência crescente' : 'Tendência decrescente';
$descricaoTendenciaDespesas = (end($variacaoDespesas) > 0) ? 'Tendência crescente' : 'Tendência decrescente';
// Fecha a conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Financeiro</title>
    <link rel="stylesheet" href="../../css/conteudos/grafico/grafico.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body>
    <div class="container--header">
        <header class="banner">
            <div class="titulo--banner">
                <img src="../../assets/icons/home--sidebar/graficos--icon.svg" alt="calendario--icon" />
                <h1>Gráficos</h1>
            </div>
     
        </header>
    </div>

    <div class="row">
        <div class="card chart-card">
            <h2 class="card-title">Visão Financeira - <?= ucfirst($interval) ?></h2>

            <!-- Botões dentro do cartão do gráfico -->
            <div class="button-container">
                <a href="?interval=mensal&month=<?= $selectedMonth ?>" class="button">Mensal</a>
                <a href="?interval=diario" class="button">Diário</a>
            </div>

            <div id="chart"></div>
        </div>

        <div class="card options-card">
            <h2 class="card-title">Análise dos Dados</h2>
            <div class="valores">
                <p>
                    <span>Receitas</span>
                    <span style="color: green;">R$<?= number_format(array_sum($receitas), 2, ',', '.') ?></span>
                </p>
                <p>
                    <span>Despesas</span>
                    <span style="color: red;">R$<?= number_format(array_sum($despesas), 2, ',', '.') ?></span>
                </p>
                <p>
                    <span>Balanço Total</span>
                    <span style="color: orange;">R$<?= number_format(array_sum($receitas) - array_sum($despesas), 2, ',', '.') ?></span>
                </p>
            </div>

            <a href="./(5) chatbot.php" id="chatbotLink">
                <div class="chat--neo" id="chatbotDiv">
                    <img src="../../assets/icons/home--sidebar/chatbot--icon.svg" alt="chat--icon" />
                    <p>Mais dúvidas? Fale com Neo</p>
                </div>
            </a>


            <h3>Dicas:</h3>
            <ul>
                <?php
                $totalReceitas = array_sum($receitas);
                $totalDespesas = array_sum($despesas);
                $porcentagemReceitas = ($totalReceitas > 0) ? (($totalReceitas - $totalDespesas) / $totalReceitas) * 100 : 0;
                $porcentagemDespesas = ($totalDespesas > 0) ? (($totalDespesas - $totalReceitas) / $totalDespesas) * 100 : 0;


                $limiteAltoReceitas = 5000;
                $limiteBaixoDespesas = 2000;

                // Condição se receitas são superiores às despesas
                if ($totalReceitas > $totalDespesas): ?>
                    <li>Ótimo trabalho! Suas receitas estão superando suas despesas em <?= number_format($porcentagemReceitas, 2) ?>%. Considere aumentar sua reserva ou investir.</li>
                    <li>Você está em uma boa situação financeira. Avalie se você pode aumentar suas contribuições para um fundo de emergência.</li>
                    <li>Considere diversificar seus investimentos para reduzir riscos.</li>
                    <?php if ($totalReceitas > $limiteAltoReceitas): ?>
                        <li>Com uma receita tão alta, considere investir em opções de renda fixa ou ações para potencializar seu capital.</li>
                    <?php endif; ?>
            </ul>
        <?php
                // Condição se despesas são superiores às receitas
                elseif ($totalDespesas > $totalReceitas): ?>
            <li>❌ Atenção! Suas despesas estão excedendo suas receitas em <?= number_format(abs($porcentagemDespesas), 2) ?>%. É fundamental tomar medidas para evitar problemas financeiros.</li>
            <li>Faça uma lista de suas despesas fixas e variáveis para identificar áreas onde você pode cortar gastos.</li>
            <li>Considere estabelecer um limite mensal para despesas variáveis.</li>
            <?php if ($totalDespesas > $limiteBaixoDespesas): ?>
            <?php endif; ?>
            <li>⚠️ A categoria onde você mais gasta é <span style="color: red; background: white; padding: 5px 10px; border-radius:24px;"><?= $categoriaMaiorGasto ?></span> com um total de R$<?= number_format($valorMaiorGasto, 2, ',', '.') ?>.</li>
            </ul>
        <?php
                // Condição se receitas e despesas estão equilibradas
                else: ?>
            <ul>
                <li>Suas receitas e despesas estão equilibradas, o que é um sinal positivo de controle financeiro!</li>
                <li>Continue monitorando suas despesas e considere criar um fundo de emergência para lidar com imprevistos.</li>
                <li>Pense em formas de aumentar suas receitas sem aumentar suas despesas.</li>
                <li>Considere investir em educação financeira para aprimorar suas habilidades.</li>
            </ul>
        <?php endif;
        ?>

        </ul>
        </div>

        <script>
            var options = {
                chart: {
                    type: 'area',
                    toolbar: {
                        show: false // Remove a toolbar
                    }
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
                                    ? array_map(function ($i) {
                                        return "Dia " . ($i + 1);
                                    }, range(0, 29))
                                    : ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'])
                                ?>
                },
                colors: ['#00E396', '#FF4560'],
                fill: {
                    colors: ['#00E396', '#FF4560'],
                    opacity: 0.5
                }

            };

            var chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();
        </script>

    </div>
</body>

</html>