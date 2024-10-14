<?php
include("../../config/database/conexao.php");

function enviarDados($conn, $userId)
{
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $valor = mysqli_real_escape_string($conn, $_POST['valor']);
    $categoria = mysqli_real_escape_string($conn, $_POST['categoria']);
    $tipo = mysqli_real_escape_string($conn, $_POST['tipo']);

    // Consultar o ícone da categoria selecionada
    $queryIcone = "SELECT icone FROM categorias WHERE id = ?";
    $stmtIcone = mysqli_prepare($conn, $queryIcone);
    mysqli_stmt_bind_param($stmtIcone, "i", $categoria);
    mysqli_stmt_execute($stmtIcone);
    mysqli_stmt_bind_result($stmtIcone, $icone);
    mysqli_stmt_fetch($stmtIcone);
    mysqli_stmt_close($stmtIcone);

    // Formata o valor recebido
    $valor = str_replace('.', '', $valor); // Remove pontos que representam milhar
    $valor = str_replace(',', '.', $valor); // Converte a vírgula para ponto

    // Insere os dados na tabela 'transacoes'
    $sql = "INSERT INTO transacoes (nome, valor, categoria_id, tipo, usuario_id, icone)
            VALUES ('$nome', '$valor', '$categoria', '$tipo', $userId, '$icone')";

    // Atualiza a página após inserção
    if (mysqli_query($conn, $sql)) {
      // Redireciona para a mesma página após a inserção
      header("Location: " . $_SERVER['PHP_SELF']);
      exit(); // Saia para garantir que o script pare aqui
    } else {
      echo "<script>alert('Erro ao salvar: " . mysqli_error($conn) . "');</script>";
    }
  }
}
