-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 15/02/2025 às 13:17
-- Versão do servidor: 10.6.20-MariaDB-cll-lve
-- Versão do PHP: 8.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `gestorplayer_gestor`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `campaigns`
--

CREATE TABLE `campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `ignore_previous_contacts` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `campanhas`
--

CREATE TABLE `campanhas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `horario` time NOT NULL,
  `ultima_execucao` timestamp NULL DEFAULT NULL,
  `data` date DEFAULT NULL,
  `contatos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `origem_contatos` varchar(255) NOT NULL,
  `ignorar_contatos` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `mensagem` text DEFAULT NULL,
  `arquivo` varchar(255) DEFAULT NULL,
  `enviar_diariamente` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `sender` enum('user','cliente') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL DEFAULT 3,
  `nome` varchar(255) NOT NULL,
  `iptv_nome` varchar(255) DEFAULT NULL,
  `iptv_senha` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `whatsapp` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `vencimento` date NOT NULL,
  `servidor_id` bigint(20) UNSIGNED NOT NULL,
  `mac` varchar(255) DEFAULT NULL,
  `notificacoes` tinyint(1) NOT NULL,
  `plano_id` bigint(20) UNSIGNED NOT NULL,
  `numero_de_telas` int(11) NOT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `role_id`, `nome`, `iptv_nome`, `iptv_senha`, `user_id`, `whatsapp`, `password`, `vencimento`, `servidor_id`, `mac`, `notificacoes`, `plano_id`, `numero_de_telas`, `notas`, `created_at`, `updated_at`) VALUES
(131, 3, 'ADRIANA APARECIDA', NULL, NULL, 116, '11956051363', 'Rodolfo01#', '2025-02-15', 58, NULL, 1, 61, 1, '11940429737', '2025-02-14 03:13:40', '2025-02-14 03:13:40'),
(132, 3, 'AFONSO SILVA', NULL, NULL, 116, '11964214478', 'Rodolfo01#', '2025-03-11', 58, NULL, 1, 61, 1, '11040429737', '2025-02-14 03:15:59', '2025-02-14 03:15:59'),
(133, 3, 'ALBERTO', NULL, NULL, 116, '8585710717', 'Rodolfo01#', '2025-02-22', 58, NULL, 1, 61, 1, '11940429737', '2025-02-14 03:17:42', '2025-02-14 03:17:42'),
(134, 3, 'ANA PATRICIA', NULL, NULL, 116, '11945891384', 'Rodolfo01#', '2025-02-28', 58, NULL, 1, 61, 1, '11940429737', '2025-02-14 03:19:32', '2025-02-14 03:19:32'),
(135, 3, 'ANDRE INDICAÇAO DANIEL', NULL, NULL, 116, '4192396503', 'Rodolfo01#', '2025-03-04', 58, NULL, 1, 61, 1, '11940429737', '2025-02-14 03:21:53', '2025-02-14 03:21:53'),
(136, 3, 'ANDRE SANCHES', NULL, NULL, 116, '12991821161', 'Rodolfo01#', '2025-02-20', 58, NULL, 1, 61, 1, '11940429737', '2025-02-14 03:23:43', '2025-02-14 03:23:43'),
(137, 3, 'ANDRESSA GOMES', NULL, NULL, 116, '21966074128', 'Rodolfo01#', '2025-02-22', 58, NULL, 1, 61, 2, '11940429737', '2025-02-14 03:25:34', '2025-02-14 03:25:34'),
(138, 3, 'ANGELICA', NULL, NULL, 116, '11972872179', 'Rodolfo01#', '2025-02-28', 58, NULL, 1, 61, 1, '11940429737', '2025-02-14 03:29:08', '2025-02-14 03:29:08'),
(139, 3, 'ANITTA', NULL, NULL, 116, '21974729718', 'Rodolfo01#', '2025-02-28', 58, NULL, 1, 61, 2, '11940429737', '2025-02-14 03:32:43', '2025-02-14 03:33:21'),
(140, 3, 'ANTONIO CPS', NULL, NULL, 116, '19971036328', 'Rodolfo01#', '2025-02-26', 58, NULL, 1, 61, 1, '11940429737', '2025-02-14 03:35:29', '2025-02-14 03:35:29'),
(141, 3, 'AQUINALIA CORREA', NULL, NULL, 116, '19991553902', 'Rodolfo01#', '2025-03-19', 58, NULL, 1, 61, 1, '11940429737', '2025-02-14 03:37:16', '2025-02-14 03:37:16'),
(142, 3, 'BEATRIZ GOMES', NULL, NULL, 116, '16991840555', 'Rodolfo01#', '2025-02-28', 58, NULL, 1, 61, 1, '11940429737', '2025-02-14 03:38:55', '2025-02-14 03:38:55'),
(143, 3, 'BILAO', NULL, NULL, 116, '19992810820', 'Rodolfo01#', '2025-03-06', 58, NULL, 1, 61, 1, '11940429737', '2025-02-14 03:40:47', '2025-02-14 03:40:47'),
(144, 3, 'BRANCO CADRE', NULL, NULL, 116, '19989212089', 'Rodolfo01#', '2025-03-10', 58, NULL, 1, 61, 1, '11940429737', '2025-02-14 03:42:15', '2025-02-14 03:42:15'),
(145, 3, 'BRUNO', NULL, NULL, 116, '11982373759', 'Rodolfo01#', '2025-02-28', 58, NULL, 1, 61, 1, '11940429737', '2025-02-14 03:43:58', '2025-02-14 03:43:58'),
(146, 3, 'BUJA ARY', NULL, NULL, 116, '13991682243', 'Rodolfo01#', '2025-02-24', 58, NULL, 1, 61, 1, '11940429737', '2025-02-14 03:45:19', '2025-02-14 03:45:19');

-- --------------------------------------------------------

--
-- Estrutura para tabela `company_details`
--

CREATE TABLE `company_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_whatsapp` varchar(255) NOT NULL,
  `access_token` varchar(255) DEFAULT NULL,
  `notification_url` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `company_logo` varchar(255) DEFAULT NULL,
  `pix_manual` varchar(255) DEFAULT NULL,
  `referral_balance` decimal(8,2) DEFAULT NULL,
  `api_session` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `public_key` varchar(255) DEFAULT NULL,
  `site_id` varchar(255) DEFAULT NULL,
  `evolution_api_url` varchar(255) DEFAULT NULL,
  `evolution_api_key` varchar(255) DEFAULT NULL,
  `not_gateway` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `company_details`
--

INSERT INTO `company_details` (`id`, `user_id`, `company_name`, `company_whatsapp`, `access_token`, `notification_url`, `favicon`, `company_logo`, `pix_manual`, `referral_balance`, `api_session`, `created_at`, `updated_at`, `public_key`, `site_id`, `evolution_api_url`, `evolution_api_key`, `not_gateway`) VALUES
(15, 3, 'Gestor HL', '71997279885', 'TokenMP', 'https://gestorplayer.fun/webhook/mercadopago', '/assets/img/favicons/Post instagram de marketing digital ousado preto e vermelho (1).png', '/assets/img/logos/Post instagram de marketing digital ousado preto e vermelho (1).png', '088888888', 10.00, NULL, '2024-10-12 03:45:55', '2024-10-17 17:37:28', 'PublicMP', 'MLB', 'https://api.gestorplayer.fun', '78ff9d8fb835ae86b454e81e9f412f62', 1),
(16, 114, 'Gestor Play', '71 99727-9885', 'r', 'https://gestorplayer.fun/webhook/mercadopago', '/assets/img/favicons/Create.xyz Brasil.png', '/assets/img/logos/Create.xyz Brasil.png', '42781524859', NULL, NULL, '2025-02-13 18:40:09', '2025-02-15 16:13:09', NULL, 'MLB', 'https://api.infinitehost.live', '484a69927079dc0874790eb09e2f9dcb', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `conexoes`
--

CREATE TABLE `conexoes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `qrcode` text DEFAULT NULL,
  `conn` tinyint(1) NOT NULL DEFAULT 0,
  `whatsapp` varchar(255) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_alteracao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `tokenid` varchar(255) DEFAULT NULL,
  `notifica` tinyint(1) NOT NULL DEFAULT 0,
  `saudacao` varchar(255) DEFAULT NULL,
  `arquivo` varchar(255) DEFAULT NULL,
  `midia` varchar(255) DEFAULT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `conexoes`
