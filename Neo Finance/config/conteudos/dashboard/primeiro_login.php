<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Neo Finance</title>
  <link rel="stylesheet" href="../../../css/conteudos/dashboard/primeiro_login.css">
</head>
<body>
  <!-- Popup de Boas-Vindas -->
  <div id="popup-bem-vindo-usuario" class="popup-container-bem-vindo">
    <div class="popup-bem-vindo">
      <h2>Bem-vindo ao Neo Finance, <?php echo $_SESSION['username']; ?></h2>
      <p>Adicione ou atualize seu saldo inicial.</p>

      <!-- Formulário para adicionar saldo inicial -->
      <form action="" method="POST">
        <label for="saldo_inicial">Saldo Inicial (R$):</label>
        <input type="text" name="saldo_inicial" id="saldo_inicial" required placeholder="0,00">
        <button type="submit">Adicionar Saldo</button>
      </form>

      <!-- Botão para fechar o popup -->
      <div class="close-btn-primeiroLogin" onclick="document.getElementById('popup-bem-vindo-usuario').style.display = 'none';">&times;</div>
    </div>
  </div>

  <script>
    // Exibir o popup automaticamente
    document.getElementById('popup-bem-vindo-usuario').style.display = 'block';

    function formatarMoeda(valor) {
      valor = valor.replace(/\D/g, ""); // Remove todos os caracteres que não são dígitos
      if (valor.length > 8) {
        valor = valor.slice(0, 8); // Limita a 8 caracteres (6 dígitos inteiros + 2 decimais)
      }
      let valorFormatado = (valor / 100)
        .toFixed(2) // Converte para decimal e fixa em 2 casas decimais
        .replace(".", ",") // Substitui o ponto decimal pela vírgula
        .replace(/\B(?=(\d{3})+(?!\d))/g, "."); // Adiciona pontos para os milhares
      return valorFormatado;
    }

    // Evento de digitação
    document.getElementById("saldo_inicial").addEventListener("input", function () {
      let valorAtual = this.value;
      this.value = formatarMoeda(valorAtual.replace(/\D/g, ""));
    });
  </script>
</body>
</html>
