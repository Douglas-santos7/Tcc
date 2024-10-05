<?php
// Iniciar a sessão
session_start();

// Incluir a conexão com o banco de dados
include('../../database/conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../views/login/login.php");
    exit();
}

// Pega o ID da categoria a ser excluída
$id_categoria = $_POST['id_categoria'];

// Prepare a consulta para excluir a categoria
$query = "DELETE FROM categorias WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($query);

// O ID do usuário logado deve ser validado para garantir que ele só possa excluir suas próprias categorias
$usuario_id = $_SESSION['user_id'];
$stmt->bind_param("ii", $id_categoria, $usuario_id);

if ($stmt->execute()) {
    // Redirecionar de volta para a página de categorias
    header("Location: ../../../views/conteudos/(6) categorias.php?status=success");
} else {
    // Redirecionar de volta com um erro
    header("Location: ../../../views/conteudos/(6) categorias.php?status=error");
}
