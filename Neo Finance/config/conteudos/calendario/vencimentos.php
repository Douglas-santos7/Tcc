<?php
session_start();
include('../../config/database/conexao.php');
include('funcoes.php');

// Função para buscar os vencimentos do mês selecionado
function buscarVencimentos($mesSelecionado, $conn)
{
    $usuario_id = $_SESSION['user_id']; // Pega o ID do usuário logado

    // Prepara a consulta
    $query = "SELECT v.id, v.descricao, v.data_vencimento, v.valor, v.categoria, v.status, v.tipo_transacao, c.id AS categoria_id, c.icone
    FROM vencimentos v
    JOIN categorias c ON v.categoria = c.nome
    WHERE MONTH(v.data_vencimento) = ? AND v.usuario_id = ?
    GROUP BY v.id
    ORDER BY v.id DESC";

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
                    'id' => $row['id'],
                    'descricao' => $row['descricao'],
                    'data_vencimento' => $row['data_vencimento'],
                    'valor' => $row['valor'],
                    'categoria' => $row['categoria'],
                    'categoria_id' => $row['categoria_id'],
                    'icone' => $row['icone'],
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

    // Monta a query SQL de inserção
    $query = "INSERT INTO vencimentos (descricao, data_vencimento, valor, categoria, status, usuario_id, tipo_transacao)
              VALUES (?, ?, ?, ?, 'Pendente', ?, ?)";

    // Usa prepared statements
    if ($stmt = mysqli_prepare($conn, $query)) {
        // Liga os parâmetros
        mysqli_stmt_bind_param($stmt, 'ssdsss', $descricao, $data_vencimento, $valor, $categoria, $_SESSION['user_id'], $tipo_transacao);

        // Executa a consulta
        if (mysqli_stmt_execute($stmt)) {
            // Fecha a declaração
            mysqli_stmt_close($stmt);
            return true; // Sucesso
        } else {
            mysqli_stmt_close($stmt);
            return false; // Falha
        }
    } else {
        return false; // Falha na preparação
    }
}


function confirmarPagamento($conn, $vencimentoId)
{
  // Inicia uma transação
  mysqli_begin_transaction($conn);

  // Busca os detalhes do vencimento
  $stmt = $conn->prepare("SELECT v.*, c.id AS categoria_id, c.icone
                            FROM vencimentos v
                            JOIN categorias c ON v.categoria = c.nome
                            WHERE v.id = ?");
  $stmt->bind_param("i", $vencimentoId);
  $stmt->execute();
  $vencimento = $stmt->get_result()->fetch_assoc();

  if ($vencimento) {
    // Insere a transação na tabela de transações
    $stmt = $conn->prepare("INSERT INTO transacoes (usuario_id, tipo, categoria_id, nome, valor, data, criado_em, icone)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $usuario_id = $vencimento['usuario_id']; // Certifique-se que esta chave está correta
    $tipo_transacao = $vencimento['tipo_transacao']; // Certifique-se que esta chave está correta
    $categoria_id = $vencimento['categoria_id']; // Certifique-se que esta chave está correta
    $nome = $vencimento['descricao'];
    $valor = $vencimento['valor'];
    $data = $vencimento['data_vencimento'];
    $criado_em = date('Y-m-d H:i:s');
    $icone = $vencimento['icone']; // Certifique-se que esta chave está correta

    // Use 'isssssss' se estiver vinculando 8 parâmetros
    $stmt->bind_param("isssssss", $usuario_id, $tipo_transacao, $categoria_id, $nome, $valor, $data, $criado_em, $icone);

    if ($stmt->execute()) {
      // Marca o vencimento como pago
      $stmt = $conn->prepare("UPDATE vencimentos SET status = 'Pago' WHERE id = ?");
      $stmt->bind_param("i", $vencimentoId);
      $stmt->execute();

      // Confirma a transação
      mysqli_commit($conn);
      return true;
    } else {
      // Se a inserção falhar, reverte a transação
      mysqli_rollback($conn);
      return false;
    }
  }

  // Se o vencimento não existir, reverte a transação
  mysqli_rollback($conn);
  return false;
}

