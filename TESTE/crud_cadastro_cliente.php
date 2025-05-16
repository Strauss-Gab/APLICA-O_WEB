<?php

//Login do ADM
session_start();
if (!isset($_SESSION['adm'])) {
    header("Location: login.php");
    exit;
}

// Conexão com o banco
$pdo = new PDO('mysql:host=localhost;dbname=aplicacao', 'root', '');

// Processar ações
$acao = $_GET['acao'] ?? '';
$id = $_GET['id'] ?? '';

if ($acao === 'salvar') {
    $nome = $_POST['nome_cliente'];
    $cpf = $_POST['cpf_cliente'];
    $id_editar = $_POST['id'] ?? '';

    if ($id_editar) {
        $stmt = $pdo->prepare("UPDATE cliente SET nome_cliente = ?, cpf_cliente = ? WHERE id_cliente = ?");
        $stmt->execute([$nome, $cpf, $id_editar]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cliente (nome_cliente, cpf_cliente, atividade) VALUES (?, ?, 1)");
        $stmt->execute([$nome, $cpf]);
    }
    header('Location: crud_cadastro_cliente.php');
    exit;
}

if ($acao === 'editar' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM cliente WHERE id_cliente = ?");
    $stmt->execute([$id]);
    $registro = $stmt->fetch();
}

if ($acao === 'inativar' && $id) {
    $stmt = $pdo->prepare("UPDATE cliente SET atividade = 0 WHERE id_cliente = ?");
    $stmt->execute([$id]);
    header('Location: crud_cadastro_cliente.php');
    exit;
}

if ($acao === 'reativar' && $id) {
    $stmt = $pdo->prepare("UPDATE cliente SET atividade = 1 WHERE id_cliente = ?");
    $stmt->execute([$id]);
    header('Location: crud_cadastro_cliente.php');
    exit;
}

// Listar tudo
$stmt = $pdo->query("SELECT * FROM cliente");
$clientes = $stmt->fetchAll();
?>
<head>
  <meta charset="UTF-8">
  <title>BelezaStore</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<h2>Cadastro de Clientes</h2>
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
    <?php foreach ($clientes as $cliente): ?>
    <tr>
        <td><?= $cliente['id_cliente'] ?></td>
        <td><?= $cliente['nome_cliente'] ?></td>
        <td><?= $cliente['cpf_cliente'] ?></td>
        <td><?= $cliente['atividade'] == 1 ? 'Sim' : 'Não' ?></td>
        <td>
            <a href="?acao=editar&id=<?= $cliente['id_cliente'] ?>">Editar</a> |
            <?php if ($cliente['atividade'] == 1): ?>
                <a href="?acao=inativar&id=<?= $cliente['id_cliente'] ?>">Inativar</a>
            <?php else: ?>
                <a href="?acao=reativar&id=<?= $cliente['id_cliente'] ?>">Reativar</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>