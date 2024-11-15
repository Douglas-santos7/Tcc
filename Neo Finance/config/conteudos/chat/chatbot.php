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

    // Respostas associadas a comandos
    $responses = [
        'oi' => function () use ($username) {
            return gerarRespostaSaudacao($username, 'oi');
        },
        'ola' => function () use ($username) {
            return gerarRespostaSaudacao($username, 'ola');
        },
        'bom dia' => function () use ($username) {
            return gerarRespostaSaudacao($username, 'bom dia');
        },
        'boa tarde' => function () use ($username) {
            return gerarRespostaSaudacao($username, 'boa tarde');
        },
        'boa noite' => function () use ($username) {
            return gerarRespostaSaudacao($username, 'boa noite');
        },

        'saldo' => function () use ($conn, $userId) {
            return getSaldo($conn, $userId);
        },
        '1' => function () use ($conn, $userId) {
            return getSaldo($conn, $userId);
        },
        'economizar' => function () use ($conn, $userId) {
            return implode("\n", getDicasEconomizar($conn, $userId));
        },
        '2' => function () use ($conn, $userId) {
            return implode("\n", getDicasEconomizar($conn, $userId));
        },
        'investir' => function () use ($conn, $userId) {
            return implode("\n", getDicasInvestir($conn, $userId));
        },
        '3' => function () use ($conn, $userId) {
            return implode("\n", getDicasInvestir($conn, $userId));
        },
        'resumo mensal' => function () use ($conn, $userId) {
            return getResumoMensal($conn, $userId);
        },
        '4' => function () use ($conn, $userId) {
            return getResumoMensal($conn, $userId);
        },
        'resumo diário' => function () use ($conn, $userId) {
            return getResumoDiario($conn, $userId);
        },
        '5' => function () use ($conn, $userId) {
            return getResumoDiario($conn, $userId);
        },
        'historico' => function () use ($conn, $userId) {
            return getHistoricoTransacoes($conn, $userId);
        },
        '6' => function () use ($conn, $userId) {
            return getHistoricoTransacoes($conn, $userId);
        },
        'análise' => function () use ($conn, $userId) {
            return getAnaliseGastos($conn, $userId);
        },
        '7' => function () use ($conn, $userId) {
            return getAnaliseGastos($conn, $userId);
        },
        'exportar' => function () use ($conn, $userId) {
            return exportarRelatorio($conn, $userId);
        },
        '8' => function () use ($conn, $userId) {
            return exportarRelatorio($conn, $userId);
        },
        'previsão financeira' => function () use ($conn, $userId) {
            return previsaoFinanceira($conn, $userId);
        },
        '9' => function () use ($conn, $userId) {
            return previsaoFinanceira($conn, $userId);
        },
        'comparação' => function () use ($conn, $userId) {
            return comparacaoGastosMensais($conn, $userId);
        },
        '10' => function () use ($conn, $userId) {
            return comparacaoGastosMensais($conn, $userId);
        },
        'desafio' => function () use ($conn, $userId) {
            return obterDesafioFinanceiroAleatorio($conn, $userId);
        },
        '11' => function () use ($conn, $userId) {
            return obterDesafioFinanceiroAleatorio($conn, $userId);
        },
        'obrigado' => 'De nada! Se precisar de mais ajuda, estou aqui.',
        'valeu' => 'De nada! Se precisar de mais ajuda, estou aqui.',
        'ajuda' => 'Claro! Estou aqui para ajudar com questões financeiras. O que você gostaria de saber?',
        'como funciona' => 'Posso ajudá-lo a entender orçamentos, investimentos e muito mais!',
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
        return $mensagem .
            "Escolha uma das opções abaixo:\n" .
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
            "11. Desafios\n";
    }

    // Adicionando sinônimos para as respostas
    $synonyms = [
        'oi',
        'saldo',
        'quanto tenho',
        'meu saldo',
        'saldo atual',
        'categorias',
        'quais são minhas categorias',
        'minhas categorias',
        'economizar',
        'dicas de economia',
        'como economizar',
        'investir',
        'dicas de investimento',
        'como investir',
        'resumo mensal',
        'meu resumo mensal',
        'resumo do mês',
        'resumo diário',
        'meu resumo diário',
        'resumo de hoje',
        'ajuda',
        'preciso de ajuda',
        'me ajuda',
        'como funciona',
        'como isso funciona',
        'me explique'
    ];

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
        'Desafios'
    ];

    // Gera a lista de tópicos como uma string
    $topics = implode(', ', $allowedTopics);

    // Resposta padrão se não encontrar uma correspondência
    $defaultResponse = 'Desculpe, não entendi. Podemos falar sobre os seguintes assuntos: ' . $topics . '.';

    // Inicializa variáveis de controle
    $responseFound = false;
    $responseMessage = '';

    // Verifica se a mensagem corresponde a um comando ou palavra-chave
    foreach ($synonyms as $keyword) {
        if (strpos($message, $keyword) !== false) {
            if (array_key_exists($keyword, $responses)) {
                $response = $responses[$keyword];
                $responseMessage = is_callable($response) ? $response() : $response;
                $responseFound = true;
                break;
            }
        }
    }

    // Verificação adicional para números diretamente
    if (!$responseFound && isset($responses[$message])) {
        $response = $responses[$message];
        $responseMessage = is_callable($response) ? $response() : $response;
        $responseFound = true;
    }

    // Define a resposta final se nenhuma correspondência foi encontrada
    if (!$responseFound) {
        $responseMessage = $defaultResponse;  // Já inclui os tópicos no $defaultResponse
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
    $stmt = $conn->prepare("SELECT SUM(valor) FROM transacoes WHERE usuario_id = ? AND DATE(data) = CURDATE()");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($totalDiario);
    $stmt->fetch();
    $stmt->close();

    return "Seu resumo diário total é R$ " . number_format($totalDiario, 2, ',', '.');
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
