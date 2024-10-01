<?php
// Arquivo: categorias.php

include('../../config/database/conexao.php');

// Função para buscar as categorias do usuário logado
function buscarCategorias($usuarioId, $conn)
{
    $query = "SELECT nome FROM categorias WHERE usuario_id = $usuarioId";
    $result = mysqli_query($conn, $query);
    $categorias = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categorias[] = $row['nome'];
        }
    } else {
        echo "Erro ao buscar categorias.";
    }

    return $categorias;
}
