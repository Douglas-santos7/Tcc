<?php
session_start();
include("../../config/database/conexao.php");
include("../../config/conteudos/calendario/funcoes.php");

// Verifica se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: ../../views/login/login.php"); // Redireciona para a página de login
  exit();
}

$userId = $_SESSION['user_id']; // ID do usuário logado

/*=================================
INICIO - FUNÇÕES DO BALANÇO TOTAL
===================================*/
// Consultar receitas e despesas
$queryReceitas = "SELECT SUM(valor) AS totalReceitas FROM transacoes WHERE tipo = 'receita' AND usuario_id = $userId";
$queryDespesas = "SELECT SUM(valor) AS totalDespesas FROM transacoes WHERE tipo = 'despesa' AND usuario_id = $userId";

$resultReceitas = mysqli_query($conn, $queryReceitas);
$resultDespesas = mysqli_query($conn, $queryDespesas);

// Extrair valores
$receitas = mysqli_fetch_assoc($resultReceitas)['totalReceitas'] ?? 0;
$despesas = mysqli_fetch_assoc($resultDespesas)['totalDespesas'] ?? 0;
$total = $receitas + $despesas;
$proporcaoReceitas = ($total > 0) ? ($receitas / $total) * 800 : 0; // Largura em pixels
$proporcaoDespesas = ($total > 0) ? ($despesas / $total) * 800 : 0; // Largura em pixels

// Calcular Balanço Total
$balanco = $receitas - $despesas;

/*=================================
FIM - FUNÇÕES DO BALANÇO TOTAL
===================================*/


/*=================================
INICIO - LÓGICA VENCIMENTO
===================================*/
// Consultar o próximo vencimento a partir de hoje
$queryVencimentos = "SELECT descricao, data_vencimento, valor, categoria
                     FROM vencimentos
                     WHERE usuario_id = $userId
                     AND status = 'Pendente'
                     AND data_vencimento >= CURDATE()
                     ORDER BY data_vencimento ASC
                     LIMIT 1";
$resultVencimentos = mysqli_query($conn, $queryVencimentos);

// Extrair o próximo vencimento
$vencimento = mysqli_fetch_assoc($resultVencimentos);

// Verifica se existe um vencimento
if ($vencimento) {
  // Se existir, armazene as informações
  $descricao = $vencimento['descricao'];
  $data_vencimento = $vencimento['data_vencimento'];
  $valor = $vencimento['valor'];
  $categoria = $vencimento['categoria'];
} else {
  // Caso não exista, defina valores padrão
  $descricao = "Sem vencimentos pendentes";
  $data_vencimento = "";
  $valor = 0;
  $categoria = "";
}

/*=================================
FIM- LÓGICA CALENDÁRIO
===================================*/


/*=================================
INICIO - LÓGICA CALENDÁRIO
===================================*/
// Função para traduzir o mês para português
function mesEmPortugues($data)
{
  $meses = [
    1 => 'Janeiro',
    2 => 'Fevereiro',
    3 => 'Março',
    4 => 'Abril',
    5 => 'Maio',
    6 => 'Junho',
    7 => 'Julho',
    8 => 'Agosto',
    9 => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro'
  ];
  return $meses[(int) date('m', strtotime($data))];
}
/*=================================
FIM - LÓGICA CALENDÁRIO
===================================*/


/*=================
INICIO - SELECIONAR CATEGORIAS
===================*/
$sql = "SELECT id, nome, icone FROM categorias"; // Seleciona id, nome e icone
$result = $conn->query($sql);

// Criando um array para armazenar as categorias
$categorias = [];

// Verifica se encontrou resultados
if ($result->num_rows > 0) {
  // Itera pelos resultados e armazena as categorias em um array
  while ($row = $result->fetch_assoc()) {
    $categorias[] = $row;
  }
} else {
  // Caso não existam categorias, o array será vazio
  $categorias = null; // Ou pode deixar como [] para manter a consistência
}
/*=================
FIM - SELECIONAR CATEGORIAS
===================*/

