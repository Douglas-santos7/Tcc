<?php
session_start();

// Incluir a conexão com o banco de dados
include('../../database/conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../views/login/login.php");
    exit();
}

$usuario_id = $_SESSION['user_id'];

// Verifica se o usuário existe na tabela 'users'
$queryCheckUser = "SELECT id FROM users WHERE id = ?";
$stmtCheckUser = $conn->prepare($queryCheckUser);
$stmtCheckUser->bind_param("i", $usuario_id);
$stmtCheckUser->execute();
$resultCheckUser = $stmtCheckUser->get_result();

if ($resultCheckUser->num_rows === 0) {
    // Usuário não encontrado, encerra o processo
    echo "Usuário inválido.";
    exit();
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $icone = $_POST['icone'];

    // Verifica se o nome da categoria já existe para o usuário logado
    $query = "SELECT * FROM categorias WHERE usuario_id = ? AND nome = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $usuario_id, $nome);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $erro = "Já existe uma categoria com esse nome.";
    } else {
        // Inserir a nova categoria
        $query = "INSERT INTO categorias (usuario_id, nome, icone) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iss", $usuario_id, $nome, $icone);

        if ($stmt->execute()) {
            header("Location: ../../../views/conteudos/(6) categorias.php");
            exit();
        } else {
            $erro = "Erro ao adicionar a categoria.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Categoria</title>
    <link rel="stylesheet" href="../../../css/conteudos/categorias/categorias.css">
</head>

<body>
    <h1>Adicionar Categoria</h1>

    <?php if (isset($erro)): ?>
        <p style="color: red;"><?php echo $erro; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="nome">Nome da Categoria:</label>
        <input type="text" id="nome" name="nome" required>

        <label>Ícone da Categoria:</label>
        <div class="container--icones">
            <div class="icon">
                <input type="radio" name="icone" value="fi-br-scissors" id="icon1" required>
                <label for="icon1"><i class="fi fi-br-scissors"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-sr-home" id="icon2" required>
                <label for="icon2"><i class="fi fi-sr-home"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-br-smartphone" id="icon3" required>
                <label for="icon3"><i class="fi fi-br-smartphone"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-sr-file-invoice-dollar" id="icon4" required>
                <label for="icon4"><i class="fi fi-sr-file-invoice-dollar"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-br-money-coin-transfer" id="icon5" required>
                <label for="icon5"><i class="fi fi-br-money-coin-transfer"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-ss-plane-alt" id="icon6" required>
                <label for="icon6"><i class="fi fi-ss-plane-alt"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-ss-bus-alt" id="icon7" required>
                <label for="icon7"><i class="fi fi-ss-bus-alt"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-ss-wrench-alt" id="icon8" required>
                <label for="icon8"><i class="fi fi-ss-wrench-alt"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-ss-car-mechanic" id="icon9" required>
                <label for="icon9"><i class="fi fi-ss-car-mechanic"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-sr-shopping-cart" id="icon10" required>
                <label for="icon10"><i class="fi fi-sr-shopping-cart"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-sr-wallet" id="icon11" required>
                <label for="icon11"><i class="fi fi-sr-wallet"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-sr-gamepad" id="icon12" required>
                <label for="icon12"><i class="fi fi-sr-gamepad"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-ss-hotdog" id="icon13" required>
                <label for="icon13"><i class="fi fi-ss-hotdog"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-sr-user-md" id="icon14" required>
                <label for="icon14"><i class="fi fi-sr-user-md"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-sr-dog-leashed" id="icon15" required>
                <label for="icon15"><i class="fi fi-sr-dog-leashed"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-sr-bone" id="icon16" required>
                <label for="icon16"><i class="fi fi-sr-bone"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-sr-cat" id="icon17" required>
                <label for="icon17"><i class="fi fi-sr-cat"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-sr-devices" id="icon18" required>
                <label for="icon18"><i class="fi fi-sr-devices"></i></label>
            </div>
            <div class="icon">
                <input type="radio" name="icone" value="fi-ss-book-alt" id="icon19" required>
                <label for="icon19"><i class="fi fi-ss-book-alt"></i></label>
            </div>
        </div>
        <button type="submit">Adicionar</button>
    </form>
</body>

</html>