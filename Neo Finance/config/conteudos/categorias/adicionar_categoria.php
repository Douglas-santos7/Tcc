<?php
include '../../database/conexao.php';

// Captura o corpo da requisição JSON
$data = json_decode(file_get_contents("php://input"), true);

// Verifica se os campos obrigatórios estão definidos
if (isset($data['nome'], $data['icone'], $data['usuario_id'])) {
    $nome = $data['nome'];
    $icone = $data['icone'];
    $usuario_id = $data['usuario_id']; // Captura o usuario_id

    // Prepara a instrução SQL
    $stmt = $conn->prepare("INSERT INTO categorias (usuario_id, nome, icone) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $usuario_id, $nome, $icone); // Bindando usuario_id como inteiro e nome, icone como strings

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Categoria adicionada com sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao adicionar categoria: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Nome, ícone ou usuário não definidos.']);
}

$conn->close();
?>
