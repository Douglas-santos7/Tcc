<?php
include("../../config/database/conexao.php");

function consultarHistorico($conn, $userId)
{
  // Consultar histórico recente de transações
  $queryHistorico = "
      SELECT t.nome AS transacao_nome, t.valor, t.tipo, t.criado_em, c.id AS categoria_id, c.nome AS categoria_nome, t.icone
      FROM transacoes t
      JOIN categorias c ON t.categoria_id = c.id
      WHERE t.usuario_id = ?
      ORDER BY t.criado_em DESC
      LIMIT 5";

  // Preparando a consulta
  $stmt = mysqli_prepare($conn, $queryHistorico);

  // Ligando o parâmetro
  mysqli_stmt_bind_param($stmt, "i", $userId);

  // Executando a consulta
  mysqli_stmt_execute($stmt);

  // Obtendo o resultado
  $resultHistorico = mysqli_stmt_get_result($stmt);

  // Inicializar uma variável para armazenar os itens do histórico
  $historicoItems = ""; // Inicializar a variável para armazenar o HTML do histórico

  if (mysqli_num_rows($resultHistorico) > 0) {
    while ($row = mysqli_fetch_assoc($resultHistorico)) {
      // Use a string de ícone armazenada na tabela como classe
      $tipoIcon = htmlspecialchars($row['icone']); // Pega a string do ícone

      // Concatena cada item ao histórico, do mais recente ao mais antigo
      $historicoItems .= '<li>
              <div class="parte--um-info">
                  <div class="img--categoria">
                      <i class="' . $tipoIcon . '"></i> <!-- Aqui adiciona o ícone como classe -->
                  </div>
                  <div class="info--detalhada">
                      <span class="nome--historico">' . htmlspecialchars($row['transacao_nome']) . '</span> <!-- Exibe o nome -->
                      <span class="categoria--historico">' . htmlspecialchars($row['categoria_nome']) . '</span> <!-- Exibe o nome da categoria -->
                  </div>
              </div>
              <div class="parte--dois-info">
                  <span class="data--historico">' . date('d/m/Y', strtotime($row['criado_em'])) . '</span> <!-- Exibe a data -->
                  <span class="valor--historico" style="color: ' . ($row['tipo'] === 'Receita' ? 'green' : 'red') . ';">
                      R$ ' . number_format($row['valor'], 2, ',', '.') . ' <!-- Exibe o valor -->
                  </span>
              </div>
          </li>';
    }
  } else {
    // Caso não haja transações recentes
    $historicoItems .= '<li>Nenhuma transação recente encontrada.</li>';
  }

  // Fechando a declaração
  mysqli_stmt_close($stmt);

  // Retorna o HTML contendo o histórico de transações
  return $historicoItems;
}
