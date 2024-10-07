<?php
session_start();
include '../../database/conexao.php'; // Conexão com o banco de dados

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não logado']);
    exit();
}

// Busca as categorias do banco de dados
$sql = "SELECT id, nome FROM categorias";
$result = $conn->query($sql);

$categorias = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row;
    }
    echo json_encode(['status' => 'success', 'categorias' => $categorias]);
} else {
    echo json_encode(['status' => 'success', 'categorias' => []]); // Retorna uma lista vazia se não houver categorias
}
?>