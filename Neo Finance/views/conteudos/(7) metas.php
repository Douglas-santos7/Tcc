<?php
// Incluir a conexão com o banco de dados
include("../../config/database/conexao.php");

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome_meta = $_POST['nome'] ?? null;
  $valor_meta = $_POST['valor'] ?? null;
  $data_meta = $_POST['data'] ?? null;
  $usuario_id = $_POST['usuario_id'] ?? null;

  // Converter valor da meta para o formato correto (remover vírgulas, pontos etc.)
  if ($valor_meta) {
    $valor_meta = str_replace(",", ".", str_replace(".", "", $valor_meta));
  }

  // Operação de depósito
  if (isset($_POST['valor_deposito']) && isset($_POST['id_meta'])) {
    $id_meta = $_POST['id_meta'];
    $valor_deposito = $_POST['valor_deposito'];

    if ($valor_deposito) {
      $valor_deposito = str_replace(",", ".", str_replace(".", "", $valor_deposito));
    }

    // Atualizar o valor atual da meta com o valor do depósito
    $sql = "UPDATE metas SET valor_atual = valor_atual + ? WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dii", $valor_deposito, $id_meta, $usuario_id);

    if ($stmt->execute()) {
      header("Location: ../conteudos/(7) metas.php?sucesso=deposito");
      exit();
    } else {
      echo "Erro ao depositar valor: " . $conn->error;
    }
  }

  // Operação de resgatar
  if (isset($_POST['valor_resgatar']) && isset($_POST['id_meta'])) {
    $id_meta = $_POST['id_meta'];
    $valor_resgatar = $_POST['valor_resgatar'];

    if ($valor_resgatar) {
      $valor_resgatar = str_replace(",", ".", str_replace(".", "", $valor_resgatar));
    }

    // Atualizar o valor atual da meta subtraindo o valor resgatado
    $sql = "UPDATE metas SET valor_atual = valor_atual - ? WHERE id = ? AND usuario_id = ? AND valor_atual >= ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("diii", $valor_resgatar, $id_meta, $usuario_id, $valor_resgatar);

    if ($stmt->execute()) {
      header("Location: ../conteudos/(7) metas.php?sucesso=resgatar");
      exit();
    } else {
      echo "Erro ao resgatar valor: " . $conn->error;
    }
  }

  // Adicionar uma nova meta
  if (!empty($nome_meta) && is_numeric($valor_meta) && !empty($data_meta)) {
    $sql = "INSERT INTO metas (nome_meta, valor_alvo, data_limite, usuario_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nome_meta, $valor_meta, $data_meta, $usuario_id);

    if ($stmt->execute()) {
      header("Location: ../conteudos/(7) metas.php?sucesso=1");
      exit();
    } else {
      echo "Erro ao adicionar meta: " . $conn->error;
    }
  }

  // Exclusão de meta (somente quando o botão de exclusão for clicado)
  if (isset($_POST['id_meta']) && isset($_POST['acao']) && $_POST['acao'] === 'deletar') {
    $id_meta = $_POST['id_meta'];

    $sql = "DELETE FROM metas WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_meta, $usuario_id);

    if ($stmt->execute()) {
      header("Location: ../conteudos/(7) metas.php?sucesso=2");
      exit();
    } else {
      echo "Erro ao remover meta: " . $conn->error;
    }
  }
}

// Buscar as metas do usuário
$sql = "SELECT * FROM metas WHERE usuario_id = 1"; // Ajuste para pegar o ID do usuário logado
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Metas</title>
  <link rel="stylesheet" href="../../css/conteudos/metas/metas.css">
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

</head>

