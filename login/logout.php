<?php
/*
 * Arquivo: logout.php
 * Descrição: Encerra a sessão do usuário e o redireciona para a página de login.
 */

session_start(); // Inicia a sessão PHP
session_destroy(); // Destroi todas as variáveis de sessão e encerra a sessão

// Redireciona para a página de login. Caminho ajustado (permanece na mesma pasta 'login').
header("Location: login.php");
exit(); // Encerra o script
?>