--

INSERT INTO `conexoes` (`id`, `user_id`, `qrcode`, `conn`, `whatsapp`, `data_cadastro`, `data_alteracao`, `tokenid`, `notifica`, `saudacao`, `arquivo`, `midia`, `tipo`, `created_at`, `updated_at`) VALUES
(43, 3, 'data:image/png;base64,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', 1, '11942829450', '2024-10-12 01:48:20', '2024-10-12 01:48:42', 'd613af77abcafc099220a258190fbe7e', 0, NULL, NULL, NULL, NULL, '2024-10-12 03:48:20', '2024-10-12 03:48:42');

-- --------------------------------------------------------

--
-- Estrutura para tabela `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `indicacoes`
--

CREATE TABLE `indicacoes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `referred_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `ganhos` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `indicacoes`
--

INSERT INTO `indicacoes` (`id`, `user_id`, `referred_id`, `status`, `ganhos`, `created_at`, `updated_at`) VALUES
(9, 116, 117, 'pendente', 0.00, '2025-02-11 01:14:09', '2025-02-11 01:14:09');

-- --------------------------------------------------------

--
-- Estrutura para tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2014_10_12_200000_add_two_factor_columns_to_users_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2024_08_20_090459_create_roles_and_permissions_tables', 1),
(7, '2024_08_29_154753_create_sessions_table', 1),
(8, '2024_08_29_173140_add_trial_ends_at_to_users_table', 1),
(10, '2024_08_29_175225_create_roles_table', 2),
(13, '2024_08_29_193522_add_profile_photo_url_to_users_table', 5),
(12, '2024_08_29_211333_add_status_to_users_table', 4),
(16, '2024_08_29_214223_create_conexoes_table', 6),
(31, '2024_08_31_194807_create_pagamentos_table', 13),
(18, '2024_08_30_105433_create_servidores_table', 8),
(29, '2024_08_31_160355_create_templates_table', 11),
(28, '2024_08_30_111641_update_clientes_table_nullable_fields', 10),
(30, '2024_08_30_103737_create_clientes_table', 12),
(36, '2024_09_01_015616_create_company_details_table', 17),
(33, '2024_09_02_011112_create_indicacoes_table', 15),
(34, '2024_09_02_024111_add_ganhos_to_indicacoes_table', 16),
(38, '2024_09_03_214007_create_planos_renovacao_table', 18),
(39, '2024_09_04_001535_add_plano_id_to_users_table', 19),
(40, '2024_09_04_230720_add_api_session_to_company_details_table', 20),
(41, '2024_09_04_234614_add_limite_to_planos_renovacao_table', 21),
(42, '2024_09_05_001515_add_limite_to_users_table', 22),
(43, '2024_09_05_032130_alter_pagamentos_table_nullable_cliente_id', 23),
(45, '2024_09_05_191941_add_duracao_to_planos_renovacao_table', 24),
(47, '2024_09_05_235507_create_revendas_table', 25),
(48, '2024_09_06_014140_add_nome_to_revendas_table', 26),
(49, '2024_09_06_021658_add_preco_por_credito_to_revendas_table', 27),
(50, '2024_09_06_022044_add_creditos_to_users_table', 28),
(51, '2024_09_06_225454_add_user_id_to_users_table', 29),
(55, '2024_09_11_022806_create_user_data_table', 30),
(56, '2024_09_12_024122_add_plano_id_and_is_anual_to_pagamentos_table', 31),
(57, '2024_09_13_015958_add_payment_date_to_pagamentos_table', 32),
(61, '2024_09_14_054916_add_credito_id_to_pagamentos_table', 34),
(60, '2024_09_13_042927_add_role_id_to_clientes_table', 33),
(62, '2024_09_16_023514_create_campanhas_table', 35),
(63, '2024_09_17_012705_create_schedule_settings_table', 36),
(64, '2024_09_17_202451_add_mensagem_and_arquivo_to_campanhas_table', 37),
(65, '2024_09_18_140628_add_public_key_and_site_id_to_company_details_table', 38),
(66, '2024_09_18_144534_add_evolution_api_columns_to_company_details_table', 39),
(67, '2024_09_19_163217_add_not_gateway_to_company_details_table', 40),
(68, '2024_09_19_184939_add_status_to_schedule_settings_table', 41),
(69, '2024_09_19_194041_add_data_to_campanhas_table', 42),
(70, '2024_09_20_022256_create_plugins_table', 43),
(71, '2024_09_23_140519_add_enviar_diariamente_to_campanhas_table', 44),
(72, '2024_09_25_012745_add_ultima_execucao_to_campanhas_table', 45),
(73, '2024_09_25_021254_add_notification_url_to_company_details_table', 46),
(74, '2024_09_25_133950_alter_templates_table_charset', 47),
(75, '2024_09_25_151344_add_iptv_nome_to_clientes_table', 48),
(76, '2024_09_26_182230_add_iptv_senha_to_clientes_table', 49),
(77, '2024_09_28_034740_create_chat_messages_table', 50),
(78, '2024_10_15_204040_add_favicon_to_company_details_table', 50),
(79, '2024_10_15_212312_add_use_saldo_ganhos_to_pagamentos_table', 50);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `plano_id` bigint(20) UNSIGNED NOT NULL,
  `isAnual` tinyint(1) NOT NULL,
  `mercado_pago_id` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `credito_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `use_saldo_ganhos` tinyint(1) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `permission_role`
