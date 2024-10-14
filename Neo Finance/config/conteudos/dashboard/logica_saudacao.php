<?php
function obterSaudacao() {
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

  return $saudacao;
}
?>
