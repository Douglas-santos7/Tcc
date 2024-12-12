<?php
// Incluir a conexão com o banco de dados
include('../../database/conexao.php');

// Verifica se o método de requisição é POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pega os dados do formulário
    $id_categoria = $_POST['id_categoria'];
    $nome = $_POST['nome'];
    $icone = isset($_POST['icone']) ? $_POST['icone'] : null; // Verifica se o ícone foi selecionado

    // Prepara a consulta
    if ($icone) {
        // Atualiza a categoria incluindo o ícone
        $query = "UPDATE categorias SET nome = ?, icone = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $nome, $icone, $id_categoria);
    } else {
        // Atualiza a categoria sem alterar o ícone
        $query = "UPDATE categorias SET nome = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $nome, $id_categoria);
    }

    if ($stmt->execute()) {
        // Redirecionar ou exibir mensagem de sucesso
        header("Location: ../../../views/conteudos/(6) categorias.php?msg=Categoria editada com sucesso!");
        exit();
    } else {
        // Redirecionar ou exibir mensagem de erro
        header("Location: ../../../views/conteudos/(6) categorias.php?msg=Erro ao editar categoria.");
        exit();
    }
} else {
    // Redirecionar se não for um POST
    header("Location: ../../../views/conteudos/(6) categorias.php");
    exit();
}
