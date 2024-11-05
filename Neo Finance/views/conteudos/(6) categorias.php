<?php
// Iniciar a sessão
session_start();

// Incluir a conexão com o banco de dados
include('../../config/database/conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
  header("Location: ../login/login.php");
  exit();
}

$usuario_id = $_SESSION['user_id']; // Pega o ID do usuário logado da sessão

// Buscar todas as categorias do usuário logado
$query = "SELECT * FROM categorias WHERE usuario_id = ? AND excluida =  false";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Neo Finance - Categorias</title>
  <link rel="stylesheet" href="../../css/conteudos/categorias/categorias.css">
  <style>
    .modal {
      display: none;
      /* Garante que os modais estejam ocultos ao carregar a página */
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.6);
    }

    .modal-conteudo {
      background-color: var(--verde--vidro);
      padding: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      border-radius: 10px;
      width: 80%;
      font-family: var(--fonte-principal);
      backdrop-filter: blur(80px);
      border: 2px solid var(--cor--destaque-verde);
      animation: fadeIn 0.3s;
      max-width: 600px;
      max-height: 80%;
      overflow-y: auto;
      /* Permite rolagem se o conteúdo for muito grande */
      position: absolute;
      /* Altera de relative para absolute */
      top: 50%;
      /* Move o modal para o meio da tela */
      left: 50%;
      /* Move o modal para o meio da tela */
      transform: translate(-50%, -50%);
      /* Centraliza o modal */
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    .modal-conteudo label {
      font-size: 1rem;
      color: white;
    }

    .modal-conteudo h2 {
      text-align: center;
      margin-top: 30px;
      color: white;
      font-weight: bold;
    }

    .modal-conteudo h3 {
      text-align: center;
      font-size: 1.5rem;
      margin-top: 30px;
      color: red;
      border-radius: 12px;
      background-color: white;
      padding: 5px 15px;
      font-weight: bold;
    }

    .modal-conteudo p {
      font-size: 1.4rem;
      text-align: center;
      color: white;
    }

    .modal-conteudo .fechar {
      position: absolute;
      top: 10px;
      right: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 30px;
      height: 30px;
      border: none;
      cursor: pointer;
      color: white;
      font-size: 2.5rem;
      font-weight: bold;
    }

    .modal-conteudo .fechar:hover,
    .modal-conteudo .fechar:focus {
      color: red;
      text-decoration: none;
      cursor: pointer;
    }

    .modal-conteudo form {
      display: flex;
      flex-direction: column;
      width: 80%;
    }

    .modal-conteudo label {
      margin-bottom: 5px;
    }

    .modal-conteudo input[type="text"],
    .modal-conteudo input[type="radio"] {
      border-radius: 8px;
      margin-bottom: 10px;
      padding: 10px 5px;
      font-size: 1.1rem;
      outline: none;
      background-color: white;
      border: 2px solid var(--cor--destaque-verde);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
    }

    .espacamento {
      margin: 20px 0px;
    }

    .modal-conteudo {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 30px;
    }

    .modal-conteudo button {
      padding: 15px;
      background-color: #5cb85c;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-family: var(--fonte-principal);
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 12px;
      transition: background-color 0.3s, transform 0.2s;
    }

    .modal-conteudo button:hover {
      background-color: #4cae4f;
      transform: translateY(-2px);
    }

    .modal-conteudo button:disabled,
    .modal-conteudo button:disabled:hover {
      background-color: #aaa;
      cursor: not-allowed;
    }

    .modal-conteudo .container--icones {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      overflow-y: auto;
      /* Permite rolagem vertical na lista de ícones */
      max-height: 200px;
      /* Define uma altura máxima para a lista de ícones */
    }

    .modal-conteudo .icon {
      margin: 5px;
    }

    .modal-conteudo .icon input[type="radio"] {
      display: none;
    }

    .modal-conteudo .icon label {
      display: inline-block;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      cursor: pointer;
    }

    .modal-conteudo .icon input[type="radio"]:checked+label {
      background-color: #4CAF50;
      color: white;
    }

    .modal-conteudo .mensagem-sucesso,
    .modal-conteudo .mensagem-erro {
      margin-top: 10px;
      text-align: center;
      padding: 10px;
      border-radius: 5px;
    }

    .modal-conteudo .mensagem-sucesso {
      background-color: #d1fae5;
      color: #4CAF50;
    }

    .modal-conteudo .mensagem-erro {
      background-color: #f8d7da;
      color: #dc3545;
    }

    .excluir {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      margin: auto;
    }

    .excluir button {
      padding: 15px;
      width: 100%;
      background-color: #5cb85c;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-family: var(--fonte-principal);
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 12px;
      transition: background-color 0.3s, transform 0.2s;
    }

    .excluir button:hover {
      background-color: white;
      color: red;
      transform: translateY(-2px);
    }
  </style>
</head>

<body>
  <!-- Início do Header -->
  <div class="container--header">
    <header class="banner">
      <div class="titulo--banner">
        <img src="../../assets/icons/categoria--icon.svg" alt="categoria--icon" />
        <h1 id="bloquear-selecao">Categorias</h1>
      </div>
    </header>
  </div>
  <!-- Fim do Header -->

  <!-- Início do Conteúdo Principal -->
  <div class="container--conteudo">
    <!-- Botão de Adicionar -->
    <div class="adicionar--btn">
      <img src="../../assets/icons/add--icon.svg" alt="add--btn" onclick="abrirModalAdicionar()">
    </div>

    <!-- Início dos Cards de Categorias -->

    <div class="cards--categorias">
      <div class="scroll--categorias">
        <?php while ($row = $result->fetch_assoc()): ?>
          <!-- Card Individual de Categoria -->
          <div class="card--categoria">
            <!-- Lado Esquerdo do Card -->
            <div class="lado--esquerdo-card">
              <div class="icon--categoria">
                <!-- Exibir o ícone da categoria -->
                <i class="<?php echo htmlspecialchars($row['icone']); ?>"></i>
              </div>
              <div class="descricao--categoria">
                <span><?php echo htmlspecialchars($row['nome']); ?></span>
              </div>
            </div>
            <!-- Fim do Lado Esquerdo do Card -->

            <!-- Lado Direito do Card -->
            <div class="lado--direito-card">
              <div class="icon--apagar">
                <!-- Botão de apagar categoria -->
                <img src="../../assets/icons/lixeira--icon.svg" alt="icon--excluir" onclick="abrirModalExcluir('<?php echo htmlspecialchars($row['nome']); ?>', '<?php echo $row['id']; ?>')">
              </div>
              <div class="icon--editar">
                <!-- Botão de editar categoria -->
                <img src="../../assets/icons/lapis--icon.svg" alt="icon--editar" onclick='abrirModalEditar(<?php echo json_encode($row); ?>)'>
              </div>
            </div>
            <!-- Fim do Lado Direito do Card -->
          </div>
          <!-- Fim do Card Individual de Categoria -->
        <?php endwhile; ?>
      </div>
      <!-- Fim dos Cards de Categorias -->
    </div>
    <!-- Fim do Conteúdo Principal -->
  </div>


  <!-- Modal de Adicionar Categoria -->
  <div id="modalAdicionar" class="modal" style="display: none;">
    <div class="modal-conteudo">
      <span class="fechar" onclick="fecharModal('modalAdicionar')">&times;</span>
      <h2>Adicionar Categoria</h2>
      <form id="formAdicionar" method="POST" action="../../config/conteudos/categorias/adicionar_categoria.php">
        <label for="nomeAdicionar">Nome da Categoria:</label>
        <input type="text" id="nomeAdicionar" name="nome" required>

        <div class="espacamento"></div>
        <button type="button" id="botaoSelecionarIcone" onclick="toggleListaIconesAdicionar()">Selecionar Ícone</button>
        <div id="listaIconesAdicionar" class="container--icones lista-icones-adicionar" style="display: none;">
          <div class="container--icones">
            <div class="icon">
              <input type="radio" name="icone" value="fi-br-scissors" id="icon1Adicionar" required>
              <label for="icon1Adicionar"><i class="fi fi-br-scissors"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-home" id="icon2Adicionar" required>
              <label for="icon2Adicionar"><i class="fi fi-sr-home"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-br-smartphone" id="icon3Adicionar" required>
              <label for="icon3Adicionar"><i class="fi fi-br-smartphone"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-file-invoice-dollar" id="icon4Adicionar" required>
              <label for="icon4Adicionar"><i class="fi fi-sr-file-invoice-dollar"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-br-money-coin-transfer" id="icon5Adicionar" required>
              <label for="icon5Adicionar"><i class="fi fi-br-money-coin-transfer"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-ss-plane-alt" id="icon6Adicionar" required>
              <label for="icon6Adicionar"><i class="fi fi-ss-plane-alt"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-ss-bus-alt" id="icon7Adicionar" required>
              <label for="icon7Adicionar"><i class="fi fi-ss-bus-alt"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-ss-wrench-alt" id="icon8Adicionar" required>
              <label for="icon8Adicionar"><i class="fi fi-ss-wrench-alt"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-ss-car-mechanic" id="icon9Adicionar" required>
              <label for="icon9Adicionar"><i class="fi fi-ss-car-mechanic"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-shopping-cart" id="icon10Adicionar" required>
              <label for="icon10Adicionar"><i class="fi fi-sr-shopping-cart"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-wallet" id="icon11Adicionar" required>
              <label for="icon11Adicionar"><i class="fi fi-sr-wallet"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-gamepad" id="icon12Adicionar" required>
              <label for="icon12Adicionar"><i class="fi fi-sr-gamepad"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-ss-hotdog" id="icon13Adicionar" required>
              <label for="icon13Adicionar"><i class="fi fi-ss-hotdog"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-user-md" id="icon14Adicionar" required>
              <label for="icon14Adicionar"><i class="fi fi-sr-user-md"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-dog-leashed" id="icon15Adicionar" required>
              <label for="icon15Adicionar"><i class="fi fi-sr-dog-leashed"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-bone" id="icon16Adicionar" required>
              <label for="icon16Adicionar"><i class="fi fi-sr-bone"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-cat" id="icon17Adicionar" required>
              <label for="icon17Adicionar"><i class="fi fi-sr-cat"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-devices" id="icon18Adicionar" required>
              <label for="icon18Adicionar"><i class="fi fi-sr-devices"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-ss-book-alt" id="icon19Adicionar" required>
              <label for="icon19Adicionar"><i class="fi fi-ss-book-alt"></i></label>
            </div>
          </div>
        </div>
        <button type="submit">Adicionar</button>
      </form>
      <div id="mensagemSucesso" class="mensagem-sucesso" style="display: none;">
        Categoria adicionada com sucesso!
      </div>
      <div id="mensagemErro" class="mensagem-erro" style="display: none;">
        Ocorreu um erro ao adicionar a categoria. Por favor, tente novamente.
      </div>
    </div>
  </div>

  <div id="modalEditar" class="modal">
    <div class="modal-conteudo">
      <span class="fechar" onclick="fecharModal('modalEditar')">&times;</span>
      <h2>Editar Categoria</h2>
      <form id="formEditar" method="POST" action="../../config/conteudos/categorias/editar_categoria.php">
        <input type="hidden" name="id_categoria" id="id_categoria" required>
        <label for="nomeEditar">Nome da Categoria:</label>
        <input type="text" id="nomeEditar" name="nome" required>
        <div class="espacamento"></div>
        <label for="iconeEditar">Ícone da Categoria (opcional):</label>
        <button type="button" id="botaoSelecionarIconeEditar" onclick="toggleListaIconesEditar()">Selecionar Ícone</button>
        <input type="hidden" name="icone" id="iconeSelecionadoEditar">
        <div id="listaIconesEditar" class="container--icones" style="display: none;">
          <!-- Ícones -->
          <div class="container--icones">
            <div class="icon">
              <input type="radio" name="icone" value="fi-br-scissors" id="icon1">
              <label for="icon1"><i class="fi fi-br-scissors"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-home" id="icon2">
              <label for="icon2"><i class="fi fi-sr-home"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-br-smartphone" id="icon3">
              <label for="icon3"><i class="fi fi-br-smartphone"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-file-invoice-dollar" id="icon4">
              <label for="icon4"><i class="fi fi-sr-file-invoice-dollar"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-br-money-coin-transfer" id="icon5">
              <label for="icon5"><i class="fi fi-br-money-coin-transfer"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-ss-plane-alt" id="icon6">
              <label for="icon6"><i class="fi fi-ss-plane-alt"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-ss-bus-alt" id="icon7">
              <label for="icon7"><i class="fi fi-ss-bus-alt"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-ss-wrench-alt" id="icon8">
              <label for="icon8"><i class="fi fi-ss-wrench-alt"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-ss-car-mechanic" id="icon9">
              <label for="icon9"><i class="fi fi-ss-car-mechanic"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-shopping-cart" id="icon10">
              <label for="icon10"><i class="fi fi-sr-shopping-cart"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-wallet" id="icon11">
              <label for="icon11"><i class="fi fi-sr-wallet"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-gamepad" id="icon12">
              <label for="icon12"><i class="fi fi-sr-gamepad"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-ss-hotdog" id="icon13">
              <label for="icon13"><i class="fi fi-ss-hotdog"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-user-md" id="icon14">
              <label for="icon14"><i class="fi fi-sr-user-md"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-dog-leashed" id="icon15">
              <label for="icon15"><i class="fi fi-sr-dog-leashed"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-bone" id="icon16">
              <label for="icon16"><i class="fi fi-sr-bone"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-cat" id="icon17">
              <label for="icon17"><i class="fi fi-sr-cat"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-sr-devices" id="icon18">
              <label for="icon18"><i class="fi fi-sr-devices"></i></label>
            </div>
            <div class="icon">
              <input type="radio" name="icone" value="fi-ss-book-alt" id="icon19">
              <label for="icon19"><i class="fi fi-ss-book-alt"></i></label>
            </div>
          </div>
          <!-- Adicione mais ícones conforme necessário -->
        </div>

        <button type="submit">Salvar</button>
      </form>
    </div>
  </div>


  <!-- Modal de Exclusão -->
  <div id="modalExcluir" class="modal">
    <div class="modal-conteudo">
      <span class="fechar" onclick="fecharModal('modalExcluir')">&times;</span>
      <h2>Excluir Categoria !!!</h2>
      <p>Você tem certeza de que deseja excluir esta categoria?</p>
      <h3><strong id="nomeCategoriaExcluir">Nome da Categoria</strong></h3> <!-- Nome da categoria a ser excluída -->
      <form id="formExcluir" method="POST" action="../../config/conteudos/categorias/excluir_categoria.php">
        <input type="hidden" name="id_categoria" id="id_categoria_excluir" required>
        <div class="excluir">
          <button type="submit">Excluir</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function abrirModalAdicionar() {
      document.getElementById('modalAdicionar').style.display = 'block';
    }

    function abrirModalEditar(categoria) {

      document.getElementById('id_categoria').value = categoria.id;
      document.getElementById('nomeEditar').value = categoria.nome;
      document.querySelector(`input[name="icone"][value="${categoria.icone}"]`).checked = true;
      document.getElementById('modalEditar').style.display = 'block';
      console.log(categoria); // Verifique os dados da categoria
    }

    function fecharModal(modalId) {
      document.getElementById(modalId).style.display = 'none';
    }

    // Função para exibir/ocultar a lista de ícones no modal de adição
    function toggleListaIconesAdicionar() {
      var listaIconesAdicionar = document.getElementById('listaIconesAdicionar');
      listaIconesAdicionar.style.display = (listaIconesAdicionar.style.display === 'none' || listaIconesAdicionar.style.display === '') ? 'block' : 'none';
    }

    // Função para exibir/ocultar a lista de ícones no modal de edição
    function toggleListaIconesEditar() {
      var listaIconesEditar = document.getElementById('listaIconesEditar');
      listaIconesEditar.style.display = (listaIconesEditar.style.display === 'none' || listaIconesEditar.style.display === '') ? 'block' : 'none';
    }

    function abrirModalExcluir(nome, id) {
      document.getElementById('nomeCategoriaExcluir').innerText = nome;
      document.getElementById('id_categoria_excluir').value = id;
      document.getElementById('modalExcluir').style.display = 'block';
    }

    function selecionarIcone(icone) {
      document.getElementById('iconeSelecionadoEditar').value = icone; // Define o valor do campo oculto
    }

    function confirmarExclusao() {
      var categoriaId = document.getElementById('botaoConfirmarExcluir').getAttribute('data-id'); // Recupera o ID da categoria
      // Redireciona para a página de exclusão com o ID da categoria
      window.location.href = '../../config/conteudos/categorias/excluir_categoria.php?id=' + categoriaId;
    }
  </script>
</body>

</html>