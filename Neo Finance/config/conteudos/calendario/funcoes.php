<?php
function calcularDiasRestantes($dataVencimento)
{
    // Cria objetos DateTime para hoje e para a data de vencimento
    $hoje = new DateTime('now');
    $vencimento = new DateTime($dataVencimento);

    // Zera a hora para comparar apenas as datas
    $hoje->setTime(0, 0, 0);
    $vencimento->setTime(0, 0, 0);

    $interval = $hoje->diff($vencimento);

    // Verifica se vence hoje
    if ($vencimento->format('Y-m-d') == $hoje->format('Y-m-d')) {
        return "Vence hoje"; // Se vence hoje
    }

    // Verifica se já venceu
    if ($interval->invert == 1) {
        if ($interval->days == 1) {
            return "Venceu há 1 dia"; // Se venceu há 1 dia
        }
        return "Venceu há " . $interval->days . " dias"; // Se venceu há mais de 1 dia
    }

    // Se ainda faltam dias
    if ($interval->days == 1) {
        return "Vence amanhã"; // Se falta 1 dia
    }
    return $interval->days . " dias restantes"; // Se ainda faltam mais de 1 dia
}
