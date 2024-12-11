<?php
/* ================================
ENVIO DE FORMULARIO DA META CRIADA
===================================*/
// Verifique se o formulário foi enviado para criação de meta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['goal_name']) && isset($_POST['target_amount']) && isset($_POST['deadline'])) {
    $goalName = $_POST['goal_name'];
    $targetAmount = $_POST['target_amount'];
    $deadline = $_POST['deadline'];

    // Converter o valor alvo de volta para um número decimal
    $targetAmount = str_replace(',', '.', str_replace('.', '', $targetAmount));

    // Inserção de dados na tabela 'metas_usuario'
    $sql = "INSERT INTO metas_usuario (id_usuario, nome_meta, valor_alvo, prazo) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isds", $userId, $goalName, $targetAmount, $deadline);

    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }
    $stmt->close();
}
?>
