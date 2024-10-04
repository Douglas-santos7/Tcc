<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conecte-se ao banco de dados
    include '../../database/conexao.php'; // Inclua o arquivo de conexão

    // Recebe o ID da categoria
    $categoriaId = $_POST['id'];

    // Executa o comando DELETE no banco de dados
    $sql = "DELETE FROM categorias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoriaId);
    
    if ($stmt->execute()) {
        echo "Categoria excluída com sucesso!";
    } else {
        echo "Erro ao excluir a categoria: " . $conn->error;
    }

    // Fecha a conexão
    $stmt->close();
    $conn->close();
}
?>