<body>

  <div class="container">

    <!-- Menu Lateral -->
    <div class="menu-lateral" onclick="abrirModalAdicionar()">
      <div class="adicionar--btn">
        <img src="../../assets/icons/quadrado-adicionar.png" alt="add--btn">
      </div>
    </div>

    <!-- POPUP ADICIONAR META -->
    <div class="pop-up-adicionar-container" id="pop-up-adicionar-container" style="display: none;">
      <div class="pop-up-adicionar-conteudo">
        <span class="popup-adicionar-close-btn" id="btn-fechar-popup-adicionar">&times;</span>
        <h2 class="adicionar-titulo">Adicionar Meta</h2>

        <!-- Formulário para adicionar uma meta -->
        <form method="POST" action="">
          <label for="nome_meta">Nome da Meta:</label>
          <input type="text" id="nome_meta" name="nome" required placeholder="Digite o nome da meta">

          <label for="valor_meta">Valor da Meta:</label>
          <input type="text" id="valor_meta" name="valor" required placeholder="0,00">

          <label for="data_meta">Data Limite:</label>
          <input type="date" id="data_meta" name="data" required>

          <input type="hidden" name="usuario_id" value="1"> <!-- Exemplo para `usuario_id` -->

          <button type="submit">Adicionar Meta</button>
        </form>
      </div>
    </div>


    <!-- Cards de Metas -->
    <div class="container-cards">
      <?php while ($meta = $result->fetch_assoc()) { ?>
        <div class="card-meta">
          <div class="titulo-card">
            <span><?php echo htmlspecialchars($meta['nome_meta']); ?></span>
            <!-- Formulário para remover a meta -->
            <form method="POST" action="../conteudos/(7) metas.php" style="display:inline;">
              <input type="hidden" name="id_meta" value="<?php echo $meta['id']; ?>">
              <input type="hidden" name="acao" value="deletar"> <!-- Adicionar ação -->
              <button type="submit" class="icone-lixeira" aria-label="Remover meta"
                onclick="return confirm('Tem certeza que deseja remover esta meta?');">
                <i class="fi fi-sr-trash"></i>
              </button>
            </form>

          </div>
          <div class="progresso-meta">
            <span>R$ <?php echo number_format($meta['valor_atual'], 2, ',', '.'); ?></span> <!-- Valor atual -->
            <div class="barra-progresso">
              <div class="barra-progresso-preenchida"
                style="width: <?php echo ($meta['valor_atual'] / $meta['valor_alvo']) * 100; ?>%;"></div>
              <!-- Progresso -->
            </div>
            <span>de R$ <?php echo number_format($meta['valor_alvo'], 2, ',', '.'); ?></span>
          </div>
          <div class="data-limite">
            <span>Prazo para Meta: <?php echo date('d/m/Y', strtotime($meta['data_limite'])); ?></span>
          </div>
          <div class="botoes-meta">
            <button class="btn-depositar" onclick="abrirModalDepositar(<?php echo $meta['id']; ?>)">
              <div for="icon2"><i class="fi fi-sr-home"></i></div> Depositar
            </button>
            <button class="btn-resgatar" onclick="abrirModalResgatar(<?php echo $meta['id']; ?>)">
              <div for="icon2"><i class="fi fi-sr-home"></i></div> Resgatar
            </button>

            <button class="btn-historico">
              <div for="icon2"><i class="fi fi-sr-home"></i></div> Histórico
            </button>
          </div>
          <!-- Elemento de gráfico para a meta -->
          <div class="grafico-meta" id="grafico-<?php echo $meta['id']; ?>"></div>

        </div>
      <?php } ?>
    </div>

    <!-- POPUP DEPOSITAR -->
    <div class="pop-up-depositar-container" id="pop-up-depositar-container" style="display: none;">
      <div class="pop-up-depositar-conteudo">
        <span class="popup-depositar-close-btn" id="btn-fechar-popup-depositar">&times;</span>
        <h2 class="depositar-titulo">Depositar Valor</h2>

        <!-- Formulário para depósito -->
        <form method="POST" action="" id="form-depositar">
          <label for="valor_deposito">Valor a Depositar:</label>
          <input type="text" id="valor_deposito" name="valor_deposito" required placeholder="0,00">

          <input type="hidden" name="usuario_id" value="1"> <!-- Exemplo para `usuario_id` -->
          <input type="hidden" name="id_meta" id="id_meta_depositar" value="">

          <button type="submit">Depositar</button>
        </form>
      </div>
    </div>

    <!-- POPUP RESGATAR -->
    <div class="pop-up-resgatar-container" id="pop-up-resgatar-container" style="display: none;">
      <div class="pop-up-resgatar-conteudo">
        <span class="popup-resgatar-close-btn" id="btn-fechar-popup-resgatar">&times;</span>
        <h2 class="resgatar-titulo">Resgatar Valor</h2>

        <!-- Formulário para resgatar -->
        <form method="POST" action="" id="form-resgatar">
          <label for="valor_resgatar">Valor a Resgatar:</label>
          <input type="text" id="valor_resgatar" name="valor_resgatar" required placeholder="0,00">

          <input type="hidden" name="usuario_id" value="1"> <!-- Exemplo para `usuario_id` -->
          <input type="hidden" name="id_meta" id="id_meta_resgatar" value="">

          <button type="submit">Resgatar</button>
        </form>
      </div>
    </div>

  </div>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
        var metas = <?php echo json_encode($result->fetch_all(MYSQLI_ASSOC)); ?>; // Pega as metas do PHP

        metas.forEach(function (meta) {
            var progresso = (meta.valor_atual / meta.valor_alvo) * 100;

            var options = {
                series: [progresso], // Porcentagem inicial baseada no progresso da meta
                chart: {
                    height: 200,
                    type: 'radialBar',
                    toolbar: { show: true }
                },
                plotOptions: {
                    radialBar: {
                        startAngle: -135,
                        endAngle: 225,
                        hollow: {
                            margin: 0,
                            size: '60%',
                            background: '#fff',
                        },
                        track: {
                            background: '#fff',
                            strokeWidth: '67%',
                        },
                        dataLabels: {
                            show: true,
                            name: {
                                offsetY: -10,
                                color: '#888',
                                fontSize: '12px'
                            },
                            value: {
                                formatter: function (val) {
                                    return parseInt(val);
                                },
                                color: '#111',
                                fontSize: '20px',
                                show: true,
                            }
                        }
                    }
                },
                fill: {
                    colors: ['#00e060'],
                },
                stroke: {
                    lineCap: 'round'
                },
                labels: ['Progresso'],
            };

            var chart = new ApexCharts(document.querySelector("#grafico-" + meta.id), options);
            chart.render();

        });
    });

    // Abrir modal para depositar valor
    function abrirModalDepositar(idMeta) {
        var modalDepositar = document.getElementById('pop-up-depositar-container');
        var idMetaInput = document.getElementById('id_meta_depositar');
        idMetaInput.value = idMeta;
        modalDepositar.style.display = 'block';
    }

    // Fechar modal Depositar
    var closeBtnDepositar = document.getElementById('btn-fechar-popup-depositar');
    closeBtnDepositar.onclick = function () {
        document.getElementById('pop-up-depositar-container').style.display = 'none';
    }

    // Abrir modal para resgatar valor
    function abrirModalResgatar(idMeta) {
        var modalResgatar = document.getElementById('pop-up-resgatar-container');
        var idMetaInput = document.getElementById('id_meta_resgatar');
        idMetaInput.value = idMeta;
        modalResgatar.style.display = 'block';
    }

    // Fechar modal Resgatar
    var closeBtnResgatar = document.getElementById('btn-fechar-popup-resgatar');
    closeBtnResgatar.onclick = function () {
        document.getElementById('pop-up-resgatar-container').style.display = 'none';
    }
</script>


  </div>


  <script>
    // Função para abrir o modal de adicionar meta
    function abrirModalAdicionar() {
      var modalAdicionar = document.getElementById('pop-up-adicionar-container');
      modalAdicionar.style.display = 'block';
    }

    // Função para fechar o modal de adicionar meta
    var closeBtnAdicionar = document.getElementById('btn-fechar-popup-adicionar');
    closeBtnAdicionar.onclick = function () {
      document.getElementById('pop-up-adicionar-container').style.display = 'none';
    }

    // Fechar o modal se clicar fora dele
    window.onclick = function (event) {
      var modalAdicionar = document.getElementById('pop-up-adicionar-container');
      if (event.target == modalAdicionar) {
        modalAdicionar.style.display = 'none';
      }
    }
  </script>
  <script>
    //Função de obter a data atual - input data limite
    // Obtém a data atual
    const today = new Date();

    // Formata a data como 'AAAA-MM-DD' para ser compatível com o valor do input de tipo date
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0'); // meses começam em 0
    const day = String(today.getDate()).padStart(2, '0');

    // Define o valor mínimo no input para a data atual
    const minDate = `${year}-${month}-${day}`;
    document.getElementById('data_meta').setAttribute('min', minDate);
  </script>

</body>

</html>