/*======================
ENVIO DO FORMULARIOS COM OS DADOS P/ BANCO
========================*/
// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nome = mysqli_real_escape_string($conn, $_POST['nome']);
  $valor = mysqli_real_escape_string($conn, $_POST['valor']);
  $categoria = mysqli_real_escape_string($conn, $_POST['categoria']);
  $tipo = mysqli_real_escape_string($conn, $_POST['tipo']);

  // Consultar o ícone da categoria selecionada
  $queryIcone = "SELECT icone FROM categorias WHERE id = ?";
  $stmtIcone = mysqli_prepare($conn, $queryIcone);
  mysqli_stmt_bind_param($stmtIcone, "i", $categoria);
  mysqli_stmt_execute($stmtIcone);
  mysqli_stmt_bind_result($stmtIcone, $icone);
  mysqli_stmt_fetch($stmtIcone);
  mysqli_stmt_close($stmtIcone);


  /*======================
  INICIO - ENVIO DE DADOS
  ========================*/

  // Formata o valor recebido
$valor = str_replace('.', '', $valor); // Remove pontos que representam milhar
$valor = str_replace(',', '.', $valor); // Converte a vírgula para ponto

// Agora $valor deve estar no formato correto para o MySQL (e.g., 1500.00)

// Insere os dados na tabela 'transacoes'
$sql = "INSERT INTO transacoes (nome, valor, categoria_id, tipo, usuario_id, icone)
        VALUES ('$nome', '$valor', '$categoria', '$tipo', $userId, '$icone')";

// Atualiza a página após inserção
if (mysqli_query($conn, $sql)) {
    // Redireciona para a mesma página após a inserção
    header("Location: " . $_SERVER['PHP_SELF']);
    exit(); // Saia para garantir que o script pare aqui
} else {
    echo "<script>alert('Erro ao salvar: " . mysqli_error($conn) . "');</script>";
}

}
/*======================
 FIM - ENVIO DE DADOS
 ========================*/


/*======================
 INICIO - CONSULTANDO HISTÓRICO
 ========================*/
// Consultar histórico recente de transações
$queryHistorico = "
    SELECT t.nome AS transacao_nome, t.valor, t.tipo, t.data, c.id AS categoria_id, c.nome AS categoria_nome, t.icone
    FROM transacoes t
    JOIN categorias c ON t.categoria_id = c.id
    WHERE t.usuario_id = ?
    ORDER BY t.data DESC
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
                <span class="data--historico">' . date('d/m/Y', strtotime($row['data'])) . '</span> <!-- Exibe a data -->
                <span class="valor--historico" style="color: ' . ($row['tipo'] === 'receita' ? 'green' : 'red') . ';">
                    R$ ' . number_format($row['valor'], 2, ',', '.') . ' <!-- Exibe o valor -->
                </span>
            </div>
        </li>';
  }
} else {
  $historicoItems .= '<li>Nenhuma transação recente encontrada.</li>';
}

// Fechando a declaração
mysqli_stmt_close($stmt);

/*======================
 FIM - CONSULTA HISTÓRICO
 ========================*/

/*======================
INICIO - LOGICA MENSAGEM DE SAUDAÇÃO
========================*/

// Lógica Mensagem saudação
date_default_timezone_set('America/Sao_Paulo');

// Obter a hora atual
$hora = date("H");

// Definir a saudação com base na hora
if ($hora >= 5 && $hora < 12) {
  $saudacao = "Bom dia";
} elseif ($hora >= 12 && $hora < 18) {
  $saudacao = "Boa tarde";
} else {
  $saudacao = "Boa noite";
}

