<?php
/*
 * Arquivo: valida_login.php
 * Descrição: Processa os dados do formulário de login, verifica as credenciais
 * e redireciona o usuário.
 */

session_start(); // Inicia a sessão PHP

// Obtém o nome de usuário e senha enviados via POST
$usuario = $_POST['usuario'];
$senha = $_POST['senha'];

// Conexão com o banco de dados MySQLi
$conn = new mysqli('localhost', 'root', '', 'aplicacao');

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die('Erro na conexão com o banco de dados: ' . $conn->connect_error);
}

// Prepara a consulta SQL para buscar o administrador com o usuário e senha fornecidos
// Usa prepared statements para prevenir injeção SQL
$sql = "SELECT * FROM administrador WHERE usuario = ? AND senha = ?";
$stmt = $conn->prepare($sql);
// 'ss' indica que ambos os parâmetros são strings
$stmt->bind_param("ss", $usuario, $senha);
// Executa a consulta
$stmt->execute();
// Obtém o resultado da consulta
$result = $stmt->get_result();

// Verifica se exatamente um administrador foi encontrado (login válido)
if ($result->num_rows === 1) {
    // Se o login for válido, armazena o nome de usuário na sessão
    $_SESSION['usuario'] = $usuario;
    // Redireciona para o menu principal. Caminho ajustado (subindo um nível).
    header("Location: ../menu_principal.php");
    exit(); // Encerra o script
} else {
    // Se o login for inválido, exibe uma mensagem e um link para tentar novamente
    echo "<p>Login inválido. <a href='login.php'>Tentar novamente</a></p>";
}

// Fecha o statement e a conexão com o banco de dados
$stmt->close();
$conn->close();
?>