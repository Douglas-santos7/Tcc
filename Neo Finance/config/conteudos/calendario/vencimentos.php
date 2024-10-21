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
    ORDER BY status DESC";

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

    // Monta a query SQL de inserção, agora incluindo o tipo de transação
    $query = "INSERT INTO vencimentos (descricao, data_vencimento, valor, categoria, status, usuario_id, tipo_transacao)
              VALUES (?, ?, ?, ?, 'Pendente', ?, ?)";

    // Usa prepared statements
    if ($stmt = mysqli_prepare($conn, $query)) {
        // Liga os parâmetros
        mysqli_stmt_bind_param($stmt, 'ssdsss', $descricao, $data_vencimento, $valor, $categoria, $_SESSION['user_id'], $tipo_transacao);

        // Executa a consulta
        if (mysqli_stmt_execute($stmt)) {
            echo "Vencimento adicionado com sucesso!";
        } else {
            echo "Erro: " . mysqli_error($conn);
        }

        // Fecha a declaração
        mysqli_stmt_close($stmt);
    } else {
        echo "Erro ao preparar a consulta: " . mysqli_error($conn);
    }
}

// Função para confirmar o pagamento de um vencimento
function confirmarPagamento($vencimento_id, $conn)
{
    // Busca os detalhes do vencimento
    $stmt = $conn->prepare("SELECT v.*, c.id AS categoria_id, c.icone
                            FROM vencimentos v
                            JOIN categorias c ON v.categoria = c.nome
                            WHERE v.id = ?");
    $stmt->bind_param("i", $vencimento_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vencimento = $result->fetch_assoc();

    if ($vencimento) {
        // Insere a transação na tabela de transações
        $stmt = $conn->prepare("INSERT INTO transacoes (usuario_id, tipo, categoria_id, nome, valor, data, criado_em, icone) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $usuario_id = $vencimento['usuario_id'];
        $tipo_transacao = $vencimento['tipo_transacao'];
        $categoria_id = $vencimento['categoria_id'];
        $nome = $vencimento['descricao'];
        $valor = $vencimento['valor']; // Valor decimal
        $data = $vencimento['data_vencimento'];
        $criado_em = date('Y-m-d H:i:s');
        $icone = $vencimento['icone'];

        // O tipo do parâmetro 'valor' deve ser 'd' (decimal), não 's' (string)
        $stmt->bind_param("isssdsss", $usuario_id, $tipo_transacao, $categoria_id, $nome, $valor, $data, $criado_em, $icone);
        $stmt->execute();

        return true;
    } else {
        return false;
    }
}



// Verifica se o formulário de confirmação de pagamento foi submetido
if (isset($_POST['confirmarPagamento'])) {
    $vencimento_id = $_POST['vencimento_id'];

    // Chama a função para confirmar o pagamento
    if (confirmarPagamento($vencimento_id, $conn)) {
        // Exibe o modal de sucesso
        echo "<script>document.getElementById('modalSucesso').style.display = 'flex';</script>";
    } else {
        echo "<script>alert('Erro ao confirmar o pagamento.');</script>";
    }
}
