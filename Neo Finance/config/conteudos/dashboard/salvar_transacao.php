<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../../database/conexao.php'; // Conexão com o banco de dados

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não logado']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
error_log(print_r($input, true)); // Log para depuração

if (isset($input['tipo'], $input['categoria'], $input['nome'], $input['valor'], $input['icone'])) {
    $usuario_id = $_SESSION['user_id'];
    $tipo = $input['tipo'];

    // Certifique-se de que a categoria é um número
    $categoria_id = (int)$input['categoria'];
    if (!is_numeric($categoria_id) || $categoria_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Categoria inválida']);
        exit();
    }

    $nome = $input['nome'];
    $valor = $input['valor'];

    // Verifique se o valor é numérico e dentro de um limite específico
    if (!is_numeric($valor) || $valor < 0 || $valor > 1000000) {
        echo json_encode(['status' => 'error', 'message' => 'Valor inválido']);
        exit();
    }

    $icone = $input['icone']; // Obter o ícone do input

    // Query de inserção
    $sql = "INSERT INTO transacoes (usuario_id, tipo, categoria_id, nome, valor, icone) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao preparar a consulta: ' . $conn->error]);
        exit();
    }

    // Ajuste os tipos conforme necessário
    $stmt->bind_param("issssi", $usuario_id, $tipo, $categoria_id, $nome, $valor, $icone);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        error_log('Erro ao executar a consulta: ' . $stmt->error); // Log da mensagem de erro
        echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar transação: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
}

error_log('Entrou na função de salvamento');
