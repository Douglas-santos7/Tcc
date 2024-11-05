<?php
// Inclui os arquivos necessários para a conexão com o banco de dados e outras funcionalidades
include('../../config/database/conexao.php');
include('../../config/conteudos/calendario/vencimentos.php');
include('../../config/conteudos/calendario/get_categorias.php');

// Obtém o mês selecionado pelo usuário
$mesSelecionado = isset($_POST['mes']) ? $_POST['mes'] : date('m');

// Verifica se o formulário de adição de vencimento foi submetido
if (isset($_POST['adicionarVencimento'])) {
  $descricao = $_POST['descricao'];
  $data_vencimento = $_POST['data_vencimento'];
  $valor = str_replace('.', '', $_POST['valor']);
  $valor = str_replace(',', '.', $valor);
  $categoria = $_POST['categoria'];
  $tipo_transacao = isset($_POST['tipo_transacao']) ? $_POST['tipo_transacao'] : null;

  // Verifica se o tipo_transacao foi definido
  if ($tipo_transacao === null) {
    echo "Por favor, selecione o tipo de transação.";
  } else {
    $vencimentoAdicionado = adicionarVencimento($descricao, $data_vencimento, $valor, $categoria, $tipo_transacao, $conn);
    echo "<script>
              window.onload = function() {
                  var modalSucesso = document.getElementById('modalSucesso');
                  if (modalSucesso) {
                      modalSucesso.style.display = 'flex';
                  } else {
                      console.log('Modal Sucesso não encontrado');
                  }
              };
            </script>";
  }
}


if (isset($_POST['confirmarPagamento'])) {
  $vencimento_id = $_POST['vencimento_id'];

  // Chama a função para confirmar o pagamento
  if (confirmarPagamento($vencimento_id, $conn)) {
    // Exibe o modal de sucesso
    echo "<script>
              window.onload = function() {
                  var modalSucessoPagamento = document.getElementById('modalSucessoPagamento');
                  if (modalSucessoPagamento) {
                      modalSucessoPagamento.style.display = 'flex';
                  } else {
                      console.log('Modal Sucesso do Pagamento não encontrado');
                  }
              };
            </script>";
  } else {
    echo "<script>alert('Erro ao confirmar o pagamento.');</script>";
  }
}

// Busca os vencimentos do mês selecionado
$vencimentos = buscarVencimentos($mesSelecionado, $conn);

// Busca as categorias do usuário
$usuario_id = $_SESSION['user_id'];
$categorias = buscarCategorias($usuario_id, $conn);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Neo Finance - Calendário</title>
  <link rel="stylesheet" href="../../css/conteudos/calendario/calendario.css" />
  <script src="../../js/conteudos/calendario/calendario.js" defer></script>
</head>

