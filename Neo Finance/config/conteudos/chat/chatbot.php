<?php
include("../../database/conexao.php"); // Inclui a conexão com o banco de dados


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim(strtolower($_POST['message'])); // Normaliza a mensagem para minúsculas
    $userId = 1; // Defina aqui o ID do usuário atual, isso pode ser passado pela sessão ou autenticação


    // Obtendo o nome do usuário
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();




    $responses = [
        'oi' => function () use ($username) { return gerarRespostaSaudacao($username, 'oi'); },
        'ola' => function () use ($username) { return gerarRespostaSaudacao($username, 'ola'); },
        'bom dia' => function () use ($username) { return gerarRespostaSaudacao($username, 'bom dia'); },
        'boa tarde' => function () use ($username) { return gerarRespostaSaudacao($username, 'boa tarde'); },
        'boa noite' => function () use ($username) { return gerarRespostaSaudacao($username, 'boa noite'); },
        'saldo' => function () use ($conn, $userId) { return getSaldo($conn, $userId); },
        '1' => function () use ($conn, $userId) { return getSaldo($conn, $userId); },
        'economizar' => function () use ($conn, $userId) { return implode("\n", getDicasEconomizar($conn, $userId)); },
        '2' => function () use ($conn, $userId) { return implode("\n", getDicasEconomizar($conn, $userId)); },
        'investimento' => function () use ($conn, $userId) { return implode("\n", getDicasInvestir($conn, $userId)); },
        '3' => function () use ($conn, $userId) { return implode("\n", getDicasInvestir($conn, $userId)); },
        'resumo mensal' => function () use ($conn, $userId) { return getResumoMensal($conn, $userId); },
        '4' => function () use ($conn, $userId) { return getResumoMensal($conn, $userId); },
        'resumo diário' => function () use ($conn, $userId) { return getResumoDiario($conn, $userId); },
        '5' => function () use ($conn, $userId) { return getResumoDiario($conn, $userId); },
        'historico' => function () use ($conn, $userId) { return getHistoricoTransacoes($conn, $userId); },
        '6' => function () use ($conn, $userId) { return getHistoricoTransacoes($conn, $userId); },
        'análise' => function () use ($conn, $userId) { return getAnaliseGastos($conn, $userId); },
        '7' => function () use ($conn, $userId) { return getAnaliseGastos($conn, $userId); },
        'exportar' => function () use ($conn, $userId) { return exportarRelatorio($conn, $userId); },
        '8' => function () use ($conn, $userId) { return exportarRelatorio($conn, $userId); },
        'previsão financeira' => function () use ($conn, $userId) { return previsaoFinanceira($conn, $userId); },
        '9' => function () use ($conn, $userId) { return previsaoFinanceira($conn, $userId); },
        'comparação' => function () use ($conn, $userId) { return comparacaoGastosMensais($conn, $userId); },
        '10' => function () use ($conn, $userId) { return comparacaoGastosMensais($conn, $userId); },
        'desafio' => function () use ($conn, $userId) { return obterDesafioFinanceiroAleatorio($conn, $userId); },
        '11' => function () use ($conn, $userId) { return obterDesafioFinanceiroAleatorio($conn, $userId); }, 
        'planejamento mensal' => function () use ($conn, $userId) { return planejamentoMensal($conn, $userId); },
        '12' => function () use ($conn, $userId) { return planejamentoMensal($conn, $userId); },
        'obrigado' => 'De nada! Se precisar de mais ajuda, estou aqui.',
        'valeu' => 'De nada! Se precisar de mais ajuda, estou aqui.',
        'ajuda' => 'Claro! Estou aqui para ajudar com questões financeiras. O que você gostaria de saber?',
        'como funciona' => 'Posso ajudá-lo a entender orçamentos, investimentos e muito mais!'
    ];


    // Função para gerar a resposta de saudação
    function gerarRespostaSaudacao($username, $saudacao)
    {
        // Personaliza a resposta com base na saudação
        switch (strtolower($saudacao)) {
            case 'bom dia':
                $mensagem = "Bom dia, $username! Como posso ajudar você com suas finanças?\n";
                break;
            case 'boa tarde':
                $mensagem = "Boa tarde, $username! Como posso ajudar você com suas finanças?\n";
                break;
            case 'boa noite':
                $mensagem = "Boa noite, $username! Como posso ajudar você com suas finanças?\n";
                break;
            case 'oi':
            case 'ola':
                $mensagem = "Olá, $username! Como posso ajudar você com suas finanças?\n";
                break;
            default:
                $mensagem = "Olá, $username! Como posso ajudar você com suas finanças?\n";
                break;
        }


        // Retorna as opções de ajuda
        return $mensagem . "Escolha uma das opções abaixo:\n" .
            "1. Saldo\n" .
            "2. Dicas de Economia\n" .
            "3. Dicas de Investimento\n" .
            "4. Resumo Mensal\n" .
            "5. Resumo Diário\n" .
            "6. Histórico de Transações\n" .
            "7. Análise de Gastos\n" .
            "8. Exportar Relatório\n" .
            "9. Previsão Financeira com Base no Histórico\n" .
            "10. Comparação de Gastos Mensais\n" .
            "11. Desafios\n" .
            "12. Planejamento Mensal\n";
    }


    // Mapeamento de sinônimos para palavras-chave
    $synonyms = [
      'saldo' => [
    'quanto tenho', 'meu saldo', 'saldo atual', 'quanto há na conta',
    'valor disponível', 'dinheiro disponível', 'quanto posso gastar',
    'saldo da conta', 'quanto resta', 'quantos reais tenho',
    'saldo bancário', 'meu dinheiro'
],
'economizar' => [
    'dicas de economia', 'como economizar', 'formas de poupar', 'como poupar',
    'dicas para gastar menos', 'reduzir despesas', 'guardar dinheiro',
    'melhorar finanças', 'cortar gastos', 'dicas financeiras',
    'economizar dinheiro', 'gastar menos', 'dicas para poupar'
],
'investimento' => [
    'dicas de investimento', 'como investir', 'onde investir',
    'investir dinheiro', 'boas práticas de investimento', 'o que é um bom investimento',
    'melhores investimentos', 'dicas para crescer dinheiro', 'aplicações financeiras',
    'investir com segurança', 'investimentos fáceis', 'como aplicar dinheiro'
],
'resumo mensal' => [
    'meu resumo mensal', 'resumo do mês', 'desempenho mensal', 'dados do mês',
    'balanço mensal', 'relatório mensal', 'análise do mês', 'fechamento do mês',
    'gastos mensais', 'lucros e despesas do mês', 'resumo financeiro do mês'
],
'resumo diário' => [
    'meu resumo diário', 'resumo de hoje', 'balanço de hoje', 'dados do dia',
    'relatório diário', 'movimentações do dia', 'resumo financeiro diário',
    'o que gastei hoje', 'gastos do dia', 'ganhos de hoje', 'atividade do dia'
],
'ajuda' => [
    'preciso de ajuda', 'me ajuda', 'como faço isso', 'dúvida', 'não sei usar',
    'socorro', 'me explique isso', 'tenho uma dúvida', 'ajude-me',
    'o que fazer', 'preciso de suporte', 'não entendi', 'me oriente'
],
'como funciona' => [
    'como isso funciona', 'me explique', 'como utilizar', 'como operar',
    'modo de uso', 'me mostra como', 'para que serve', 'o que isso faz',
    'como usar', 'explica o funcionamento', 'detalhes sobre isso',
    'como funciona o sistema'
],
'oi' => [
    'oi', 'olá', 'olá tudo bem', 'bom dia', 'boa tarde', 'boa noite',
    'e aí', 'alô', 'tudo bem', 'como vai', 'opa', 'oi tudo certo',
    'saudações', 'hey', 'oii', 'fala ai'
],


    ];


    // Lista de tópicos permitidos
    $allowedTopics = [
        'Saldo',
        'Categorias',
        'Dicas de Economia',
        'Dicas de Investimento',
        'Resumo Mensal',
        'Resumo Diário',
        'Histórico de Transações',
        'Análise de Gastos',
        'Exportar Relatório',
        'Previsão Financeira com Base no Histórico',
        'Comparação de Gastos Mensais',
        'Desafios',
        'Planejamento Mensal'
    ];


    // Gera a lista de tópicos como uma string
    $topics = implode(', ', $allowedTopics);


    // Inicializa variáveis de controle
    $responseFound = false;
    $responseMessage = '';


    // Verifica se a mensagem contém algum dos sinônimos
    foreach ($synonyms as $key => $synonymList) {
        foreach ($synonymList as $synonym) {
            if (strpos($message, $synonym) !== false) {
                if (array_key_exists($key, $responses)) {
                    $response = $responses[$key];
                    $responseMessage = is_callable($response) ? $response() : $response;
                    $responseFound = true;
                    break 2; // Break out of both loops
                }
            }
        }
    }


    // Se não encontrou uma correspondência, verifica diretamente por palavras-chave
    if (!$responseFound && isset($responses[$message])) {
        $response = $responses[$message];
        $responseMessage = is_callable($response) ? $response() : $response;
        $responseFound = true;
    }


    // Resposta padrão caso não tenha encontrado uma correspondência
    if (!$responseFound) {
        // Concatena os tópicos na resposta padrão
        $responseMessage = 'Desculpe, não entendi. Podemos falar sobre os seguintes assuntos: ' . $topics . '.';
    }


    // Armazena a mensagem e resposta no histórico
    $stmt = $conn->prepare("INSERT INTO historico_conversas (user_id, mensagem, resposta) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $message, $responseMessage);
    $stmt->execute();
    $stmt->close();


    // Retorna a resposta ao usuário
    echo $responseMessage;
}


