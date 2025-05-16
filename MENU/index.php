<?php
// Conexão com o banco
$host = "localhost";
$db = "aplicacao";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Consulta para pegar os tipos únicos
$sql = "SELECT DISTINCT tipo FROM produto ORDER BY tipo";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>BelezaStore</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 font-sans">

  <!-- Navbar -->
  <nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <!-- Logo -->
      <div class="text-2xl font-bold text-pink-600">BelezaStore</div>

      <!-- Menu Dinâmico -->
      <ul class="hidden md:flex space-x-6 text-pink-700 font-medium">
        <li><a href="index.php" class="hover:text-pink-500">Início</a></li>
	<li><a href="crud_cadastro_cliente.php" class="hover:text-pink-500">Cadastros</a></li>

        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $tipo = htmlspecialchars($row['tipo']);
                echo "<li><a href='produtos.php?tipo={$tipo}' class='hover:text-pink-500 transition'>{$tipo}</a></li>";
            }
        }
        ?>

        <li><a href="contato.php" class="hover:text-pink-500">Contato</a></li>
      </ul>

      <!-- Login/Carrinho -->
      <div class="flex items-center space-x-4">
        <a href="carrinho.php" class="text-pink-600 hover:text-pink-400">🛒</a>
        <a href="login.php" class="bg-pink-600 text-white px-4 py-2 rounded-full hover:bg-pink-500 transition">
          Login
        </a>
      </div>
    </div>
  </nav>

</body>
</html>

<?php
$conn->close();
?>

<?php if (isset($_SESSION['adm'])): ?>
  <li><a href="painel_adm.php" class="hover:text-pink-500">Painel ADM</a></li>
  <li><a href="crud_cadastro_cliente.php" class="hover:text-pink-500">Cadastros</a></li>  <!-- Aqui -->
  <li><a href="logout.php" class="text-red-500 hover:text-red-400">Sair</a></li>
<?php else: ?>
  <li><a href="login.php" class="hover:text-pink-500">Login ADM</a></li>
<?php endif; ?>