<body>
  <div class="containter">
    <!-- Início Header -->
    <div class="container--header">
      <header class="banner">
        <div class="titulo--banner">
          <img src="../../assets/icons/icon--calendario.svg" alt="calendario--icon" />
          <h1 id="bloquear-selecao">Calendário</h1>
        </div>
        <div class="notificacao--usuario">
          <img src="../../assets/icons/sino--icon.svg" alt="icon-notificacao" id="bloquear-selecao" />
        </div>
      </header>
    </div>
    <!-- Fim Header -->

    <!-- Início Conteúdo -->
    <div class="container--conteudo">
      <!-- Botão Adicionar Vencimento -->
      <div class="adicionar--btn" onclick="abrirModalAdicionar()">
        <img src=" ../../assets/icons/add--icon.svg" alt="adicionar--btn" />
      </div>

      <!-- Cards de Vencimentos -->
      <div class=" cards--vencimentos">
        <div class="header--card-vencimentos">
          <span class="titulo--card" id="bloquear-selecao">Próximos pagamentos</span>
          <div class="filtro--mes">
            <form method="POST">
              <select name="mes">
                <option value="01" <?php if ($mesSelecionado == '01') echo 'selected'; ?>>Janeiro</option>
                <option value="02" <?php if ($mesSelecionado == '02') echo 'selected'; ?>>Fevereiro</option>
                <option value="03" <?php if ($mesSelecionado == '03') echo 'selected'; ?>>Março</option>
                <option value="04" <?php if ($mesSelecionado == '04') echo 'selected'; ?>>Abril</option>
                <option value="05" <?php if ($mesSelecionado == '05') echo 'selected'; ?>>Maio</option>
                <option value="06" <?php if ($mesSelecionado == '06') echo 'selected'; ?>>Junho</option>
                <option value="07" <?php if ($mesSelecionado == '07') echo 'selected'; ?>>Julho</option>
                <option value="08" <?php if ($mesSelecionado == '08') echo 'selected'; ?>>Agosto</option>
                <option value="09" <?php if ($mesSelecionado == '09') echo 'selected'; ?>>Setembro</option>
                <option value="10" <?php if ($mesSelecionado == '10') echo 'selected'; ?>>Outubro</option>
                <option value="11" <?php if ($mesSelecionado == '11') echo 'selected'; ?>>Novembro</option>
                <option value="12" <?php if ($mesSelecionado == '12') echo 'selected'; ?>>Dezembro</option>
              </select>
              <button type="submit">Filtrar</button>
            </form>
          </div>
        </div>

        <div class="conteudo--card-vencimentos">
          <div class="scroll-card">
            <?php if (empty($vencimentos)): ?>
              <p>Nenhum vencimento encontrado para este mês.</p>
            <?php else: ?>
              <?php foreach ($vencimentos as $vencimento): ?>
                <div class="card">
                  <div class="lado--esquerdo--card">
                    <div class="data--vencimento">
                      <span><?php echo date('d', strtotime($vencimento['data_vencimento'])); ?></span>
                    </div>
                    <div class="linha--vertical--card"></div>
                    <div class="descricao--completa-v">
                      <div class="tags--descricao">
                        <span class="categoria--1"><?php echo calcularDiasRestantes($vencimento['data_vencimento']); ?></span>
                        <span class="categoria--2"><?php echo $vencimento['categoria']; ?></span>
                        <span class="categoria--3 <?php echo ($vencimento['status'] === 'Pago') ? 'status-pago' : ''; ?>">
                          <?php echo $vencimento['status']; ?>
                        </span>
                        <?php if (isset($vencimento) && isset($vencimento['tipo_transacao'])): ?>
                          <span class="<?php echo strtolower(trim($vencimento['tipo_transacao'])) === 'receita' ? 'categoria--receita' : 'categoria--despesa'; ?>">
                            <?php echo htmlspecialchars($vencimento['tipo_transacao']); ?>
                          </span>
                        <?php else: ?>
                          <span class="categoria--despesa">Tipo de transação não definido</span>
                        <?php endif; ?>
                      </div>
                      <div class="descricao--v">
                        <span><?php echo $vencimento['descricao']; ?></span>
                      </div>
                    </div>
                  </div>
                  <div class="lado--direito--card">
                    <?php if ($vencimento['status'] !== 'Pago'): ?>
                      <button class="confirmar-pagamento" onclick="abrirModalConfirmarPagamento(<?php echo $vencimento['id']; ?>)">
                        <img src="../../assets/icons/pagamento--icon.svg" class="icon">
                        Confirmar Pagamento
                      </button>
                    <?php endif; ?>
                    <div class="data--X--valor-v">
                      <div class="data--completa-v">
                        <span><?php echo date('d M, Y', strtotime($vencimento['data_vencimento'])); ?></span>
                      </div>
                      <div class="valor--v">
                        <span>R$ <?php echo number_format($vencimento['valor'], 2, ',', '.'); ?></span>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Modal Adicionar Vencimento -->
      <div id="modalAdicionar" class="modal">
        <div class="modal-content">
          <button id="fechar-modal" onclick="fecharModalAdicionar()">&times;</button>
          <h2>Adicionar Vencimento</h2>
          <form id="formAdicionar" method="POST" onsubmit="return validarFormulario(event)">
            <!-- Campo de Descrição -->
            <input type="text" name="descricao" placeholder="Descrição" required />

            <!-- Campo de Data de Vencimento -->
            <input type="date" id="data_vencimento" name="data_vencimento" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required />

            <!-- Campo de Valor -->
            <input type="text" name="valor" placeholder="Valor" required oninput="formatarValor(this)" maxlength="10" />

            <!-- Seleção de Categoria -->
            <select name="categoria" required>
              <option value="" disabled selected>Selecione uma Categoria</option>
              <?php foreach ($categorias as $categoria): ?>
                <option value="<?php echo $categoria ?>"><?php echo $categoria ?></option>
              <?php endforeach; ?>
            </select>

            <!-- Opções de Tipo de Transação -->
            <div class="radio-group">
              <label class="radio-label">
                <input type="radio" name="tipo" value="Receita" required>
                <span class="receita--radio">Receita</span>
              </label>
              <label class="radio-label">
                <input type="radio" name="tipo" value="Despesa">
                <span class="despesa--radio">Despesa</span>
              </label>
            </div>

            <!-- Botão de Enviar -->
            <button type="submit" name="adicionarVencimento">Adicionar Vencimento</button>
          </form>
        </div>
      </div>

      <!-- Modal de Confirmação -->
      <div id="modalConfirmacao" class="modal" ">
        <div class=" modal-content">
        <h2>Atenção !!!</h2>
        <p>Datas anteriores ao dia de hoje não são válidas, pois se referem a pagamentos que já deveriam ter sido feitos.</p>
        <button id="btn-ok">Ok, entendi</button>
      </div>
    </div>

    <!-- Modal de Sucesso -->
    <div id="modalSucesso" class="modal" style="display: none;">
      <div class="modal-content">
        <h2>Sucesso !!!</h2>
        <p>Vencimento adicionado com sucesso.</p>
        <button id="btn-ok" onclick="fecharModalSucesso()">OK</button>
      </div>
    </div>

    <!-- Modal de Sucesso do Pagamento -->
    <div id="modalSucessoPagamento" class="modal" style="display: none;">
      <div class="modal-content">
        <h2>Sucesso!</h2>
        <p>Pagamento confirmado com sucesso.</p>
        <button id="btn-ok" onclick="fecharModalSucessoPagamento()">OK</button>
      </div>
    </div>

    <!-- Modal para Confirmar Pagamento -->
    <div id="modalConfirmarPagamento" class="modal">
      <div class="modal-content">
        <span class="fechar--modal" onclick="fecharModalConfirmarPagamento()">&times;</span>
        <h2>Confirmar Pagamento</h2>
        <p>Você tem certeza que deseja confirmar o pagamento deste vencimento?</p>
        <form id="formConfirmarPagamento" method="POST">
          <input type="hidden" id="vencimento_id" name="vencimento_id" value="">
          <button type="submit" name="confirmarPagamento">Confirmar</button>
        </form>
      </div>
    </div>
  </div>
  </div>
</body>

</html>