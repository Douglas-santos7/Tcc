<?php
include("../../config/database/conexao.php");
include("../../config/conteudos/login/verifica_login.php");
include("../../config/conteudos/dashboard/funcoes_balanco.php");
include("../../config/conteudos/dashboard/logica_vencimentos.php");
include("../../config/conteudos/dashboard/logica_calendario.php");
include("../../config/conteudos/dashboard/seleciona_categorias.php");
include("../../config/conteudos/dashboard/envio_dados.php");
include("../../config/conteudos/dashboard/consulta_historico.php");
include("../../config/conteudos/dashboard/logica_saudacao.php");
include("../../config/conteudos/calendario/funcoes.php");

$userId = $_SESSION['user_id']; // ID do usuário logado

// Obter dados do balanço
$balancoData = calcularBalanco($conn, $userId);
$receitas = $balancoData['receitas'];
$despesas = $balancoData['despesas'];
$balanco = $balancoData['balanco'];
$proporcaoReceitas = $balancoData['proporcaoReceitas'];
$proporcaoDespesas = $balancoData['proporcaoDespesas'];

// Obter próximo vencimento
$vencimentoData = obterProximoVencimento($conn, $userId);
$descricao = $vencimentoData['descricao'];
$data_vencimento = $vencimentoData['data_vencimento'];
$valor = $vencimentoData['valor'];
$categoria = $vencimentoData['categoria'];

// Obter categorias
$categorias = obterCategorias($conn);

// Enviar dados do formulário
enviarDados($conn, $userId);

// Consultar histórico
$historicoItems = consultarHistorico($conn, $userId);

// Obter saudação
$saudacao = obterSaudacao();

// Definindo as dicas financeiras
$dicasFinanceiras = [
  "Mantenha um registro detalhado de suas receitas e despesas.",
  "Estabeleça um orçamento mensal e siga-o.",
  "Considere usar aplicativos de finanças pessoais para monitorar suas despesas.",
  "Reserve uma porcentagem de suas receitas para poupança.",
  "Evite compras por impulso; dê um tempo antes de tomar a decisão.",
  "Revise seus gastos mensais e identifique onde você pode economizar.",
  "Considere investir em educação financeira para aprimorar suas habilidades.",
  "Crie um fundo de emergência para cobrir despesas imprevistas.",
  "Defina metas financeiras claras e monitore seu progresso regularmente."
];

// Fecha a conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Neo Finance - Dashboard</title>
  <link rel="stylesheet" href="../../css/conteudos/dashboard/dashboard.css">
  <link rel="stylesheet" href="../../css/conteudos/dashboard/popUp.css">
</head>

