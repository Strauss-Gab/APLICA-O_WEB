<?php
// Conexão com o banco
$pdo = new PDO('mysql:host=localhost;dbname=aplicacao', 'root', '');

// Criar tabela se não existir
//$pdo->exec("CREATE TABLE IF NOT EXISTS tipo_atendimento (
    //id_tipo_atendimento INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    //tipo_atendimento VARCHAR(45) NOT NULL,
    //ativo VARCHAR(1) NOT NULL DEFAULT 'S'
//)");

// Processar ações
$acao = $_GET['acao'] ?? '';
$id = $_GET['id'] ?? '';

if ($acao === 'salvar') {
    $tipo = $_POST['aplicacao'];
    $id_editar = $_POST['id'] ?? '';

    if ($id_editar) {
        $stmt = $pdo->prepare("UPDATE aplicacao SET aplicacao = ? WHERE id_cliente = ?");
        $stmt->execute([$tipo, $id_editar]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO aplicacao (aplicacao, ativo) VALUES (?, 'S')");
        $stmt->execute([$tipo]);
    }
    header('Location: crud_tipo_atendimento.php');
    exit;
}

if ($acao === 'editar' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM aplicacao WHERE id_cliente = ?");
    $stmt->execute([$id]);
    $registro = $stmt->fetch();
}

if ($acao === 'inativar' && $id) {
    $stmt = $pdo->prepare("UPDATE aplicacao SET atividade = '0' WHERE id_cliente = ?");
    $stmt->execute([$id]);
    header('Location: crud_tipo_atendimento.php');
    exit;
}

if ($acao === 'reativar' && $id) {
    $stmt = $pdo->prepare("UPDATE aplicacao SET atividade = '1' WHERE id_cliente = ?");
    $stmt->execute([$id]);
    header('Location: crud_tipo_atendimento.php');
    exit;
}

// Listar tudo
$stmt = $pdo->query("SELECT * FROM aplicacao");
$tipos = $stmt->fetchAll();
?>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet"> <!-- Seu arquivo CSS personalizado -->
</head>

<h2>Clientes</h2>
<form method="POST" action="?acao=salvar">
    <input type="hidden" name="id" value="<?= $registro['id_cliente'] ?? '' ?>">
    <label>Tipo:</label>
    <input type="text" name="cliente" value="<?= $registro['nome_cliente'] ?? '' ?>" required>
    <button type="submit">Salvar</button>
</form>

<h3>Lista de Tipos</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Tipo</th>
        <th>Ativo</th>
        <th>Ações</th>
    </tr>
    <?php foreach ($tipos as $tipo): ?>
    <tr>
        <td><?= $tipo['id_cliente'] ?></td>
        <td><?= $tipo['nome_cliente'] ?></td>
        <td><?= $tipo['atividade'] === '1' ? 'Sim' : 'Não' ?></td>
        <td>
            <a href="?acao=editar&id=<?= $tipo['id_cliente'] ?>">Editar</a> |
            <?php if ($tipo['ativo'] === '1'): ?>
                <a href="?acao=inativar&id=<?= $tipo['id_cliente'] ?>">Inativar</a>
            <?php else: ?>
                <a href="?acao=reativar&id=<?= $tipo['id_cliente'] ?>">Reativar</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>