--

CREATE TABLE `permission_role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos`
--

CREATE TABLE `planos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `preco` decimal(8,2) NOT NULL,
  `duracao` int(11) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `planos`
--

INSERT INTO `planos` (`id`, `nome`, `preco`, `duracao`, `user_id`, `created_at`, `updated_at`) VALUES
(60, 'power', 25.00, 30, 114, '2025-02-10 23:56:07', '2025-02-10 23:56:07'),
(61, 'IPTV MENSAL', 25.00, 30, 116, '2025-02-11 00:21:09', '2025-02-11 00:21:09'),
(62, 'IPTV TRIMESTRAL', 70.00, 90, 116, '2025-02-11 00:21:50', '2025-02-11 00:21:50'),
(63, 'IPTV SEMESTRAL', 130.00, 180, 116, '2025-02-11 00:23:28', '2025-02-11 00:32:05'),
(64, 'ANUAL', 300.00, 365, 116, '2025-02-11 00:35:51', '2025-02-11 00:36:35');

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos_renovacao`
--

CREATE TABLE `planos_renovacao` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `detalhes` text DEFAULT NULL,
  `botao` varchar(255) DEFAULT NULL,
  `limite` int(11) NOT NULL DEFAULT 0,
  `duracao` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `planos_renovacao`
