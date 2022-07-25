-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25-Jul-2022 às 04:02
-- Versão do servidor: 10.4.24-MariaDB
-- versão do PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `santri`
--
CREATE DATABASE IF NOT EXISTS `santri` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `santri`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `autorizacoes`
--

CREATE TABLE `autorizacoes` (
  `USUARIO_ID` int(11) UNSIGNED NOT NULL,
  `CHAVE_AUTORIZACAO` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `autorizacoes`
--

INSERT INTO `autorizacoes` (`USUARIO_ID`, `CHAVE_AUTORIZACAO`) VALUES
(1, 'cadastrar_clientes'),
(1, 'excluir_clientes'),
(2, 'cadastrar_usuarios'),
(5, 'cadastrar_clientes'),
(5, 'editar_clientes');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `USUARIO_ID` int(11) UNSIGNED NOT NULL,
  `LOGIN` varchar(30) NOT NULL,
  `SENHA` varchar(30) NOT NULL,
  `ATIVO` varchar(1) NOT NULL DEFAULT 'S',
  `NOME_COMPLETO` varchar(50) DEFAULT NULL,
  `TOKEN` char(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`USUARIO_ID`, `LOGIN`, `SENHA`, `ATIVO`, `NOME_COMPLETO`, `TOKEN`) VALUES
(1, 'MARIA', '123', 'S', 'Maria do Carmo', 'ecb8e3f524666f3e280e87ef96a7750b07a74338ba296d81304d51139392071d'),
(2, 'Joao', '12345', 'N', 'Joao Constantino', NULL),
(5, 'Teste', 'teste', 'S', 'Teste', NULL);

-- --------------------------------------------------------

--
-- Estrutura stand-in para vista `vw_autorizacoes`
-- (Veja abaixo para a view atual)
--
CREATE TABLE `vw_autorizacoes` (
`CHAVE` varchar(22)
,`DESCRICAO` varchar(25)
);

-- --------------------------------------------------------

--
-- Estrutura para vista `vw_autorizacoes`
--
DROP TABLE IF EXISTS `vw_autorizacoes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_autorizacoes`  AS SELECT 'cadastrar_clientes' AS `CHAVE`, 'Cadastrar clientes' AS `DESCRICAO` union all select 'excluir_clientes' AS `CHAVE`,'Excluir clientes' AS `DESCRICAO` union all select 'cadastrar_fornecedores' AS `CHAVE`,'Cadastrar fornecedores' AS `DESCRICAO` union all select 'excluir_fornecedores' AS `CHAVE`,'Excluir fornecedores' AS `DESCRICAO` union all select 'cadastrar_produtos' AS `CHAVE`,'Cadastrar produtos' AS `DESCRICAO` union all select 'alterar_preco_produtos' AS `CHAVE`,'Alterar preço de produtos' AS `DESCRICAO`  ;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `autorizacoes`
--
ALTER TABLE `autorizacoes`
  ADD PRIMARY KEY (`USUARIO_ID`,`CHAVE_AUTORIZACAO`) USING BTREE;

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`USUARIO_ID`),
  ADD UNIQUE KEY `IDX_USUARIOS_LOGIN` (`LOGIN`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `USUARIO_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `autorizacoes`
--
ALTER TABLE `autorizacoes`
  ADD CONSTRAINT `FK_USUARIOS_AUTORIZACOES` FOREIGN KEY (`USUARIO_ID`) REFERENCES `usuarios` (`USUARIO_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
