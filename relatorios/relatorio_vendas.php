<?php
/*
 * Arquivo: relatorio_vendas.php
 * Descrição: Script PHP que gera um relatório geral de vendas em PDF.
 * Não inclui o caminho da imagem do produto. Requer a biblioteca FPDF.
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

// Consulta para buscar TODAS as vendas (sem filtros).
// A estrutura da sua tabela 'venda' é respeitada.
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
        ORDER BY v.id_venda DESC";

$stmt = $pdo->query($sql);
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- GERAÇÃO DO PDF usando FPDF ---
$pdf = new FPDF(); // Cria uma nova instância da classe FPDF
$pdf->AddPage();    // Adiciona uma nova página
$pdf->SetFont('Arial', 'B', 16); // Define a fonte
$pdf->Cell(0, 10, utf8_decode('Relatório Geral de Vendas'), 0, 1, 'C'); // Título
$pdf->Ln(10); // Espaçamento

// Cabeçalho da tabela no PDF
$pdf->SetFont('Arial', 'B', 10); // Fonte para o cabeçalho
$pdf->SetFillColor(244, 143, 177); // Cor de fundo rosa
$pdf->Cell(20, 8, 'ID Venda', 1, 0, 'C', true);           // Coluna ID Venda
$pdf->Cell(45, 8, 'Cliente', 1, 0, 'C', true);             // Coluna Cliente
$pdf->Cell(60, 8, utf8_decode('Produto Vendido'), 1, 0, 'C', true); // Coluna Produto
$pdf->Cell(35, 8, 'Preco Unit. Item', 1, 0, 'C', true); // Coluna Preço Unitário
$pdf->Cell(30, 8, 'Total Venda', 1, 1, 'C', true);       // Coluna Total Venda, com quebra de linha

$pdf->SetFont('Arial', '', 9); // Fonte para o conteúdo da tabela
$pdf->SetFillColor(255, 235, 238); // Cor de fundo rosa claro para linhas pares
$fill = false; // Variável para alternar a cor de fundo das linhas

// Preenche a tabela com os dados das vendas
foreach ($vendas as $venda) {
    $pdf->SetFillColor($fill ? 255 : 255, $fill ? 235 : 255, $fill ? 238 : 255);
    $pdf->Cell(20, 8, $venda['id_venda'], 1, 0, 'C', $fill);
    $pdf->Cell(45, 8, utf8_decode($venda['nome_cliente']), 1, 0, 'L', $fill);

    // MultiCell para o nome do produto, permitindo quebra de linha
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->MultiCell(60, 8, utf8_decode($venda['nome_produto_vendido'] ?? 'N/A'), 1, 'L', $fill);
    $cell_height = $pdf->GetY() - $y; // Altura que o MultiCell consumiu
    $pdf->SetXY($x + 60, $y); // Retorna a posição para alinhar as próximas células

    // Preço Unitário do Item (convertido para float e formatado)
    $pdf->Cell(35, $cell_height, 'R$' . number_format((float)$venda['preco_unitario_item'], 2, ',', '.'), 1, 0, 'R', $fill);
    // Total da Venda (convertido para float e formatado)
    $pdf->Cell(30, $cell_height, 'R$' . number_format((float)$venda['total_venda'], 2, ',', '.'), 1, 1, 'R', $fill); // Quebra de linha

    $fill = !$fill; // Alterna a cor de fundo
}

// Gera a saída do PDF
$pdf->Output('I', 'relatorio_geral_vendas.pdf');
?>