<?php
/*
 * Arquivo: relatorio_clientes.php
 * Descrição: Script PHP que gera um relatório geral de clientes em PDF.
 * Requer a biblioteca FPDF.
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

// Consulta para buscar todos os clientes
$sql = "SELECT id_cliente, nome_cliente, cpf_cliente, atividade FROM cliente ORDER BY nome_cliente ASC";
$stmt = $pdo->query($sql);
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- GERAÇÃO DO PDF usando FPDF ---
$pdf = new FPDF(); // Cria uma nova instância da classe FPDF
$pdf->AddPage();    // Adiciona uma nova página
$pdf->SetFont('Arial', 'B', 16); // Define a fonte
$pdf->Cell(0, 10, utf8_decode('Relatório de Clientes'), 0, 1, 'C'); // Título
$pdf->Ln(10); // Espaçamento

// Cabeçalho da tabela no PDF
$pdf->SetFont('Arial', 'B', 10); // Fonte para o cabeçalho
$pdf->SetFillColor(244, 143, 177); // Cor de fundo rosa
$pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);       // Coluna ID
$pdf->Cell(70, 8, 'Nome do Cliente', 1, 0, 'L', true); // Coluna Nome
$pdf->Cell(50, 8, 'CPF', 1, 0, 'C', true);       // Coluna CPF
$pdf->Cell(30, 8, 'Ativo', 1, 1, 'C', true);     // Coluna Ativo, com quebra de linha

$pdf->SetFont('Arial', '', 9); // Fonte para o conteúdo da tabela
$pdf->SetFillColor(255, 235, 238); // Cor de fundo rosa claro para linhas pares
$fill = false; // Variável para alternar a cor de fundo das linhas

// Preenche a tabela com os dados dos clientes
foreach ($clientes as $cliente) {
    $pdf->SetFillColor($fill ? 255 : 255, $fill ? 235 : 255, $fill ? 238 : 255);
    $atividade_texto = ($cliente['atividade'] == 1) ? 'Sim' : 'Não'; // Converte 1/0 para Sim/Não
    $pdf->Cell(20, 8, $cliente['id_cliente'], 1, 0, 'C', $fill);
    $pdf->Cell(70, 8, utf8_decode($cliente['nome_cliente']), 1, 0, 'L', $fill);
    $pdf->Cell(50, 8, utf8_decode($cliente['cpf_cliente']), 1, 0, 'C', $fill);
    $pdf->Cell(30, 8, utf8_decode($atividade_texto), 1, 1, 'C', $fill); // Quebra de linha
    $fill = !$fill; // Alterna a cor de fundo
}

// Gera a saída do PDF
$pdf->Output('I', 'relatorio_clientes.pdf');
?>