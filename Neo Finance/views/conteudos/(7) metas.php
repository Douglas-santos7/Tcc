<?php
// Incluir a conexão com o banco de dados
include("../../config/database/conexao.php");

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Receber dados do formulário
  $nome_meta = $_POST['nome'];
  $valor_meta = $_POST['valor'];
  $data_meta = $_POST['data'];
  $usuario_id = $_POST['usuario_id'];

  // Converter valor da meta para o formato correto (remover vírgulas, pontos etc.)
  $valor_meta = str_replace(",", ".", str_replace(".", "", $valor_meta));

  // Validar os campos
  if (!empty($nome_meta) && is_numeric($valor_meta) && !empty($data_meta)) {
    // Preparar a query SQL para inserir na coluna correta (valor_alvo)
    $sql = "INSERT INTO metas (nome_meta, valor_alvo, data_limite, usuario_id) VALUES (?, ?, ?, ?)";

    // Preparar a declaração SQL
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nome_meta, $valor_meta, $data_meta, $usuario_id);

    // Executar a query
    if ($stmt->execute()) {
      // Redirecionar para a página de metas ou exibir uma mensagem de sucesso
      header("Location: metas.php?sucesso=1");
      exit();
    } else {
      echo "Erro ao adicionar meta: " . $conn->error;
    }
  } else {
    echo "Todos os campos são obrigatórios e o valor deve ser numérico!";
  }
}

// APAGAR META
if (isset($_POST['id_meta'])) {
  $id_meta = $_POST['id_meta'];

  // Preparar a query SQL para deletar a meta
  $sql = "DELETE FROM metas WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id_meta);

  // Executar a query
  if ($stmt->execute()) {
    // Redirecionar para a página de metas após apagar a meta
    header("Location: metas.php?sucesso=2");
    exit();
  } else {
    echo "Erro ao remover meta: " . $conn->error;
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

    <!-- Cards de Metas -->
    <div class="container-cards">
      <?php while ($meta = $result->fetch_assoc()) { ?>
        <div class="card-meta">
          <div class="titulo-card">
            <span><?php echo htmlspecialchars($meta['nome_meta']); ?></span>
            <!-- Formulário para remover a meta -->
            <form method="POST" action="metas.php" style="display:inline;">
              <input type="hidden" name="id_meta" value="<?php echo $meta['id']; ?>">
              <button type="submit" class="icone-lixeira" aria-label="Remover meta"
                onclick="return confirm('Tem certeza que deseja remover esta meta?');">
                <i class="fi fi-sr-trash"></i>
              </button>
            </form>
          </div>
          <div class="progresso-meta">
            <span>R$ 0,00</span> <!-- Valor atual -->
            <div class="barra-progresso">
              <div class="barra-progresso-preenchida" style="width: 0%;"></div> <!-- Progresso -->
            </div>
            <span>de R$ <?php echo number_format($meta['valor_alvo'], 2, ',', '.'); ?></span>
          </div>
          <div class="data-limite">
            <span>Prazo para Meta: <?php echo date('d/m/Y', strtotime($meta['data_limite'])); ?></span>
          </div>
          <div class="botoes-meta">
            <button class="btn-depositar">
              <div for="icon2"><i class="fi fi-sr-home"></i></div> Depositar
            </button>
            <button class="btn-resgatar">
              <div for="icon2"><i class="fi fi-sr-home"></i></div> Resgatar
            </button>
            <button class="btn-historico">
              <div for="icon2"><i class="fi fi-sr-home"></i></div> Histórico
            </button>
          </div>
          <!-- Elemento para o gráfico -->
          <div class="grafico" id="chart-<?php echo $meta['id']; ?>" style="height: 100px; width: 100%;"></div>
        </div>
      <?php } ?>
    </div>

    <script>
      var options = {
        series: [0], // Porcentagem inicial
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
        labels: ['Porcentagem'],
      };

      var chart = new ApexCharts(document.querySelector("#chart-<?php echo $meta['id']; ?>"), options);
      chart.render();
    </script>
  </div>

  <!-- Botão de Avançar -->
  <div class="btn-avancar">
    >
  </div>

  <!-- POPUP METAS -->
  <div class="pop-up-metas-container" id="pop-up-metas-container" style="display: none;">
    <div class="pop-up-metas-conteudo">
      <span class="popup-metas-close-btn" id="btn-fechar-popup-metas">&times;</span>
      <h2 class="metas-titulo">Crie uma meta</h2>

      <!-- Formulário para adicionar metas -->
      <form method="POST" action="">
        <label for="nome">Meta:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="valor">Valor para alcançar meta:</label>
        <input type="text" id="valor" name="valor" required placeholder="0,00">

        <label for="data">Prazo para meta:</label>
        <input type="date" id="data" name="data" required>

        <input type="hidden" name="usuario_id" value="1"> <!-- Exemplo para `usuario_id` -->

        <button type="submit">Adicionar meta</button>
      </form>
    </div>
  </div>

  </div>

  <script src="../../js/conteudos/dashboard/formataMoeda.js"></script>
  <script>
    function abrirModalAdicionar() {
      document.getElementById('pop-up-metas-container').style.display = 'flex'; // Abre o popup
    }

    document.getElementById('btn-fechar-popup-metas').onclick = function () {
      document.getElementById('pop-up-metas-container').style.display = 'none'; // Fecha o popup
    };

    // Para fechar o popup clicando fora dele (opcional)
    window.onclick = function (event) {
      const popup = document.getElementById('pop-up-metas-container');
      if (event.target === popup) {
        popup.style.display = 'none'; // Fecha o popup
      }
    };
  </script>

</body>

</html>