--

INSERT INTO `planos_renovacao` (`id`, `nome`, `descricao`, `preco`, `detalhes`, `botao`, `limite`, `duracao`, `created_at`, `updated_at`) VALUES
(14, 'Básico', 'Ideal para iniciantes', 0.00, 'Até 20 clientes\r\nGerenciamento básico\r\nPlano Free', 'Seu Plano Atual', 100, NULL, '2024-09-05 04:12:53', '2024-09-25 13:42:26'),
(15, 'Pro', 'Para pequenas e médias empresas', 1.00, 'Até 500 clientes\r\nGerenciamento avançado\r\nSuporte prioritário', 'Upgrade', 500, NULL, '2024-09-05 04:13:11', '2024-09-27 00:42:16'),
(16, 'Enterprise', 'Para grandes organizações', 0.00, 'Clientes ilimitados\r\nGerenciamento completo\r\nSuporte dedicado', 'Assine', 5000000, NULL, '2024-09-05 04:13:17', '2025-02-10 23:39:39'),
(20, 'plano premiu', 'Para pequenas e médias empresas', 70.00, 'Até 1000 clientes\r\nGerenciamento avançado\r\nSuporte prioritário', 'Upgrade', 1000, NULL, '2025-02-10 23:37:35', '2025-02-10 23:37:35');

-- --------------------------------------------------------

--
-- Estrutura para tabela `revendas`
--

