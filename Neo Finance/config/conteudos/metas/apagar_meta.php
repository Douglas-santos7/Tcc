<?php
// APAGAR META
if (isset($_POST['id_meta']) && !isset($_POST['valor_deposito']) && !isset($_POST['valor_resgatar'])) {
    $id_meta = $_POST['id_meta'];

    // Preparar a query SQL para deletar a meta
    $sql = "DELETE FROM metas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_meta);

    // Executar a query
    if ($stmt->execute()) {
      // Redirecionar para a página de metas após apagar a meta
      header("Location: ../conteudos/(7) metas.php?sucesso=2");
      exit();
    } else {
      echo "Erro ao remover meta: " . $conn->error;
    }
  }
?>