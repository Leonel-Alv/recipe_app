-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16-Jul-2025 às 22:47
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `recipe_app`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `tipo_categoria` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `tipo_categoria`) VALUES
(1, 'Sobremesa'),
(2, 'Rápida');

-- --------------------------------------------------------

--
-- Estrutura da tabela `ingredientes`
--

CREATE TABLE `ingredientes` (
  `id_ingrediente` int(11) NOT NULL,
  `nome_ingrediente` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `ingredientes`
--

INSERT INTO `ingredientes` (`id_ingrediente`, `nome_ingrediente`) VALUES
(1, 'ovo'),
(2, 'fatia de queijo'),
(3, 'sal'),
(4, 'tablet de chocolate'),
(5, 'natas vegetais'),
(6, 'açucar');

-- --------------------------------------------------------

--
-- Estrutura da tabela `receita`
--

CREATE TABLE `receita` (
  `id_receita` int(11) NOT NULL,
  `nome` varchar(40) NOT NULL,
  `preparacao` text DEFAULT NULL,
  `tempo_estimado` int(11) NOT NULL,
  `num_doses` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `receita`
--

INSERT INTO `receita` (`id_receita`, `nome`, `preparacao`, `tempo_estimado`, `num_doses`) VALUES
(1, 'Ovos mexidos com queijo', 'Parte os ovos para uma tigela e bate com um garfo | Junta uma pitada de sal | Aquece uma frigideira antiaderente | Deita os ovos batidos e mexe com uma espátula | Quando começarem a coagular, junta o queijo cortado em pedaços | Mexe até o queijo derreter ligeiramente e os ovos ficarem ao teu gosto', 5, 1),
(2, 'Mousse de chocolate', 'Derrete o chocolate no micro-ondas (em intervalos de 30 segundos, mexendo entre cada um) ou em banho-maria | Bate as natas até ficarem firmes (podes juntar o açúcar aqui) | Envolve o chocolate derretido nas natas com cuidado, até ficar homogéneo | Leva ao frigorífico por 30 minutos (ou come logo se estiveres com pressa!)', 10, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `receita_categoria`
--

CREATE TABLE `receita_categoria` (
  `id_receita_categoria` int(11) NOT NULL,
  `id_receita` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `receita_ingredientes`
--

CREATE TABLE `receita_ingredientes` (
  `id_receita_ingrediente` int(11) NOT NULL,
  `id_receita` int(11) NOT NULL,
  `id_ingrediente` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `unidade_medida` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `receita_ingredientes`
--

INSERT INTO `receita_ingredientes` (`id_receita_ingrediente`, `id_receita`, `id_ingrediente`, `quantidade`, `unidade_medida`) VALUES
(1, 1, 1, 120, 'gramas'),
(2, 1, 2, 40, 'gramas'),
(3, 1, 3, 1, 'gramas'),
(4, 2, 4, 150, 'gramas'),
(5, 2, 5, 200, 'gramas'),
(6, 2, 6, 15, 'gramas');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Índices para tabela `ingredientes`
--
ALTER TABLE `ingredientes`
  ADD PRIMARY KEY (`id_ingrediente`);

--
-- Índices para tabela `receita`
--
ALTER TABLE `receita`
  ADD PRIMARY KEY (`id_receita`);

--
-- Índices para tabela `receita_categoria`
--
ALTER TABLE `receita_categoria`
  ADD PRIMARY KEY (`id_receita_categoria`),
  ADD KEY `id_receita` (`id_receita`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Índices para tabela `receita_ingredientes`
--
ALTER TABLE `receita_ingredientes`
  ADD PRIMARY KEY (`id_receita_ingrediente`),
  ADD KEY `id_receita` (`id_receita`),
  ADD KEY `id_ingrediente` (`id_ingrediente`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `ingredientes`
--
ALTER TABLE `ingredientes`
  MODIFY `id_ingrediente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `receita`
--
ALTER TABLE `receita`
  MODIFY `id_receita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `receita_categoria`
--
ALTER TABLE `receita_categoria`
  MODIFY `id_receita_categoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `receita_ingredientes`
--
ALTER TABLE `receita_ingredientes`
  MODIFY `id_receita_ingrediente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `receita_categoria`
--
ALTER TABLE `receita_categoria`
  ADD CONSTRAINT `receita_categoria_ibfk_1` FOREIGN KEY (`id_receita`) REFERENCES `receita` (`id_receita`) ON DELETE CASCADE,
  ADD CONSTRAINT `receita_categoria_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `receita_ingredientes`
--
ALTER TABLE `receita_ingredientes`
  ADD CONSTRAINT `receita_ingredientes_ibfk_1` FOREIGN KEY (`id_receita`) REFERENCES `receita` (`id_receita`) ON DELETE CASCADE,
  ADD CONSTRAINT `receita_ingredientes_ibfk_2` FOREIGN KEY (`id_ingrediente`) REFERENCES `ingredientes` (`id_ingrediente`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
