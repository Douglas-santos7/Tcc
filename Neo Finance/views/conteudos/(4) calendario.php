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
  $valor = $_POST['valor'];
  $categoria = $_POST['categoria'];

  adicionarVencimento($descricao, $data_vencimento, $valor, $categoria, $conn);
}

// Busca os vencimentos do mês selecionado
$vencimentos = buscarVencimentos($mesSelecionado, $conn);

// Busca as categorias do usuário
$usuario_id = $_SESSION['user_id']; // Pega o ID do usuário logado
$categorias = buscarCategorias($usuario_id, $conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Neo Finance - Calendário</title>
  <link rel="stylesheet" href="../../css/conteudos/calendario/calendario.css" />
  <style>
    /* Adicione estilos personalizados aqui */
  </style>
</head>

<body>
  <!-- Início da estrutura principal da página -->
  <div class="containter">
    <!-- Início do Header -->
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
    <!-- Fim do Header -->

    <!-- Início do Conteúdo Principal -->
    <div class="container--conteudo">
      <!-- Botão de Adicionar -->
      <div class="adicionar--btn">
        <img src="../../assets/icons/add--icon.svg" alt="adicionar--btn" onclick="abrirModalAdicionar()" />
      </div>

      <!-- Início dos Cards de Vencimentos -->
      <div class="cards--vencimentos">
        <div class="header--card-vencimentos">
          <span class="titulo--card">Próximas a pagar</span>
          <!-- Filtro por Mês -->
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
      </div>
      <!-- Fim dos Cards de Vencimentos -->
    </div>
    <!-- Fim do Conteúdo Principal -->

    <!-- Modal para Adicionar Vencimento -->
    <div id="modalAdicionar" class="modal">
      <div class="modal-content">
        <button id="fechar-modal" onclick="fecharModalAdicionar()">&times;</button>
        <h2>Adicionar Vencimento</h2>
        <form id="formAdicionar" method="POST" onsubmit="return validarFormulario(event)">
          <input type="text" name="descricao" placeholder="Descrição" required />
          <input type="date" id="data_vencimento" name="data_vencimento" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required />
          <input type="text" name="valor" placeholder="Valor" required oninput="formatarValor(this)" />

          <select name="categoria" required>
            <option value="" disabled selected>Selecione uma Categoria</option>
            <?php foreach ($categorias as $categoria): ?>
              <option value="<?php echo $categoria ?>"><?php echo $categoria ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" name="adicionarVencimento">Adicionar Vencimento</button>
        </form>
      </div>
    </div>

    <!-- Modal de Confirmação -->
    <div id="modalConfirmacao" class="modal">
      <div class="modal-content">
        <span class="fechar--modal-confirmacao" onclick="fecharModal()">&times;</span>
        <h2>Aviso</h2>
        <p>Datas anteriores ao dia de hoje não são válidas, pois se referem a pagamentos que já deveriam ter sido feitos.</p>
        <button id="btn-ok">OK</button>
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

      // Função para validar o formulário
      function validarFormulario(event) {
        const valorInput = document.querySelector('input[name="valor"]');
        const dataInput = document.getElementById('data_vencimento');

        // Validação do valor
        if (valorInput.value < 0) {
          alert("O valor não pode ser negativo.");
          return false; // Impede o envio do formulário
        }

        // Validação da data
        const dataSelecionada = new Date(dataInput.value);
        const hoje = new Date();
        hoje.setHours(0, 0, 0, 0); // Zera horas para comparação

        if (dataSelecionada < hoje) {
          event.preventDefault(); // Impede o envio do formulário
          abrirModal(); // Abre o modal de confirmação
          return false; // Impede o envio do formulário
        }

        return true; // Permite o envio do formulário
      }

      // Função para abrir o modal de confirmação
      function abrirModal() {
        document.getElementById('modalConfirmacao').style.display = 'flex';
      }

      // Função para fechar o modal
      function fecharModal() {
        document.getElementById('modalConfirmacao').style.display = 'none';
      }

      // Confirmar ação no modal
      document.getElementById('btn-ok').onclick = function() {
        document.getElementById('formAdicionar').submit(); // Envia o formulário
        fecharModal(); // Fecha o modal de confirmação
      };

      // Fecha o modal ao clicar no 'x'
      document.querySelectorAll('.close').forEach(item => {
        item.onclick = function() {
          fecharModal();
        }
      });

      function formatarValor(input) {
        // Remove todos os caracteres que não sejam dígitos
        let valor = input.value.replace(/\D/g, '');

        // Formata o valor como moeda
        if (valor) {
          // Adiciona os pontos para milhar e vírgula para centavos
          valor = (parseInt(valor) / 100).toFixed(2); // Converte para float com duas casas decimais
          valor = valor.replace('.', ','); // Troca o ponto pela vírgula

          // Divide a parte inteira da parte decimal
          let partes = valor.split(',');
          partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Adiciona pontos à parte inteira

          input.value = partes.join(','); // Junte as partes de volta
        } else {
          input.value = ''; // Limpa o campo se estiver vazio
        }
      }
    </script>
</body>

</html>