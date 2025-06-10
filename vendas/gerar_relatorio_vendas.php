<?php
/*
 * Arquivo: gerar_relatorio_vendas.php
 * Descrição: Script PHP que gera um relatório de vendas em PDF com base em filtros.
 * Requer a biblioteca FPDF (fpdf.org) para funcionar.
 */

// Inclui a biblioteca FPDF. Caminho ajustado (subindo um nível para a pasta 'fpdf').
require('../fpdf/fpdf.php');

session_start(); // Inicia a sessão PHP
// Verifica se o usuário está logado. Se não, encerra o script.
if (!isset($_SESSION['usuario'])) {
    die("Acesso negado. Por favor, faça login.");
}

// Conexão com o banco de dados
try {
    $pdo = new PDO('mysql:host=localhost;dbname=aplicacao', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro na conexão com o banco de dados: ' . $e->getMessage());
}

// --- Lógica de Filtragem (obtém filtros da URL - GET) ---
$filtro_cliente_nome = $_GET['filtro_cliente_nome'] ?? '';
$filtro_produto_nome = $_GET['filtro_produto_nome'] ?? '';
$filtro_data_inicio = $_GET['filtro_data_inicio'] ?? '';
$filtro_data_fim = $_GET['filtro_data_fim'] ?? '';

// SQL base para buscar vendas, com joins para cliente e produto
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
        WHERE 1=1"; // Cláusula base para facilitar a adição de condições

$params = []; // Array para armazenar os parâmetros da consulta preparada

// Adiciona condições de filtro se os campos estiverem preenchidos
if ($filtro_cliente_nome) {
    $sql .= " AND c.nome_cliente LIKE ?";
    $params[] = '%' . $filtro_cliente_nome . '%';
}
if ($filtro_produto_nome) {
    $sql .= " AND p.nome LIKE ?";
    $params[] = '%' . $filtro_produto_nome . '%';
}
// Filtros de data não aplicáveis sem coluna de data na tabela `venda` (conforme seu BD)

// Ordena os resultados
$sql .= " ORDER BY v.id_venda DESC";

// Prepara e executa a consulta
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
// Obtém os dados das vendas
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- GERAÇÃO DO PDF usando FPDF ---
$pdf = new FPDF(); // Cria uma nova instância da classe FPDF
$pdf->AddPage();    // Adiciona uma nova página ao documento
$pdf->SetFont('Arial', 'B', 16); // Define a fonte: Arial, Negrito, tamanho 16
$pdf->Cell(0, 10, 'Relatório de Vendas', 0, 1, 'C'); // Adiciona um título centralizado
$pdf->Ln(10); // Adiciona um espaçamento de 10 unidades

// Adicionar filtros aplicados ao relatório
$pdf->SetFont('Arial', '', 10); // Define fonte: Arial, normal, tamanho 10
$pdf->Cell(0, 7, 'Filtros:', 0, 1);
// Exibe cada filtro que foi aplicado
if ($filtro_cliente_nome) $pdf->Cell(0, 5, utf8_decode('  - Cliente: ' . $filtro_cliente_nome), 0, 1);
if ($filtro_produto_nome) $pdf->Cell(0, 5, utf8_decode('  - Produto: ' . $filtro_produto_nome), 0, 1);
// Filtros de data não exibidos se não forem aplicáveis à tabela
$pdf->Ln(5); // Espaçamento após os filtros

// Cabeçalho da tabela no PDF
$pdf->SetFont('Arial', 'B', 10); // Fonte para o cabeçalho: Negrito, tamanho 10
$pdf->SetFillColor(244, 143, 177); // Cor de fundo rosa para o cabeçalho (RGB)
$pdf->Cell(20, 8, 'ID Venda', 1, 0, 'C', true); // Coluna ID Venda
$pdf->Cell(45, 8, 'Cliente', 1, 0, 'C', true);     // Coluna Cliente
$pdf->Cell(60, 8, utf8_decode('Produto Vendido'), 1, 0, 'C', true); // Coluna Produto (com utf8_decode para acentos)
$pdf->Cell(35, 8, 'Preco Unit. Item', 1, 0, 'C', true); // Coluna Preço Unitário
$pdf->Cell(30, 8, 'Total Venda', 1, 1, 'C', true); // Coluna Total Venda, com quebra de linha (1)

$pdf->SetFont('Arial', '', 9); // Fonte para o conteúdo da tabela: Normal, tamanho 9
$pdf->SetFillColor(255, 235, 238); // Cor de fundo rosa claro para linhas pares
$fill = false; // Variável para alternar a cor de fundo das linhas

// Preenche a tabela com os dados das vendas
foreach ($vendas as $venda) {
    // Define a cor de fundo da célula com base na variável $fill
    $pdf->SetFillColor($fill ? 255 : 255, $fill ? 235 : 255, $fill ? 238 : 255);
    $pdf->Cell(20, 8, $venda['id_venda'], 1, 0, 'C', $fill); // ID Venda
    $pdf->Cell(45, 8, utf8_decode($venda['nome_cliente']), 1, 0, 'L', $fill); // Nome do Cliente

    // MultiCell para o nome do produto, permitindo quebra de linha se for muito longo
    $x = $pdf->GetX(); // Salva a posição X atual
    $y = $pdf->GetY(); // Salva a posição Y atual
    $pdf->MultiCell(60, 8, utf8_decode($venda['nome_produto_vendido'] ?? 'N/A'), 1, 'L', $fill);
    $cell_height = $pdf->GetY() - $y; // Calcula a altura que o MultiCell consumiu
    $pdf->SetXY($x + 60, $y); // Retorna a posição X e Y para alinhar as próximas células

    // Preço Unitário do Item (convertido para float e formatado)
    $pdf->Cell(35, $cell_height, 'R$' . number_format((float)$venda['preco_unitario_item'], 2, ',', '.'), 1, 0, 'R', $fill);
    // Total da Venda (convertido para float e formatado)
    $pdf->Cell(30, $cell_height, 'R$' . number_format((float)$venda['total_venda'], 2, ',', '.'), 1, 1, 'R', $fill); // Quebra de linha

    $fill = !$fill; // Alterna a variável para a próxima linha (true vira false, false vira true)
}

// Gera a saída do PDF para o navegador ('I' para inline, 'D' para download)
// O nome do arquivo será 'relatorio_vendas_filtrado.pdf'
$pdf->Output('I', 'relatorio_vendas_filtrado.pdf');
?>