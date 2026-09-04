-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : dim. 10 août 2025 à 19:56
-- Version du serveur : 8.4.3
-- Version de PHP : 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ong_partenaire`
--
CREATE DATABASE IF NOT EXISTS `ong_partenaire` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `ong_partenaire`;

-- --------------------------------------------------------

--
-- Structure de la table `abouts`
--

CREATE TABLE `abouts` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `features` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `abouts`
--

INSERT INTO `abouts` (`id`, `title`, `subtitle`, `content`, `video_url`, `features`, `created_at`, `updated_at`) VALUES
(1, 'La première plateforme de networking pour les organisations en Afrique', 'À Propos de Nous', 'Nous sommes une organisation basée en Afrique dont la mission est de fédérer les acteurs du changement. Nous avons créé un écosystème numérique où les organisations, associations et collectifs peuvent se connecter, partager leurs expériences et collaborer pour un impact plus grand. Notre plateforme offre les outils nécessaires pour amplifier la voix de chaque membre et valoriser les initiatives locales à l\'échelle du continent et au-delà.', 'https://www.youtube.com/embed/YkYYQAHBH6c', '[\"Réseau Panafricain\", \"Membres Vérifiés\", \"Partage Facilité\", \"Support Dédié\"]', '2025-08-09 08:19:34', '2025-08-10 17:39:43'),
(2, 'La première plateforme de networking pour les organisations en Afrique', 'À Propos de Nous', 'Nous sommes une organisation basée en Afrique dont la mission est de fédérer les acteurs du changement. Nous avons créé un écosystème numérique où les organisations, associations et collectifs peuvent se connecter, partager leurs expériences et collaborer pour un impact plus grand. Notre plateforme offre les outils nécessaires pour amplifier la voix de chaque membre et valoriser les initiatives locales à l\'échelle du continent et au-delà.', 'https://www.youtube.com/embed/a_q_I-h2QyY', '[\"Réseau Panafricain\", \"Membres Vérifiés\", \"Partage Facilité\", \"Support Dédié\"]', '2025-08-10 13:19:54', '2025-08-10 13:19:54');

-- --------------------------------------------------------

--
-- Structure de la table `achievements`
--

CREATE TABLE `achievements` (
  `id` bigint UNSIGNED NOT NULL,
  `partner_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `achievements`
--

