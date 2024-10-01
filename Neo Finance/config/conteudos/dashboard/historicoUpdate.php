<?php
// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "finance";

include '../../database/conexao.php'; // Conexão com o banco de dados

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Consulta SQL para buscar as 5 transações mais recentes
$sql = "SELECT t.tipo, c.nome AS categoria, t.nome, t.valor, t.data, c.icone
        FROM transacoes t
        JOIN categorias c ON t.categoria_id = c.id
        ORDER BY t.data DESC
        LIMIT 5";

$result = $conn->query($sql);

$historico = array();

// Verificar se a consulta retornou resultados
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $historico[] = $row;
    }
} else {
    // Nenhum dado foi encontrado
    error_log("Nenhuma transação encontrada.");
}

// Definir o cabeçalho como JSON e retornar os resultados
header('Content-Type: application/json');
echo json_encode($historico);

$conn->close();
