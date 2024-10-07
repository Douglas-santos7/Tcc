<?php
function consultarProximoVencimento($conn, $userId) {
    // Consultar o próximo vencimento a partir de hoje
    $queryVencimentos = "SELECT v.descricao, v.data_vencimento, v.valor, c.nome AS categoria 
                         FROM vencimentos v 
                         JOIN categorias c ON v.categoria = c.id 
                         WHERE v.usuario_id = $userId 
                         AND v.status = 'Pendente' 
                         AND v.data_vencimento >= CURDATE() 
                         ORDER BY v.data_vencimento ASC 
                         LIMIT 1";
                         
    $resultVencimentos = mysqli_query($conn, $queryVencimentos);
    
    if ($resultVencimentos) {
        return mysqli_fetch_assoc($resultVencimentos);
    } else {
        echo "Erro ao consultar os vencimentos: " . mysqli_error($conn);
        return null; // Retorna null em caso de erro
    }
}
?>
