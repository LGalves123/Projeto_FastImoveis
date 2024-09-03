-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 01/09/2024 às 05:17
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `fastimoveis`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `corretores`
--

CREATE TABLE `corretores` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `corretores`
--

INSERT INTO `corretores` (`id`, `nome`) VALUES
(1, 'Zorobabel'),
(2, 'Eliseu'),
(3, 'Elias'),
(4, 'José'),
(5, 'Judá'),
(6, 'Rúben'),
(7, 'Salomao');

-- --------------------------------------------------------

--
-- Estrutura para tabela `favoritos`
--

CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `imovel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `favoritos`
--

INSERT INTO `favoritos` (`id`, `usuario_id`, `imovel_id`) VALUES
(23, 4, 4),
(24, 4, 5),
(31, 4, 1),
(32, 4, 3),
(34, 4, 2),
(36, 10, 1),
(37, 10, 3),
(38, 10, 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `imoveis`
--

CREATE TABLE `imoveis` (
  `id` int(20) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `nome_vendedor` varchar(255) NOT NULL,
  `telefone_vendedor` varchar(20) NOT NULL,
  `email_vendedor` varchar(255) NOT NULL,
  `status` varchar(30) DEFAULT NULL,
  `foto` varchar(255) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `imoveis`
--

INSERT INTO `imoveis` (`id`, `cidade`, `endereco`, `categoria`, `preco`, `nome_vendedor`, `telefone_vendedor`, `email_vendedor`, `status`, `foto`, `descricao`, `latitude`, `longitude`) VALUES
(1, 'São Vicente', 'avenida presidente wilson, 178, ', 'Casa', 150000.00, 'Lucas Santos', '3490-8375', 'santos@gmail.com', 'à_venda', 'img/casa3.jpg', 'Casa', -23.9708760, -46.3709485),
(2, 'Praia Grande', 'avenida presidente kennedy, 390, ', 'Casa', 200000.00, 'Luiz Alves', '3380-9084', 'luiz123@gmail.com', 'à_venda', 'img/casa1.jpg', 'casa', -24.0118880, -46.4008702),
(3, 'Santos', 'rua pernambuco, 35, ', 'Apartamento', 100000.50, 'Iago Teixeira', '3267-9327', 'teixeira@gmail.com', 'à_venda', 'img/casa2.jpg', 'Um bairro seguro', -23.9631917, -46.3371515),
(4, 'Praia Grande', 'avenida guilhermina, 648,', 'Casa', 300000.80, 'Bolt Santos', '98250-3798', 'bolt980@gmail.com', 'à_venda', 'img/decoracao-casa-moderna-casa-j-a-fachada-externa-revisitearquiteturaeconstru-295624-proportional-height_cover_medium.jpg', 'casa', -24.0075982, -46.4222105),
(5, 'Santos', 'avenida paulista, 2028, ', 'Casa', 980000.00, 'Alfred Silva', '98720-8054', 'alfredsilva@gmail.com', 'à_venda', 'img/ed2.jpg', 'Um ótimo condomínio', -23.5583401, -46.6591293),
(6, 'São Vicente', 'rua saldanha da gama, 217, ', 'Casa', 300000.00, 'João Simões', '3289-2157', 'johnsimoes@gmail.com', 'à_venda', 'img/mansao.jpg', 'Uma ótima localização', -23.9728485, -46.3726628),
(7, 'Rio de Janeiro', ' Av. Gen. San Martin, 320, ', 'Casa', 350000.00, 'Sophia Alves', '96703-0548', 'alves2802@gmail.com', 'à_venda', 'img/mansao_2.png', 'Uma ótima localização', -22.9845733, -43.2190805),
(8, 'Rio de Janeiro', 'R. Prudente de Morais, 81811, ', 'Apartamento', 150000.00, 'Marvado', '13997655041', 'lucas.mendes16@fatec.sp.gov.br', 'à_venda', 'img/peruibe.jpg', 'Um ótimo condomínio', -22.9850980, -43.2063495),
(9, 'São Paulo', 'rua conselheiro zacarias, 35', 'Casa', 50000000.00, 'Vitor Santana', '1132459087', 'vitorsantana@gmail.com', 'à_venda', 'img/casa3.jpg', 'Um bairro seguro', -23.5777431, -46.6605798),
(10, 'São Paulo', 'Alameda Itu, 885', 'Apartamento', 300000.00, 'Tony Stark', '(13)32459087', 'tonystark@gmail.com', 'à_venda', 'img/baxter.jpg', 'Um ótimo condomínio', -23.5621588, -46.6616843);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(20) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT 0,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `isAdmin`, `ativo`) VALUES
(4, 'João', 'joao@gmail.com', 'dccd96c256bc7dd39bae41a405f25e43', 1, 1),
(8, 'Davi', 'davi@gmail.com', '4aa606997465fd6fc4e825ff8695fcdf', 0, 1),
(9, 'Eliseu', 'eliseu@gmail.com', 'b1918e144a5df80a0e17e1d65a5fb940', 0, 1),
(10, 'Judá', 'juda@gmail.com', '175cb7e79b1c9e13a13b013d1be2b424', 0, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitas`
--

CREATE TABLE `visitas` (
  `id` int(11) NOT NULL,
  `id_imovel` int(11) NOT NULL,
  `id_corretor` int(11) NOT NULL,
  `data_visita` datetime NOT NULL,
  `status` enum('Pendente','Confirmada','Cancelada') DEFAULT 'Pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `visitas`
--

INSERT INTO `visitas` (`id`, `id_imovel`, `id_corretor`, `data_visita`, `status`) VALUES
(7, 7, 1, '2024-09-18 12:30:00', 'Pendente'),
(10, 3, 4, '2024-09-10 19:40:00', 'Pendente'),
(11, 2, 2, '2024-09-12 13:30:00', 'Pendente'),
(12, 5, 3, '2024-09-11 14:35:00', 'Pendente'),
(20, 8, 2, '2024-10-09 12:30:00', 'Pendente');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `corretores`
--
ALTER TABLE `corretores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `imovel_id` (`imovel_id`);

--
-- Índices de tabela `imoveis`
--
ALTER TABLE `imoveis`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `visitas`
--
ALTER TABLE `visitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_imovel` (`id_imovel`),
  ADD KEY `id_corretor` (`id_corretor`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `corretores`
--
ALTER TABLE `corretores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de tabela `imoveis`
--
ALTER TABLE `imoveis`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `visitas`
--
ALTER TABLE `visitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`imovel_id`) REFERENCES `imoveis` (`id`);

--
-- Restrições para tabelas `visitas`
--
ALTER TABLE `visitas`
  ADD CONSTRAINT `visitas_ibfk_1` FOREIGN KEY (`id_imovel`) REFERENCES `imoveis` (`id`),
  ADD CONSTRAINT `visitas_ibfk_2` FOREIGN KEY (`id_corretor`) REFERENCES `corretores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