/*======================
 FIM - LOGICA MENSAGEM DE SAUDAÇÃO
 ========================*/

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
        <span><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></span>
        <h1>Olá, <?php echo $saudacao . ' ' . $_SESSION['username']; ?>!</h1>
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
          <span>Balanço Total</span>
          <h1 id="balanco--valor--total">R$ <?php echo number_format($balanco, 2, ',', '.'); ?></h1>
        </div>
        <!-- Fim Lado Esquerdo do Card -->

        <!-- Lado Direito do Card -->
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
          <span>Histórico Recente</span>
          <button onclick="window.location.href='./(3) historico.html';">Ver tudo</button>
        </div>
        <!-- Histórico de Transações -->
        <div class="info--historico">
          <ul id="historicoList">
            <?php echo $historicoItems; // Itens do histórico serão exibidos aqui
            ?>
          </ul>
        </div>
        <div class="seta--pra--baixo"></div>
      </div>
      <!-- Fim Histórico Recente -->

      <!-- Card Receitas x Despesas -->
      <div class="card--receitasXdespesas">
        <!-- Lado Esquerdo do Card -->
        <div class="lado--esquerdo-rd">
          <span>Receitas x Despesas</span>
          <div class="grafico--receitasXdespesas">
            <div class="grafico--receitas" data-largura="<?php echo $proporcaoReceitas; ?>"></div>
            <div class="grafico--despesas" data-largura="<?php echo $proporcaoDespesas; ?>"></div>
          </div>
        </div>
        <!-- Informações e Filtro -->
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
      <!-- Fim Card Próximos Vencimentos -->

      <!-- Card Lembretes -->
      <div class="card--lembretes">
        <div class="header--card-l">
          <span class="titulo">Lembretes</span>
          <span class="descricao--lembrete">Moradia</span>
        </div>
        <div class="info--lembrete">
          <div class="detalhes--info">
            <span class="descricao--info">Pagar aluguel</span>
            <span class="valor--lembrete">$ 350,00</span>
          </div>
          <div class="status--info">
            <span>Em aberto</span>
            <input type="checkbox" name="status--checkbox" />
          </div>
        </div>
      </div>
      <!-- Fim Card Lembretes -->
    </div>
  </div>
  <!-- Fim Conteúdo -->

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

        <!-- Botão que abrirá o popup -->
        <label for="categoria">Categoria:</label>
        <button type="button" id="btn-selecionar-categoria">Selecionar Categoria</button>

        <!-- Input oculto para armazenar o ID da categoria selecionada -->
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
  <!-- FIM PopUp Adição de Item -->

 <!-- INICIO POP-UP SELECT DE CATEGORIAS -->
<div id="popup-categorias-unico" class="popup-categorias" style="display: none;">
  <div class="popup-categorias-conteudo">
    <span class="popup-categorias-close-btn" id="btn-fechar-popup-categorias">&times;</span>
    <h2 class="categoria-titulo">Selecionar uma categoria</h2>
    
    <!-- Botão de filtragem -->
    <button id="botao-filtro-categorias" class="btn-filtro-categorias">
      A-Z
    </button>
    
    <!-- LISTAGEM DAS CATEGORIAS -->
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
<!-- FIM POP-UP SELECT DE CATEGORIAS -->



<script>
    let ordemAtual = 'A-Z'; // Estado inicial

document.getElementById('botao-filtro-categorias').addEventListener('click', function() {
    const listaCategorias = document.getElementById('lista-categorias');
    const itens = Array.from(listaCategorias.querySelectorAll('.categoria-item-unico'));

    // Alternar o texto do botão
    if (ordemAtual === 'A-Z') {
        ordemAtual = 'Mais usadas';
        this.textContent = 'Mais usadas';
        //  ordenar as categorias com base nas mais usadas.
        itens.sort((a, b) => {
            return Math.random() - 0.5; // Aqui, deve ser a lógica para ordenar as mais usadas
        });
    } else {
        ordemAtual = 'A-Z';
        this.textContent = 'A-Z';
        // Ordenar as categorias em ordem alfabética
        itens.sort((a, b) => {
            const nomeA = a.querySelector('.categoria-nome').textContent.toLowerCase();
            const nomeB = b.querySelector('.categoria-nome').textContent.toLowerCase();
            return nomeA.localeCompare(nomeB);
        });
    }

    // Atualizar a lista de categorias no DOM
    listaCategorias.innerHTML = ''; // Limpar a lista
    itens.forEach(item => listaCategorias.appendChild(item)); // Adicionar novamente na nova ordem
});

</script>


  <script>
 function formatarMoeda(valor) {
    // Remove qualquer caractere que não seja número ou vírgula
    valor = valor.replace(/[^0-9,]/g, "");

    // Se houver uma vírgula, separa a parte inteira da parte decimal
    const partes = valor.split(',');
    let inteiro = partes[0].replace(/\./g, ""); // Remove os pontos da parte inteira
    let decimal = partes[1] ? partes[1].slice(0, 2) : ''; // Limita a parte decimal a 2 dígitos

    // Converte a parte inteira em formato monetário
    inteiro = inteiro.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1."); // Adiciona os pontos de milhar

    // Se a parte decimal estiver presente, adiciona-a
    return inteiro + (decimal ? ',' + decimal : '') || '0,00';
}

