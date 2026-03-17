/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.24-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: chatmego_db
-- ------------------------------------------------------
-- Server version	10.6.24-MariaDB-cll-lve

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_at_available_at_index` (`queue`,`reserved_at`,`available_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_03_14_120623_create_users_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('0az3LcEzacceiR5lRIcBpRvI1nw5jIh8TOyoJ8wN',NULL,'51.195.215.150','Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUTFCV0hjOWVkZk5telZ2dWc1ZGZaTVVVWmRlUE9McnFRdjYxU3hyViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjQ6Imh0dHBzOi8vd3d3LmNoYXRtZWdvLmNvbSI7czo1OiJyb3V0ZSI7czoyNzoiZ2VuZXJhdGVkOjpFd21ZM05MQ2FpZXVxQTdiIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773519366),('27Q3oUfhlqTOljoPnqmOyvdsqRkrOOpNOuTtHx9i',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWW1PQWxsbDBuSTVvdmtRdnpkQ3NQNWt1QUhrWnd4MU9RYm9LWm9GdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508590),('2NPOMCszM0D9c0mijRMEXGICFnvD7ftlBtrrXvaE',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoid3ZERWpDY0cwMjB6UDVrTVRqaG9PRkJ5YWxrZnFWMktKUWs1Z3VzaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508872),('2q5axkV3Wat2dAktPRv5QPsNiHvU3xLvlH1ymAsN',NULL,'45.8.220.205','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoieGdneHRHRzNXQmMwSzd5RVh1NEFNTGljSEFHMFBTc2F3RTc3cnd3SSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773509386),('2YyT9wL3r2suPh4ONFZ1a8kn3c1HzyfMSu0Q4uEM',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoiWk9aYkdYVXM1d3JPN05HNmxTV2VJaXpIZlc4eVlaVzE3em9UVERjcSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773506694),('37bWPC2Fd0Aa8qrkLwr73SZ6UOBR8YDcogo3zRxR',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZHZrc2pjbDF2RmxYVGlzSnlDVkpabVZTZVpzREE5bGo2N2ZqVWVKNiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508865),('4zRYFYFu5J6Gom1CftZwZSjRNxDOQHW445TcjBGo',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoibWc2VndhVWUzQWx5cjZNRkFKNXo1eGlMSTVxSXVoaDkzU0FpbENreiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508848),('5JmQhxYdEBZ19pdw2yXYXQoWLkjnhHp8Ck2BcZvO',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoibUZIUWVaZDNKUHNxdXNYZGt3QmZJR2JYSlc1UkJFYldWdElXWnNmNiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508906),('5YIqfKBWB8xLnHv7iheqsLaEgMaU41Xw2vpF97bB',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoiSW5IQndLUXdUOWlBRGRrbE5RVFZxaHlGZ2VMbTRXdUYwQmR3VWJQVSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773507939),('7qFZrShj3bHojtCYwhZWxOttpNRhiIyuRr9bssrM',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoiWmFtQkV0YnlobXVBbjFhZWFtaXU1WW5hNTlLcjhWMnpGeVZ2ZTh0SSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773507945),('8CJ6lf582h9IcuD6j4CO3lveKGUHie73dalwXsRX',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoidk1BcVBHOENGbjAwODJsUGhKVDN5c05RZmphQWtjbWdaWHlDNzN6aCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMDoiaHR0cHM6Ly9jaGF0bWVnby5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773508219),('8Lf1NegjShmv2p6seyEtenVFfLLqMyo613y5kmWG',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoiVGdhamY2ZXFwVjVUMTJNOVF3N2l2M3UwWUI5dnhtWU9IdG5hNHFGdCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773506670),('8ndqLZtuv2Fn7hwvTAvCyjVNhtxzb3k0Xus7Yc2C',NULL,'45.55.60.244','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiS1ZHTHNvZFdqcnVKaXEwRHNUZGI4QmoxUUFZYXhoem4wZFAxWVZxbyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjQ6Imh0dHBzOi8vd3d3LmNoYXRtZWdvLmNvbSI7czo1OiJyb3V0ZSI7czoyNzoiZ2VuZXJhdGVkOjpFd21ZM05MQ2FpZXVxQTdiIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773545325),('9IZcIgFdzIgbWjXS0AIgubwx9qVihWaMAzA01fIl',NULL,'49.51.183.84','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSHVSWEZwZFdHelVSeTVJN1NNZW5EeVBkdGxVOVlxeTNxbHlDNXFmViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly9jaGF0bWVnby5jb20vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773526550),('9oXQAqsQLwRq1TlS4Pbdfu1zDoT2Ycsv5o5oqA4U',NULL,'43.153.123.3','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYUE3RG5OMXpFWkRZRXY2SHdjWHFyaVZ6WUpKMUdnOEMzOFhuZ2xXWCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly93d3cuY2hhdG1lZ28uY29tL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773522196),('a6pNnnyp2wKuND0sllpdqO02ye1vRQGUcJilGfZp',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiaUdaTkZKRUw3OFVSR3lncVB6Yzh1R1FtV1ByR0YyVlM2enhYY2dVcCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vY2hhdG1lZ28uY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773506807),('b94UpuwjfE22HkADkbLruA0N16jFowhAUjOMctOD',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoiV3g3MldmbVY4dVJ5M0dTY2NJVjVRT0dkZmpaeGVTMmM2SFpqYXE1WSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773507497),('bd0fkWfrqrLAzFqBC9w6NcypvavauFs1wjW4a5aH',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNlBMTjdkRFhVcUJRNWs1eTg4WnlVNnNCaEtacVMxM3duT0N2SHN2RiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508706),('BPqwgu8uHMpPmBvJuk2yeYWvG28fpJkDTxkwe1gz',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiT3g3NklOckU4Z1NzUlBrMEk4cGZWVE5tbmw5YTN0Um9sZVdqSTR5bSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508524),('bqGMWnc1m1kMOiUXOllWRpGwlieTHuWHh4SbLnoS',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoicUNqTUE4MEZBcFV2WDZHWElTODZRR3F3azJyR01mdWI3a0w4WFV1MSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508081),('BQyrnIAm3ZNSeIAe2thD3E5GId3DlGOh9n3pXVOB',NULL,'171.13.25.220','User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVFpWeXlIbnZwaFF2dG5GeFpIUHBpTktlOGNSamNnTzRsR2VremlwcCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHA6Ly93d3cuY2hhdG1lZ28uY29tIjtzOjU6InJvdXRlIjtzOjI3OiJnZW5lcmF0ZWQ6OkV3bVkzTkxDYWlldXFBN2IiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773518255),('BwuINv0vkYHOVgwrkvbmcqoPTSqrkxpMkRsWzewf',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoibHNyQVFZUnVjRUpobThXMFgySTZHY2puQjRrcm5aU3owTW5hQ1RlVyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773506738),('cxxcrWf3Qw5wE56iqiwF7TaEjsMf61uuf9m5pyhv',NULL,'183.227.126.98','curl/8.7.1','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRXZyT1FFeldNeGh5WGx6Z0JsWU15UWxRQmhMd0s4c1BKamxTblh2OSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluIjtzOjU6InJvdXRlIjtzOjMwOiJmaWxhbWVudC5hZG1pbi5wYWdlcy5kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YToxOntzOjg6ImludGVuZGVkIjtzOjI2OiJodHRwczovL2NoYXRtZWdvLmNvbS9hZG1pbiI7fX0=',1773509651),('dOpO2swavvpdz593VQ6oNXhXuPE14BXx5L6mqLNC',NULL,'59.82.83.139','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.3991.22 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoid1FjOU1mNHpBNnUyRUo1OUg1T3BOY2d4SFFvbkd5YzlkYVd4OTN2MSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTk6Imh0dHA6Ly9jaGF0bWVnby5jb20iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773506821),('ef3CeZ5cvLLuUJ8VBlhhdkX395N8tzAvXIYhS7Gq',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTmtlWTVXUEw5SlBJTDI1aVBsbXNqckR4YTR2T2FmYWoxejcxakNyaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773546189),('EGKyrIWCZudfGYRUTMCqgsnHUWBhYpxsIjpxWBNS',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoiQWZ4S1JHSGlFcG5SUnA5c0pmNWYxMzFkN2xIN1FZbmxrTGk5emQ2WSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773507951),('fDO1z21LSMfAKRAi39KhQwu26oK9Ud3BeunvFkMC',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoiSkpac2JnYzZIaDBzWThFRVhRQWdhZGZTSlFYS2xkbXB3OU01U2NlRSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773506795),('FxycTZA97QOpqEm2qDWTOdQwzY1URkpUcwDl8sFO',1,'94.124.118.70','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Safari/605.1.15','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiS2Q5bFpKb2dIY3JWR3JueGtvWEhaZTdlSmp0N3lZT0hYSjUyS1FndiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluIjtzOjU6InJvdXRlIjtzOjMwOiJmaWxhbWVudC5hZG1pbi5wYWdlcy5kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiN2NlOTQzMzJhYjBiYzllNjRiZTFmYjE5MDY2YzJmNmEyMjJmMjJmY2Q5N2VkNzlkNDdjYjcwYjcyNzI1N2MxYiI7fQ==',1773508290),('g66eaJZmwCRciRUcJ32VbN6z8latq0Kb20sGPMBo',NULL,'43.165.70.220','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZTEzSEJzU0VqQW5QNFFZMlN2OWpoeUFsSkZBb01xTE5mcnBKMVRmeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly9jaGF0bWVnby5jb20vcmVnaXN0ZXIiO3M6NToicm91dGUiO3M6ODoicmVnaXN0ZXIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773527167),('HdjJYPJ6kmXzdzwBbiE2ODLsE04A6rarSjWqvn3J',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiekJlUFBMYU00TjlHbnJuZmNWT0VOajllVk9QVFFINnJNaWhMV2FqbiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMDoiaHR0cHM6Ly9jaGF0bWVnby5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773506821),('ieyNIoAZIjtIOcxShbH0C7Sms99TVqNkehQWPCPN',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSFp5ZjgxOGxqWUdrc3luUHNScHJza3VSV2puYUxuc2QzTWxsU3VseiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773509613),('jSQy541ILJgSL3NR6roN4SRCyRZD7IzsTN99ag1r',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoiMEgySHFVWE0wZHRwTlFXRGh0V2VoU2F2bVY1NjZVVzN4SXZtZlB5eCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773506815),('julMAGAbv82TA4PYCsOz9Jeu2U2WbtNT6Ra0qiLd',NULL,'161.35.38.85','Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUGx2ZFYxUmJZbjZPYkthN01pSnNBNWxHako3b0Q2QjNVWU9vcG5QZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vbWFpbC5jaGF0bWVnby5jb20iO3M6NToicm91dGUiO3M6Mjc6ImdlbmVyYXRlZDo6RXdtWTNOTENhaWV1cUE3YiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773543471),('KMoSJzgFkWxgKKLBf8n0i00w0FM55eycA3DpSNAw',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoib3RiNGpkREd3MWRnSUdSYlRvNlRnbXBBNU5Yald3ZWpvTzZlVmlsQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508698),('kNIJFZHqkP55VjjKrtHzEsPDm67K1jghDhu27vZZ',NULL,'183.227.126.98','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoibEg2YW93NkxnOHZQRlY1dThzOERNQWZoMVZtSlNYbkdLaWZCUTRLaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773546381),('lfkATtuDLQUA00E1VkwNpqXkX50f2N2aPDedNPj8',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNFZtU1ZwajZDWkVJSDNXdTJoTktyRkRkVDR6MlpteUhVYjFCalhSNyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508859),('lRECXt7qsVOh3qqvvcZ9KwHcUWucbEZvdi5kPdyf',NULL,'94.23.188.209','Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiM0sxQWxscjlRbE5PQzdyV0tuZ0lvaHZyVVhFdWkybUlKTUtsa0x5TyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vY2hhdG1lZ28uY29tIjtzOjU6InJvdXRlIjtzOjI3OiJnZW5lcmF0ZWQ6OkV3bVkzTkxDYWlldXFBN2IiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773541054),('MEpEoXlxNjH4NsrTP6c6JAoLkRhAO4bvxscJXGhy',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiN0pHd1N4aUxVQTNaZjlhQkl6ZjNFdnhYM1IxeXRQcWI1NVQwVmxnOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508842),('mfIVKBJ4pKe5PmjuCdSYMzXaE9zNR7Q7ggFOP7zy',NULL,'43.156.204.134','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiR1FidXhmcVpGUGdOOGFaY0YyME1wY2F4bDRuU0R3eWhUWXlTejl0UiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTk6Imh0dHA6Ly9jaGF0bWVnby5jb20iO3M6NToicm91dGUiO3M6Mjc6ImdlbmVyYXRlZDo6RXdtWTNOTENhaWV1cUE3YiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773536786),('MnTGirbpgzN0WxO8FuPRHldRvWszhPJlLMZfgLxW',4,'183.227.126.98','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Safari/605.1.15','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRm9WS3FzVk9ER0JrazEwOWEzQ05nNnFVUGxrZG43aEtGRmZ6djFTVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluIjtzOjU6InJvdXRlIjtzOjMwOiJmaWxhbWVudC5hZG1pbi5wYWdlcy5kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJlMmRlM2ViMmVlZGQyMzY2ZTNjOGU1NGJjM2YzMGE1ZWUxM2RiY2Q5NWU1ODdjODk4ODcyZWIxNTFjYWMwZDBjIjt9',1773546365),('nf5W8TMcoBpJG7syYXnUM10p6cR16SSeItEHrJ7D',NULL,'142.44.220.206','Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ3dPcVpLZ0Fmdm9kaVppak9lYnlkMENEbTN6Q2lLblFMN2lLVXp3ayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly9jaGF0bWVnby5jb20vcmVnaXN0ZXIiO3M6NToicm91dGUiO3M6ODoicmVnaXN0ZXIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773537960),('nUfc8urS1CPxq8anOqwyCQw6SZk4nKncLWHYf0vb',NULL,'43.156.156.96','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoic2JjMldHOFVqUDhTeXM1aTRKYUhYenNuUEVhc25XWmFNY0xLeFJrRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHA6Ly93d3cuY2hhdG1lZ28uY29tIjtzOjU6InJvdXRlIjtzOjI3OiJnZW5lcmF0ZWQ6OkV3bVkzTkxDYWlldXFBN2IiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773540253),('O0GizGI2rpTrUyYzF3te2TNKdNqJl0wEYwGV0FXd',NULL,'43.156.109.53','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZjB4Y2ZadXM3QkhGUzZ5Q2NsTVkyWWdMbHp0YjJHMVkwbVhXUkFXQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly9jaGF0bWVnby5jb20vcGFzc3dvcmQvcmVzZXQiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773528412),('O27nqjIIXYXNb9k2YOASmvnore693A6Wa8c015QX',NULL,'198.244.226.235','Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiaWtDdk9uTHNob3NnZDA5eDRRSkZ2UG1oQkdWTXVTOHNrVFhUNGt5RCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHA6Ly93d3cuY2hhdG1lZ28uY29tIjtzOjU6InJvdXRlIjtzOjI3OiJnZW5lcmF0ZWQ6OkV3bVkzTkxDYWlldXFBN2IiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773533328),('OLzkG2GxYjGysSrD1q44uiZvOzI6VvDSxISNgpZW',NULL,'198.244.183.248','Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoieWF5T3ZPTFU2WUZXQ3dZZVRlMHN4M0dWMWQ0RmFsTnRtT0cwOUhuYSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly93d3cuY2hhdG1lZ28uY29tL3JlZ2lzdGVyIjtzOjU6InJvdXRlIjtzOjg6InJlZ2lzdGVyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773545507),('ONw7dtAylpclFtKIM7GBWo1TTDtpwEPbDpd2tjnp',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoiMW1xbkhGY3NrUFF6Q29OTUdIZnRLQ2Rka05GYXY2M1BmVThGVVN4UiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773509001),('OXrxt0MAc5wGHi38BO2zavmvShYoUN6h1RwLHlDl',3,'61.221.120.114','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQmVTQ3JJRUkyaThrTGVydHVTdlU4R1k4QXIxaWZlUWhSQmprWVdEUiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluIjtzOjU6InJvdXRlIjtzOjMwOiJmaWxhbWVudC5hZG1pbi5wYWdlcy5kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=',1773509637),('OYQqmuBlDQCZtcmbtcAwiDDkFagnubMK1QLELDJ6',NULL,'103.169.92.155','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 QuarkPC/6.5.0.748','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTmdmMVJiR3JjeUd6REUyM2tETlJzOWtvNzVJMVc5NUVrME9tS3lRQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vY2hhdG1lZ28uY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773506213),('p5CfekCIIIAipyZqHsomCItPxDI1MuNWE49Slu5b',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUW1tY1dSTzN5aGhpQUhqc2RDZnBMT0lWNDdpT0h2M1NjMGltODRuYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508686),('P5vRIraDUrxU7DBEdLcRcg6UcZ6jgBDZIYKMJ1Gi',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTzdwbUtma01vNGtKTDZyVmRmNm1xVnRNd1lyZXl6TUxZQkIyd21reCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508714),('pChIjIN6y8W0Qnk9btMgNT9yYq8k0AJJOzHCx6iy',1,'183.227.126.98','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Safari/605.1.15','YTo0OntzOjY6Il90b2tlbiI7czo0MDoidlExdTdSZnZKN2VYako0Z3A3akZDVDFtYUp5RlJMVkYwcE1OTkY3SyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluIjtzOjU6InJvdXRlIjtzOjMwOiJmaWxhbWVudC5hZG1pbi5wYWdlcy5kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1773546373),('PK7VJ1JYQqJVI4fRkLAYSSu9b23355yt6Rgx2zcE',NULL,'45.55.60.244','Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWkxTWjBuazhqd2hqTzJCUnBndENzYXBZM0tieXJxVG91UG02emo0MyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHA6Ly93d3cuY2hhdG1lZ28uY29tIjtzOjU6InJvdXRlIjtzOjI3OiJnZW5lcmF0ZWQ6OkV3bVkzTkxDYWlldXFBN2IiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773545323),('PoGBDqKURmlko8TJV6wj4LpGQtrv80tBf3FiAriQ',NULL,'49.235.136.28','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiazl6dzBTd2hCdkgyWDVTWmtUUHpMa2N2OGF3ZXRWbUg1dGxhSlBvOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTk6Imh0dHA6Ly9jaGF0bWVnby5jb20iO3M6NToicm91dGUiO3M6Mjc6ImdlbmVyYXRlZDo6RXdtWTNOTENhaWV1cUE3YiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773511743),('QAIwe78eGacXiEVyfrFdiDI7tIiRtyVmNzhoLvga',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoicDJMQkgzRUVKNm5QMXJkdzVMa3hjU05hSTRybm1lZkdtRGlUYlpibyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508462),('qlVBvTTab2vPd1v6YAkqC2jVMd40wErytVb2r2yO',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVGRYOXh6bkhxZUNvTW9lbGxmMmVDdjNxNUxnTWdUcldTZHpCSFVJYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773509605),('qObjauRmkx3uQpgusOHKNdBy6AsSZVUrq68lKMGV',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoiMTZ1RVV4UDRHUE5OUGlsYVE0MjYxamhhSVNiQVZ3d28wQ0lsN3BxeSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773507906),('qss6BhxuKtsvKLfAaTZOO3dSVsgmdvy5nOad9FIH',NULL,'43.135.130.202','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoieFJyMnFkR1BpZ1RISktDSFpDb3JPNFppRmxLdW5iS0liaWwwNXlvOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly93d3cuY2hhdG1lZ28uY29tL3JlZ2lzdGVyIjtzOjU6InJvdXRlIjtzOjg6InJlZ2lzdGVyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773522660),('rmdjjC61IM513Enaeq9GcTRzmTcPpflZ87W22iFo',NULL,'45.143.232.70','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMmxzZDBhRlByQTdqeGFvN2RvVTIwNmFuZFVFVER2cWYzWjJYTzhGdCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNjoiaHR0cHM6Ly9jaGF0bWVnby5jb20vYWRtaW4iO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9jaGF0bWVnby5jb20vYWRtaW4vbG9naW4iO3M6NToicm91dGUiO3M6MjU6ImZpbGFtZW50LmFkbWluLmF1dGgubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773508211),('rQ0QfErSwJgU0RZPXZX2B4pSMw1NjC5dPP9XCSU7',NULL,'43.158.91.71','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiM1BSM2h6amY5ZjNRdFAwNTc4VjZuNGdIdzBkMjdCQ1VBU1NPVnZaYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly93d3cuY2hhdG1lZ28uY29tL3Bhc3N3b3JkL3Jlc2V0IjtzOjU6InJvdXRlIjtzOjE2OiJwYXNzd29yZC5yZXF1ZXN0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773523891),('S6dJBxZbw8LpBJJfIMr31Pjw7LRcZHm6ESnbZBxf',NULL,'103.169.92.57','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 QuarkPC/6.5.0.748','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVVk5cW5MMVhVUlZYRkdGSjJvRTlNRWd1UnBWV3VEY3lLZmxOeExUMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vY2hhdG1lZ28uY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773505944),('SfnkNrC2onsgLkt41tCIM65g2l5xEZSwjxnwUWf6',1,'195.245.219.179','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Safari/605.1.15','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYkNUaXFXSG55d2REZGdsYWZ6cFhkY1luNTZjVWJqRGFiOEs1M2NQUiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI2OiJodHRwczovL2NoYXRtZWdvLmNvbS9hZG1pbiI7czo1OiJyb3V0ZSI7czozMDoiZmlsYW1lbnQuYWRtaW4ucGFnZXMuZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1773509024),('T3SdAlpzUJwXA8lvXeIWx1vX7pn2VkSpOrU0gptS',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoibzhJcTJIaHBja3A1eks2WVl1eURoMkR0RWtYaDY5YWpSbURnVDk1cyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773507653),('tobZulcz7ctX2uCbJInf43QyLbUFDfAR6OkClXRe',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVmU3TlBNak44YXM3UktlMlFWQzVrZldoQzRuMDVwUU5CYk1paGJRMiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMDoiaHR0cHM6Ly9jaGF0bWVnby5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773509751),('TrosEFzKOBhuwIX9EmzL4pqD0NmM71wbu0A05w7p',NULL,'183.227.126.98','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUzJXVXpiVWd5MmVjVjJOOGh2QkQyOUhGWTdwZTltaHZTMkZVMGxKUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773546240),('tsJUZGIOXxAuTy0iAMLEWDLbn8xqqPQ9M7uRyviU',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZTR3djdrekVobTlxZWlYNTR5cUluTHFIZ1lueEYyVXhSTUhuQTV2ayI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMDoiaHR0cHM6Ly9jaGF0bWVnby5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773509397),('UtNMqCp9VG2Ilp2JNzmBVOJvLKhjOxBOrVWG87Gl',1,'195.245.219.179','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoibXdOaXVMUlltazF3a2tLa3JpaHRUMnl4bUFra2JMS29yWkJEOVhuUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluIjtzOjU6InJvdXRlIjtzOjMwOiJmaWxhbWVudC5hZG1pbi5wYWdlcy5kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1773509067),('VEzIoZmeOB0s7cqALbuVEVGGEHndShf8fF2qArpb',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoiNjl5MnNMZnZUMWN1MnBaaWFidk1qWElUZGwwakxSRkJOSnBsRmlpRyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508088),('VLU0LRv3uc6WMJimwZEZBR0kjh3lzbbrBFa7KX9B',1,'94.124.118.70','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoidEdTUkJwTGl5a0pLeWlldWFzSURMeWg2THEyWnBZaWVpYUNOR1lrUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluIjtzOjU6InJvdXRlIjtzOjMwOiJmaWxhbWVudC5hZG1pbi5wYWdlcy5kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1773508334),('vm85a5PG4Q0wmsExl6SfwCO6wRNExjKymR4sPNtO',NULL,'43.131.253.14','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOFd4Z3E4ME8wVVpGM2dDcEtQQktWUlllWWkzSzJxVFNHVk1xT1NkWiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHA6Ly93d3cuY2hhdG1lZ28uY29tIjtzOjU6InJvdXRlIjtzOjI3OiJnZW5lcmF0ZWQ6OkV3bVkzTkxDYWlldXFBN2IiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773543646),('VZ7GGo3rvM1e7tAmmxqYuflwYlWzzAmOlp9Vn5Cn',NULL,'43.157.52.37','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWWJIQUQzTFF1bWdocFJrWGZpSDVySnVVQ2E5bVB2TVNOeGJZc3hJdyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTk6Imh0dHA6Ly9jaGF0bWVnby5jb20iO3M6NToicm91dGUiO3M6Mjc6ImdlbmVyYXRlZDo6RXdtWTNOTENhaWV1cUE3YiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773524398),('wkJb7G5yZgYcBJ1TuI5BefBVMkXZR5LHk59SXz6i',NULL,'161.35.38.85','Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiS1dremVvRnBkZ1laR3Q3OGM2OWhjVjV2TFoyRWg3Y3oyY0x4eEFGNCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly9tYWlsLmNoYXRtZWdvLmNvbSI7czo1OiJyb3V0ZSI7czoyNzoiZ2VuZXJhdGVkOjpFd21ZM05MQ2FpZXVxQTdiIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773543469),('Wu3NcMWvsrtOGs24I9uJw8F693V6OP1nBesUUaxZ',NULL,'183.227.126.98','curl/8.7.1','YToyOntzOjY6Il90b2tlbiI7czo0MDoiT0ROcEVtSjVRdE5BWmFxbklONWpQZnM1blFRUGdkTFF4SEJOSnNCMiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508227),('ww78g61nlGSqo0DqUkJdb6oejoaOEZaiOiAzXUXg',NULL,'162.62.231.139','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiT2VsdHFKMnhZeGd4UGRkRkZIV21Mc1c4eHNhdllzUWZwMFZuMlhUTCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHA6Ly93d3cuY2hhdG1lZ28uY29tIjtzOjU6InJvdXRlIjtzOjI3OiJnZW5lcmF0ZWQ6OkV3bVkzTkxDYWlldXFBN2IiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1773520007),('X3EYi8UUXu3xaCFb9vDnPxyoXTg5mNfgyEykhHVO',NULL,'183.227.126.98','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiREVJZWhlWmdtZmhNemFZaXcwV05JS1hHMnNWRjNUQTB2R2YzZDFTNiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY2hhdG1lZ28uY29tL2FkbWluL2xvZ2luIjtzOjU6InJvdXRlIjtzOjI1OiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773508879),('ycqKAXVLFWfcmcMBVMWxALF9txeirQM9biFL55fy',NULL,'54.38.147.104','Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNVA2aWV6TGtUQ0NScHBOaHEybndxVW55SzRzMHVhZTFBb3lVcjdHRCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vd3d3LmNoYXRtZWdvLmNvbS9yZWdpc3RlciI7czo1OiJyb3V0ZSI7czo4OiJyZWdpc3RlciI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773528492),('Z1aMly1GPALn0U3wUYOwHBXIPc0lmQd1hadqD9Ly',NULL,'142.44.225.41','Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoic2pVbnk0RnlwY3FqMWxnaVBHY2NIU2JJVko1YU0yM1RVc2ZRV0tycCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTk6Imh0dHA6Ly9jaGF0bWVnby5jb20iO3M6NToicm91dGUiO3M6Mjc6ImdlbmVyYXRlZDo6RXdtWTNOTENhaWV1cUE3YiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1773523891);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `height` varchar(255) DEFAULT NULL,
  `weight` varchar(255) DEFAULT NULL,
  `hobbies` varchar(255) DEFAULT NULL,
  `specialty` varchar(255) DEFAULT NULL,
  `love_declaration` text DEFAULT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'初始管理员（系统）','admin@example.com','111','$2y$12$W4q2qbNDu5.BXuGdjB/kWuN39qFlWr.tVK5UM1w/5f0Y0ecseajx6',NULL,'male',11,'11','11','111','1111','111',1000,1,0,'TXnMFPxrxrBQeXJ6ytJRbzqO9tap5rdAJpqBP7klvwe0mpnYf6IsuvzmjnEg','2026-03-14 16:57:23','2026-03-15 13:30:54'),(3,'小妹妹','xiaomeimei@163.com','sss','$2y$12$4K35Hws/6odLbCuMrOvwjuO7BO7qbeMoJHRMpTJp/W3B8jyMV6M6i','avatars/1773566680_355fba6a5cb049f5b98c2ed9f03cc5e1.jpeg.webp','female',1111,'111','5','1111','111','1111',0,1,0,NULL,'2026-03-15 00:21:59','2026-03-15 16:35:24'),(4,'小美美','xiaomeimei@11.com',NULL,'$2y$12$rxiCK7DzkdmPyDix.hKuX.4vQ5hmCwS./z24fqn/S0hHQepgn9Fry',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 10:21:57','2026-03-15 10:21:57'),(5,'admin','admin@qq.com','11','$2y$12$F3M32MvNtKAGlBBGu1xpRe96YCDJwKJ0tfsNwt6tGcR2DzCMGEmRK','avatars/1773565697_004.png','male',18,'111','39','11','11','111',100000,1,1,'ohfxDCn8h9XXaPMYIp70MV0HSWQMP9Ub2nVddTQggzO9Ca63VxsPSHz8zOHP','2026-03-15 13:09:59','2026-03-15 16:39:42'),(6,'淑芬','11111@gmail.com',NULL,'$2y$12$xKafFfxEA0dfU/0Wfq0vAOymN2gQHSsIUB7LFor.HUCH9/TwPrpfG','avatars/1773586643_8542541a937204de1415f56f675c8c80.png','female',38,'156',NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 21:55:20','2026-03-15 21:57:23'),(7,'淑惠','11112@gmail.com',NULL,'$2y$12$6rZfkj2vr2hwaAcVdmBxp.XfOdyL1Jt8njvNzN60htbP08rOuEbIe','avatars/1773586852_微信图片_20260315224635_48_111.png','female',38,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 22:00:06','2026-03-15 22:00:52'),(8,'美玲','11113@gmail.com',NULL,'$2y$12$VGPN4G.bRjevaiHhMP4r7.t0JQBv1o19RGoyfciERcepRQTXnQzue','avatars/1773586919_微信图片_20260315224648_49_111.png','female',39,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 22:01:35','2026-03-15 22:01:59'),(9,'雅婷','11114@gmail.com',NULL,'$2y$12$1nBGkIrXlTNkxlA6kXv8HeIozFFY.mNABqMJ4QcRuQQ1oWFwNrLdK','avatars/1773586991_微信图片_20260315224704_50_111.png','female',39,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 22:02:44','2026-03-15 22:03:11'),(10,'美惠','11115@gmail.com',NULL,'$2y$12$lxBJLaNOTlnG.uG0Szh9COi/GjUP2KrEVCGuO/mP0EZCDmnzx4O4q','avatars/1773587056_微信图片_20260315224720_51_111.png','female',40,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 22:03:42','2026-03-15 22:04:16'),(11,'丽华','11116@gmail.com',NULL,'$2y$12$CIMwtk1wXfDPbH2hxFRX5Om8MeQBhdCOnQGZgigitwF9oH3ahDP3C','avatars/1773587143_微信图片_20260315224748_52_111.png','female',41,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 22:05:04','2026-03-15 22:05:43'),(12,'淑娟','11117@gmail.com',NULL,'$2y$12$nG1iErVI9/VCmeLksnYAauiTDWJ3ll6h5z0RLT5G3N9zDX54ekzHa','avatars/1773587202_微信图片_20260315224801_53_111.png','female',38,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 22:06:13','2026-03-15 22:06:43'),(13,'淑贞','11118@gmail.com',NULL,'$2y$12$DwbR948tSL0w.i1caNK41.UrNCO5mY/CZGq8K8xRbEra0/.TJCtPq','avatars/1773587266_微信图片_20260315224820_54_111.png','female',39,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 22:07:18','2026-03-15 22:07:46'),(14,'怡君','11119@gmail.com',NULL,'$2y$12$C7ctAkZuRVdngGE.sl2AZeRfWz1QjVi9TcrYKu6FY5EJdsPHOtHQq','avatars/1773587341_微信图片_20260315224837_55_111.png','female',37,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 22:08:27','2026-03-15 22:09:01'),(15,'淑华','11120@gmail.com',NULL,'$2y$12$rZB0C5iZ0E0EJ/.IJO29/OAkEQq29wZuWgkO0uQsAXDLcINBprbci','avatars/1773587411_微信图片_20260315224902_56_111.png','female',37,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 22:09:36','2026-03-15 22:10:11'),(16,'佩玲','11121@gmail.com',NULL,'$2y$12$QPvC4CYeFNn3QFl85ikDC.wdsedaNJBS4LUg/5nZBhdw1qWby.O2i','avatars/1773587467_微信图片_20260315224954_57_111.png','female',38,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 22:10:45','2026-03-15 22:11:07'),(17,'惠雯','11122@gmail.com',NULL,'$2y$12$WZP5QCQC5ahPDNSC4jI4geEt9fpp.QxGlEd5ouBM88F76ddmsFR12','avatars/1773587523_微信图片_20260315225012_58_111.png','female',37,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 22:11:41','2026-03-15 22:12:03'),(18,'秀琴','11123@gmail.com',NULL,'$2y$12$iGed1IqxpiuaTJPo/XxVzOFCJ5HeHLH2HWe4rocLihWnunHQGlsQi','avatars/1773587582_微信图片_20260315225026_59_111.png','female',36,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 22:12:38','2026-03-15 22:13:02'),(19,'玉华','11124@gmail.com',NULL,'$2y$12$uZdsTDnNE2CYoZEw6FSFYeKcIHyX1rG21NN8Io.2pM15Wog0Y64/C','avatars/1773587637_微信图片_20260315225110_60_111.png','female',39,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 22:13:36','2026-03-15 22:13:57'),(20,'雅惠','11125@gmail.com',NULL,'$2y$12$l1aN05V2RrNqLAoR4VqvZuwKWO0CbLZb0VZirgFHlXa041lwjj39u','avatars/1773587742_微信图片_20260315225128_61_111.png','female',36,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-15 22:14:28','2026-03-15 22:15:42'),(21,'admin888','admin888@123.com',NULL,'$2y$12$hbj8jKU8lrgpZBshEvoCmeO2tIzOn9p.CuI3xsNqVr6BQ90dwyij.',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,1,0,NULL,'2026-03-16 21:06:26','2026-03-16 21:06:26');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-17  8:02:13
