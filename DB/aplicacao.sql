-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/05/2025 às 01:04
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `aplicacao`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `nome_cliente` varchar(80) NOT NULL,
  `cpf_cliente` varchar(14) NOT NULL,
  `atividade` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto`
--

CREATE TABLE `produto` (
  `id_produto` int(11) NOT NULL,
  `nome` varchar(120) NOT NULL,
  `tipo` varchar(45) NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produto`
--

INSERT INTO `produto` (`id_produto`, `nome`, `tipo`, `valor`) VALUES
(1, 'Hidratante Facial Ácido Hialurônico', 'Skincare', 59.90),
(2, 'Sabonete Facial Detox de Carvão Ativado', 'Skincare', 34.90),
(3, 'Sérum Vitamina C 10%', 'Skincare', 69.90),
(4, 'Base Líquida Matte Alta Cobertura', 'Maquiagem', 49.90),
(5, 'Batom Líquido Nude Rosado', 'Maquiagem', 24.90),
(6, 'Paleta de Sombras Neutras', 'Maquiagem', 69.90),
(7, 'Shampoo Nutritivo Óleo de Argan', 'Cabelos', 39.90),
(8, 'Máscara Capilar Hidratação Intensa', 'Cabelos', 54.90),
(9, 'Leave-in Protetor Térmico', 'Cabelos', 44.90),
(10, 'Shampoo Nutritivo Óleo de Argan', 'Cabelo', 39.90),
(11, 'Máscara Capilar Hidratação Intensa', 'Cabelo', 54.90),
(12, 'Leave-in Protetor Térmico', 'Cabelo', 44.90),
(13, 'Esmalte Vermelho Clássico', 'Unha', 9.90),
(14, 'Removedor de Esmalte sem Acetona', 'Unha', 14.90),
(15, 'Kit Manicure (Alicate, Lixa, Palito)', 'Unha', 29.90),
(16, 'Colônia Floral Frutas Vermelhas', 'Perfumaria', 89.90),
(17, 'Body Splash Vanilla Dream', 'Perfumaria', 39.90),
(18, 'Perfume Eau de Parfum Elegance', 'Perfumaria', 129.90);

-- --------------------------------------------------------

--
-- Estrutura para tabela `venda`
--

CREATE TABLE `venda` (
  `id_venda` int(11) NOT NULL,
  `total_venda` varchar(45) NOT NULL,
  `Valor` varchar(20) NOT NULL,
  `ID_cliente` int(11) NOT NULL,
  `ID_produto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Índices de tabela `produto`
--
ALTER TABLE `produto`
  ADD PRIMARY KEY (`id_produto`);

--
-- Índices de tabela `venda`
--
ALTER TABLE `venda`
  ADD PRIMARY KEY (`id_venda`),
  ADD KEY `ID_cliente` (`ID_cliente`),
  ADD KEY `ID_produto` (`ID_produto`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produto`
--
ALTER TABLE `produto`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `venda`
--
ALTER TABLE `venda`
  MODIFY `id_venda` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `venda`
--
ALTER TABLE `venda`
  ADD CONSTRAINT `venda_ibfk_1` FOREIGN KEY (`ID_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `venda_ibfk_2` FOREIGN KEY (`ID_produto`) REFERENCES `produto` (`id_produto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