// Evento para capturar o valor enquanto o usuário digita
document.getElementById('valor').addEventListener('input', function () {
    let valorAtual = this.value;
    
    // Remove a formatação temporariamente para permitir digitação livre
    let valorNumerico = valorAtual.replace(/[^0-9,]/g, '');

    // Limita a parte decimal a 2 dígitos
    const partes = valorNumerico.split(',');
    if (partes[1] && partes[1].length > 2) {
        partes[1] = partes[1].slice(0, 2);
    }

    this.value = formatarMoeda(partes.join(',')); // Atualiza o campo com o valor formatado
});

// Formatar o valor ao perder o foco (quando o usuário sai do campo)
document.getElementById('valor').addEventListener('blur', function () {
    let valorAtual = this.value;
    if (valorAtual !== "") {
        this.value = formatarMoeda(valorAtual);
    }
});

// Formatar o valor ao focar (para mostrar o valor formatado)
document.getElementById('valor').addEventListener('focus', function () {
    let valorAtual = this.value;
    if (valorAtual !== "") {
        this.value = valorAtual.replace(/\./g, "").replace(",", "."); // Remove a formatação ao focar
    }
});


  </script>

  <script>
    /*======================
    Script | Select categorias
    ========================*/
    // Referências aos elementos
    const btnSelecionarCategoria = document.getElementById('btn-selecionar-categoria');
    const popupCategorias = document.getElementById('popup-categorias-unico');
    const btnFecharPopup = document.getElementById('btn-fechar-popup-categorias');
    const categoriaItems = document.querySelectorAll('.categoria-item-unico');
    const categoriaInput = document.getElementById('categoria-id');

    // Abrir o popup quando o botão for clicado
    btnSelecionarCategoria.addEventListener('click', function () {
      popupCategorias.style.display = 'flex';
    });

    // Fechar o popup ao clicar no botão de fechar
    btnFecharPopup.addEventListener('click', function () {
      popupCategorias.style.display = 'none';
    });

    // Fechar o popup ao clicar fora do conteúdo
    window.addEventListener('click', function (event) {
      if (event.target === popupCategorias) {
        popupCategorias.style.display = 'none';
      }
    });

    // Selecionar uma categoria e fechar o popup
    categoriaItems.forEach(function (item) {
      item.addEventListener('click', function () {
        const categoriaId = this.getAttribute('data-id');
        const categoriaNome = this.innerText;

        // Atualiza o texto do botão e o input escondido
        btnSelecionarCategoria.innerText = 'Categoria: ' + categoriaNome;
        categoriaInput.value = categoriaId;

        // Fecha o popup
        popupCategorias.style.display = 'none';
      });
    });
  </script>

  <script>
    /*======================
    Script | Btn PopUp Principal
    ========================*/
    // Captura o novo botão de abrir popup com o ícone
    const openPopupIcon = document.getElementById('btn--abrir--popup');
    const closePopupBtn = document.getElementById('close-btn');
    const popupContainer = document.getElementById('popup-container');

    // Abrir o popup ao clicar no ícone de adicionar
    openPopupIcon.addEventListener('click', function () {
      popupContainer.style.display = 'flex'; // Mostrar o popup
    });

    // Fechar o popup ao clicar no botão fechar
    closePopupBtn.addEventListener('click', function () {
      popupContainer.style.display = 'none'; // Esconder o popup
    });

    // Fechar o popup ao clicar fora dele
    window.addEventListener('click', function (event) {
      if (event.target === popupContainer) {
        popupContainer.style.display = 'none'; // Esconder o popup
      }
    });

    window.onload = function () {
      // Seleciona os elementos de receitas e despesas
      var receitas = document.querySelector('.grafico--receitas');
      var despesas = document.querySelector('.grafico--despesas');

      // Obtém o valor da largura a partir dos atributos de dados
      var larguraReceitas = receitas.getAttribute('data-largura');
      var larguraDespesas = despesas.getAttribute('data-largura');

      // Define a largura final, ativando a animação
      receitas.style.width = larguraReceitas + 'px';
      despesas.style.width = larguraDespesas + 'px';
    };
  </script>

</body>

</html>