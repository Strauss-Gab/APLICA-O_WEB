<?php
/*
 * Arquivo: crud_cadastro_cliente.php
 * Descrição: Gerencia o cadastro, edição, inativação e reativação de clientes.
 */

session_start(); // Inicia a sessão PHP
// Verifica se o usuário está logado. Se não, redireciona para a página de login.
if (!isset($_SESSION['usuario'])) {
    // Caminho ajustado para a pasta 'login'
    header("Location: ../login/login.php");
    exit();
}

// Conexão com o banco de dados usando PDO
$pdo = new PDO('mysql:host=localhost;dbname=aplicacao', 'root', '');

// Obtém a ação a ser realizada (salvar, editar, inativar, reativar) a partir dos parâmetros GET
$acao = $_GET['acao'] ?? '';
// Obtém o ID do cliente, se fornecido
$id = $_GET['id'] ?? '';

// --- Lógica para Salvar/Atualizar Cliente ---
if ($acao === 'salvar') {
    $nome = $_POST['nome_cliente']; // Obtém o nome do cliente do formulário
    $cpf = $_POST['cpf_cliente'];   // Obtém o CPF do cliente do formulário
    $id_editar = $_POST['id'] ?? ''; // Obtém o ID do cliente se for uma edição

    if ($id_editar) {
        // Se há um ID para editar, atualiza o cliente existente
        $stmt = $pdo->prepare("UPDATE cliente SET nome_cliente = ?, cpf_cliente = ? WHERE id_cliente = ?");
        $stmt->execute([$nome, $cpf, $id_editar]);
    } else {
        // Caso contrário, insere um novo cliente com atividade padrão '1' (ativo)
        $stmt = $pdo->prepare("INSERT INTO cliente (nome_cliente, cpf_cliente, atividade) VALUES (?, ?, 1)");
        $stmt->execute([$nome, $cpf]);
    }
    // Redireciona de volta para a mesma página para atualizar a lista após a operação
    header('Location: crud_cadastro_cliente.php');
    exit;
}

// --- Lógica para Editar Cliente (carregar dados no formulário) ---
$registro = null; // Inicializa a variável para armazenar os dados do cliente a ser editado
if ($acao === 'editar' && $id) {
    // Busca os dados do cliente pelo ID
    $stmt = $pdo->prepare("SELECT * FROM cliente WHERE id_cliente = ?");
    $stmt->execute([$id]);
    $registro = $stmt->fetch(); // Obtém os dados do cliente
}

// --- Lógica para Inativar Cliente ---
if ($acao === 'inativar' && $id) {
    // Atualiza o status de atividade do cliente para '0' (inativo)
    $stmt = $pdo->prepare("UPDATE cliente SET atividade = 0 WHERE id_cliente = ?");
    $stmt->execute([$id]);
    // Redireciona de volta para a mesma página
    header('Location: crud_cadastro_cliente.php');
    exit;
}

// --- Lógica para Reativar Cliente ---
if ($acao === 'reativar' && $id) {
    // Atualiza o status de atividade do cliente para '1' (ativo)
    $stmt = $pdo->prepare("UPDATE cliente SET atividade = 1 WHERE id_cliente = ?");
    $stmt->execute([$id]);
    // Redireciona de volta para a mesma página
    header('Location: crud_cadastro_cliente.php');
    exit;
}

// --- Lógica para Listar Todos os Clientes ---
// Busca todos os clientes na tabela
$stmt = $pdo->query("SELECT * FROM cliente");
$clientes = $stmt->fetchAll(); // Obtém todos os clientes
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="top-bar">
        <a href="../menu_principal.php" class="logo">PANDA</a>
        <nav>
            <ul>
                <li><a href="crud_cadastro_cliente.php">Cadastro de Clientes</a></li>
                <li><a href="../produtos/crud_produtos.php">Ver Produtos</a></li>
                <li><a href="../vendas/ver_vendas.php">Últimas Vendas</a></li>
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

    <div class="container-crud">
        <h2><?php echo $registro ? 'Editar Cliente' : 'Adicionar Novo Cliente'; ?></h2>
        <form method="POST" action="?acao=salvar" class="mb-4">
            <input type="hidden" name="id" value="<?= $registro['id_cliente'] ?? '' ?>">
            <div class="mb-2">
                <label>Nome:</label>
                <input type="text" name="nome_cliente" class="form-control" value="<?= $registro['nome_cliente'] ?? '' ?>" required>
            </div>
            <div class="mb-2">
                <label>CPF:</label>
                <input type="text" name="cpf_cliente" class="form-control" value="<?= $registro['cpf_cliente'] ?? '' ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>

        <h3>Lista de Clientes</h3>
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Ativo</th>
                <th>Ações</th>
            </tr>
            <?php
            // Itera sobre cada cliente e exibe na tabela
            foreach ($clientes as $cliente): ?>
            <tr>
                <td><?= $cliente['id_cliente'] ?></td>
                <td><?= $cliente['nome_cliente'] ?></td>
                <td><?= $cliente['cpf_cliente'] ?></td>
                <td><?= $cliente['atividade'] == 1 ? 'Sim' : 'Não' ?></td>
                <td>
                    <a href="?acao=editar&id=<?= $cliente['id_cliente'] ?>">Editar</a> |
                    <?php
                    // Exibe o link de inativar ou reativar dependendo do status atual
                    if ($cliente['atividade'] == 1): ?>
                        <a href="?acao=inativar&id=<?= $cliente['id_cliente'] ?>">Inativar</a>
                    <?php else: ?>
                        <a href="?acao=reativar&id=<?= $cliente['id_cliente'] ?>">Reativar</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <a href="../menu_principal.php" class="back-button">Voltar ao Menu Principal</a>
    </div>
</body>
</html>