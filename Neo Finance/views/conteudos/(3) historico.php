<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

include '../../config/database/conexao.php';
include '../../config/conteudos/dashboard/logica_calendario.php';

$user_id = $_SESSION['user_id'];

// Inicializa as variáveis de filtragem usando $_GET
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Construir a query básica
$query = "SELECT t.id, t.nome AS descricao, c.nome AS categoria, c.icone AS icone, t.valor, t.data, t.tipo , t.criado_em
          FROM transacoes t 
          JOIN categorias c ON t.categoria_id = c.id 
          WHERE t.usuario_id = ?";

// Adiciona filtros conforme os dados inseridos
$params = [$user_id]; // Inicializa o array de parâmetros

if ($startDate && $endDate) {
    // Ajusta a data de fim para incluir todo o dia
    $endDate = date('Y-m-d', strtotime($endDate . ' +1 day')); // Adiciona um dia
    $query .= " AND t.data BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
}

// Adiciona a ordenação para do mais recente para o mais antigo
$query .= " ORDER BY t.criado_em DESC";

// Prepara e executa a consulta
$stmt = $conn->prepare($query);
$stmt->bind_param(str_repeat('s', count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();
$formStartDate = isset($startDate) ? htmlspecialchars($startDate) : '';
$formEndDate = isset($endDate) ? htmlspecialchars($endDate) : '';

if (!$result) {
    echo "Erro ao buscar transações: " . mysqli_error($conn);
}

if ($startDate && $endDate) {
    // Ajusta a data de fim para incluir todo o dia
    $endDate = date('Y-m-d', strtotime($endDate . ' +1 day')); // Adiciona um dia

    // Adiciona a opção de filtrar por mês, se o usuário marcou a caixa de seleção
    $filterByMonth = isset($_GET['filter_by_month']) ? 1 : 0;
    if ($filterByMonth) {
        $query .= " AND MONTH(t.data) BETWEEN MONTH(?) AND MONTH(?)";
        $params[] = $startDate;
        $params[] = $endDate;
    } else {
        $query .= " AND t.data BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Neo Finance - Histórico</title>
    <link rel="stylesheet" href="../../css/conteudos/historico/historico.css">
</head>

<body>
    <div class="containter">
        <div class="container--header">
            <header class="banner">
                <div class="titulo--banner">
                    <h1>Histórico</h1>
                </div>
                <div class="notificacao--usuario">
                    <img src="../../assets/icons/sino--icon.svg" alt="icon-notificacao" />
                </div>
            </header>
        </div>
        <div class="filtro--periodo">
            <form action="" method="GET">
                <label for="start_date">De:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>" />

                <label for="end_date">Até:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>" />

                <button type="submit">Filtrar</button>
            </form>
        </div>
        <div class="conteudo--scroll">
            <div class="container--conteudo">
                <?php
                if ($result->num_rows > 0) {
                    $currentMonth = '';

                    while ($row = $result->fetch_assoc()) {
                        $month = date('F Y', strtotime($row['data']));

                        if ($month !== $currentMonth) {
                            if ($currentMonth !== '') {
                                echo '</div>'; // Fecha a div do mês anterior, se houver
                            }

                            echo '<div class="month--historico">';
                            echo '<h2>' . mesEmPortugues($row['data']) . ' de ' . date('Y', strtotime($row['data'])) . '</h2>';
                            echo '<div class="card--historico-group">';
                        }

                        // Define a cor do valor com base no tipo
                        $valorCor = (strtolower($row['tipo']) === 'receita') ? 'green' : 'red'; // Verde para receita, vermelho para despesa
                        echo '<div class="card--historico">';
                        echo '    <div class="parte1--card">';
                        echo '        <div class="icone--card">';
                        // Exibe o ícone da categoria como uma classe
                        echo '            <i class="' . htmlspecialchars($row['icone']) . '" aria-hidden="true"></i>'; // Aqui, a classe do ícone é usada
                        echo '        </div>';
                        echo '        <div class="descricao--card">';
                        echo '            <div class="descricao--nome">';
                        echo '                <span>' . htmlspecialchars($row['descricao']) . '</span>';
                        echo '            </div>';
                        echo '            <div class="descricao--categoria">';
                        echo '                <span>' . htmlspecialchars($row['categoria']) . '</span>';
                        echo '            </div>';
                        echo '        </div>';
                        echo '    </div>';
                        echo '    <div class="data-valor--card">';
                        echo '        <div class="data--card">';
                        echo '            <span>' . date('d/m/Y', strtotime($row['data'])) . '</span>';
                        echo '        </div>';
                        echo '        <div class="valor--card" style="color:' . $valorCor . ';">'; // Aplica a cor do valor
                        echo '            <span>R$ ' . number_format($row['valor'], 2, ',', '.') . '</span>';
                        echo '        </div>';
                        echo '    </div>';
                        echo '</div>';

                        $currentMonth = $month;
                    }

                    if ($currentMonth !== '') {
                        echo '</div>'; // Fecha a div do grupo de cards do último mês
                        echo '</div>'; // Fecha a div do último mês
                    }
                } else {
                    echo "<div>Nenhum lançamento encontrado.</div>";
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>