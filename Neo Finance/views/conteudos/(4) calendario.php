<?php
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

  // Verifica se o tipo_transacao foi definido
  $tipo_transacao = isset($_POST['tipo_transacao']) ? $_POST['tipo_transacao'] : null;

  // Se o tipo_transacao não estiver definido, exiba uma mensagem de erro
  if ($tipo_transacao === null) {
    echo "Por favor, selecione o tipo de transação.";
  } else {
    $vencimentoAdicionado = adicionarVencimento($descricao, $data_vencimento, $valor, $categoria, $tipo_transacao, $conn);
  }
}

// Busca os vencimentos do mês selecionado
$vencimentos = buscarVencimentos($mesSelecionado, $conn);

// Busca as categorias do usuário
$usuario_id = $_SESSION['user_id'];
$categorias = buscarCategorias($usuario_id, $conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Neo Finance - Calendário</title>
  <link rel="stylesheet" href="../../css/conteudos/calendario/calendario.css" />
</head>

<body>
  <div class="containter">
    <div class="container--header">
      <header class="banner">
        <div class="titulo--banner">
          <img src="../../assets/icons/icon--calendario.svg" alt="calendario--icon" />
          <h1>Calendário</h1>
        </div>
        <div class="notificacao--usuario">
          <img src="../../assets/icons/sino--icon.svg" alt="icon-notificacao" />
        </div>
      </header>
    </div>

    <div class="container--conteudo">
      <div class="adicionar--btn">
        <img src="../../assets/icons/add--icon.svg" alt="adicionar--btn" onclick="abrirModalAdicionar()" />
      </div>

      <div class="cards--vencimentos">
        <div class="header--card-vencimentos">
          <span class="titulo--card">Próximos pagamentos</span>
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
                        <span class="categoria--3"><?php echo $vencimento['status']; ?></span>
                        <?php if (isset($vencimento) && isset($vencimento['tipo_transacao'])): ?>
                          <span class="<?php echo strtolower(trim($vencimento['tipo_transacao'])) === 'receita' ? 'categoria--receita' : 'categoria--despesa'; ?>">
                            <?php echo htmlspecialchars($vencimento['tipo_transacao']); // Protege contra XSS 
                            ?>
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
              <div class="tipo-transacao">
                <label>
                  <input type="radio" name="tipo_transacao" value="receita" required /> Receita
                </label>
                <label>
                  <input type="radio" name="tipo_transacao" value="despesa" required /> Despesa
                </label>
              </div>

              <!-- Botão de Enviar -->
              <button type="submit" name="adicionarVencimento">Adicionar Vencimento</button>
            </form>
          </div>
        </div>


        <div id="modalConfirmacao" class="modal">
          <div class="modal-content">
            <span class="fechar--modal-confirmacao" onclick="fecharModal()">&times;</span>
            <h2>Aviso</h2>
            <p>Datas anteriores ao dia de hoje não são válidas, pois se referem a pagamentos que já deveriam ter sido feitos.</p>
            <button id="btn-ok">OK</button>
          </div>
        </div>

        <div id="modalSucesso" class="modal">
          <div class="modal-content">
            <span class="fechar--modal" onclick="fecharModalSucesso()">&times;</span>
            <h2>Sucesso!</h2>
            <p>Vencimento adicionado com sucesso.</p>
            <button id="btn-ok" onclick="fecharModalSucesso()">OK</button>
          </div>
        </div>

        <script>
          // Função para abrir o modal de adicionar vencimento
          function abrirModalAdicionar() {
            document.getElementById('modalAdicionar').style.display = 'flex';
          }

          // Função para fechar o modal de adicionar vencimento
          function fecharModalAdicionar() {
            document.getElementById("modalAdicionar").style.display = "none";
          }

          // Função para fechar o modal de sucesso
          function fecharModalSucesso() {
            document.getElementById('modalSucesso').style.display = 'none';
          }

          // Função para validar o formulário antes do envio
          function validarFormulario(event) {
            const valorInput = document.querySelector('input[name="valor"]');
            const dataInput = document.getElementById('data_vencimento');
            if (valorInput.value < 0) {
              alert("O valor não pode ser negativo.");
              return false;
            }
            const dataSelecionada = new Date(dataInput.value);
            const hoje = new Date();
            hoje.setHours(0, 0, 0, 0); // Zera as horas da data de hoje
            if (dataSelecionada < hoje) {
              document.getElementById('modalConfirmacao').style.display = 'flex';
              return false;
            }
            return true;
          }

          // Função para formatar o valor do input
          function formatarValor(input) {
            let valor = input.value.replace(/\D/g, ''); // Remove todos os caracteres não numéricos
            valor = (valor / 100).toFixed(2); // Converte para um número com duas casas decimais

            // Separa a parte inteira da parte decimal
            let partes = valor.split('.');
            let inteiro = partes[0];
            let decimal = partes[1];

            // Adiciona pontos como separadores de milhar
            inteiro = inteiro.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            // Combina a parte inteira com a parte decimal
            valor = inteiro + ',' + decimal;

            input.value = valor;
          }

          // Adiciona o evento de clique no botão de OK do modal de confirmação
          document.getElementById("btn-ok").onclick = function() {
            document.getElementById('modalConfirmacao').style.display = 'none';
          }

          // Exibe o modal de sucesso se o vencimento for adicionado
          <?php if (isset($vencimentoAdicionado) && $vencimentoAdicionado): ?>
            document.getElementById('modalSucesso').style.display = 'flex';
          <?php endif; ?>
        </script>
      </div>
</body>

</html>