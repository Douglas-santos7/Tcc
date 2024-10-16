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

// Adicione o código abaixo para garantir que o gráfico só mostre dados do mês selecionado
if ($interval === 'diario') {
    // Consulta para o dia atual
    $queryReceitas = "SELECT DATE(data) as dia, SUM(valor) AS totalReceitas 
                      FROM transacoes WHERE tipo = 'receita' AND usuario_id = $userId 
                      AND DATE(data) = CURDATE() GROUP BY dia ORDER BY dia";
    $queryDespesas = "SELECT DATE(data) as dia, SUM(valor) AS totalDespesas 
                      FROM transacoes WHERE tipo = 'despesa' AND usuario_id = $userId 
                      AND DATE(data) = CURDATE() GROUP BY dia ORDER BY dia";
} else {
    // Para o mês específico
    if ($selectedMonth) {
        $queryReceitas = "SELECT DAY(data) as dia, SUM(valor) AS totalReceitas 
                          FROM transacoes WHERE tipo = 'receita' AND usuario_id = $userId 
                          AND MONTH(data) = $selectedMonth AND YEAR(data) = YEAR(CURDATE()) 
                          GROUP BY dia ORDER BY dia";
        $queryDespesas = "SELECT DAY(data) as dia, SUM(valor) AS totalDespesas 
                          FROM transacoes WHERE tipo = 'despesa' AND usuario_id = $userId 
                          AND MONTH(data) = $selectedMonth AND YEAR(data) = YEAR(CURDATE()) 
                          GROUP BY dia ORDER BY dia";
    } else {
        // Se não for mês específico
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

$categoriaMaisCara = null;
$maiorDespesaCategoria = 0;
foreach ($categoriasDespesas as $categoria) {
    if ($categoria['total'] > $maiorDespesaCategoria) {
        $maiorDespesaCategoria = $categoria['total'];
        $categoriaMaisCara = $categoria['categoria'];
    }
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
    <link rel="stylesheet" href="../../css/conteudos/grafico/grafico.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body>
     <!-- Início do Header -->
  <div class="container--header">
    <header class="banner">
      <div class="titulo--banner">
        <img src="../../assets/icons/home--sidebar/graficos--icon.svg" alt="categoria--icon" />
        <h1>Gráfico</h1>
      </div>
    </header>
  </div>
    <div class="container">
        <div class="row">
            <!-- Gráfico e botões -->
            <div class="card chart-card">
                <h2 class="card-title">Visão Financeira - <?= ucfirst($interval) ?></h2>

                <div class="button-container">
    <a href="?interval=mensal&month=<?= $selectedMonth ?>" class="button">Mensal</a>
    <a href="?interval=diario" class="button">Diário</a>
</div>


                <div id="chart"></div>
            </div>

            <!-- Dicas e análise de dados -->
            <div class="card dicas-card">
                <h2 class="card-title">Análise e Dicas</h2>
                <p style="color: green;">Receitas: R$<?= number_format(array_sum($receitas), 2, ',', '.') ?></p>
                <p style="color: red;">Despesas: R$<?= number_format(array_sum($despesas), 2, ',', '.') ?></p>
                <h3>Dicas:</h3>
<ul>
    <?php
    $totalReceitas = array_sum($receitas);
    $totalDespesas = array_sum($despesas);
    $totalPoupanca = isset($totalPoupanca) ? $totalPoupanca : 0;
    $porcentagemReceitas = ($totalReceitas > 0) ? (($totalReceitas - $totalDespesas) / $totalReceitas) * 100 : 0;
    $porcentagemDespesas = ($totalDespesas > 0) ? (($totalDespesas - $totalReceitas) / $totalDespesas) * 100 : 0;

    // Verifica se as receitas estão superando as despesas
    if ($totalReceitas > $totalDespesas) {
        echo "<li style='color: green;'>Ótimo trabalho! Suas receitas estão superando suas despesas em " . number_format($porcentagemReceitas, 2) . "%.</li>";
        if ($totalReceitas > 5000) {
            echo "<li style='color: green;'>Com uma receita alta, avalie diversificar seus investimentos.</li>";
        }
        if ($totalDespesas < 30) {
            echo "<li style='color: red;'>Você está mantendo suas despesas extremamente baixas. Avalie se isso é sustentável a longo prazo.</li>";
        }
    } elseif ($totalDespesas > $totalReceitas) {
        echo "<li style='color: red;'>Atenção! Suas despesas excedem suas receitas em " . number_format(abs($porcentagemDespesas), 2) . "%.</li>";
        if ($totalDespesas > 2000) {
            echo "<li style='color: red;'>Suas despesas estão elevadas. Considere formas de reduzir gastos em categorias não essenciais.</li>";
        }
    } else {
        echo "<li>Suas receitas e despesas estão equilibradas, o que é um sinal positivo de controle financeiro!</li>";
    }

    // Analisar categorias de despesas
    if (!empty($categoriasDespesas)) {
        foreach ($categoriasDespesas as $categoria) {
            $porcentagemDespesaCategoria = ($totalDespesas > 0) ? ($categoria['total'] / $totalDespesas) * 100 : 0;
            
            // Se uma categoria representa mais de 20% das despesas totais, adicionar uma dica
            if ($porcentagemDespesaCategoria > 20) {
                echo "<li style='color: red;'>Você está gastando " . number_format($porcentagemDespesaCategoria, 2) . "% de suas despesas na categoria " . $categoria['categoria'] . ". Considere revisar esses gastos.</li>";
            }
            if ($porcentagemDespesaCategoria < 5 && $categoria['categoria'] == 'Educação') {
                echo "<li style='color: green;'>Considere investir mais na categoria Educação. Um pequeno aumento pode gerar grandes benefícios a longo prazo.</li>";
            }
        }
    }

    // Verificar poupança
    $porcentagemPoupanca = ($totalPoupanca > 0 && $totalReceitas > 0) ? ($totalPoupanca / $totalReceitas) * 100 : 0;
    if ($porcentagemPoupanca < 10) {
        echo "<li style='color: red;'>Você está economizando menos de 10% de suas receitas. Tente aumentar sua poupança para garantir uma segurança financeira.</li>";
    } elseif ($porcentagemPoupanca > 20) {
        echo "<li style='color: green;'>Ótimo trabalho! Você está economizando mais de 20% de suas receitas, o que é excelente para sua estabilidade financeira futura.</li>";
    }
    ?>
</ul>

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
