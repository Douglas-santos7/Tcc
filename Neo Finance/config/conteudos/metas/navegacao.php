<?php
/*=================
Lógica de Navegação
===================*/
// Variáveis para controle de navegação
$offset = 0;
$limit = 2;

// Verificar se foi clicado na setinha para avançar ou retroceder
if (isset($_GET['offset'])) {
  $offset = (int) $_GET['offset'];
}

// Buscar as metas do usuário, limitando a 2 e aplicando o offset
$sql = "SELECT * FROM metas WHERE usuario_id = 1 ORDER BY criada_em DESC LIMIT $limit OFFSET $offset"; // Ajuste para pegar o ID do usuário logado
$result = $conn->query($sql);

// Contar o total de metas do usuário
$totalSql = "SELECT COUNT(*) as total FROM metas WHERE usuario_id = 1"; // Ajuste para pegar o ID do usuário logado
$totalResult = $conn->query($totalSql);
$total = $totalResult->fetch_assoc()['total'];

// Calcular o próximo e anterior offsets
$nextOffset = $offset + $limit < $total ? $offset + $limit : null;
$prevOffset = $offset > 0 ? $offset - $limit : null;


?>