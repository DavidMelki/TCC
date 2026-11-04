-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04/11/2026 às 02:05
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
  `usuario_id` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `nome` varchar(100) DEFAULT 'Você'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `comentarios`
--

INSERT INTO `comentarios` (`id`, `sugestao_id`, `usuario_id`, `comentario`, `data_criacao`, `nome`) VALUES
(63, 96, 7, 'Não Concordo!', '2026-09-17 08:51:46', 'Alexandre'),
(64, 101, 6, 'SIM!', '2026-09-17 08:53:52', 'Julia'),
(65, 101, 10, 'super concordo rs', '2026-09-17 09:11:28', 'rafaela'),
(66, 97, 17, '😁', '2026-10-29 08:58:45', 'Leonardo Buso');

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
-- Estrutura para tabela `curtidas`
--

CREATE TABLE `curtidas` (
  `id` int(11) NOT NULL,
  `sugestao_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_curtida` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `curtidas`
--

INSERT INTO `curtidas` (`id`, `sugestao_id`, `usuario_id`, `data_curtida`) VALUES
(6, 97, 7, '2026-10-29 11:33:32'),
(7, 97, 16, '2026-10-29 11:34:04'),
(11, 97, 17, '2026-10-29 11:59:05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `favoritos_vendas`
--

CREATE TABLE `favoritos_vendas` (
  `id` int(11) NOT NULL,
  `venda_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `favoritos_vendas`
--

INSERT INTO `favoritos_vendas` (`id`, `venda_id`, `usuario_id`, `data_criacao`) VALUES
(1, 17, 7, '2026-10-29 11:41:01'),
(3, 17, 16, '2026-10-29 11:41:27'),
(4, 16, 16, '2026-10-29 11:41:28'),
(7, 17, 17, '2026-10-29 11:58:11'),
(10, 16, 17, '2026-10-29 11:58:13');

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `remetente_id` int(11) NOT NULL,
  `destinatario_id` int(11) NOT NULL,
  `mensagem` text NOT NULL,
  `anexo` varchar(255) DEFAULT NULL,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `lida` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `mensagens`
--

INSERT INTO `mensagens` (`id`, `remetente_id`, `destinatario_id`, `mensagem`, `anexo`, `data_envio`, `lida`) VALUES
(32, 18, 19, 'oi', NULL, '2026-11-04 00:59:54', 0);

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
(97, 'Minha sugestão é criar mais espaços de convivência na escola, com bancos, mesas e lugares para os alunos descansarem durante o intervalo. Acho que isso deixaria a escola mais confortável e agradável para todos.', '2026-09-01 21:29:06', 'Você', '@usuario', 3, 6),
(101, 'acho que a entrada na escola devia ser a partir das 9h', '2026-09-17 08:05:28', 'Você', '@usuario', 0, 14),
(102, 'yhgfg', '2026-11-03 21:59:03', 'Você', '@usuario', 0, 18);

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
  `ano` tinyint(4) DEFAULT NULL,
  `curso` varchar(100) DEFAULT NULL,
  `foto_capa` varchar(255) DEFAULT NULL,
  `tipo` enum('aluno','coordenador','professor','diretor') DEFAULT 'aluno',
  `curso_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `data_criacao`, `foto_perfil`, `biografia`, `ano`, `curso`, `foto_capa`, `tipo`, `curso_id`) VALUES
(18, 'Bruna de Mello', 'bruna@aluno.cps.sp.gov.br', '$2y$10$4cXkQZou2bTJcE3kZQkOa.N1ITBUKE2hTTS.3zgra9OQt/WpCOOPu', '2026-11-04 00:01:43', 'perfil_6aea76cee55501.75373739.jpeg', '', 3, 'Analise e Desenvolvimento de Sistemas', 'capa_6aea76cee63032.63165704.jpg', 'aluno', 3),
(19, 'Davi Afonso', 'daviafonso@cps.sp.gov.br', '$2y$10$ukJm/lNgc0o44PEXRQmjJOdwQHA5SHIsA0iNtWqkG7t7VeLiBjcNK', '2026-11-04 00:45:20', 'default.png', NULL, NULL, NULL, NULL, 'diretor', 4);

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
(24, 18, 'jbh', 'R$ 5,84', 'segundo andar', 'ijhjo', '../uploads/venda_6aea83778a8a46.31782275.jpg', '2026-11-03 21:59:35');

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
-- Índices de tabela `curtidas`
--
ALTER TABLE `curtidas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `curtida_unica` (`sugestao_id`,`usuario_id`);

--
-- Índices de tabela `favoritos_vendas`
--
ALTER TABLE `favoritos_vendas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unico_favorito` (`venda_id`,`usuario_id`);

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `remetente_id` (`remetente_id`),
  ADD KEY `destinatario_id` (`destinatario_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

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
-- AUTO_INCREMENT de tabela `curtidas`
--
ALTER TABLE `curtidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `favoritos_vendas`
--
ALTER TABLE `favoritos_vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `sugestoes`
--
ALTER TABLE `sugestoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
-- Restrições para tabelas `mensagens`
--
ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`remetente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`destinatario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

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
