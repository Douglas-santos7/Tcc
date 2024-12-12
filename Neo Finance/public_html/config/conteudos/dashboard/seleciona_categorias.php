<?php
include("../../config/database/conexao.php");

function obterCategorias($conn)
{
  // Seleciona id, nome e icone onde excluida é false
  $sql = "SELECT id, nome, icone FROM categorias WHERE excluida = FALSE";
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
    $categorias = []; // Mantendo a consistência como um array vazio
  }

  return $categorias;
}
