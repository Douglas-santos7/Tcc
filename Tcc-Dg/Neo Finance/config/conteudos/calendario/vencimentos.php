<?php
// Arquivo: vencimentos.php
session_start();
include('../../config/database/conexao.php');
include('funcoes.php');

// Função para buscar os vencimentos do mês selecionado
function buscarVencimentos($mesSelecionado, $conn)
{
    $usuario_id = $_SESSION['user_id']; // Pega o ID do usuário logado

    // Prepara a consulta
    $query = "SELECT descricao, data_vencimento, valor, categoria, status, tipo_transacao FROM vencimentos 
              WHERE MONTH(data_vencimento) = ? AND status = 'Pendente' AND usuario_id = ? 
              ORDER BY data_vencimento ASC";

    // Usa prepared statements
    if ($stmt = mysqli_prepare($conn, $query)) {
        // Liga os parâmetros
        mysqli_stmt_bind_param($stmt, 'ii', $mesSelecionado, $usuario_id);

        // Executa a consulta
        mysqli_stmt_execute($stmt);

        // Obtém o resultado
        $result = mysqli_stmt_get_result($stmt);
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
                    'tipo_transacao' => $row['tipo_transacao'], // Inclui o tipo de transação
                    'dias_restantes' => $diasRestantes
                ];
            }
        } else {
            echo "Erro ao buscar os vencimentos: " . mysqli_error($conn);
        }

        // Fecha a declaração
        mysqli_stmt_close($stmt);
    } else {
        echo "Erro ao preparar a consulta: " . mysqli_error($conn);
    }

    return $vencimentos;
}


// Função para adicionar um novo vencimento
function adicionarVencimento($descricao, $data_vencimento, $valor, $categoria, $tipo_transacao, $conn)
{
    // Verifica se a conexão ($conn) é do tipo 'mysqli'
    if (!($conn instanceof mysqli)) {
        die('Erro na conexão com o banco de dados.');
    }

    // Monta a query SQL de inserção, agora incluindo o tipo de transação
    $query = "INSERT INTO vencimentos (descricao, data_vencimento, valor, categoria, status, usuario_id, tipo_transacao) 
              VALUES ('$descricao', '$data_vencimento', '$valor', '$categoria', 'Pendente', 1, '$tipo_transacao')";

    // Executa a query usando a conexão
    if (mysqli_query($conn, $query)) {
        echo "Vencimento adicionado com sucesso!";
    } else {
        echo "Erro: " . mysqli_error($conn);
    }
}