INSERT INTO `achievements` (`id`, `partner_id`, `title`, `description`, `image`, `date`, `location`, `created_at`, `updated_at`) VALUES
(2, 6, 'Programme de nutrition pour enfants', 'Distribution de repas nutritifs à 2000 enfants dans des écoles primaires à Madagascar.', 'achievements/pGZ1zK4FLacKQRtj0OmXgzvKBl8hTxS9N8SZBZut.jpg', '2024-06-15', 'Antananarivo, Madagascar', '2025-08-09 08:19:36', '2025-08-10 12:08:39'),
(3, 4, 'Formation agricole pour 100 femmes', 'Renforcement des compétences en agriculture durable pour les femmes rurales au Sénégal.', 'achievements/F3D3MFOWkvo6SyUp4aRsJliRnSda0MVVOEoPV365.jpg', '2024-07-01', 'Kaolack, Sénégal', '2025-08-09 08:19:36', '2025-08-10 12:08:57'),
(4, 4, 'Programme de distribution des mousticaies ', 'Distribution des mousticaies dans des maisons en RD Congo.', 'achievements/pWqLfePsCCgCLSFpaPdTOsyDzpV7p5LS9jxR4bxr.jpg', '2025-08-10', 'Bukavu', '2025-08-10 12:54:53', '2025-08-10 12:54:53');

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_da4b9237bacccdf19c0760cab7aec4a8359010b0', 'i:2;', 1754840013),
('laravel_cache_da4b9237bacccdf19c0760cab7aec4a8359010b0:timer', 'i:1754840013;', 1754840013);

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `updated_at`) VALUES
(1, 'Joe', 'joe@gmail.com', 'Test mail', 'Test mail du site pour le monde', '2025-08-09 11:10:45', '2025-08-09 11:10:45'),
(2, 'Dolore unde veniam ', 'copafido@mailinator.com', 'Delectus exercitati', 'Qui dolore qui repud', '2025-08-10 16:34:06', '2025-08-10 16:34:06'),
(3, 'Quidem velit maxime ', 'jibybip@mailinator.com', 'Amet repellendus H', 'Dignissimos rerum mo', '2025-08-10 16:34:21', '2025-08-10 16:34:21'),
(5, 'Joe', 'joe@gmail.com', 'Test mail', 'Test mail du site pour le monde', '2025-08-09 11:10:45', '2025-08-09 11:10:45'),
(6, 'Dolore unde veniam ', 'copafido@mailinator.com', 'Delectus exercitati', 'Qui dolore qui repud', '2025-08-10 16:34:06', '2025-08-10 16:34:06'),
(7, 'Quidem velit maxime ', 'jibybip@mailinator.com', 'Amet repellendus H', 'Dignissimos rerum mo', '2025-08-10 16:34:21', '2025-08-10 16:34:21'),
(8, 'Joe', 'joe@gmail.com', 'Test mail', 'Test mail du site pour le monde', '2025-08-09 11:10:45', '2025-08-09 11:10:45'),
(9, 'Dolore unde veniam ', 'copafido@mailinator.com', 'Delectus exercitati', 'Qui dolore qui repud', '2025-08-10 16:34:06', '2025-08-10 16:34:06'),
(10, 'Quidem velit maxime ', 'jibybip@mailinator.com', 'Amet repellendus H', 'Dignissimos rerum mo', '2025-08-10 16:34:21', '2025-08-10 16:34:21'),
(11, 'Joe', 'joe@gmail.com', 'Test mail', 'Test mail du site pour le monde', '2025-08-09 11:10:45', '2025-08-09 11:10:45'),
(12, 'Dolore unde veniam ', 'copafido@mailinator.com', 'Delectus exercitati', 'Qui dolore qui repud', '2025-08-10 16:34:06', '2025-08-10 16:34:06'),
(13, 'Quidem velit maxime ', 'jibybip@mailinator.com', 'Amet repellendus H', 'Dignissimos rerum mo', '2025-08-10 16:34:21', '2025-08-10 16:34:21');

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `features`
--