// Função para obter saldo do usuário
function getSaldo($conn, $userId)
{
    // Somar receitas
    $stmtReceitas = $conn->prepare("SELECT SUM(valor) FROM transacoes WHERE usuario_id = ? AND tipo = 'receita'");
    $stmtReceitas->bind_param("i", $userId);
    $stmtReceitas->execute();
    $stmtReceitas->bind_result($totalReceitas);
    $stmtReceitas->fetch();
    $stmtReceitas->close();


    // Somar despesas
    $stmtDespesas = $conn->prepare("SELECT SUM(valor) FROM transacoes WHERE usuario_id = ? AND tipo = 'despesa'");
    $stmtDespesas->bind_param("i", $userId);
    $stmtDespesas->execute();
    $stmtDespesas->bind_result($totalDespesas);
    $stmtDespesas->fetch();
    $stmtDespesas->close();


    // Calcular saldo
    $saldo = ($totalReceitas ?? 0) - ($totalDespesas ?? 0);


    // Verificando se o saldo é 0
    if ($saldo == 0) {
        return "Parece que você não tem saldo registrado. Que tal adicionar uma receita?";
    }


    return "Seu saldo atual é R$ " . number_format($saldo, 2, ',', '.');
}


// Função para obter dicas de economia com base no saldo do usuário
function getDicasEconomizar($conn, $userId)
{
    // Obter o saldo atual do usuário
    $stmt = $conn->prepare("SELECT SUM(valor) FROM transacoes WHERE usuario_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($saldo);
    $stmt->fetch();
    $stmt->close();


    // Retornar dicas com base no saldo
    if ($saldo < 100) {
        return [
            "Tente reduzir gastos supérfluos, como refeições em restaurantes ou lanches. Que tal cozinhar mais em casa?",
            "Considere fazer compras com uma lista. Isso pode ajudar a evitar compras por impulso, especialmente em produtos que você não precisa.",
            "Pesquise sobre ofertas e cupons antes de fazer compras. Existem muitos sites e aplicativos que podem ajudar.",
            "Avalie a possibilidade de vender itens que você não usa mais. Isso pode gerar uma renda extra e liberar espaço em casa."
        ];
    } elseif ($saldo < 500) {
        return [
            "Você pode usar aplicativos de comparação de preços para economizar nas compras. Isso pode fazer uma grande diferença no final do mês.",
            "Avalie suas despesas mensais e veja onde pode cortar custos. Por exemplo, você pode cancelar assinaturas que não usa com frequência.",
            "Tente negociar melhores tarifas com provedores de serviços, como internet e telefone. Muitas vezes, é possível conseguir um desconto.",
            "Considere realizar um orçamento mensal. Isso pode ajudá-lo a ter uma visão clara de onde seu dinheiro está sendo gasto."
        ];
    } else {
        return [
            "Parabéns! Com esse saldo, você pode começar a pensar em investir uma parte do seu dinheiro. Você já considerou investir em ações ou fundos de investimento?",
            "Tente reservar uma parte do seu saldo para uma poupança de emergência. Isso pode ajudá-lo a se sentir mais seguro financeiramente no futuro.",
            "Considere diversificar seus investimentos para minimizar riscos. Pense em alocar uma parte em renda fixa e outra em renda variável.",
            "Pesquise sobre opções de investimentos com rendimentos a longo prazo, como previdência privada ou imóveis, para garantir um futuro mais seguro."
        ];
    }
}


// Função para obter dicas de investimento com base no valor do usuário
function getDicasInvestir($conn, $userId)
{
    // Consulta para obter o saldo ou valor total de investimentos do usuário
    $stmt = $conn->prepare("SELECT SUM(valor) FROM transacoes WHERE usuario_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($saldo);
    $stmt->fetch();
    $stmt->close();


    // Dicas com base no saldo do usuário
    if ($saldo < 1000) {
        return [
            "Considere investir em uma conta de poupança com rendimento.",
            "Pesquise sobre tesouro direto, que é uma forma segura de investimento.",
            "Considere iniciar com fundos de investimento de baixo custo.",
            "Avalie a possibilidade de participar de programas de fidelidade que oferecem benefícios em suas compras.",
            "Busque cursos gratuitos ou de baixo custo sobre finanças pessoais e investimentos para aumentar seu conhecimento."
        ];
    } elseif ($saldo < 5000) {
        return [
            "Diversifique seus investimentos em ações e títulos.",
            "Pesquise sobre ETFs, que podem oferecer uma boa diversificação.",
            "Considere um fundo de investimento que se alinhe aos seus objetivos.",
            "Explore a possibilidade de abrir uma conta em uma corretora que oferece isenção de taxas para novos clientes.",
            "Acompanhe regularmente o desempenho de seus investimentos e faça ajustes quando necessário."
        ];
    } else {
        return [
            "Considere investir em ações de empresas sólidas.",
            "Pesquise sobre imóveis para investimento.",
            "Explore investimentos em startups ou crowdfunding.",
            "Avalie a criação de uma carteira de investimentos que inclua ativos de diferentes setores.",
            "Considere consultar um assessor financeiro para otimizar sua estratégia de investimentos e garantir que esteja alinhada aos seus objetivos.",
            "Compare as taxas e rendimentos de diferentes tipos de investimentos antes de decidir onde aplicar seu dinheiro." // Dica adicional para comparação de investimentos alternativos.
        ];
    }
}




// Função para obter resumo mensal
function getResumoMensal($conn, $userId)
{
    $stmt = $conn->prepare("SELECT SUM(valor) FROM transacoes WHERE usuario_id = ? AND MONTH(data) = MONTH(CURRENT_DATE())");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($totalMensal);
    $stmt->fetch();
    $stmt->close();


    return "Seu resumo mensal total é R$ " . number_format($totalMensal, 2, ',', '.');
}


// Função para obter resumo diário
function getResumoDiario($conn, $userId)
{
    // Prepara a consulta SQL para somar os valores das transações do usuário no dia atual
    $stmt = $conn->prepare("SELECT SUM(valor) FROM transacoes WHERE usuario_id = ? AND DATE(data) = CURDATE()");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($totalDiario);
    $stmt->fetch();
    $stmt->close();


    // Verifica se o total diário é NULL (não houve transações no dia)
    if ($totalDiario === null) {
        $totalDiario = 0; // Define como 0 caso não haja transações no dia
    }


    // Formata o total diário, incluindo a possibilidade de ser negativo
    $totalFormatado = number_format($totalDiario, 2, ',', '.');


    // Se o valor for negativo, adicionar o sinal de negativo na frente
    if ($totalDiario < 0) {
        $totalFormatado = "-" . $totalFormatado;
    }


    // Retorna o resumo diário
    return "Seu resumo diário total é R$ " . $totalFormatado;
}




function getHistoricoTransacoes($conn, $userId)
{
    $stmt = $conn->prepare("SELECT tipo, valor, data FROM transacoes WHERE usuario_id = ? ORDER BY data DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($tipo, $valor, $data);


    $historico = [];
    while ($stmt->fetch()) {
        $historico[] = "$data - $tipo: R$ " . number_format($valor, 2, ',', '.');
    }
    $stmt->close();


    if (empty($historico)) {
        return "Você não tem transações registradas.";
    }
    return implode("\n", $historico);
}


function getAnaliseGastos($conn, $userId)
{
    $stmt = $conn->prepare("SELECT c.nome, SUM(t.valor) AS total FROM transacoes t JOIN categorias c ON t.categoria_id = c.id WHERE t.usuario_id = ? GROUP BY c.nome");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($categoria, $total);


    $analise = [];
    while ($stmt->fetch()) {
        $analise[] = "$categoria: R$ " . number_format($total, 2, ',', '.');
    }
    $stmt->close();


    if (empty($analise)) {
        return "Nenhum gasto registrado.";
    }
    return implode("\n", $analise);
}


function exportarRelatorio($conn, $userId)
{
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();


    // Obter histórico de transações
    $historico = getHistoricoTransacoes($conn, $userId);


    // Verifica se o histórico não está vazio
    if (empty($historico)) {
        return "Nenhum dado disponível para exportar.";
    }


    // Cria um arquivo temporário em memória
    $tempFile = tempnam(sys_get_temp_dir(), 'relatorio_') . ".txt";
    file_put_contents($tempFile, $historico);


    // Montar a URL completa para download
    $urlArquivo = "http://localhost/chat/" . basename($tempFile);


    // Criar um link para o download
    $html = "Relatório gerado. Você pode <a href='$urlArquivo' download>baixar aqui</a>.";


    return $html;
}






function previsaoFinanceira($conn, $userId, $meses = 3)
{
    $stmt = $conn->prepare("
        SELECT tipo, AVG(valor) AS media
        FROM transacoes
        WHERE usuario_id = ?
        GROUP BY tipo
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($tipo, $media);


    $previsao = [];
    while ($stmt->fetch()) {
        $previsao[$tipo] = $media;
    }
    $stmt->close();


    $gastosEstimados = isset($previsao['despesa']) ? $previsao['despesa'] * $meses : 0;
    $receitasEstimadas = isset($previsao['receita']) ? $previsao['receita'] * $meses : 0;
    $saldoEstimado = $receitasEstimadas - $gastosEstimados;


    return
        "Previsão para os próximos $meses meses:\n" .
        "Receitas: R$ " . number_format($receitasEstimadas, 2, ',', '.') . "\n" .
        "Gastos: R$ " . number_format($gastosEstimados, 2, ',', '.') . "\n" .
        "Saldo Estimado: R$ " . number_format($saldoEstimado, 2, ',', '.');
}
function comparacaoGastosMensais($conn, $userId)
{
    $stmt = $conn->prepare("
    SELECT DATE_FORMAT(data, '%M de %Y') AS mes, SUM(valor) AS total
    FROM transacoes
    WHERE usuario_id = ? AND tipo = 'despesa'
    GROUP BY YEAR(data), MONTH(data)
    ORDER BY data DESC
    LIMIT 2
");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($mes, $total);


    $gastos = [];
    while ($stmt->fetch()) {
        $gastos[] = ['mes' => $mes, 'total' => $total];
    }
    $stmt->close();


    if (count($gastos) < 2) {
        return "Dados insuficientes para comparação.";
    }


    $diferenca = $gastos[0]['total'] - $gastos[1]['total'];
    $resultado = $diferenca >= 0 ? "aumento" : "redução";


    return
        "Comparação de Gastos:\n" .
        "{$gastos[0]['mes']}: R$ " . number_format($gastos[0]['total'], 2, ',', '.') . "\n" .
        "{$gastos[1]['mes']}: R$ " . number_format($gastos[1]['total'], 2, ',', '.') . "\n" .
        "Diferença: R$ " . number_format(abs($diferenca), 2, ',', '.') . " ($resultado).";
}


function obterDesafioFinanceiroAleatorio($conn, $userId)
{
    $desafios = [
        "Economize R$ 100 em um mês",
        "Reduza suas despesas com alimentação em 20% neste mês",
        "Economize R$ 50 em compras de supermercado",
        "Tente não gastar nada por uma semana",
        "Use transporte público em vez de carro por uma semana",
        "Desafie-se a cancelar uma assinatura mensal que você não usa",
        "Compre apenas itens essenciais por um mês",
        "Estabeleça um limite de gastos e siga-o por 30 dias",
        "Aproveite promoções e compre apenas produtos em oferta durante um mês",
        "Tente cozinhar em casa ao invés de comer fora por uma semana",
        "Monitore seus gastos diários em um aplicativo por um mês",
        "Desafie-se a economizar R$ 200 em um mês e veja como se sai",
        "Crie um orçamento mensal e cumpra-o rigorosamente",
        "Evite compras por impulso, e faça uma lista antes de ir ao mercado",
        "Participe de um workshop de finanças pessoais online",
        "Faça um dia sem gastar nada por uma semana e anote suas reflexões",
        "Estabeleça um desafio de poupança com um amigo e compare resultados"
    ];


    $desafioAleatorio = $desafios[array_rand($desafios)];
    return $desafioAleatorio;
}


function planejamentoMensal($conn, $userId)
{
    // Obtém o saldo atual do usuário
    $stmt = $conn->prepare("SELECT saldo FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($saldoAtual);
    $stmt->fetch();
    $stmt->close();


    if ($saldoAtual === null) {
        return "Não foi possível obter o saldo atual.";
    }


    // Obtém a receita total do mês atual
    $stmt = $conn->prepare("
        SELECT SUM(valor) FROM transacoes
        WHERE usuario_id = ? AND tipo = 'receita' AND MONTH(data) = MONTH(CURDATE()) AND YEAR(data) = YEAR(CURDATE())
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($receitaMensal);
    $stmt->fetch();
    $stmt->close();
    $receitaMensal = $receitaMensal ?? 0;


    // Obtém a despesa total do mês atual
    $stmt = $conn->prepare("
        SELECT SUM(valor) FROM transacoes
        WHERE usuario_id = ? AND tipo = 'despesa' AND MONTH(data) = MONTH(CURDATE()) AND YEAR(data) = YEAR(CURDATE())
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($despesaMensal);
    $stmt->fetch();
    $stmt->close();
    $despesaMensal = $despesaMensal ?? 0;


    // Verifica vencimentos próximos (nos próximos 7 dias)
    $stmt = $conn->prepare("
        SELECT descricao, data_vencimento, valor
        FROM vencimentos
        WHERE usuario_id = ? AND data_vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ORDER BY data_vencimento ASC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $vencimentosProximos = [];
    while ($row = $result->fetch_assoc()) {
        $vencimentosProximos[] = $row;
    }
    $stmt->close();


    // Calcula o saldo disponível para o restante do mês
    $diasNoMes = date("t");
    $diaAtual = date("j");
    $diasRestantes = $diasNoMes - $diaAtual;
    $saldoDisponivel = $saldoAtual + $receitaMensal - $despesaMensal;


    // Média de gastos diários até agora
    $mediaGastoDiarioAtual = $diaAtual > 0 ? $despesaMensal / $diaAtual : 0;


    // Planejamento sugerido
    $gastoDiarioSugerido = $diasRestantes > 0 ? $saldoDisponivel / $diasRestantes : $saldoDisponivel;
    $saldoEstimadoFinalMes = $saldoDisponivel - ($gastoDiarioSugerido * $diasRestantes);


    // Formata os valores para exibição
    $saldoFormatado = number_format($saldoDisponivel, 2, ',', '.');
    $gastoDiarioFormatado = number_format(max(0, $gastoDiarioSugerido), 2, ',', '.');
    $mediaGastoDiarioFormatado = number_format($mediaGastoDiarioAtual, 2, ',', '.');
    $saldoFinalEstimadoFormatado = number_format($saldoEstimadoFinalMes, 2, ',', '.');
    $receitaFormatada = number_format($receitaMensal, 2, ',', '.');
    $despesaFormatada = number_format($despesaMensal, 2, ',', '.');


    // Consulta as metas financeiras do usuário
    $stmt = $conn->prepare("SELECT nome_meta, valor_alvo, valor_atual, prazo FROM metas_usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $metas = [];
    while ($row = $result->fetch_assoc()) {
        $metas[] = $row;
    }
    $stmt->close();


    // Calcula o progresso de cada meta
    $metasComProgresso = [];
    foreach ($metas as $meta) {
        $progresso = 0;
        if ($meta['valor_alvo'] > 0) {
            // Calcula o progresso com base no valor atual da meta
            $progresso = min(100, ($meta['valor_atual'] / $meta['valor_alvo']) * 100);  // Calcula progresso com o valor atual
        }
        $metasComProgresso[] = [
            'nome_meta' => $meta['nome_meta'],
            'valor_alvo' => number_format($meta['valor_alvo'], 2, ',', '.'),
            'valor_atual' => number_format($meta['valor_atual'], 2, ',', '.'),
            'prazo' => date("d/m/Y", strtotime($meta['prazo'])),
            'progresso' => number_format($progresso, 2, ',', '.')
        ];
    }


    // Monta a mensagem do planejamento em formato HTML
    $mensagem =
        "Planejamento Mensal:<br>" .
        "Saldo disponível para o mês: R$ " . $saldoFormatado . "<br>" .
        "Receita total do mês: R$ " . $receitaFormatada . "<br>" .
        "Despesa total do mês: R$ " . $despesaFormatada . "<br>" .
        "Dias restantes no mês: " . $diasRestantes . " dias<br>" .
        "Média de gastos diários até agora: R$ " . $mediaGastoDiarioFormatado . "<br>" .
        "Gasto diário sugerido: R$ " . $gastoDiarioFormatado . "<br>" .
        "Saldo estimado ao final do mês: R$ " . $saldoFinalEstimadoFormatado . "<br>";
       
    // Exibe os vencimentos nos próximos 7 dias
    if (!empty($vencimentosProximos)) {
        $mensagem .= "Vencimentos nos próximos 7 dias:<br>";
        foreach ($vencimentosProximos as $vencimento) {
            $mensagem .= "- " . $vencimento['descricao'] . " em " . date("d/m/Y", strtotime($vencimento['data_vencimento'])) . ": R$ " . number_format($vencimento['valor'], 2, ',', '.') . "<br>";
        }
    }
    return $mensagem;
}
