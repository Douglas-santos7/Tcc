<?php
// Incluir a conexão com o banco de dados
include('../../database/conexao.php');

// Verifica se foi passado o ID da categoria pela URL
if (!isset($_GET['id'])) {
    header("Location: ../../../views/conteudos/(6) categorias.php"); // Redireciona para a lista de categorias
    exit();
}

$categoria_id = $_GET['id'];
$usuario_id = 1; // Trocar para pegar o usuário logado corretamente

// Excluir a categoria
$query = "DELETE FROM categorias WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $categoria_id, $usuario_id);

if ($stmt->execute()) {
    header("Location: ../../../views/conteudos/(6) categorias.php"); // Redireciona para a lista de categorias
    exit();
} else {
    echo "Erro ao excluir a categoria.";
}
?>
