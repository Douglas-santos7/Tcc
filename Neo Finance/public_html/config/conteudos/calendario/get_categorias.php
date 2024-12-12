<?php
// Arquivo: categorias.php

include('../../config/database/conexao.php');

// Função para buscar as categorias do usuário logado
function buscarCategorias($usuarioId, $conn)
{
    // Prepara a consulta para evitar SQL Injection
    $query = "SELECT nome FROM categorias WHERE usuario_id = ? AND excluida =  false";
    $stmt = mysqli_prepare($conn, $query);

    // Bind the parameter
    mysqli_stmt_bind_param($stmt, 'i', $usuarioId);

    // Executa a consulta
    mysqli_stmt_execute($stmt);

    // Armazena o resultado
    $result = mysqli_stmt_get_result($stmt);
    $categorias = [];

    // Busca as categorias
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categorias[] = $row['nome'];
        }
    } else {
        echo "Erro ao buscar categorias: " . mysqli_error($conn);
    }

    // Fecha a instrução
    mysqli_stmt_close($stmt);

    return $categorias;
}
