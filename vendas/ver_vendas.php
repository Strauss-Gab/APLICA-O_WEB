<?php
/*
 * Arquivo: ver_vendas.php
 * Descrição: Exibe a lista de vendas com opções de filtro e um botão para gerar relatório PDF.
 */

session_start(); // Inicia a sessão PHP
// Verifica se o usuário está logado. Se não, redireciona para a página de login.
if (!isset($_SESSION['usuario'])) {
    // Caminho ajustado para a pasta 'login'
    header("Location: ../login/login.php");
    exit();
}

// Conexão com o banco de dados usando PDO
try {
    $pdo = new PDO('mysql:host=localhost;dbname=aplicacao', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro na conexão com o banco de dados: ' . $e->getMessage());
}

// --- Lógica de Filtragem de Vendas ---
// Obtém os valores dos filtros a partir dos parâmetros GET
$filtro_cliente_nome = $_GET['filtro_cliente_nome'] ?? '';
$filtro_produto_nome = $_GET['filtro_produto_nome'] ?? '';
$filtro_data_inicio = $_GET['filtro_data_inicio'] ?? '';
$filtro_data_fim = $_GET['filtro_data_fim'] ?? '';

// SQL base para buscar vendas com JOINs para cliente e produto
$sql = "SELECT
            v.id_venda,
            v.total_venda,
            v.Valor AS preco_unitario_item,
            c.nome_cliente,
            p.nome AS nome_produto_vendido,
            p.tipo AS tipo_produto_vendido
        FROM
            venda v
        JOIN
            cliente c ON v.ID_cliente = c.id_cliente
        JOIN
            produto p ON v.ID_produto = p.id_produto
        WHERE 1=1";

$params = [];

if ($filtro_cliente_nome) {
    $sql .= " AND c.nome_cliente LIKE ?";
    $params[] = '%' . $filtro_cliente_nome . '%';
}
if ($filtro_produto_nome) {
    $sql .= " AND p.nome LIKE ?";
    $params[] = '%' . $filtro_produto_nome . '%';
}

if ($filtro_data_inicio) {
    // Adicione a lógica de filtro de data se sua tabela 'venda' tiver a coluna 'data_venda'
    // $sql .= " AND DATE(v.data_venda) >= ?";
    // $params[] = $filtro_data_inicio;
}
if ($filtro_data_fim) {
    // Adicione a lógica de filtro de data se sua tabela 'venda' tiver a coluna 'data_venda'
    // $sql .= " AND DATE(v.data_venda) <= ?";
    // $params[] = $filtro_data_fim;
}

$sql .= " ORDER BY v.id_venda DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Constrói os parâmetros da query para o relatório.
// Isso garante que o script de relatório receba exatamente os mesmos filtros.
$report_query_params = http_build_query([
    'filtro_cliente_nome' => $filtro_cliente_nome,
    'filtro_produto_nome' => $filtro_produto_nome,
    'filtro_data_inicio' => $filtro_data_inicio,
    'filtro_data_fim' => $filtro_data_fim
]);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Últimas Vendas</title>
    <link rel="stylesheet" href="../css/style.css">
    <script>
        // Função JavaScript para gerar o relatório PDF
        function gerarRelatorio() {
            // Pega os valores dos filtros do formulário
            const cliente = document.getElementById('filtro_cliente_nome').value;
            const produto = document.getElementById('filtro_produto_nome').value;
            const dataInicio = document.getElementById('filtro_data_inicio') ? document.getElementById('filtro_data_inicio').value : '';
            const dataFim = document.getElementById('filtro_data_fim') ? document.getElementById('filtro_data_fim').value : '';

            // Constrói a string de query com os filtros
            let queryParams = new URLSearchParams();
            if (cliente) queryParams.append('filtro_cliente_nome', cliente);
            if (produto) queryParams.append('filtro_produto_nome', produto);
            if (dataInicio) queryParams.append('filtro_data_inicio', dataInicio);
            if (dataFim) queryParams.append('filtro_data_fim', dataFim);

            // Abre o relatório em uma nova aba com os filtros
            window.open('gerar_relatorio_vendas.php?' + queryParams.toString(), '_blank');
        }

        // Função JavaScript para limpar os filtros
        function limparFiltros() {
            window.location.href = 'ver_vendas.php'; // Redireciona sem filtros
        }
    </script>
</head>
<body>
    <div class="top-bar">
        <a href="../menu_principal.php" class="logo">PANDA</a>
        <nav>
            <ul>
                <li><a href="../clientes/crud_cadastro_cliente.php">Cadastro de Clientes</a></li>
                <li><a href="../produtos/crud_produtos.php">Ver Produtos</a></li>
                <li><a href="ver_vendas.php">Últimas Vendas</a></li>
                <li class="dropdown">
                    <a href="#">Relatórios</a>
                    <div class="dropdown-content">
                        <a href="../relatorios/relatorio_clientes.php">Clientes</a>
                        <a href="../relatorios/relatorio_vendas.php">Vendas</a>
                        <a href="../relatorios/relatorio_produtos.php">Produtos</a>
                    </div>
                </li>
            </ul>
        </nav>
        <div class="user-actions">
            <span>Olá, <?php echo $_SESSION['usuario']; ?>!</span>
            <a href="../login/logout.php" class="logout-btn">Sair</a>
        </div>
    </div>

    <div class="container-vendas">
        <h2>Últimas Vendas</h2>

        <form method="GET" action="ver_vendas.php" class="filter-form">
            <div>
                <label for="filtro_cliente_nome">Cliente:</label>
                <input type="text" id="filtro_cliente_nome" name="filtro_cliente_nome" value="<?= htmlspecialchars($filtro_cliente_nome) ?>">
            </div>
            <div>
                <label for="filtro_produto_nome">Produto:</label>
                <input type="text" id="filtro_produto_nome" name="filtro_produto_nome" value="<?= htmlspecialchars($filtro_produto_nome) ?>">
            </div>
            <button type="submit">Filtrar</button>
            <button type="button" onclick="limparFiltros()" class="btn-clear-filters">Limpar Filtros</button>
        </form>

        <button type="button" onclick="gerarRelatorio()" class="report-button">Gerar Relatório (PDF)</button>

        <table class="table-vendas">
            <thead>
                <tr>
                    <th>ID Venda</th>
                    <th>Cliente</th>
                    <th>Produto Vendido</th>
                    <th>Preço Unitário (Item)</th>
                    <th>Total da Venda</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($vendas)): ?>
                    <?php foreach ($vendas as $venda): ?>
                    <tr>
                        <td><?= htmlspecialchars($venda['id_venda']) ?></td>
                        <td><?= htmlspecialchars($venda['nome_cliente']) ?></td>
                        <td><?= htmlspecialchars($venda['nome_produto_vendido'] ?? 'N/A') ?></td>
                        <td>R$<?= number_format((float)$venda['preco_unitario_item'], 2, ',', '.') ?></td>
                        <td>R$<?= number_format((float)$venda['total_venda'], 2, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nenhuma venda encontrada com os filtros aplicados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="../menu_principal.php" class="back-button">Voltar ao Menu Principal</a>
    </div>
</body>
</html>