-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17/09/2026 às 03:40
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
-- Banco de dados: `eicamargo`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `sugestao_id` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `nome` varchar(100) DEFAULT 'Você'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `comentarios`
--

INSERT INTO `comentarios` (`id`, `sugestao_id`, `comentario`, `data_criacao`, `nome`) VALUES
(54, 96, 'Comentario', '2026-08-27 08:05:50', 'Você');

-- --------------------------------------------------------

--
-- Estrutura para tabela `comunicados`
--

CREATE TABLE `comunicados` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `curso_id` int(11) DEFAULT NULL,
  `titulo` varchar(150) NOT NULL,
  `conteudo` text NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `comunicados`
--

INSERT INTO `comunicados` (`id`, `usuario_id`, `curso_id`, `titulo`, `conteudo`, `data_criacao`) VALUES
(5, 12, NULL, 'Expocuca – Participe!', 'No dia 25/09 será realizada nossa Expocuca! O evento contará com apresentações, exposições de trabalhos, atividades e momentos de interação entre os alunos.\r\n\r\nTodos estão convidados a participar e prestigiar os trabalhos desenvolvidos pelas turmas. Contamos com a presença de todos!', '2026-09-16 22:26:52'),
(6, 11, 1, 'Visita Técnica', 'Informamos aos alunos do curso de Administração que será realizada uma visita técnica a uma empresa parceira no dia 30/09. A atividade terá como objetivo proporcionar aos alunos uma experiência prática sobre o funcionamento e a organização de uma empresa.\r\n\r\nOs alunos interessados deverão confirmar sua participação com a coordenação do curso até o dia 25/09.', '2026-09-16 22:28:24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `codigo_coordenador` varchar(20) NOT NULL,
  `codigo_aluno` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cursos`
--

INSERT INTO `cursos` (`id`, `nome`, `codigo_coordenador`, `codigo_aluno`) VALUES
(1, 'Administração', 'ADM-COORD-01', 'ADM-ALUNO-01'),
(2, 'Gastronomia', 'GAS-COORD-02', 'GAS-ALUNO-02'),
(3, 'Análise e Desenvolvimento de Sistemas', 'ADS-COORD-03', 'ADS-ALU-03'),
(4, 'Direção', 'DRC-COORD-04', 'DRC-ALUNO-04');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sugestoes`
--

CREATE TABLE `sugestoes` (
  `id` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `nome` varchar(100) DEFAULT 'Você',
  `usuario` varchar(100) DEFAULT '@usuario',
  `likes` int(11) DEFAULT 0,
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `sugestoes`
--

INSERT INTO `sugestoes` (`id`, `descricao`, `data_criacao`, `nome`, `usuario`, `likes`, `usuario_id`) VALUES
(96, 'Sugiro que...', '2026-08-27 08:00:23', 'Você', '@usuario', 0, 5),
(97, 'Minha sugestão é criar mais espaços de convivência na escola, com bancos, mesas e lugares para os alunos descansarem durante o intervalo. Acho que isso deixaria a escola mais confortável e agradável para todos.', '2026-09-01 21:29:06', 'Você', '@usuario', 0, 6);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto_perfil` varchar(255) DEFAULT 'default.png',
  `biografia` text DEFAULT NULL,
  `foto_capa` varchar(255) DEFAULT NULL,
  `tipo` enum('aluno','coordenador') DEFAULT 'aluno',
  `curso_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `data_criacao`, `foto_perfil`, `biografia`, `foto_capa`, `tipo`, `curso_id`) VALUES
(5, 'Bruna', 'bruna@gmail.com', '$2y$10$6QVe3VrPsNKyzTZlQtqGpOckexTK30lvqYZp/1VnCHfjywxPHKjqG', '2026-08-27 10:59:21', 'perfil_5_1787832005.png', 'Terceiro ano do curso Análise e Desenvolvimento de Sistemas.', NULL, 'aluno', 3),
(6, 'Julia', 'julia@gmail.com', '$2y$10$HbW4KOcTYEKTRVANHJLCXet/EbZ7MdUXdCbZcNyKFzviT0AehGHai', '2026-09-02 00:26:58', 'perfil_6a977611ceeda7.85533089.jpg', 'Terceiro ano de Analise e Desenvolvimento de Sistemas', 'capa_6a977611cf7165.09826406.jpg', 'aluno', 3),
(7, 'Alexandre', 'alexandre@gmail.com', '$2y$10$eh4HUdnoTsNTP6orUzts.uGX6ToNMc3rAX2MXVJ5quZmmFAfFEPge', '2026-09-02 00:50:13', 'default.png', NULL, NULL, 'aluno', 3),
(8, 'Bruno Cano', 'bruno@gmail.com', '$2y$10$OW5MiobE82mYjIssNxsvP.2ZNRpS.PwYh.GzafESsCkpQeTz/BlKe', '2026-09-17 00:06:06', 'default.png', NULL, NULL, 'coordenador', 3),
(10, 'rafaela', 'rafaela@gmail.com', '$2y$10$6RupzRShpOYMwkkz./NM2uiw83sOBcmM1RCSJ8prFWMTBLl1Y/nZe', '2026-09-17 01:15:21', 'default.png', NULL, NULL, 'aluno', 1),
(11, 'Alice', 'alice@gmail.com', '$2y$10$H3Ua84vvLEYsRuzC6shnDuOjrlOmueXjVCQf6EVItl4.YlixhUaku', '2026-09-17 01:20:09', 'default.png', NULL, NULL, 'coordenador', 1),
(12, 'Davi Afonso', 'afonso@gmail.com', '$2y$10$zUG3HyU.7tMA7ndvdvFwse8M8qJkW.gR5oZ6DLyRPHu0gL7NFyZgW', '2026-09-17 01:25:33', 'default.png', NULL, NULL, 'coordenador', 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

CREATE TABLE `vendas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `preco` varchar(50) NOT NULL,
  `local` varchar(100) DEFAULT NULL,
  `legenda` text NOT NULL,
  `midia` varchar(255) DEFAULT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vendas`
--

INSERT INTO `vendas` (`id`, `usuario_id`, `titulo`, `preco`, `local`, `legenda`, `midia`, `data_criacao`) VALUES
(9, 5, 'Doce Crocante de Amendoim', 'R$ 8,00', 'primeiro andar', 'Crocante, dourado e delicioso, feito com amendoim e caramelo.', '../uploads/venda_6a902d13604d12.18005686.png', '2026-08-27 09:26:59'),
(10, 5, 'Ovo de Chocolate com Morango', 'R$ 18,00', 'Pátio', 'Chocolate cremoso recheado com creme e pedaços de morango fresco.', '../uploads/venda_6a902d3c4cd627.95478877.png', '2026-08-27 09:27:40'),
(11, 5, 'Taça de Morango', 'R$ 15,00', '3 andar', 'Camadas de creme suave, morangos frescos e uma deliciosa calda de frutas.', '../uploads/venda_6a902d6312f1c6.38747826.png', '2026-08-27 09:28:19'),
(12, 5, 'Donuts', 'R$ 7,00', 'Primeiro andar', 'Donuts macios e fofinhos, cobertos com chocolate, glacê e confeitos coloridos.', '../uploads/venda_6a902d8bd038f6.79126338.png', '2026-08-27 09:28:59'),
(13, 5, 'Paçoquita', 'R$ 2,00', 'segundo andar', 'Doce artesanal de amendoim com textura macia, sabor marcante e o toque caseiro perfeito. Ideal para festas juninas, sobremesa ou acompanhamento de café.', '../uploads/venda_6a902e11159735.34096558.png', '2026-08-27 09:31:13'),
(14, 5, 'Cheesecake de Morango', 'R$ 14,00', 'Pátio', 'Sobremesa clássica com base crocante de biscoito, recheio leve e cremoso de cream cheese, coberta com generosa camada de morangos frescos e folhas de hortelã. (Fatia)', '../uploads/venda_6a902e4c930966.90253957.png', '2026-08-27 09:32:12'),
(15, 5, 'Brigadeiros Gourmet', 'R$ 35,00', '', 'Seleção especial de brigadeiros artesanais e doces finos, incluindo opções tradicionais, de coco, nozes e decorados com frutas (como physalis). Perfeito para eventos, festas ou presentes. (caixa presenteável com 12 unidades).', '../uploads/venda_6a902e818ee2b4.78528111.png', '2026-08-27 09:33:05'),
(16, 6, 'Torta de Morango com Caramelo', 'R$ 12,00', 'Pátio', 'Deliciosa tortinha com recheio cremoso, coberta com uma generosa camada de caramelo e finalizada com castanhas crocantes. Uma combinação irresistível de cremosidade e crocância!', '../uploads/venda_6a9779cea1d092.82249371.jpg', '2026-09-01 22:20:14');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sugestao_id` (`sugestao_id`);

--
-- Índices de tabela `comunicados`
--
ALTER TABLE `comunicados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Índices de tabela `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_coordenador` (`codigo_coordenador`),
  ADD UNIQUE KEY `codigo_aluno` (`codigo_aluno`);

--
-- Índices de tabela `sugestoes`
--
ALTER TABLE `sugestoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuarios_cursos` (`curso_id`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de tabela `comunicados`
--
ALTER TABLE `comunicados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `sugestoes`
--
ALTER TABLE `sugestoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`sugestao_id`) REFERENCES `sugestoes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `comunicados`
--
ALTER TABLE `comunicados`
  ADD CONSTRAINT `comunicados_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `comunicados_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`);

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_cursos` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`);

--
-- Restrições para tabelas `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
