<?php
function consultarProximoVencimento($conn, $userId)
{
    // Consultar o próximo vencimento a partir de hoje
    $queryVencimentos = "SELECT descricao, data_vencimento, valor, categoria
FROM vencimentos
WHERE usuario_id = $userId
AND status = 'Pendente'
AND data_vencimento >= CURDATE()
ORDER BY data_vencimento ASC
LIMIT 1;";

    $resultVencimentos = mysqli_query($conn, $queryVencimentos);

    if ($resultVencimentos) {
        return mysqli_fetch_assoc($resultVencimentos);
    } else {
        echo "Erro ao consultar os vencimentos: " . mysqli_error($conn);
        return null; // Retorna null em caso de erro
    }
}
