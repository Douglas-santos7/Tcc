<?php
include '../../config/database/conexao.php';

// Supondo que você tenha uma forma de autenticar o usuário e obter o ID do usuário logado
session_start();
if (!isset($_SESSION['user_id'])) {
  echo "Usuário não autenticado.";
  exit();
}

$usuario_id = $_SESSION['user_id']; // Obtendo o ID do usuário logado

// Query para selecionar as categorias do usuário
$sql = "SELECT id, nome, icone FROM categorias WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Neo Finance - Categorias</title>
  <link rel="stylesheet" href="../../css/conteudos/categorias/categorias.css" />
  <link rel="stylesheet" href="../../css/conteudos/categorias/popUpCategoria.css">
</head>

<body>
  <div class="container--header">
    <header class="banner">
      <div class="titulo--banner">
        <h1>Categorias</h1>
        <img src="../../assets/icons/categoria--icon.svg" alt="categoria--icon" />
      </div>
    </header>
  </div>

  <div class="container--conteudo">
    <div class="adicionar--btn" onclick="abrirPopup()">
      <img src="../../assets/icons/add--icon.svg" alt="adicionar--btn" />
    </div>

    <!-- Início dos Cards de Categorias -->
    <div class="cards--categorias" id="cards-categorias">
      <?php
      if ($result->num_rows > 0) {
        // Exibe as categorias
        while ($row = $result->fetch_assoc()) {
          echo '<div class="card--categoria" data-id="' . $row["id"] . '">';
          echo '  <div class="icone--categoria"><i class="' . $row["icone"] . '"></i></div>';
          echo '  <div class="nome--categoria">' . $row["nome"] . '</div>';
          // Adicionando os ícones de editar e apagar com o atributo data-id
          echo '  <div class="icon--apagar" data-id="' . $row["id"] . '">';
          echo '    <img src="../../assets/icons/lixeira--icon.svg" alt="icon--lixeira" />';
          echo '  </div>';
          echo '  <div class="icon--editar" data-id="' . $row["id"] . '">';
          echo '    <img src="../../assets/icons/lapis--icon.svg" alt="icon--editar" />';
          echo '  </div>';
          echo '</div>';
        }
      } else {
        echo "<p>Nenhuma categoria encontrada.</p>";
      }
      $conn->close();
      ?>
    </div>
    <!-- Fim dos Cards de Categorias -->

    <div class="popup--categoria" id="id--popup--categoria">
      <div class="popup--categoria--conteudo">
        <span class="fecha--btn--categoria" id="id--fecha--btn--categoria">&times;</span>
        <h2>Adicionar nova categoria</h2>
        <div class="box--categoria--nome">
          <div class="box--categoria--nome--item">
            <label for="nome--categoria">Nome</label>
            <div class="input--categoria">
              <input type="text" placeholder="Digite a nova categoria" id="id--nova--categoria" required>
            </div>
          </div>
        </div>
        <div class="box--icone--categoria">
          <button class="selecione-icone" id="selecione-icone">Selecione um ícone</button>
          <div class="icone--grade" id="icone-grade" style="display: none;">
            <div class="icon" data-icon="fi-sr-home"><i class="fi fi-sr-home"></i></div>
            <div class="icon" data-icon="fi-br-smartphone"><i class="fi fi-br-smartphone"></i></div>
            <div class="icon" data-icon="fi-sr-file-invoice-dollar"><i class="fi fi-sr-file-invoice-dollar"></i></div>
            <div class="icon" data-icon="fi-br-money-coin-transfer"><i class="fi fi-br-money-coin-transfer"></i></div>
            <div class="icon" data-icon="fi-ss-plane-alt"><i class="fi fi-ss-plane-alt"></i></div>
          </div>
        </div>
        <input type="hidden" id="icone-selecionado" value="">
        <button class="enviar--nova--categoria" id="enviar-nova-categoria">Enviar</button>
      </div>
    </div>
  </div>

  <!-- INICIO | POPUP EXCLUIR -->
  <div class="popup--excluir" id="id--pop--up--excluir">
    <div class="popup--excluir--conteudo">
      <span class="fecha--btn--excluir" id="id--fecha--popup--excluir">&times;</span>
      <h1>Deseja realmente excluir essa categoria?</h1>
      <div class="checkbox--nao--perguntar">
        <input type="checkbox" id="nao--perguntar--novamente" name="nao--perguntar--novamente">
        <label for="nao--perguntar--novamente">Não perguntar novamente</label>
      </div>
      <div class="acoes--popup--excluir">
        <button class="btn--confirmar" id="confirmar--exclusao">SIM</button>
        <button class="btn--cancelar" id="cancelar--exclusao">NÃO</button>
      </div>
    </div>
  </div>
  <!-- FIM | POPUP EXCLUIR -->

  <!-- INICIO | POPUP EDITAR -->
  <div class="popup--editar" id="id--popup--editar">
    <div class="popup--editar--conteudo">
      <span class="fecha--btn--editar" id="id--fecha--popup--editar">&times;</span>
      <h1>Editar Categorias</h1>
      <div class="box--select--categorias">
      </div>
      <div class="area--edicao">
        <label for="edicao--nova--categoria">Editar Nome:</label>
        <input type="text" id="edicao--nova--categoria" class="novoNome--categoria" placeholder="Digite o novo nome...">
        <div class="edicao--icone">
          <label for="edicao--novo--icone">Editar Ícone:</label>
          <button id="abrir--selecao-icones" type="button" class="btn--selecao">Selecione o Ícone</button>
        </div>
      </div>
      <div class="popup--editar--botoes">
        <button class="btn--confirmar--edicao">Salvar</button>
        <button class="btn--cancelar--edicao" id="id--fecha--popup--editar">Cancelar</button>
      </div>
    </div>
  </div>
  <!-- FIM | POPUP EDITAR -->
  <!-- FIM | ÁREA DOS POPUPS -->


  <script src="../../js/conteudos/categorias/popups_categoria.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const botaoAdicionar = document.querySelector('.adicionar--btn');
      botaoAdicionar.addEventListener('click', abrirPopup);

      const icones = document.querySelectorAll('.icon');
      icones.forEach(icon => {
        icon.addEventListener('click', function() {
          document.getElementById('icone-selecionado').value = this.getAttribute('data-icon');
          document.getElementById('icone-grade').style.display = 'none';
        });
      });

      document.getElementById('enviar-nova-categoria').addEventListener('click', async () => {
        const nomeCategoria = document.getElementById('id--nova--categoria').value;
        const iconeSelecionado = document.getElementById('icone-selecionado').value;
        const usuarioId = <?php echo $usuario_id; ?>; // Passando o ID do usuário para o JavaScript

        if (!nomeCategoria) {
          alert('Por favor, preencha o nome da categoria.');
          return;
        }
        if (!iconeSelecionado) {
          alert('Por favor, selecione um ícone.');
          return;
        }

        const response = await fetch('../../config/conteudos/categorias/adicionar_categoria.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            nome: nomeCategoria,
            icone: iconeSelecionado,
            usuario_id: usuarioId // Enviando o ID do usuário na requisição
          }),
        });

        const responseText = await response.text();
        try {
          const result = JSON.parse(responseText);
          if (result.status === 'success') {
            alert('Categoria adicionada com sucesso!');
            // Aqui você pode adicionar lógica para atualizar a lista de categorias
          } else {
            alert('Erro ao adicionar categoria: ' + result.message);
          }
        } catch (error) {
          console.error('Erro ao analisar JSON:', error);
          alert('Erro inesperado ao adicionar categoria. Resposta do servidor: ' + responseText);
        }
      });


    });
  </script>
  <script>
    /*=====================
   EXCLUINDO CATEGORIA
=========================*/

    document.querySelectorAll('.icon--apagar').forEach((icon) => {
      icon.addEventListener('click', function() {
        // Armazena o ID da categoria a ser excluída
        const categoriaId = this.getAttribute('data-id');
        // Abre o popup de confirmação
        document.getElementById('id--pop--up--excluir').style.display = 'block';

        // Quando o botão SIM for clicado, envia a exclusão ao PHP
        document.getElementById('confirmar--exclusao').onclick = function() {
          excluirCategoria(categoriaId);
        };
      });
    });

    // Fechar o popup
    document.getElementById('id--fecha--popup--excluir').onclick = function() {
      document.getElementById('id--pop--up--excluir').style.display = 'none';
    };

    function excluirCategoria(categoriaId) {
      // Envia o ID da categoria para o backend via AJAX
      fetch('../../config/conteudos/categorias/excluir_categoria.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          // O body deve estar formatado corretamente como string
          body: `id=${categoriaId}`
        })
        .then(response => response.text())
        .then(data => {
          // Fecha o popup após a exclusão
          document.getElementById('id--pop--up--excluir').style.display = 'none';
          // Remove a categoria da página
          document.querySelector(`[data-id='${categoriaId}']`).closest('.card--categoria').remove();
        })
        .catch(error => console.error('Erro ao excluir categoria:', error));
    }
  </script>
</body>

</html>