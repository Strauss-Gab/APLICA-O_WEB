<?php
/*
 * Arquivo: login.php
 * Descrição: Página de interface para o usuário realizar login.
 */

session_start(); // Inicia a sessão PHP
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="post" action="valida_login.php">
            <label for="usuario">Usuário:</label>
            <input type="text" name="usuario" required><br>
            <label for="senha">Senha:</label>
            <input type="password" name="senha" required><br>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>