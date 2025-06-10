<?php
/*
 * Arquivo: crud_produtos.php
 * Descrição: Gerencia o cadastro, edição e listagem de produtos com opções de filtro.
 */

session_start(); // Inicia a sessão PHP
// Verifica se o usuário está logado. Se não, redireciona para a página de login.
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login/login.php"); // Caminho ajustado
    exit();
}

// Conexão com o banco de dados usando PDO
try {
    $pdo = new PDO('mysql:host=localhost;dbname=aplicacao', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro na conexão com o banco de dados: ' . $e->getMessage());
}

// Obtém a ação a ser realizada (salvar, editar) e o ID, se fornecido
$acao = $_GET['acao'] ?? '';
$id = $_GET['id'] ?? '';

// --- Lógica para Salvar/Atualizar Produto ---
if ($acao === 'salvar') {
    $nome = $_POST['nome'];
    $valor = $_POST['valor'];
    $tipo = $_POST['tipo'];
    $imagem = $_POST['imagem'] ?? 'images/placeholder.jpg';

    $id_editar = $_POST['id_produto'] ?? '';

    if ($id_editar) {
        $stmt = $pdo->prepare("UPDATE produto SET nome = ?, valor = ?, tipo = ?, imagem = ? WHERE id_produto = ?");
        $stmt->execute([$nome, $valor, $tipo, $imagem, $id_editar]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO produto (nome, valor, tipo, imagem) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $valor, $tipo, $imagem]);
    }
    header('Location: crud_produtos.php'); // Redireciona para a mesma página
    exit;
}

// --- Lógica para Editar Produto (carregar dados no formulário) ---
$registro = null;
if ($acao === 'editar' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM produto WHERE id_produto = ?");
    $stmt->execute([$id]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);
}

// --- Lógica de Filtragem de Produtos ---
$filtro_nome = $_GET['filtro_nome'] ?? '';
$filtro_tipo = $_GET['filtro_tipo'] ?? '';

// SQL base para buscar produtos
$sql = "SELECT id_produto, nome, valor, imagem FROM produto WHERE 1=1";
$params = []; // Array para armazenar os parâmetros da consulta preparada

// Adiciona condição de filtro por nome do produto, se fornecido
if ($filtro_nome) {
    $sql .= " AND nome LIKE ?";
    $params[] = '%' . $filtro_nome . '%';
}
// Adiciona condição de filtro por tipo de produto, se fornecido
if ($filtro_tipo) {
    $sql .= " AND tipo LIKE ?";
    $params[] = '%' . $filtro_tipo . '%';
}

// Ordena os resultados
$sql .= " ORDER BY nome ASC"; // Ordenar por nome para visualização

// Prepara e executa a consulta com os parâmetros de filtro
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Produtos</title>
    <link rel="stylesheet" href="../css/style.css"> <style>
        /* Estilos específicos para esta página (já definidos no style.css geral) */
        /* Mantidos aqui para clareza ou se houver overrides específicos para esta página */
        .filter-form-produtos {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ffccbc;
            border-radius: 8px;
            background-color: #fffaf0;
            justify-content: center;
            align-items: flex-end;
        }

        .filter-form-produtos label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }

        .filter-form-produtos input[type="text"] {
            padding: 8px;
            border: 1px solid #ffccbc;
            border-radius: 5px;
            width: 180px;
            box-sizing: border-box;
        }

        .filter-form-produtos button {
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            background-color: #e91e63;
            color: white;
        }

        .filter-form-produtos button:hover {
            background-color: #c2185b;
        }

        .filter-form-produtos a.btn-clear-filters {
            background-color: #f06292;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-left: 0; /* Override general filter-form a.report-button */
            display: inline-block; /* Make it behave like a button */
        }
        .filter-form-produtos a.btn-clear-filters:hover {
            background-color: #e91e63;
        }

    </style>
</head>
<body>
    <div class="top-bar">
        <a href="../menu_principal.php" class="logo">PANDA</a> <nav>
            <ul>
                <li><a href="../clientes/crud_cadastro_cliente.php">Cadastro de Clientes</a></li> <li><a href="crud_produtos.php">Ver Produtos</a></li>
                <li><a href="../vendas/ver_vendas.php">Últimas Vendas</a></li> <li class="dropdown">
                    <a href="#">Relatórios</a>
                    <div class="dropdown-content">
                        <a href="../relatorios/relatorio_clientes.php">Clientes</a> <a href="../relatorios/relatorio_vendas.php">Vendas</a> <a href="../relatorios/relatorio_produtos.php">Produtos</a> </div>
                </li>
            </ul>
        </nav>
        <div class="user-actions">
            <span>Olá, <?php echo $_SESSION['usuario']; ?>!</span>
            <a href="../login/logout.php" class="logout-btn">Sair</a> </div>
    </div>

    <div class="container-crud">
        <h2><?php echo $registro ? 'Editar Produto' : 'Adicionar Novo Produto'; ?></h2>
        <form method="POST" action="?acao=salvar">
            <input type="hidden" name="id_produto" value="<?= $registro['id_produto'] ?? '' ?>">
            <div class="mb-2">
                <label for="nome">Nome do Produto:</label>
                <input type="text" name="nome" id="nome" class="form-control" value="<?= htmlspecialchars($registro['nome'] ?? '') ?>" required>
            </div>
            <div class="mb-2">
                <label for="valor">Valor:</label>
                <input type="number" name="valor" id="valor" class="form-control" step="0.01" value="<?= htmlspecialchars($registro['valor'] ?? '') ?>" required>
            </div>
            <div class="mb-2">
                <label for="tipo">Tipo:</label>
                <input type="text" name="tipo" id="tipo" class="form-control" value="<?= htmlspecialchars($registro['tipo'] ?? '') ?>">
            </div>
            <div class="mb-2">
                <label for="imagem">Caminho da Imagem:</label>
                <input type="text" name="imagem" id="imagem" class="form-control" value="<?= htmlspecialchars($registro['imagem'] ?? 'images/placeholder.jpg') ?>">
                <small>Ex: images/nome_da_imagem.jpg</small>
            </div>
            <button type="submit">Salvar Produto</button>
        </form>

        <h3>Filtrar Produtos</h3>
        <form method="GET" action="crud_produtos.php" class="filter-form-produtos">
            <div>
                <label for="filtro_nome">Nome:</label>
                <input type="text" id="filtro_nome" name="filtro_nome" value="<?= htmlspecialchars($filtro_nome) ?>">
            </div>
            <div>
                <label for="filtro_tipo">Tipo:</label>
                <input type="text" id="filtro_tipo" name="filtro_tipo" value="<?= htmlspecialchars($filtro_tipo) ?>">
            </div>
            <button type="submit">Filtrar</button>
            <a href="crud_produtos.php" class="btn-clear-filters">Limpar Filtros</a>
        </form>

        <h3>Lista de Produtos</h3>
        <?php if (!empty($produtos)): ?>
            <table class="product-list-table">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><img src="../images/<?= htmlspecialchars(basename($produto['imagem'] ?? 'placeholder.jpg')) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>"></td> <td><?= htmlspecialchars($produto['nome']) ?></td>
                        <td>R$<?= number_format($produto['valor'], 2, ',', '.') ?></td>
                        <td class="action-links">
                            <a href="?acao=editar&id=<?= $produto['id_produto'] ?>" title="Editar">
                                &#9998; Editar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum produto cadastrado ainda ou correspondente aos filtros.</p>
        <?php endif; ?>
        <a href="../menu_principal.php" class="back-button">Voltar ao Menu Principal</a> </div>
</body>
</html>