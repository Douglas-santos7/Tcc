<?php
include("../../config/conteudos/dashboard/init.php"); // Inclui init.php que contém a lógica de inicialização

// Função para obter o saldo inicial do usuário
function obterSaldoInicial($conn, $userId)
{
    $query = "SELECT SUM(valor) AS total_saldo_inicial FROM transacoes WHERE usuario_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total_saldo_inicial'] ?? 0; // Retorna saldo inicial ou 0 se não houver
}

// Verifica se o formulário de saldo inicial foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['saldo_inicial'])) {
        // Converte o valor para o formato correto (de ',' para '.')
        $saldo_inicial = str_replace(',', '.', $_POST['saldo_inicial']);

        // Aqui você pode adicionar a lógica de salvar o saldo inicial no banco de dados, se necessário

        // Redirecionar após o envio do formulário para evitar reenvios
        header("Location: ../../../views/conteudos/dashboard.php");
        exit();
    }
} else {
    // Se o formulário não foi enviado, obter o saldo inicial existente
    $saldo_inicial = obterSaldoInicial($conn, $userId);
}

// Não é mais necessário calcular o balanço aqui, já que ele será calculado no dashboard.php
?>


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
