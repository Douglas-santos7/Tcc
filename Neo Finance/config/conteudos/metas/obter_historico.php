<?php
// Incluir a conexão com o banco de dados
include("../../config/database/conexao.php");

// Função para obter o histórico de depósitos de uma meta
function obterHistoricoDepositos($meta_id) {
    global $conn;
    $sql = "SELECT valor_atual, criada_em FROM metas WHERE id = ? ORDER BY criada_em DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $meta_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Verificar se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $meta_id = $_POST['meta_id'] ?? null;
    if ($meta_id) {
        $historico = obterHistoricoDepositos($meta_id);
        echo json_encode($historico);
    } else {
        echo json_encode([]);
    }
}
?>
