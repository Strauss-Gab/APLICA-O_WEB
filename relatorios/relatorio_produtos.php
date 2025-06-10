<?php
/*
 * Arquivo: relatorio_produtos.php
 * Descrição: Script PHP que gera um relatório geral de produtos em PDF.
 * Não inclui o caminho da imagem. Requer a biblioteca FPDF.
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

// Consulta para buscar todos os produtos - A COLUNA 'imagem' NÃO É SELECIONADA
$sql = "SELECT id_produto, nome, tipo, valor FROM produto ORDER BY nome ASC";
$stmt = $pdo->query($sql);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- GERAÇÃO DO PDF usando FPDF ---
$pdf = new FPDF(); // Cria uma nova instância da classe FPDF
$pdf->AddPage();    // Adiciona uma nova página
$pdf->SetFont('Arial', 'B', 16); // Define a fonte
$pdf->Cell(0, 10, utf8_decode('Relatório de Produtos'), 0, 1, 'C'); // Título
$pdf->Ln(10); // Espaçamento

// Cabeçalho da tabela no PDF - Coluna 'Imagem (caminho)' removida
$pdf->SetFont('Arial', 'B', 10); // Fonte para o cabeçalho
$pdf->SetFillColor(244, 143, 177); // Cor de fundo rosa
$pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);           // Coluna ID
$pdf->Cell(80, 8, 'Nome do Produto', 1, 0, 'L', true); // Coluna Nome (largura ajustada)
$pdf->Cell(40, 8, 'Tipo', 1, 0, 'C', true);         // Coluna Tipo
$pdf->Cell(50, 8, 'Valor', 1, 1, 'C', true);       // Coluna Valor (largura ajustada), com quebra de linha

$pdf->SetFont('Arial', '', 9); // Fonte para o conteúdo da tabela
$pdf->SetFillColor(255, 235, 238); // Cor de fundo rosa claro para linhas pares
$fill = false; // Variável para alternar a cor de fundo das linhas

// Preenche a tabela com os dados dos produtos
foreach ($produtos as $produto) {
    $pdf->SetFillColor($fill ? 255 : 255, $fill ? 235 : 255, $fill ? 238 : 255);
    $pdf->Cell(20, 8, $produto['id_produto'], 1, 0, 'C', $fill);
    $pdf->Cell(80, 8, utf8_decode($produto['nome']), 1, 0, 'L', $fill);
    $pdf->Cell(40, 8, utf8_decode($produto['tipo']), 1, 0, 'C', $fill);
    $pdf->Cell(50, 8, 'R$' . number_format($produto['valor'], 2, ',', '.'), 1, 1, 'R', $fill); // Quebra de linha
    $fill = !$fill; // Alterna a cor de fundo
}

// Gera a saída do PDF
$pdf->Output('I', 'relatorio_produtos.pdf');
?>