CREATE TABLE `revendas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `creditos` int(11) NOT NULL,
  `preco` decimal(8,2) NOT NULL,
  `total` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `revendas`
--

INSERT INTO `revendas` (`id`, `nome`, `user_id`, `creditos`, `preco`, `total`, `created_at`, `updated_at`) VALUES
(1, 'Plano 10 Créditos', 3, 10, 0.01, 0.10, '2024-09-06 03:05:47', '2025-02-11 00:04:52');

-- --------------------------------------------------------

--
-- Estrutura para tabela `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'admin', '2024-08-29 20:55:46', '2024-08-29 20:55:46'),
(2, 'master', '2024-08-29 20:55:46', '2024-08-29 20:55:46'),
(3, 'cliente', '2024-08-29 20:55:46', '2024-08-29 20:55:46'),
(4, 'revendedor', '2024-09-06 18:00:59', '2024-09-06 18:00:59');

-- --------------------------------------------------------

--
-- Estrutura para tabela `schedule_settings`
--

CREATE TABLE `schedule_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `finalidade` varchar(255) NOT NULL,
  `command` varchar(255) NOT NULL,
  `execution_time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `servidores`
--

CREATE TABLE `servidores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `servidores`
--

INSERT INTO `servidores` (`id`, `user_id`, `nome`, `created_at`, `updated_at`) VALUES
(55, 112, 'Evolution', '2025-02-10 14:55:52', '2025-02-10 14:55:52'),
(56, 114, 'power', '2025-02-10 18:01:22', '2025-02-10 18:01:22'),
(57, 114, 'power', '2025-02-10 18:01:23', '2025-02-10 18:01:23'),
(58, 116, 'ONITRON', '2025-02-10 23:50:56', '2025-02-10 23:50:56');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('dkleyJlCKWkdowlbePeuzZJiLniVTLVHwvIYB6s8', NULL, '177.125.174.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 OPR/116.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNEJIZWxjSENJTkZRckRNMHNuMXNheWxFWkZBa01pU0syUHlVQ3N5NCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHBzOi8vZ2VzdG9ycGxheWVyLmZ1bi9hdXRoL2xvZ2luLWJhc2ljIjt9fQ==', 1739636096),
('mY9GW2ckQZSd2mKq55GpLLSpnl3lgPqf7NSYVfUE', NULL, '170.84.117.90', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRUpzckliY0Z4aXhiUHJUdUc2dVdOcm1wSTlMSnI5SXpPV3Z5eUFoMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vZ2VzdG9ycGxheWVyLmZ1bi9zdGF0dXMtZG9tYWluIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739636193),
('6vEpdXpoPpuwWqF0uBDxRNG8uY8tnZAEAeV997Ar', NULL, '177.125.174.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 OPR/116.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNWhETWdPakhsVWplWjdhNGtaa3hqakh6WjhSd0pnM3paY0pGNzBZZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vZ2VzdG9ycGxheWVyLmZ1bi9zdGF0dXMtZG9tYWluIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739635013),
('6whjlZAOppEboWOCt941gK4VsrGGnXoA22UFxCY5', NULL, '43.157.207.78', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRGlNbVNTVnNjRmhsUEYzZ3VSZ1RyWHhUakRWOEQ2akVsSFlLaEN2eiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDc6Imh0dHA6Ly93d3cuZ2VzdG9ycGxheWVyLmZ1bi9mcm9udC1wYWdlcy9sYW5kaW5nIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1739632212),
('9eqabMUunHEEj4ziuTT20WbM3UJ9byyCLhG4WaH5', NULL, '43.157.207.78', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNDJUUkZ5UU1ZVXcyUFcwN0VramN0YUY4RGJTWlVqSmdNTTh2cEQyTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly93d3cuZ2VzdG9ycGxheWVyLmZ1biI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1739632209);

-- --------------------------------------------------------

--
-- Estrutura para tabela `templates`
--

CREATE TABLE `templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `finalidade` varchar(255) NOT NULL,
  `conteudo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `templates`
--

INSERT INTO `templates` (`id`, `user_id`, `nome`, `finalidade`, `conteudo`, `created_at`, `updated_at`) VALUES
(39, 114, 'hl', 'cobranca_hoje', 'Olá #NOMECOMPLETO#\r\n\r\n🟦Este é apenas um lembrete da sua fatura vence hoje: \r\n\r\nDADOS DA FATURA\r\n------------------------------------\r\n🔸Vencimento: #VENCIMENTO# \r\n🔸Valor da Fatura: R$ #VALOR#\r\n------------------------------------\r\n\r\nPara facilitar o pagamento você pode utilizar nosso PIX:\r\n🔑 . .  Nome do banco: picpay > titula :\r\nApós o pagamento encaminhar o comprovante via whatsapp.', '2025-02-10 23:55:40', '2025-02-10 23:55:40'),
(40, 116, 'Renovado', 'pagamentos', 'Olá {saudacao}!!! {nome_cliente}\r\n\r\n✅Estamos confirmando o pagamento da fatura:\r\n\r\nDADOS DA FATURA\r\n------------------------------------\r\n🔸 Próximo Vencimento:  {vencimento_cliente}\r\n🔸Valor da Fatura: R$ {plano_valor}\r\n------------------------------------\r\n\r\nQualquer dúvida, estaremos sempre a disposição.\r\n\r\nObrigado!   \r\n\r\n\r\n\r\nAtt: {nome_empresa}', '2025-02-11 00:00:46', '2025-02-11 00:01:31'),
(41, 116, 'Vencido', 'vencidos', 'Olá {saudacao} *{nome_cliente}*\r\n\r\n🟥 *ESTE É APENAS UM LEMBRETE DA SUA FATURA QUE VENCEU.*\r\n\r\nDADOS DA FATURA\r\n------------------------------------\r\n🔸*VENCIMENTO:* {vencimento_cliente} \r\n\r\n🔸*VALOR DA FATURA:* R$ {plano_valor}\r\n🔸Sub-Total: R$ {plano_valor}\r\n------------------------------------\r\n\r\n\r\n*PARA FACILITAR O PAGAMENTO VOCÊ PODE UTILIZAR O LINK DE PAGAMENTO OU NOSSO PIX:*\r\n\r\n*LINK DE PAGAMENTO VIA MERCADO PAGO:* \r\n{plano_link}\r\n\r\n🔑 *PIX É CELULAR:* {notas}\r\n*BANCO:* NUBANK\r\n*TITULAR:* LETICIA NASCIMENTO \r\n\r\nAPÓS O PAGAMENTO ENCAMINHAR O COMPROVANTE VIA WHATSAPP.\r\n\r\nAtt: *{nome_empresa}*', '2025-02-11 02:15:55', '2025-02-11 02:15:55');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `whatsapp` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo_url` varchar(255) NOT NULL DEFAULT '/assets/img/avatars/1.png',
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'ativo',
  `plano_id` bigint(20) UNSIGNED DEFAULT NULL,
  `limite` int(11) NOT NULL DEFAULT 0,
  `creditos` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `user_id`, `name`, `whatsapp`, `password`, `profile_photo_url`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `role_id`, `trial_ends_at`, `remember_token`, `created_at`, `updated_at`, `status`, `plano_id`, `limite`, `creditos`) VALUES
