<?php
include("../../config/database/conexao.php");

function obterCategorias($conn) {
  $sql = "SELECT id, nome, icone FROM categorias"; // Seleciona id, nome e icone
  $result = $conn->query($sql);

  // Criando um array para armazenar as categorias
  $categorias = [];

  // Verifica se encontrou resultados
  if ($result->num_rows > 0) {
    // Itera pelos resultados e armazena as categorias em um array
    while ($row = $result->fetch_assoc()) {
      $categorias[] = $row;
    }
  } else {
    // Caso não existam categorias, o array será vazio
    $categorias = null; // Ou pode deixar como [] para manter a consistência
  }

  return $categorias;
}
?>
