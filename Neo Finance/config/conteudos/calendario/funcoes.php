<?php
function calcularDiasRestantes($dataVencimento)
{
    $hoje = new DateTime();
    $vencimento = new DateTime($dataVencimento);
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
    if ($interval->days <= 1) {
        return "Vence amanhã"; // Se falta 1 dia
    }
    return $interval->days . " dias restantes"; // Se ainda faltam mais de 1 dia
}
