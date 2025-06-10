<?php
/*
 * Arquivo: menu_principal.php
 * Descrição: Página inicial/dashboard da aplicação.
 * Exibe a barra de navegação superior, uma mensagem de boas-vindas
 * e a lista de produtos em destaque para visualização.
 */

session_start(); // Inicia a sessão PHP

// Verifica se o usuário está logado. Se não, redireciona para a página de login.
if (!isset($_SESSION['usuario'])) {
    // Caminho ajustado para a pasta 'login'
    header("Location: login/login.php");
    exit();
}

// --- Lógica de Conexão e Busca de Produtos para Exibição ---
try {
    // Tenta estabelecer uma conexão com o banco de dados usando PDO
    $pdo = new PDO('mysql:host=localhost;dbname=aplicacao', 'root', '');
    // Define o modo de erro para lançar exceções em caso de problemas
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Em caso de falha na conexão, inicializa $produtos como vazio
    $produtos = [];
    // Registra o erro no log do servidor (útil para depuração sem mostrar erro ao usuário)
    error_log('Erro na conexão com o banco de dados em menu_principal.php: ' . $e->getMessage());
}

// Inicializa a variável $produtos como um array vazio para evitar erros caso a consulta falhe
$produtos = [];
// Verifica se a conexão com o banco de dados foi estabelecida com sucesso
if (isset($pdo)) {
    try {
        // Prepara e executa a consulta SQL para buscar produtos da tabela 'produto'
        // Seleciona ID, nome, valor e caminho da imagem. Ordena por ID decrescente.
        $stmt = $pdo->query("SELECT id_produto, nome, valor, imagem FROM produto ORDER BY id_produto DESC");
        // Obtém todos os resultados como um array associativo
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Em caso de erro na consulta, loga o erro e $produtos permanece vazio
        error_log('Erro na consulta de produtos em menu_principal.php: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Menu Principal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="top-bar">
        <a href="menu_principal.php" class="logo">PANDA</a>
        <nav>
            <ul>
                <li><a href="clientes/crud_cadastro_cliente.php">Cadastro de Clientes</a></li>
                <li><a href="produtos/crud_produtos.php">Ver Produtos</a></li>
                <li><a href="vendas/ver_vendas.php">Últimas Vendas</a></li>
                <li class="dropdown">
                    <a href="#">Relatórios</a>
                    <div class="dropdown-content">
                        <a href="relatorios/relatorio_clientes.php">Clientes</a>
                        <a href="relatorios/relatorio_vendas.php">Vendas</a>
                        <a href="relatorios/relatorio_produtos.php">Produtos</a>
                    </div>
                </li>
            </ul>
        </nav>
        <div class="user-actions">
            <span>Olá, <?php echo $_SESSION['usuario']; ?>!</span>
            <a href="login/logout.php" class="logout-btn">Sair</a>
        </div>
    </div>

    <div class="welcome-section">
        <h1>BEM-VINDO A PANDA</h1>
        <p>Panda é um tema moderno, limpo e profissional para sua loja de produtos de beleza.</p>
    </div>

    <div class="featured-products">
        <h2>NOSSOS PRODUTOS</h2>
        <div class="product-grid">
            <?php
            // Verifica se o array de produtos não está vazio
            if (!empty($produtos)):
                // Itera sobre cada produto para exibi-lo na grade
                foreach ($produtos as $produto): ?>
                    <div class="product-item">
                        <img src="images/<?= htmlspecialchars(basename($produto['imagem'] ?? 'placeholder.jpg')) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                        <div class="product-name"><?= htmlspecialchars($produto['nome']) ?></div>
                        <div class="product-price">
                            <span class="current-price">R$<?= number_format($produto['valor'], 2, ',', '.') ?></span>
                        </div>
                        </div>
                <?php endforeach;
            else: ?>
                <p>Nenhum produto cadastrado ainda.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>