<?php
include("../../config/database/conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $valor = $_POST['valor'];
    $data = $_POST['data'];
    $usuario_id = $_POST['usuario_id']; // Ajuste para pegar o ID correto do usuário logado

    // SQL para inserir a nova meta no banco de dados
    $sql = "INSERT INTO metas (nome_meta, valor_meta, data_meta, usuario_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nome, $valor, $data, $usuario_id);

    if ($stmt->execute()) {
        // Redireciona para a página de metas após o sucesso
        header("Location: metas.php?success=1");
        exit();
    } else {
        echo "Erro ao adicionar meta: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
