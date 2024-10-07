<?php
session_start();
include("../../config/database/conexao.php");
include("../../config/conteudos/calendario/funcoes.php");
include("../../config/conteudos/dashboard/funcoes_transacoes.php");
include("../../config/conteudos/dashboard/funcoes_vencimentos.php");
include("../../config/conteudos/dashboard/funcoes_datas.php");

// Verifica se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: ../../views/login/login.php"); // Redireciona para a página de login
  exit();
}

$userId = $_SESSION['user_id']; // ID do usuário logado

// Consultar receitas e despesas
$transacoes = consultarReceitasDespesas($conn, $userId);
$receitas = $transacoes['receitas'];
$despesas = $transacoes['despesas'];
$total = $transacoes['total'];
$proporcaoReceitas = ($total > 0) ? ($receitas / $total) * 800 : 0; // Largura em pixels
$proporcaoDespesas = ($total > 0) ? ($despesas / $total) * 800 : 0; // Largura em pixels

// Calcular Balanço Total
$balanco = $receitas - $despesas;

// Consultar o histórico
$resultHistorico = consultarHistorico($conn, $userId);

// Consultar o próximo vencimento
$vencimento = consultarProximoVencimento($conn, $userId);

// Verifica se existe um vencimento
if ($vencimento) {
  $descricao = $vencimento['descricao'];
  $data_vencimento = $vencimento['data_vencimento'];
  $valor = $vencimento['valor'];
  $categoria = $vencimento['categoria'];
} else {
  $descricao = "Sem vencimentos pendentes";
  $data_vencimento = "";
  $valor = 0;
  $categoria = "";
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Neo Finance - Dashboard</title>
  <link rel="stylesheet" href="../../css/conteudos/dashboard/dashboard.css" />
  <link rel="stylesheet" href="../../css/conteudos/dashboard/popUp.css" />
</head>

<body>
  <!-- Início Header -->
  <div class="container--header">
    <header class="perfil">
      <div class="usuario">
        <span>
          <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
        </span>
        <h1>
          Hello,
          <?php echo $_SESSION['username']; ?>
          !
        </h1>
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
        <!-- Lado Esquerdo do Card -->
        <div class="lado--esquerdo-bt">
          <span> Balanço Total </span>
          <h1 id="balanco--valor--total">
            R$
            <?php echo number_format($balanco, 2, ',', '.'); ?>
          </h1>
        </div>
        <!-- Fim Lado Esquerdo do Card -->
        <!-- Lado Direito do Card -->
        <div class="lado--direito-geral-bt">
          <div class="lado--direito-bt">
            <div class="parte--cima-bt">
              <div class="info--valores">
                <span> Saldo </span>
                <span id="resultado--receita">
                  R$
                  <?php echo number_format($receitas, 2, ',', '.'); ?>
                </span>
              </div>
              <img src="../../assets/icons/icon--saldo.svg" alt="icon--saldo" />
            </div>
            <div class="parte--baixo-bt">
              <div class="info--valores">
                <span> Gastos </span>
                <span id="resultado--despesa">
                  R$
                  <?php echo number_format($despesas, 2, ',', '.'); ?>
                </span>
              </div>
              <img src="../../assets/icons/icon--gastos.svg" alt="icon--gastos" />
            </div>
          </div>
          <!-- Botão Adicionar -->
          <div class="botao--adicionar">
            <img id="btn--abrir--popup" src="../../assets/icons/botao--adicionar.svg" alt="Adicionar" />
          </div>
        </div>
      </div>
      <!-- Fim Card Balanço Total -->
      <!-- Card Histórico Recente -->
      <div class="card--historico-recente">
        <div class="header--card-hr">
          <span> Histórico Recente </span>
          <button>Ver tudo</button>
        </div>
        <!-- Histórico de Transações -->
        <div class="info--historico">
          <ul id="historicoList">
            <!-- Itens do histórico serão adicionados aqui -->
          </ul>
        </div>
        <div class="seta--pra--baixo"></div>
      </div>
      <!-- Fim Histórico Recente -->
      <!-- Card Receitas x Despesas -->
      <div class="card--receitasXdespesas">
        <!-- Lado Esquerdo do Card -->
        <div class="lado--esquerdo-rd">
          <span> Receitas x Despesas </span>
          <div class="grafico--receitasXdespesas">
            <div class="grafico--receitas" style="width: <?php echo $proporcaoReceitas; ?>px;"></div>
            <div class="grafico--despesas" style="width: <?php echo $proporcaoDespesas; ?>px;"></div>
          </div>
        </div>
        <!-- Informações e Filtro -->
        <div class="infoXfiltro">
          <div class="select--filtro">
            <select name="Meses" id="Filtro--mes">
              <option value="mensal">Mensal</option>
              <option value="semanal">Semanal</option>
              <option value="diario">Diário</option>
            </select>
          </div>
          <div class="receitas--filtro">
            <div class="icon--verde"></div>
            <div class="info--valores">
              <span> Receitas </span>
              <span>
                R$
                <?php echo number_format($receitas, 2, ',', '.'); ?>
              </span>
            </div>
          </div>
          <div class="despesas--filtro">
            <div class="icon--vermelho"></div>
            <div class="info--valores">
              <span> Despesas </span>
              <span>
                R$
                <?php echo number_format($despesas, 2, ',', '.'); ?>
              </span>
            </div>
          </div>
          <div class="saldo--filtro">
            <div class="icon--verde-claro"></div>
            <div class="info--valores">
              <span> Saldo </span>
              <span>
                R$
                <?php echo number_format($balanco, 2, ',', '.'); ?>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="card--vencimentos">
        <div class="header--card-v">
          <div class="titulo--header-v">
            <img src="../../assets/icons/icon--calendario.svg" alt="icon--calendario" />
            <span class="dias--restantes">
              <?php echo calcularDiasRestantes($data_vencimento); ?>
            </span>
          </div>
          <span class="mes--vencimento">
            <?php echo mesEmPortugues($data_vencimento); ?>
          </span>
        </div>
        <div class="info--vencimentos">
          <div class="info--descricao">
            <span class="data--vencimento">
              <?php echo date('d', strtotime($data_vencimento)); ?>
            </span>
            <div class="descricao--vencimento">
              <span>
                <?php echo $descricao; ?>
              </span>
              <span>
                <?php echo $categoria; ?>
              </span>
            </div>
          </div>
          <div class="linha--vertical-v"></div>
          <span class="valor--vencimento">
            R$
            <?php echo number_format($valor, 2, ',', '.'); ?>
          </span>
        </div>
      </div>
      <!-- Fim Card Próximos Vencimentos -->
      <!-- Card Lembretes -->
      <div class="card--lembretes">
        <div class="header--card-l">
          <span class="titulo"> Lembretes </span>
          <span class="descricao--lembrete"> Moradia </span>
        </div>
        <div class="info--lembrete">
          <div class="detalhes--info">
            <span class="descricao--info"> Pagar aluguel </span>
            <span class="valor--lembrete"> $ 350,00 </span>
          </div>
          <div class="status--info">
            <span> Em aberto </span>
            <input type="checkbox" name="status--checkbox" />
          </div>
        </div>
      </div>
      <!-- Fim Card Lembretes -->
    </div>
  </div>
  <!-- Fim Conteúdo -->
  <!-- ##### POPUP DE ADIÇÃO DE RECEITA/DESPESA ##### -->
  <div id="id--popup" class="popup--box">
    <div class="popup--conteudo">
      <span class="fecha--btn" id="id--fecha--btn"> &times; </span>
      <h2>Adicionar Receita/Despesa</h2>
      <div class="form--grupo--tipo">
        <div class="form--item--tipo">
          <label for="label--tipo"> Tipo </label>
          <select name="tipo" id="id--tipo">
            <option value="Receita">Receita</option>
            <option value="Despesa">Despesa</option>
          </select>
        </div>
        <div class="form-item--categoria">
          <label for="label--categoria"> Categoria </label>
          <div class="grupo--input">
            <input type="text" placeholder="Digite a nova categoria" id="id--categoria" />
            <button id="add--tipo--categoria" type="button">+</button>
          </div>
        </div>
        <div class="form--item--categoria--select">
          <label for="label--select--categoria"> Categorias </label>
          <select id="categoria--select">
            <!-- As opções serão adicionadas dinamicamente aqui -->
          </select>
        </div>
      </div>
      <div class="form--grupo--nome">
        <div class="form--item--nome">
          <label for="label--nome"> Nome </label>
          <input type="text" placeholder="Nome" id="id--nome" />
        </div>
        <div class="form-item--valor">
          <label for="label--valor"> Valor </label>
          <input type="number" placeholder="Valor" id="id--valor" step="0.01" />
        </div>
      </div>
      <div class="form-item--icone">
        <label for="label--icone"> Categoria: </label>
        <button id="abrir--selecao--icones" type="button">Selecione Categoria</button>
        <!-- Exibe o ícone selecionado -->
        <div id="selecao--icone--container" data-icon="">
          <!-- Ícone selecionado será exibido aqui -->
        </div>
      </div>
      <div class="botao--componente">
        <button id="btn--enviar" onclick="subValores()">Enviar</button>
      </div>
    </div>
  </div>
  <!-- ##### POPUP DE SELEÇÃO DE ICONES ##### -->
  <div id="id--selecao--icones" class="selecao--icones--popup" style="display: none">
    <div class="icon--popup--conteudo">
      <span class="icon-fecha-btn" id="id--fecha--icone"> &times; </span>
      <h3>Selecione uma Categoria</h3>
      <div class="icone--grade">
        <!-- ##### ICONES ##### -->
        <div class="icon" data-icon="fi-br-scissors">
          <h2>Beleza</h2>
          <i class="fi fi-br-scissors"> </i>
        </div>
        <div class="icon" data-icon="fi-br-scissors">
          <h2>Moradia</h2>
          <i class="fi fi-sr-home"> </i>
        </div>
        <div class="icon" data-icon="fi-br-scissors">
          <h2>Telefone</h2>
          <i class="fi fi-br-smartphone"> </i>
        </div>
        <div class="icon" data-icon="fi-br-scissors">
          <h2>Telefone</h2>
          <i class="fi fi-br-smartphone"> </i>
        </div>
        <div class="icon" data-icon="fi-sr-home">
          <i class="fi fi-sr-home"> </i>
        </div>
        <div class="icon" data-icon="fi-br-smartphone">
          <i class="fi fi-br-smartphone"> </i>
        </div>
        <div class="icon" data-icon="fi-sr-file-invoice-dollar">
          <i class="fi fi-sr-file-invoice-dollar"> </i>
        </div>
        <div class="icon" data-icon="fi-br-money-coin-transfer">
          <i class="fi fi-br-money-coin-transfer"> </i>
        </div>
        <div class="icon" data-icon="fi-ss-plane-alt">
          <i class="fi fi-ss-plane-alt"> </i>
        </div>
        <div class="icon" data-icon="fi-ss-bus-alt">
          <i class="fi fi-ss-bus-alt"> </i>
        </div>
        <div class="icon" data-icon="fi-ss-wrench-alt">
          <i class="fi fi-ss-wrench-alt"> </i>
        </div>
        <div class="icon" data-icon="fi-ss-car-mechanic">
          <i class="fi fi-ss-car-mechanic"> </i>
        </div>
        <div class="icon" data-icon="fi-sr-shopping-cart">
          <i class="fi fi-sr-shopping-cart"> </i>
        </div>
        <div class="icon" data-icon="fi-sr-wallet">
          <i class="fi fi-sr-wallet"> </i>
        </div>
        <div class="icon" data-icon="fi-sr-gamepad">
          <i class="fi fi-sr-gamepad"> </i>
        </div>
        <div class="icon" data-icon="fi-ss-hotdog">
          <i class="fi fi-ss-hotdog"> </i>
        </div>
        <div class="icon" data-icon="fi-sr-user-md">
          <i class="fi fi-sr-user-md"> </i>
        </div>
        <div class="icon" data-icon="fi-sr-dog-leashed">
          <i class="fi fi-sr-dog-leashed"> </i>
        </div>
        <div class="icon" data-icon="fi-sr-bone">
          <i class="fi fi-sr-bone"> </i>
        </div>
        <div class="icon" data-icon="fi-sr-cat">
          <i class="fi fi-sr-cat"> </i>
        </div>
        <div class="icon" data-icon="fi-sr-devices">
          <i class="fi fi-sr-devices"> </i>
        </div>
        <div class="icon" data-icon="fi-ss-book-alt">
          <i class="fi fi-ss-book-alt"> </i>
        </div>
        <div class="icon" data-icon="fi-sc-headphones">
          <i class="fi fi-sc-headphones"> </i>
        </div>
        <div class="icon" data-icon="fi-sc-music-alt">
          <i class="fi fi-sc-music-alt"> </i>
        </div>
        <div class="icon" data-icon="fi-sc-speaker">
          <i class="fi fi-sc-speaker"> </i>
        </div>
        <div class="icon" data-icon="fi-sc-microphone-alt">
          <i class="fi fi-sc-microphone-alt"> </i>
        </div>
      </div>
    </div>
  </div>
  <!-- Script Linkado -->
  <script src="../../js/conteudos/dashboard/addCategorias.js"></script>
  <script src="../../js/conteudos/dashboard/envioBtnAdd.js"></script>
  <script src="../../js/conteudos/dashboard/historicoUpdate.js"></script>
  <script src="../../js/conteudos/dashboard/openPopUp.js"></script>
</body>

</html>