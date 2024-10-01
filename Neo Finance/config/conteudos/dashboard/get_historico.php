<?php
session_start();
include '../../database/conexao.php';

$userId = $_SESSION['user_id']; // ID do usuário logado

// Consultar os últimos lançamentos com categoria e ícone
$queryHistorico = "
    SELECT t.tipo, t.nome, t.valor, t.data, c.nome AS categoria, c.icone
    FROM transacoes t
    JOIN categorias c ON t.categoria_id = c.id
    WHERE t.usuario_id = $userId
    ORDER BY t.data DESC
    LIMIT 5
";

$resultHistorico = mysqli_query($conn, $queryHistorico);

$historico = [];
while ($row = mysqli_fetch_assoc($resultHistorico)) {
    $historico[] = [
        'tipo' => $row['tipo'],
        'nome' => $row['nome'],
        'valor' => $row['valor'],
        'data' => $row['data'],
        'categoria' => $row['categoria'],
        'icone' => $row['icone']
    ];
}

// Retornar os dados como JSON
header('Content-Type: application/json');
echo json_encode($historico);