CREATE TABLE `features` (
  `id` bigint UNSIGNED NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `features`
--

INSERT INTO `features` (`id`, `icon`, `title`, `description`, `order`, `created_at`, `updated_at`) VALUES
(1, 'fa-sitemap', 'Réseautage Stratégique', 'Connectez-vous avec des organisations partageant les mêmes visions à travers l\'Afrique.', 1, '2025-08-09 08:19:34', '2025-08-09 08:19:34'),
(2, 'fa-bullhorn', 'Visibilité Accrue', 'Publiez vos activités sur notre blog pour atteindre une audience plus large.', 2, '2025-08-09 08:19:34', '2025-08-09 08:19:34'),
(3, 'fa-users-cog', 'Partage de Connaissances', 'Accédez à des ressources et des expertises partagées par les membres du réseau.', 3, '2025-08-09 08:19:34', '2025-08-09 08:19:34'),
(4, 'fa-handshake', 'Opportunités de Collaboration', 'Trouvez des partenaires pour vos projets et répondez à des appels à propositions communs.', 4, '2025-08-09 08:19:34', '2025-08-09 08:19:34');

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_08_09_075708_create_posts_table', 1),
(5, '2025_08_09_075808_create_team_members_table', 1),
(6, '2025_08_09_075823_create_testimonials_table', 1),
(7, '2025_08_09_075847_create_partners_table', 1),
(8, '2025_08_09_075857_create_contact_messages_table', 1),
(9, '2025_08_09_080726_create_sliders_table', 1),
(10, '2025_08_09_080750_create_abouts_table', 1),
(11, '2025_08_09_080820_create_features_table', 1),
(12, '2025_08_09_101017_create_achievements_table', 1),
(13, '2025_08_09_140541_add_status_to_posts_table', 2),
(14, '2025_08_10_151636_create_settings_table', 3);

-- --------------------------------------------------------

--
-- Structure de la table `partners`
--

CREATE TABLE `partners` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `partners`
--

INSERT INTO `partners` (`id`, `name`, `logo`, `website_url`, `created_at`, `updated_at`) VALUES
(1, 'Partenaire 1', 'partners/GURKyypRXnar4rH37ILJB4vSWpYel5GCuH0bBoll.jpg', 'http://127.0.0.1:8000', '2025-08-09 08:19:34', '2025-08-10 12:16:48'),
(3, 'Partenaire 3', 'partners/N0miDVlnm3Lyq1HWRwymqbpHlnfpu0cWD7iZsN2z.jpg', 'http://127.0.0.1:8000', '2025-08-09 08:19:35', '2025-08-10 12:13:42'),
(4, 'Partenaire 4', 'partners/yRrivwzqvtG5rYNwDpvgQ1u6SZeBSRajlYfTZZ65.jpg', 'http://127.0.0.1:8000', '2025-08-09 08:19:35', '2025-08-10 12:13:57'),
(5, 'Partenaire 5', 'partners/A9ivIKdEm57kVV5jdtMklN8iraMLS2Swi3Lc9E6m.jpg', 'http://127.0.0.1:8000', '2025-08-09 08:19:35', '2025-08-10 12:14:14'),
(6, 'Partenaire 6', 'partners/gs30w3cj1SgbvYfsqWLGv8VMkZMhZWvbxx1bfOkO.jpg', 'http://127.0.0.1:8000', '2025-08-09 08:19:35', '2025-08-10 12:14:34'),
(7, 'Partenaire 7', 'partners/pUSn0TyYghgYYoyscRBBUj8bDg8Z7wm7PGLFePDO.jpg', 'http://127.0.0.1:8000', '2025-08-09 08:19:35', '2025-08-10 12:14:50'),
(8, 'Partenaire 8', 'partners/n1mS2B7SxBmsFMOfYAwRp0iFrbdYEbU7vfF0KbYz.jpg', 'http://127.0.0.1:8000', '2025-08-09 08:19:35', '2025-08-10 12:15:09'),
(9, 'Partenaire 9', 'partners/2HtVAzLvSLLU5T2KYjfIo0P7mmMoMgHlughmRWNV.jpg', 'http://127.0.0.1:8000', '2025-08-09 08:19:35', '2025-08-10 12:15:28');

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

CREATE TABLE `posts` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('published','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `status`, `category`, `image`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Lancement de notre programme de bourses', 'Nous sommes fiers d\'annoncer le lancement de nouvelles bourses pour les étudiants méritants...', 'published', 'Éducation', 'posts/fQEy9P0JqpD6pdy4MTnmdDCItgruek6ntg9PrMMM.jpg', 1, '2025-08-09 08:19:35', '2025-08-10 12:10:39'),
(2, 'Campagne de vaccination réussie', 'Plus de 5000 personnes ont bénéficié de notre dernière campagne de vaccination dans la région...', 'published', 'Santé', 'posts/EPo5EHFXwfXKL3k2hUvaX8v0aKqypcsvNptDRsEl.jpg', 1, '2025-08-09 08:19:35', '2025-08-10 12:11:00'),
(3, 'Opération de reboisement communautaire', 'Rejoignez-nous pour notre prochaine journée de reboisement. Ensemble, plantons l\'avenir...', 'published', 'Environnement', 'posts/fedJe60UMpXbUaG2kVKBqyGvdPRWLvz2HIIM2jWU.jpg', 1, '2025-08-09 08:19:35', '2025-08-10 12:11:22');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('kyELEwrOoTLJg31pJagVyGoekV6uE5othCgXZhUu', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNGZhTktuekZkOGJjTmR5ZmM5anZyekJuZjFtcnkzUVBFTnNJUGFibyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZWdpc3RlciI7fX0=', 1754850371),
('vxAIYNLIP4CKts54oOeFDkzZTjAiQ6lPl3bsmjpH', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidHI1VHRhNENKY3RnR1dnQ2xyZkdqcmVLY1NwY3RDcnpSaDlzUklNciI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi90ZXN0aW1vbmlhbHMiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1754855718);

-- --------------------------------------------------------

--
-- Structure de la table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Plateforme ', '2025-08-10 13:19:57', '2025-08-10 17:39:52'),
(2, 'slogan', 'Rejoignez notre réseau pour amplifier votre impact.', '2025-08-10 13:19:57', '2025-08-10 17:39:52'),
(3, 'logo', 'logos/dKkQ1n2TTBOnMm6O92cW4ck2bPJG8K9DOO1T6Gkk.jpg', '2025-08-10 13:19:57', '2025-08-10 13:33:36'),
(4, 'address', '123 Rue de l\'Exemple, 75000 Paris', '2025-08-10 13:19:57', '2025-08-10 17:39:52'),
(5, 'email', 'contact@example.com', '2025-08-10 13:19:57', '2025-08-10 17:39:52'),
(6, 'phone', '+33 1 23 45 67 89', '2025-08-10 13:19:57', '2025-08-10 17:39:52'),
(7, 'twitter_url', '#', '2025-08-10 13:19:57', '2025-08-10 17:39:52'),
(8, 'facebook_url', '#', '2025-08-10 13:19:57', '2025-08-10 17:39:52'),
(9, 'linkedin_url', '#', '2025-08-10 13:19:57', '2025-08-10 17:39:52'),
(10, 'feature_image', 'features/4qDwR8Z2w4pDDSdSrORsREiAruD18HjM4Y7OoLSW.jpg', '2025-08-10 13:19:57', '2025-08-10 13:33:36');

-- --------------------------------------------------------

--
-- Structure de la table `sliders`
--

CREATE TABLE `sliders` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `button1_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `button1_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `button2_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `button2_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sliders`
--

INSERT INTO `sliders` (`id`, `title`, `subtitle`, `image`, `button1_text`, `button1_url`, `button2_text`, `button2_url`, `order`, `created_at`, `updated_at`) VALUES
(1, 'Connecter les Acteurs du Développement en Afrique', 'Un Réseau pour le Changement', 'sliders/nBGt4lvIFzMqTmMmsf6jis1LwhNkzc3ale54BcJJ.jpg', 'En Savoir Plus', 'http://127.0.0.1:8000/#about', 'Voir les Activités', 'http://127.0.0.1:8000/#blog', 1, '2025-08-09 08:19:34', '2025-08-10 11:57:34'),
(2, 'Partagez vos actions avec un public plus large', 'Visibilité et Impact', 'sliders/mWwfun9PPbd82v1nyo3WYKWCjtZAoeiu4spvIAWS.jpg', 'Rejoindre le réseau', 'http://127.0.0.1:8000/register', 'Nous Contacter', 'http://127.0.0.1:8000/#contact', 2, '2025-08-09 08:19:34', '2025-08-10 11:58:24');

-- --------------------------------------------------------

--
-- Structure de la table `team_members`
--

CREATE TABLE `team_members` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `twitter_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `team_members`
--

INSERT INTO `team_members` (`id`, `name`, `position`, `photo`, `twitter_url`, `facebook_url`, `linkedin_url`, `created_at`, `updated_at`) VALUES
(1, 'Nom Complet', 'Organisation A', 'team/ydnCGMlAWjCkLkMT6yClh3oLT8t9JiQpG8MSovB4.jpg', 'https://www.x.com', 'https://facebook.com', 'https://www.linkedin.com', '2025-08-09 08:19:34', '2025-08-10 12:02:50'),
(2, 'Nom Complet', 'Organisation B', 'team/kVdJAoz5kWltPxGwdNhGp6lv0JOHXbGZPr0cxvX8.jpg', 'https://www.x.com', 'https://facebook.com', 'https://www.linkedin.com', '2025-08-09 08:19:34', '2025-08-10 12:03:24'),
(3, 'Nom Complet', 'Organisation C', 'team/zxhu81a51EQhNqIRy6bKiuixWv5IVtkTbM8hi1uU.jpg', 'https://www.x.com', 'https://facebook.com', 'https://www.linkedin.com', '2025-08-09 08:19:34', '2025-08-10 12:03:50'),
(4, 'Nom Complet', 'Organisation A', 'team/UUiLGIBJTMVRqu26u7JNRZxdiWbQWI9yGL6JxjAQ.jpg', 'https://www.x.com', 'https://facebook.com', 'https://linkedin.com', '2025-08-09 08:19:34', '2025-08-10 12:04:42');

-- --------------------------------------------------------

--
-- Structure de la table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` bigint UNSIGNED NOT NULL,
  `author_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_photo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `testimonials`
--

INSERT INTO `testimonials` (`id`, `author_name`, `author_position`, `author_photo`, `content`, `created_at`, `updated_at`) VALUES
(1, 'Nom du Partenaire', 'Profession', 'testimonials/w6oPrSdEqFxhMYOVc99dPJDFeONZOzqJkZntIA1w.jpg', '\"Cette plateforme a transformé notre manière de collaborer. Nous avons pu nouer des partenariats solides et efficaces.\"', '2025-08-09 08:19:34', '2025-08-10 11:43:47'),
(2, 'Nom du Partenaire', 'Profession', 'testimonials/gx9mGODwt3M0bXDa0gBbYojAntwbhhhNb3k6Wum8.jpg', '\"Un outil indispensable pour la visibilité de nos actions sur le terrain. Simple, intuitif et très impactant.\"', '2025-08-09 08:19:34', '2025-08-10 11:44:41'),
(3, 'Nom du Partenaire', 'Profession', 'testimonials/0Fm2YlIAPupUnzZ3CpNpaG2QKFEYqW07HuUIopjs.jpg', '\"Grâce au réseau, nous avons trouvé des compétences que nous n\'avons pas en interne. Un vrai plus pour nos projets.\"', '2025-08-09 08:19:34', '2025-08-10 11:44:26'),
(4, 'Nom du Partenaire', 'Profession', 'testimonials/BWqV2YVBxJLRMndEzKtq9SueFstWY5boTfsuJAOG.jpg', '\"La section blog est une excellente vitrine. Nos activités n\'ont jamais eu autant de résonance.\"', '2025-08-09 08:19:34', '2025-08-10 11:45:26');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin Demo', 'admin@example.com', NULL, '$2y$12$yXHQ1PBWVg8HAs0U6076Xut9bWm/AQF2Gb62TV14PAHzi7wNAfzEe', NULL, '2025-08-09 08:19:35', '2025-08-09 08:19:35'),
(2, 'Admin', 'admin@gmail.com', NULL, '$2y$12$v8/qYYbbj85a.XSGtszS1OCueTQuPVfKW2MjSYXlu8arA51sQjjAS', NULL, '2025-08-09 11:11:49', '2025-08-10 17:54:53');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `abouts`
--
ALTER TABLE `abouts`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `achievements_partner_id_foreign` (`partner_id`);

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posts_user_id_foreign` (`user_id`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Index pour la table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `abouts`
--
ALTER TABLE `abouts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `features`
--
ALTER TABLE `features`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `achievements`
--
ALTER TABLE `achievements`
  ADD CONSTRAINT `achievements_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
