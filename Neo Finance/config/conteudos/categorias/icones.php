<?php
// Inicia a sessão
session_start();

// Função para armazenar os ícones em uma variável global
function armazenarIcones() {
  // Define os ícones que estão disponíveis para seleção
  $icones = array(
    'fi-br-scissors',
    'fi-sr-home',
    'fi-br-smartphone',
    'fi-sr-file-invoice-dollar',
    'fi-br-money-coin-transfer',
    'fi-ss-plane-alt',
    'fi-ss-bus-alt',
    'fi-ss-wrench-alt',
    'fi-ss-car-mechanic',
    'fi-sr-shopping-cart',
    'fi-sr-wallet',
    'fi-sr-gamepad',
    'fi-ss-hotdog',
    'fi-sr-user-md',
    'fi-sr-dog-leashed',
    'fi-sr-bone',
    'fi-sr-cat',
    'fi-sr-devices',
    'fi-ss-book-alt'
    // Adicione mais ícones conforme necessário
  );

  // Armazena os ícones em uma variável global
  $_SESSION['icones'] = $icones;
}

// Verifica se a variável global de ícones já foi definida
if (!isset($_SESSION['icones'])) {
  // Se não foi, define os ícones usando a função armazenarIcones()
  armazenarIcones();
}

// Função para obter os ícones armazenados em uma variável global
function obterIcones() {
  // Retorna os ícones armazenados em uma variável global
  return $_SESSION['icones'];
}
?>
