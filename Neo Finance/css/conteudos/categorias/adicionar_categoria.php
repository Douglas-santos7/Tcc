<?php
include '../../../config/database/conexao.php'; // Inclui a conexão com o banco de dados

// Lê o conteúdo da requisição
$data = json_decode(file_get_contents('php://input'), true);

// Verifica se os dados necessários foram fornecidos
if (!isset($data['nome'], $data['icone'], $data['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Nome, ícone ou usuário não definidos.']);
    exit();
}

// Captura os dados
$nome = $data['nome'];
$icone = $data['icone'];
$usuario_id = $data['usuario_id'];

// Prepara a consulta para inserir a nova categoria
$sql = "INSERT INTO categorias (usuario_id, nome, icone) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Erro na preparação da consulta: ' . $conn->error]);
    exit();
}

// Vincula os parâmetros
$stmt->bind_param('iss', $usuario_id, $nome, $icone);

// Executa a consulta
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Categoria adicionada com sucesso!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao adicionar categoria: ' . $stmt->error]);
}

// Fecha a conexão
$stmt->close();
$conn->close();
