<?php
session_start();
include '../../database/conexao.php'; // Conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado']);
    exit();
}

$user_id = $_SESSION['user_id']; // Obtém o ID do usuário logado
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['nome']) && isset($input['icone'])) { // Verifica se os dados necessários estão presentes
    $nome = $input['nome'];
    $icone = $input['icone']; // Inclui a coleta do ícone

    // Verifica se a categoria já existe para o usuário
    $sql = "SELECT id FROM categorias WHERE nome = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nome, $user_id); // Inclui o usuário na verificação
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Categoria já existe para este usuário']);
        exit();
    }

    // Insere a nova categoria vinculada ao usuário
    $sql = "INSERT INTO categorias (nome, icone, usuario_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nome, $icone, $user_id); // Insere o user_id junto com os outros dados

    if ($stmt->execute()) {
        $id = $stmt->insert_id; // ID da nova categoria
        echo json_encode(['status' => 'success', 'id' => $id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar categoria']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
}
?>