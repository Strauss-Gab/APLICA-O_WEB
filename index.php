<?php
/*
 * Arquivo: index.php
 * Descrição: Ponto de entrada principal da aplicação.
 * Verifica se o usuário está logado. Se não, redireciona para a página de login.
 * Se sim, redireciona para o menu principal.
 */

session_start(); // Inicia a sessão PHP para gerenciar variáveis de sessão

// Verifica se a variável de sessão 'usuario' está definida
if (!isset($_SESSION['usuario'])) {
    // Se o usuário não estiver logado, redireciona para a página de login
    // Caminho ajustado para a pasta 'login'
    header("Location: login/login.php");
    exit(); // Encerra o script para garantir o redirecionamento
}

// Se o usuário estiver logado, redireciona para o menu principal
header("Location: menu_principal.php");
exit(); // Encerra o script para garantir o redirecionamento
?>