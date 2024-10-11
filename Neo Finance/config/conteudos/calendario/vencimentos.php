<?php
// Arquivo: vencimentos.php
session_start();
include('../../config/database/conexao.php');
include('funcoes.php');

// Função para buscar os vencimentos do mês selecionado
function buscarVencimentos($mesSelecionado, $conn)
{

    $usuario_id = $_SESSION['user_id']; // Pega o ID do usuário logado

    $query = "SELECT * FROM vencimentos WHERE MONTH(data_vencimento) = '$mesSelecionado' AND status = 'Pendente' AND usuario_id = $usuario_id ORDER BY data_vencimento ASC";
    $result = mysqli_query($conn, $query);
    $vencimentos = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $diasRestantes = calcularDiasRestantes($row['data_vencimento']);
            $vencimentos[] = [
                'descricao' => $row['descricao'],
                'data_vencimento' => $row['data_vencimento'],
                'valor' => $row['valor'],
                'categoria' => $row['categoria'],
                'status' => $row['status'],
                'dias_restantes' => $diasRestantes
            ];
        }
    } else {
        echo "Erro ao buscar os vencimentos.";
    }

    return $vencimentos;
}

// Função para adicionar um novo vencimento
function adicionarVencimento($descricao, $data_vencimento, $valor, $categoria, $conn)
{
    $usuario_id = $_SESSION['user_id']; // Pega o ID do usuário logado
    $status = 'Pendente'; // Status padrão

    $query = "INSERT INTO vencimentos (descricao, data_vencimento, valor, categoria, status, usuario_id) 
              VALUES ('$descricao', '$data_vencimento', '$valor', '$categoria', '$status', $usuario_id)";

    if (mysqli_query($conn, $query)) {
        return "Vencimento adicionado com sucesso.";
    } else {
        return "Erro ao adicionar vencimento: " . mysqli_error($conn);
    }
}