<body>
  <!-- Início Header -->
  <div class="container--header">
    <header class="perfil">
      <div class="usuario">
        <span id="bloquear-selecao"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></span>
        <h1 id="bloquear-selecao">Olá, <?php echo $saudacao . ' ' . $_SESSION['username']; ?>!</h1>
      </div>
      <div class="notificacao--usuario">
        <img src="../../assets/icons/sino--icon.svg" alt="icon-notificacao" />
      </div>
    </header>
  </div>
  <!-- Fim Header -->

  <!-- Início Conteúdo -->
  <div class="container--dashboard">
    <div class="cards">
      <!-- Card Balanço Total -->
      <div class="card--balanco">
        <div class="lado--esquerdo-bt">
          <span>Balanço Total</span>
          <h1 id="balanco--valor--total">R$ <?php echo number_format($balanco, 2, ',', '.'); ?></h1>
        </div>
        <div class="lado--direito-geral-bt">
          <div class="lado--direito-bt">
            <div class="parte--cima-bt">
              <div class="info--valores">
                <span>Saldo</span>
                <span id="resultado--receita">R$ <?php echo number_format($receitas, 2, ',', '.'); ?></span>
              </div>
              <img src="../../assets/icons/icon--saldo.svg" alt="icon--saldo" />
            </div>
            <div class="parte--baixo-bt">
              <div class="info--valores">
                <span>Gastos</span>
                <span id="resultado--despesa">R$ <?php echo number_format($despesas, 2, ',', '.'); ?></span>
              </div>
              <img src="../../assets/icons/icon--gastos.svg" alt="icon--gastos" />
            </div>
          </div>
          <div class="botao--adicionar">
            <img id="btn--abrir--popup" src="../../assets/icons/botao--adicionar.svg" alt="Adicionar" />
          </div>
        </div>
      </div>

      <!-- Card Histórico Recente -->
      <div class="card--historico-recente">
        <div class="header--card-hr">
          <span>Histórico Recente</span>
          <button id="ver-tudo-btn">Ver tudo</button>
        </div>
        <div class="info--historico">
          <ul id="historicoList">
            <?php echo $historicoItems; ?>
          </ul>
        </div>
        <div class="seta--pra--baixo"></div>
      </div>

      <!-- Card Receitas x Despesas -->
      <div class="card--receitasXdespesas">
        <div class="lado--esquerdo-rd">
          <span>Receitas x Despesas</span>
          <div class="grafico--receitasXdespesas">
            <div class="grafico--receitas" data-largura="<?php echo $proporcaoReceitas; ?>"></div>
            <div class="grafico--despesas" data-largura="<?php echo $proporcaoDespesas; ?>"></div>
          </div>
        </div>
        <div class="infoXfiltro">
          <div class="select--filtro">
            <select name="periodo" id="Filtro--mes">
              <option value="mensal">Mensal</option>
              <option value="semanal">Semanal</option>
              <option value="diario">Diário</option>
            </select>
          </div>
          <div class="receitas--filtro">
            <div class="icon--verde"></div>
            <div class="info--valores">
              <span>Receitas</span>
              <span>R$ <?php echo number_format($receitas, 2, ',', '.'); ?></span>
            </div>
          </div>
          <div class="despesas--filtro">
            <div class="icon--vermelho"></div>
            <div class="info--valores">
              <span>Despesas</span>
              <span>R$ <?php echo number_format($despesas, 2, ',', '.'); ?></span>
            </div>
          </div>
          <div class="saldo--filtro">
            <div class="icon--verde-claro"></div>
            <div class="info--valores">
              <span>Saldo</span>
              <span>R$ <?php echo number_format($balanco, 2, ',', '.'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="card--vencimentos">
        <div class="header--card-v">
          <div class="titulo--header-v">
            <img src="../../assets/icons/icon--calendario.svg" alt="icon--calendario" />
            <span class="dias--restantes"><?php echo calcularDiasRestantes($data_vencimento); ?></span>
          </div>
          <span class="mes--vencimento"><?php echo mesEmPortugues($data_vencimento); ?></span>
        </div>
        <div class="info--vencimentos">
          <div class="info--descricao">
            <span class="data--vencimento"><?php echo date('d', strtotime($data_vencimento)); ?></span>
            <div class="descricao--vencimento">
              <span>A pagar</span>
              <span><?php echo $descricao; ?></span>
            </div>
          </div>
          <span class="valor--vencimento">R$ <?php echo number_format($valor, 2, ',', '.'); ?></span>
        </div>
      </div>

      <div class="card--dicas">
    <div class="header--card-d">
        <span class="titulo">Dica para Controle Financeiro</span>
    </div>
    <div class="info--dicas">
        <p id="dica"><?php echo htmlspecialchars($dicasFinanceiras[array_rand($dicasFinanceiras)]); ?></p>
    </div>
</div>

<script>
    // Array de dicas financeiras
    const dicasFinanceiras = [
        "Mantenha um registro detalhado de suas receitas e despesas.",
        "Estabeleça um orçamento mensal e siga-o.",
        "Considere usar aplicativos de finanças pessoais para monitorar suas despesas.",
        "Reserve uma porcentagem de suas receitas para poupança.",
        "Evite compras por impulso; dê um tempo antes de tomar a decisão.",
        "Revise seus gastos mensais e identifique onde você pode economizar.",
        "Considere investir em educação financeira para aprimorar suas habilidades.",
        "Crie um fundo de emergência para cobrir despesas imprevistas.",
        "Defina metas financeiras claras e monitore seu progresso regularmente."
    ];

    // Função para mostrar uma nova dica
    function mostrarNovaDica() {
        const dicaAleatoria = dicasFinanceiras[Math.floor(Math.random() * dicasFinanceiras.length)];
        document.getElementById("dica").innerText = dicaAleatoria;
    }

    // Muda a dica automaticamente a cada 5 segundos (5000 milissegundos)
    setInterval(mostrarNovaDica, 5000);
</script>

    </div>
  </div>

  <!-- Início PopUp Adição de Item -->
  <div class="popup-container" id="popup-container" style="display: none;">
    <div class="popup">
      <div class="close-btn" id="close-btn">&times;</div>
      <h2>Adicionar Item</h2>
      <form method="POST" action="">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" required autocomplete="off">
        <div id="suggestions" class="suggestions-box"></div>

        <label for="valor">Valor:</label>
        <input type="text" id="valor" name="valor" required placeholder="0,00">

        <label for="categoria">Categoria:</label>
        <button type="button" id="btn-selecionar-categoria">Selecionar Categoria</button>

        <input type="hidden" name="categoria" id="categoria-id" required>

        <label for="tipo">Tipo:</label>
        <select name="tipo" required>
          <option value="receita">Receita</option>
          <option value="despesa">Despesa</option>
        </select>

        <button type="submit">Adicionar</button>
      </form>
    </div>
  </div>

  <!-- INICIO POP-UP SELECT DE CATEGORIAS -->
  <div id="popup-categorias-unico" class="popup-categorias" style="display: none;">
    <div class="popup-categorias-conteudo">
      <span class="popup-categorias-close-btn" id="btn-fechar-popup-categorias">&times;</span>
      <h2 class="categoria-titulo">Selecionar uma categoria</h2>

      <button id="botao-filtro-categorias" class="btn-filtro-categorias">
        A-Z
      </button>

      <ul id="lista-categorias" class="lista-categorias">
        <?php
        if (!empty($categorias)) {
          foreach ($categorias as $categoria) {
            $iconeCategoria = isset($categoria['icone']) ? htmlspecialchars($categoria['icone']) : 'caminho/para/imagem/padrao.png';
            echo '<li>
                  <button type="button" class="categoria-item-unico" data-id="' . htmlspecialchars($categoria['id']) . '">
                      <i class="' . $iconeCategoria . ' categoria-icon"></i>
                      <span class="categoria-nome">' . htmlspecialchars($categoria['nome']) . '</span>
                  </button>
                </li>';
          }
        } else {
          echo '<li>Nenhuma categoria disponível.</li>';
        }
        ?>
      </ul>
    </div>
  </div>

  <script src="../../js/conteudos/dashboard/popup.js"></script>
  <script src="../../js/conteudos/dashboard/categorias.js"></script>
  <script src="../../js/conteudos/dashboard/formataMoeda.js"></script>
</body>

</html>