(112, NULL, 'Felipe Fernando', '11987751081', '$2y$10$pCxitcuw1bto1R31FICU3OLW6Kw6yswHwsmgjJJ/uxp27qEB6Z/GG', '/assets/img/avatars/14.png', NULL, NULL, NULL, 1, '2025-02-13 14:41:52', NULL, '2025-02-10 14:41:52', '2025-02-10 14:41:52', 'ativo', 14, 100, 100),
(114, 3, 'Henrique', '71997279885', '$2y$10$rGc/FBu/2aZ48MWx.MCgHeOcfMy8KQrtPunc8TogScekdnCXZobGC', '/assets/img/avatars/Imagem do WhatsApp de 2025-02-06 à(s) 20.49.09_f9dbf2f6.jpg', NULL, NULL, NULL, 1, '2025-02-13 17:49:39', NULL, '2025-02-10 17:49:39', '2025-02-10 18:22:30', 'ativo', 14, 100, 1000),
(115, NULL, 'luis', '71999208240', '$2y$10$NcXJ9AuPDwezRbD1WgZcDe.CosIQHajOiaYGt3WGOissCn.mwMWLe', '/assets/img/avatars/14.png', NULL, NULL, NULL, 2, '2028-12-30 03:00:00', NULL, '2025-02-10 23:30:15', '2025-02-11 00:22:27', 'ativo', 16, 5000000, 1000000000),
(116, NULL, 'Rodolfo01', '11970444881', '$2y$10$yU2WWFCFCRytyGA2VE4wOucbKuIHkQi0tX6.YXyi01J1uAVy/9gxW', '/assets/img/avatars/IMG-20250113-WA0134.jpg', NULL, NULL, NULL, 2, '2027-11-12 03:00:00', NULL, '2025-02-10 23:33:19', '2025-02-14 03:45:19', 'ativo', 16, 4999984, 999999999),
(117, NULL, 'Teste01', '11977339226', '$2y$10$jIDogkVc.P3p1qg69TVhjO1Ou3PmAY3meDV/h2wZ1nd4ay4lp4WTe', '/assets/img/avatars/14.png', NULL, NULL, NULL, 2, '2025-02-14 01:14:09', NULL, '2025-02-11 01:14:09', '2025-02-11 01:36:06', 'ativo', 14, 100, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_data`
--

CREATE TABLE `user_data` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `data_field_1` varchar(255) DEFAULT NULL,
  `data_field_2` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `campanhas`
--
ALTER TABLE `campanhas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clientes_user_id_foreign` (`user_id`),
  ADD KEY `clientes_servidor_id_foreign` (`servidor_id`),
  ADD KEY `clientes_plano_id_foreign` (`plano_id`),
  ADD KEY `clientes_role_id_foreign` (`role_id`);

--
-- Índices de tabela `company_details`
--
ALTER TABLE `company_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_details_user_id_foreign` (`user_id`);

--
-- Índices de tabela `conexoes`
--
ALTER TABLE `conexoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `conexoes_whatsapp_unique` (`whatsapp`),
  ADD KEY `conexoes_user_id_foreign` (`user_id`);

--
-- Índices de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Índices de tabela `indicacoes`
--
ALTER TABLE `indicacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `indicacoes_user_id_foreign` (`user_id`),
  ADD KEY `indicacoes_referred_id_foreign` (`referred_id`);

--
-- Índices de tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pagamentos_mercado_pago_id_unique` (`mercado_pago_id`),
  ADD KEY `pagamentos_cliente_id_foreign` (`cliente_id`),
  ADD KEY `pagamentos_user_id_foreign` (`user_id`);

--
-- Índices de tabela `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Índices de tabela `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permission_role_permission_id_foreign` (`permission_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`);

--
-- Índices de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Índices de tabela `planos`
--
ALTER TABLE `planos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `planos_user_id_foreign` (`user_id`);

--
-- Índices de tabela `planos_renovacao`
--
ALTER TABLE `planos_renovacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `revendas`
--
ALTER TABLE `revendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `revendas_user_id_foreign` (`user_id`);

--
-- Índices de tabela `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `schedule_settings`
--
ALTER TABLE `schedule_settings`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `servidores`
--
ALTER TABLE `servidores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servidores_user_id_foreign` (`user_id`);

--
-- Índices de tabela `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Índices de tabela `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `templates_user_id_foreign` (`user_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_whatsapp_unique` (`whatsapp`),
  ADD KEY `users_plano_id_foreign` (`plano_id`),
  ADD KEY `users_user_id_foreign` (`user_id`);

--
-- Índices de tabela `user_data`
--
ALTER TABLE `user_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_data_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `campanhas`
--
ALTER TABLE `campanhas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT de tabela `company_details`
--
ALTER TABLE `company_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `conexoes`
--
ALTER TABLE `conexoes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `indicacoes`
--
ALTER TABLE `indicacoes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT de tabela `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `permission_role`
--
ALTER TABLE `permission_role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `planos`
--
ALTER TABLE `planos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de tabela `planos_renovacao`
--
ALTER TABLE `planos_renovacao`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `revendas`
--
ALTER TABLE `revendas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `schedule_settings`
--
ALTER TABLE `schedule_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `servidores`
--
ALTER TABLE `servidores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de tabela `templates`
--
ALTER TABLE `templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT de tabela `user_data`
--
ALTER TABLE `user_data`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
