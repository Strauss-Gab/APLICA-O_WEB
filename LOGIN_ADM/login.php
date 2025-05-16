<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>BelezaStore</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-100 flex items-center justify-center h-screen">
  <form method="POST" action="valida_login.php" class="bg-white p-8 rounded shadow-md w-96">
    <h2 class="text-2xl mb-4 text-pink-600 font-bold">Login do Administrador</h2>
    
    <label class="block mb-2">Usuário</label>
    <input type="text" name="usuario" class="w-full p-2 mb-4 border rounded" required>

    <label class="block mb-2">Senha</label>
    <input type="password" name="senha" class="w-full p-2 mb-4 border rounded" required>

    <button type="submit" class="w-full bg-pink-600 text-white py-2 rounded hover:bg-pink-500">
      Entrar
    </button>
  </form>
</body>
</html>