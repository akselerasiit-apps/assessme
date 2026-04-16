-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: 127.0.0.1    Database: assessme_cobit2019
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

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
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) unsigned DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
INSERT INTO `activity_log` VALUES (1,'default','created','App\\Models\\User','created',6,NULL,NULL,'{\"attributes\":{\"name\":\"Ahmad Choirul\",\"email\":\"ahmad@cobit.com\"}}',NULL,'2025-12-09 18:54:12','2025-12-09 18:54:12'),(2,'default','deleted','App\\Models\\User','deleted',2,NULL,NULL,'{\"old\":{\"name\":\"System Administrator\",\"email\":\"admin@assessme.com\"}}',NULL,'2026-01-23 02:20:08','2026-01-23 02:20:08'),(3,'default','deleted','App\\Models\\User','deleted',3,NULL,NULL,'{\"old\":{\"name\":\"Assessment Manager\",\"email\":\"manager@assessme.com\"}}',NULL,'2026-01-23 02:20:08','2026-01-23 02:20:08'),(4,'default','deleted','App\\Models\\User','deleted',5,'App\\Models\\User',1,'{\"old\":{\"name\":\"Report Viewer\",\"email\":\"viewer@assessme.com\"}}',NULL,'2026-01-25 00:43:25','2026-01-25 00:43:25'),(5,'default','created','App\\Models\\User','created',7,'App\\Models\\User',1,'{\"attributes\":{\"name\":\"Ahmad Choirul Firdaus\",\"email\":\"ahmadchoirul55@gmail.com\"}}',NULL,'2026-01-25 00:43:54','2026-01-25 00:43:54'),(6,'default','deleted','App\\Models\\User','deleted',6,'App\\Models\\User',1,'{\"old\":{\"name\":\"Ahmad Choirul\",\"email\":\"ahmad@cobit.com\"}}',NULL,'2026-01-25 00:44:08','2026-01-25 00:44:08'),(7,'default','created','App\\Models\\User','created',8,'App\\Models\\User',1,'{\"attributes\":{\"name\":\"Asesi Aini\",\"email\":\"aini@gmail.com\"}}',NULL,'2026-01-26 05:42:47','2026-01-26 05:42:47'),(8,'default','Settings updated','App\\Models\\User',NULL,1,'App\\Models\\User',1,'{\"timezone\":\"Asia\\/Jakarta\",\"language\":\"id\",\"email_notifications\":\"1\",\"assessment_reminders\":\"1\"}',NULL,'2026-01-27 09:14:19','2026-01-27 09:14:19');
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_answer_capability_scores`
--

DROP TABLE IF EXISTS `assessment_answer_capability_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_answer_capability_scores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_answer_id` bigint(20) unsigned NOT NULL,
  `level` int(11) NOT NULL,
  `compliance_score` decimal(5,2) DEFAULT NULL,
  `compliance_percentage` int(11) DEFAULT NULL,
  `achievement_status` enum('NOT_ACHIEVED','PARTIALLY_ACHIEVED','FULLY_ACHIEVED') DEFAULT NULL,
  `evidence_provided` tinyint(1) NOT NULL DEFAULT 0,
  `evidence_count` int(11) NOT NULL DEFAULT 0,
  `assessment_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `aacs_answer_level_unique` (`assessment_answer_id`,`level`),
  CONSTRAINT `assessment_answer_capability_scores_assessment_answer_id_foreign` FOREIGN KEY (`assessment_answer_id`) REFERENCES `assessment_answers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_answer_capability_scores`
--

LOCK TABLES `assessment_answer_capability_scores` WRITE;
/*!40000 ALTER TABLE `assessment_answer_capability_scores` DISABLE KEYS */;
/*!40000 ALTER TABLE `assessment_answer_capability_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_answers`
--

DROP TABLE IF EXISTS `assessment_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_answers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint(20) unsigned NOT NULL,
  `question_id` bigint(20) unsigned NOT NULL,
  `gamo_objective_id` bigint(20) unsigned NOT NULL,
  `answer_text` longtext DEFAULT NULL,
  `translated_text` text DEFAULT NULL,
  `answer_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answer_json`)),
  `maturity_level` int(11) NOT NULL DEFAULT 0,
  `level` int(11) DEFAULT NULL,
  `capability_score` decimal(5,2) DEFAULT NULL,
  `capability_rating` enum('N/A','N','P','L','F') DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 1,
  `evidence_file` varchar(255) DEFAULT NULL,
  `evidence_encrypted` tinyint(1) NOT NULL DEFAULT 1,
  `current_version` int(11) NOT NULL DEFAULT 1,
  `current_version_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `evidence_updated_at` timestamp NULL DEFAULT NULL,
  `answered_by` bigint(20) unsigned NOT NULL,
  `answered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `aa_assessment_question_unique` (`assessment_id`,`question_id`),
  KEY `assessment_answers_question_id_foreign` (`question_id`),
  KEY `assessment_answers_answered_by_foreign` (`answered_by`),
  KEY `assessment_answers_gamo_objective_id_index` (`gamo_objective_id`),
  KEY `assessment_answers_evidence_updated_at_index` (`evidence_updated_at`),
  CONSTRAINT `assessment_answers_answered_by_foreign` FOREIGN KEY (`answered_by`) REFERENCES `users` (`id`),
  CONSTRAINT `assessment_answers_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_answers_gamo_objective_id_foreign` FOREIGN KEY (`gamo_objective_id`) REFERENCES `gamo_objectives` (`id`),
  CONSTRAINT `assessment_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `gamo_questions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_answers`
--

LOCK TABLES `assessment_answers` WRITE;
/*!40000 ALTER TABLE `assessment_answers` DISABLE KEYS */;
INSERT INTO `assessment_answers` VALUES (2,9,1153,1,NULL,NULL,NULL,0,3,0.67,'L',1,NULL,1,1,NULL,'okeeh deh siapp',NULL,NULL,1,'2026-01-25 09:37:13','2026-01-21 01:20:23','2026-01-25 09:37:13'),(3,9,1272,1,NULL,NULL,NULL,0,2,0.67,'L',1,NULL,1,1,NULL,'Sudah largely',NULL,NULL,1,'2026-01-24 17:28:56','2026-01-21 23:22:35','2026-01-24 17:28:56'),(4,9,1274,1,NULL,NULL,NULL,0,2,1.00,'F',1,NULL,1,1,NULL,NULL,NULL,NULL,1,'2026-01-24 18:29:40','2026-01-24 18:29:40','2026-01-24 18:29:40'),(5,9,1152,1,NULL,NULL,NULL,0,2,1.00,'F',1,NULL,1,1,NULL,NULL,NULL,NULL,1,'2026-01-24 18:29:46','2026-01-24 18:29:46','2026-01-24 18:29:46'),(6,9,1273,1,NULL,NULL,NULL,0,4,0.15,'N',1,NULL,1,1,NULL,NULL,NULL,NULL,1,'2026-01-25 09:37:04','2026-01-24 18:36:54','2026-01-25 09:37:04'),(7,9,1154,1,NULL,NULL,NULL,0,5,0.15,'N',1,NULL,1,1,NULL,NULL,NULL,NULL,1,'2026-01-24 18:39:48','2026-01-24 18:37:07','2026-01-24 18:39:48'),(8,10,1272,1,NULL,NULL,NULL,0,2,0.67,'L',1,NULL,1,1,NULL,'Oke sip, tambahkan evidence tambahan yg lain',NULL,NULL,1,'2026-01-29 07:38:40','2026-01-25 20:48:32','2026-01-29 07:38:40'),(9,10,1274,1,NULL,NULL,NULL,0,2,1.00,'F',1,NULL,1,1,NULL,NULL,NULL,NULL,1,'2026-01-29 13:01:35','2026-01-25 20:48:53','2026-01-29 13:01:35'),(10,10,1152,1,NULL,NULL,NULL,0,2,1.00,'F',1,NULL,1,1,NULL,NULL,NULL,NULL,1,'2026-01-29 13:01:46','2026-01-25 20:48:59','2026-01-29 13:01:46'),(11,10,1153,1,NULL,NULL,NULL,0,3,1.00,'F',1,NULL,1,1,NULL,NULL,NULL,NULL,4,'2026-01-25 21:21:00','2026-01-25 20:49:38','2026-01-25 21:21:00'),(12,10,1234,36,NULL,NULL,NULL,0,3,1.00,'F',1,NULL,1,1,NULL,NULL,NULL,NULL,1,'2026-01-27 13:37:52','2026-01-27 13:34:40','2026-01-27 13:37:52'),(13,10,1246,18,NULL,NULL,NULL,0,3,1.00,'F',1,NULL,1,1,NULL,NULL,NULL,NULL,1,'2026-01-27 13:48:44','2026-01-27 13:48:44','2026-01-27 13:48:44'),(14,10,1159,3,NULL,NULL,NULL,0,3,1.00,'F',1,NULL,1,1,NULL,NULL,NULL,NULL,1,'2026-01-27 23:25:05','2026-01-27 13:51:07','2026-01-27 23:25:05'),(15,10,1273,1,NULL,NULL,NULL,0,4,1.00,'F',1,NULL,1,1,NULL,NULL,NULL,NULL,1,'2026-01-27 23:22:12','2026-01-27 23:22:12','2026-01-27 23:22:12'),(16,10,1154,1,NULL,NULL,NULL,0,5,1.00,'F',1,NULL,1,1,NULL,NULL,NULL,NULL,1,'2026-01-27 23:22:25','2026-01-27 23:22:19','2026-01-27 23:22:25'),(17,10,1160,3,NULL,NULL,NULL,0,5,0.33,'P',1,NULL,1,1,NULL,NULL,NULL,NULL,1,'2026-01-27 23:25:23','2026-01-27 23:22:53','2026-01-27 23:25:23'),(18,10,1247,18,NULL,NULL,NULL,0,5,0.00,'N/A',1,NULL,1,1,NULL,NULL,NULL,NULL,1,'2026-01-27 23:27:04','2026-01-27 23:27:04','2026-01-27 23:27:04'),(19,10,1243,17,NULL,NULL,NULL,0,3,1.00,'F',1,NULL,1,1,NULL,NULL,NULL,NULL,1,'2026-01-29 13:02:45','2026-01-29 13:02:45','2026-01-29 13:02:45');
/*!40000 ALTER TABLE `assessment_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_audit_logs`
--

DROP TABLE IF EXISTS `assessment_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint(20) unsigned NOT NULL,
  `gamo_objective_id` bigint(20) unsigned NOT NULL,
  `level` int(11) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `action` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `old_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_value`)),
  `new_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_value`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_audit_logs_gamo_objective_id_foreign` (`gamo_objective_id`),
  KEY `assessment_audit_logs_user_id_foreign` (`user_id`),
  KEY `assessment_audit_logs_assessment_id_gamo_objective_id_index` (`assessment_id`,`gamo_objective_id`),
  KEY `assessment_audit_logs_created_at_index` (`created_at`),
  CONSTRAINT `assessment_audit_logs_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_audit_logs_gamo_objective_id_foreign` FOREIGN KEY (`gamo_objective_id`) REFERENCES `gamo_objectives` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_audit_logs`
--

LOCK TABLES `assessment_audit_logs` WRITE;
/*!40000 ALTER TABLE `assessment_audit_logs` DISABLE KEYS */;
INSERT INTO `assessment_audit_logs` VALUES (5,9,1,3,1,'upload_evidence','Mengunggah evidence: File Tiket',NULL,'{\"evidence_id\":2}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-21 01:20:16','2026-01-21 01:20:16'),(6,9,1,3,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-21 01:20:23','2026-01-21 01:20:23'),(7,9,1,3,1,'update_rating','Mengubah penilaian aktivitas menjadi N',NULL,'{\"rating\":\"N\",\"score\":0.15}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-21 21:52:19','2026-01-21 21:52:19'),(8,9,1,3,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-21 23:21:06','2026-01-21 23:21:06'),(9,9,1,3,1,'upload_evidence','Mengunggah evidence: Arnes',NULL,'{\"evidence_id\":3}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-21 23:21:26','2026-01-21 23:21:26'),(10,9,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi L',NULL,'{\"rating\":\"L\",\"score\":0.67}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-21 23:22:35','2026-01-21 23:22:35'),(11,9,1,3,1,'update_rating','Mengubah penilaian aktivitas menjadi L',NULL,'{\"rating\":\"L\",\"score\":0.67}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-24 11:31:48','2026-01-24 11:31:48'),(12,9,1,2,1,'upload_evidence','Mengunggah evidence: Kerjaa Fatih',NULL,'{\"evidence_id\":4}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-24 17:27:20','2026-01-24 17:27:20'),(13,9,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi L',NULL,'{\"rating\":\"L\",\"score\":0.67}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-24 17:28:56','2026-01-24 17:28:56'),(14,9,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-24 18:29:40','2026-01-24 18:29:40'),(15,9,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-24 18:29:46','2026-01-24 18:29:46'),(16,9,1,4,1,'update_rating','Mengubah penilaian aktivitas menjadi N',NULL,'{\"rating\":\"N\",\"score\":0.15}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-24 18:36:54','2026-01-24 18:36:54'),(17,9,1,4,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-24 18:37:00','2026-01-24 18:37:00'),(18,9,1,5,1,'update_rating','Mengubah penilaian aktivitas menjadi P',NULL,'{\"rating\":\"P\",\"score\":0.33}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-24 18:37:07','2026-01-24 18:37:07'),(19,9,1,5,1,'update_rating','Mengubah penilaian aktivitas menjadi N',NULL,'{\"rating\":\"N\",\"score\":0.15}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-24 18:39:48','2026-01-24 18:39:48'),(20,9,1,3,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-24 23:14:34','2026-01-24 23:14:34'),(21,9,1,4,1,'update_rating','Mengubah penilaian aktivitas menjadi N',NULL,'{\"rating\":\"N\",\"score\":0.15}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-25 09:37:04','2026-01-25 09:37:04'),(22,9,1,3,1,'update_rating','Mengubah penilaian aktivitas menjadi L',NULL,'{\"rating\":\"L\",\"score\":0.67}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-25 09:37:13','2026-01-25 09:37:13'),(23,10,1,2,4,'update_rating','Mengubah penilaian aktivitas menjadi L',NULL,'{\"rating\":\"L\",\"score\":0.67}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-25 20:48:32','2026-01-25 20:48:32'),(24,10,1,2,4,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-25 20:48:53','2026-01-25 20:48:53'),(25,10,1,2,4,'update_rating','Mengubah penilaian aktivitas menjadi P',NULL,'{\"rating\":\"P\",\"score\":0.33}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-25 20:48:59','2026-01-25 20:48:59'),(26,10,1,3,4,'update_rating','Mengubah penilaian aktivitas menjadi N',NULL,'{\"rating\":\"N\",\"score\":0.15}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-25 20:49:38','2026-01-25 20:49:38'),(27,10,1,3,4,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-25 21:21:00','2026-01-25 21:21:00'),(28,10,1,2,1,'upload_evidence','Mengunggah evidence: Link Evidence',NULL,'{\"evidence_id\":5}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-26 01:59:49','2026-01-26 01:59:49'),(29,10,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-26 02:00:12','2026-01-26 02:00:12'),(30,10,1,2,8,'upload_evidence','Mengunggah evidence: Cek url',NULL,'{\"evidence_id\":6}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-26 05:47:57','2026-01-26 05:47:57'),(31,10,1,2,1,'upload_evidence','Mengunggah evidence: Fatihhh',NULL,'{\"evidence_id\":7}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-26 23:28:39','2026-01-26 23:28:39'),(32,10,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-26 23:57:11','2026-01-26 23:57:11'),(33,10,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi L',NULL,'{\"rating\":\"L\",\"score\":0.67}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-26 23:58:06','2026-01-26 23:58:06'),(34,10,1,2,1,'upload_evidence','Mengunggah evidence: adasdasdasd',NULL,'{\"evidence_id\":8}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 00:01:32','2026-01-27 00:01:32'),(35,10,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi N',NULL,'{\"rating\":\"N\",\"score\":0.15}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 07:06:04','2026-01-27 07:06:04'),(36,10,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi L',NULL,'{\"rating\":\"L\",\"score\":0.67}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 07:06:14','2026-01-27 07:06:14'),(37,10,36,3,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 13:34:40','2026-01-27 13:34:40'),(38,10,36,3,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 13:37:52','2026-01-27 13:37:52'),(39,10,18,3,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 13:48:44','2026-01-27 13:48:44'),(40,10,3,3,1,'update_rating','Mengubah penilaian aktivitas menjadi N',NULL,'{\"rating\":\"N\",\"score\":0.15}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 13:51:07','2026-01-27 13:51:07'),(41,10,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi P',NULL,'{\"rating\":\"P\",\"score\":0.33}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 23:21:45','2026-01-27 23:21:45'),(42,10,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 23:21:52','2026-01-27 23:21:52'),(43,10,1,4,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 23:22:12','2026-01-27 23:22:12'),(44,10,1,5,1,'update_rating','Mengubah penilaian aktivitas menjadi L',NULL,'{\"rating\":\"L\",\"score\":0.67}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 23:22:19','2026-01-27 23:22:19'),(45,10,1,5,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 23:22:25','2026-01-27 23:22:25'),(46,10,3,5,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 23:22:53','2026-01-27 23:22:53'),(47,10,3,3,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 23:25:05','2026-01-27 23:25:05'),(48,10,3,5,1,'update_rating','Mengubah penilaian aktivitas menjadi P',NULL,'{\"rating\":\"P\",\"score\":0.33}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 23:25:23','2026-01-27 23:25:23'),(49,10,18,5,1,'update_rating','Mengubah penilaian aktivitas menjadi N/A',NULL,'{\"rating\":\"N\\/A\",\"score\":0}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-27 23:27:04','2026-01-27 23:27:04'),(50,10,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi L',NULL,'{\"rating\":\"L\",\"score\":0.67}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-29 07:38:40','2026-01-29 07:38:40'),(51,10,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-29 07:39:03','2026-01-29 07:39:03'),(52,10,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi L',NULL,'{\"rating\":\"L\",\"score\":0.67}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-29 13:01:29','2026-01-29 13:01:29'),(53,10,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-29 13:01:35','2026-01-29 13:01:35'),(54,10,1,2,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-29 13:01:46','2026-01-29 13:01:46'),(55,10,17,3,1,'update_rating','Mengubah penilaian aktivitas menjadi F',NULL,'{\"rating\":\"F\",\"score\":1}','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-29 13:02:45','2026-01-29 13:02:45');
/*!40000 ALTER TABLE `assessment_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_bandings`
--

DROP TABLE IF EXISTS `assessment_bandings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_bandings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint(20) unsigned NOT NULL,
  `gamo_objective_id` bigint(20) unsigned NOT NULL,
  `banding_round` int(11) NOT NULL DEFAULT 1,
  `initiated_by` bigint(20) unsigned NOT NULL,
  `banding_reason` varchar(255) NOT NULL,
  `banding_description` longtext DEFAULT NULL,
  `old_maturity_level` decimal(3,2) DEFAULT NULL,
  `new_maturity_level` decimal(3,2) DEFAULT NULL,
  `old_evidence_count` int(11) DEFAULT NULL,
  `new_evidence_count` int(11) DEFAULT NULL,
  `additional_evidence_files` varchar(500) DEFAULT NULL,
  `revised_answers` longtext DEFAULT NULL,
  `status` enum('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_bandings_assessment_id_foreign` (`assessment_id`),
  KEY `assessment_bandings_gamo_objective_id_foreign` (`gamo_objective_id`),
  KEY `assessment_bandings_initiated_by_foreign` (`initiated_by`),
  KEY `assessment_bandings_approved_by_foreign` (`approved_by`),
  KEY `assessment_bandings_status_index` (`status`),
  KEY `assessment_bandings_banding_round_index` (`banding_round`),
  CONSTRAINT `assessment_bandings_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `assessment_bandings_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_bandings_gamo_objective_id_foreign` FOREIGN KEY (`gamo_objective_id`) REFERENCES `gamo_objectives` (`id`),
  CONSTRAINT `assessment_bandings_initiated_by_foreign` FOREIGN KEY (`initiated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_bandings`
--

LOCK TABLES `assessment_bandings` WRITE;
/*!40000 ALTER TABLE `assessment_bandings` DISABLE KEYS */;
/*!40000 ALTER TABLE `assessment_bandings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_design_factors`
--

DROP TABLE IF EXISTS `assessment_design_factors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_design_factors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint(20) unsigned NOT NULL,
  `design_factor_id` bigint(20) unsigned NOT NULL,
  `selected_value` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `adf_assessment_df_unique` (`assessment_id`,`design_factor_id`),
  KEY `assessment_design_factors_design_factor_id_foreign` (`design_factor_id`),
  CONSTRAINT `assessment_design_factors_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_design_factors_design_factor_id_foreign` FOREIGN KEY (`design_factor_id`) REFERENCES `design_factors` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_design_factors`
--

LOCK TABLES `assessment_design_factors` WRITE;
/*!40000 ALTER TABLE `assessment_design_factors` DISABLE KEYS */;
INSERT INTO `assessment_design_factors` VALUES (13,9,1,NULL,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(14,9,10,NULL,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(15,10,1,NULL,NULL,'2026-01-25 20:25:38','2026-01-25 20:25:38'),(16,10,2,NULL,NULL,'2026-01-25 20:25:38','2026-01-25 20:25:38'),(17,10,3,NULL,NULL,'2026-01-25 20:25:38','2026-01-25 20:25:38'),(18,10,4,NULL,NULL,'2026-01-25 20:25:38','2026-01-25 20:25:38');
/*!40000 ALTER TABLE `assessment_design_factors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_evidence`
--

DROP TABLE IF EXISTS `assessment_evidence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_evidence` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint(20) unsigned NOT NULL,
  `activity_id` bigint(20) unsigned NOT NULL,
  `evidence_name` varchar(255) NOT NULL,
  `evidence_description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` bigint(20) unsigned DEFAULT NULL,
  `uploaded_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_evidence_activity_id_foreign` (`activity_id`),
  KEY `assessment_evidence_uploaded_by_foreign` (`uploaded_by`),
  KEY `assessment_evidence_assessment_id_activity_id_index` (`assessment_id`,`activity_id`),
  CONSTRAINT `assessment_evidence_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `gamo_questions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_evidence_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_evidence_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_evidence`
--

LOCK TABLES `assessment_evidence` WRITE;
/*!40000 ALTER TABLE `assessment_evidence` DISABLE KEYS */;
INSERT INTO `assessment_evidence` VALUES (2,9,1153,'File Tiket','asdsad','assessments/9/evidence/CZoCXgvjfudI8TXvKHMv03Z5sBUZwrri3TvH177p.pdf',NULL,'pdf',51950,1,'2026-01-21 01:20:16','2026-01-21 01:20:16'),(3,9,1153,'Arnes','Tiket Arnes','assessments/9/evidence/dznmf4E8A8k1izSbVDroQTdLuem2Vs60Wvcdw4Lg.pdf',NULL,'pdf',20511,1,'2026-01-21 23:21:26','2026-01-21 23:21:26'),(4,9,1272,'Kerjaa Fatih','Isra miraj','assessments/9/evidence/wNHvtkr5o9QXo0eSMvEgwkRktYo0Wg1ZkllK6chY.png',NULL,'png',491466,1,'2026-01-24 17:27:20','2026-01-24 17:27:20'),(5,10,1272,'Link Evidence','Ini link evidence',NULL,'https://chatgpt.com/c/696d91d3-30f8-832b-83b6-593b7b13c036',NULL,NULL,1,'2026-01-26 01:59:49','2026-01-26 01:59:49'),(6,10,1274,'Cek url','Chat Gpt',NULL,'https://chatgpt.com/',NULL,NULL,8,'2026-01-26 05:47:57','2026-01-26 05:47:57'),(7,10,1152,'Fatihhh','Whatsapp',NULL,'https://web.whatsapp.com/',NULL,NULL,1,'2026-01-26 23:28:39','2026-01-26 23:28:39'),(8,10,1272,'adasdasdasd','ads','assessments/10/evidence/MlaybFv4gRKoXiTrEgUDBNBOoLl7DEIv0FH4dkgK.xlsx',NULL,'xlsx',10565,1,'2026-01-27 00:01:32','2026-01-27 00:01:32');
/*!40000 ALTER TABLE `assessment_evidence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_gamo_selections`
--

DROP TABLE IF EXISTS `assessment_gamo_selections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_gamo_selections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint(20) unsigned NOT NULL,
  `gamo_objective_id` bigint(20) unsigned NOT NULL,
  `target_maturity_level` int(11) NOT NULL DEFAULT 3,
  `is_selected` tinyint(1) NOT NULL DEFAULT 1,
  `selection_reason` text DEFAULT NULL,
  `selected_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ags_assessment_gamo_unique` (`assessment_id`,`gamo_objective_id`),
  KEY `assessment_gamo_selections_gamo_objective_id_foreign` (`gamo_objective_id`),
  CONSTRAINT `assessment_gamo_selections_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_gamo_selections_gamo_objective_id_foreign` FOREIGN KEY (`gamo_objective_id`) REFERENCES `gamo_objectives` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_gamo_selections`
--

LOCK TABLES `assessment_gamo_selections` WRITE;
/*!40000 ALTER TABLE `assessment_gamo_selections` DISABLE KEYS */;
INSERT INTO `assessment_gamo_selections` VALUES (40,9,1,5,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(41,9,2,4,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(42,9,3,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(43,9,4,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(44,9,5,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(45,9,6,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(46,9,7,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(47,9,8,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(48,9,9,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(49,9,11,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(50,9,12,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(51,9,25,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(52,9,26,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(53,9,31,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(54,9,13,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(55,9,14,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(56,9,33,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(57,9,35,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(58,9,37,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(59,9,17,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(60,9,18,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(61,9,20,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(62,9,22,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(63,9,23,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(64,9,24,3,1,NULL,'2026-01-21 01:09:52','2026-01-21 01:09:52'),(65,10,1,4,1,NULL,'2026-01-25 20:25:38','2026-01-25 20:25:38'),(66,10,3,4,1,NULL,'2026-01-25 20:25:38','2026-01-25 20:25:38'),(67,10,6,4,1,NULL,'2026-01-25 20:25:38','2026-01-25 20:25:38'),(68,10,7,5,1,NULL,'2026-01-25 20:25:38','2026-01-25 20:25:38'),(69,10,16,2,1,NULL,'2026-01-25 20:25:38','2026-01-25 20:25:38'),(70,10,36,3,1,NULL,'2026-01-25 20:25:38','2026-01-25 20:25:38'),(71,10,17,4,1,NULL,'2026-01-25 20:25:38','2026-01-25 20:25:38'),(72,10,18,3,1,NULL,'2026-01-25 20:25:38','2026-01-25 20:25:38'),(73,10,22,4,1,NULL,'2026-01-25 20:25:38','2026-01-25 20:25:38'),(74,10,23,4,1,NULL,'2026-01-25 20:25:38','2026-01-25 20:25:38'),(75,10,24,4,1,NULL,'2026-01-25 20:25:38','2026-01-25 20:25:38');
/*!40000 ALTER TABLE `assessment_gamo_selections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_gamo_target_levels`
--

DROP TABLE IF EXISTS `assessment_gamo_target_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_gamo_target_levels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint(20) unsigned NOT NULL,
  `gamo_objective_id` bigint(20) unsigned NOT NULL,
  `current_maturity_level` decimal(3,2) NOT NULL DEFAULT 0.00,
  `target_maturity_level` decimal(3,2) NOT NULL DEFAULT 3.00,
  `priority` enum('LOW','MEDIUM','HIGH','CRITICAL') NOT NULL DEFAULT 'MEDIUM',
  `estimated_effort` varchar(255) DEFAULT NULL,
  `target_achievement_date` date DEFAULT NULL,
  `gap_analysis` text DEFAULT NULL,
  `recommended_actions` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agtl_assessment_gamo_unique` (`assessment_id`,`gamo_objective_id`),
  KEY `assessment_gamo_target_levels_gamo_objective_id_foreign` (`gamo_objective_id`),
  CONSTRAINT `assessment_gamo_target_levels_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_gamo_target_levels_gamo_objective_id_foreign` FOREIGN KEY (`gamo_objective_id`) REFERENCES `gamo_objectives` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_gamo_target_levels`
--

LOCK TABLES `assessment_gamo_target_levels` WRITE;
/*!40000 ALTER TABLE `assessment_gamo_target_levels` DISABLE KEYS */;
/*!40000 ALTER TABLE `assessment_gamo_target_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_notes`
--

DROP TABLE IF EXISTS `assessment_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint(20) unsigned NOT NULL,
  `activity_id` bigint(20) unsigned NOT NULL,
  `level` int(11) NOT NULL,
  `note_text` text NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_notes_activity_id_foreign` (`activity_id`),
  KEY `assessment_notes_created_by_foreign` (`created_by`),
  KEY `assessment_notes_assessment_id_activity_id_index` (`assessment_id`,`activity_id`),
  CONSTRAINT `assessment_notes_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `gamo_questions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_notes_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_notes`
--

LOCK TABLES `assessment_notes` WRITE;
/*!40000 ALTER TABLE `assessment_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `assessment_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_ofis`
--

DROP TABLE IF EXISTS `assessment_ofis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_ofis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint(20) unsigned NOT NULL,
  `gamo_objective_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` enum('auto','manual') NOT NULL DEFAULT 'manual',
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `category` varchar(255) DEFAULT NULL,
  `recommended_action` text DEFAULT NULL,
  `target_date` date DEFAULT NULL,
  `current_level` int(11) DEFAULT NULL,
  `target_level` int(11) DEFAULT NULL,
  `gap_score` decimal(5,2) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_ofis_gamo_objective_id_foreign` (`gamo_objective_id`),
  KEY `assessment_ofis_created_by_foreign` (`created_by`),
  KEY `assessment_ofis_updated_by_foreign` (`updated_by`),
  KEY `assessment_ofis_assessment_id_gamo_objective_id_index` (`assessment_id`,`gamo_objective_id`),
  KEY `assessment_ofis_type_index` (`type`),
  KEY `assessment_ofis_status_index` (`status`),
  CONSTRAINT `assessment_ofis_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_ofis_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `assessment_ofis_gamo_objective_id_foreign` FOREIGN KEY (`gamo_objective_id`) REFERENCES `gamo_objectives` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_ofis_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_ofis`
--

LOCK TABLES `assessment_ofis` WRITE;
/*!40000 ALTER TABLE `assessment_ofis` DISABLE KEYS */;
INSERT INTO `assessment_ofis` VALUES (1,9,1,'Rekomendasi Peningkatan Level 0 ke Level 5','<p><strong>Kesenjangan Kapabilitas: Level 0 → Level 5</strong></p><p>Untuk mencapai target level, disarankan untuk meningkatkan aktivitas berikut:</p><ul><li><strong>[EDM01.02.003]</strong> Menilai faktor internal dan eksternal yang memengaruhi desain tata kelola. (Level 2, Kepatuhan saat ini: %)</li><li><strong>[EDM01.02.001]</strong> Aku akan datang (Level 2, Kepatuhan saat ini: %)</li><li><strong>[EDM01.02.002]</strong> Coba adalah try (Level 2, Kepatuhan saat ini: %)</li><li><strong>[EDM01.01.A2]</strong> Menetapkan peran I&T dalam mendukung tujuan perusahaan. (Level 3, Kepatuhan saat ini: %)</li><li><strong>[EDM01.04.001]</strong> Si Doktor yang baik hati (Level 4, Kepatuhan saat ini: %)</li><li><strong>[EDM01.05.001]</strong> Mengintegrasikan persyaratan hukum, regulasi, dan kontrak. (Level 5, Kepatuhan saat ini: %)</li></ul>','auto','high','open','Process',NULL,NULL,0,5,5.00,1,NULL,'2026-01-25 09:45:25','2026-01-25 09:45:25'),(2,9,1,'OFI untuk EDM01','<p><strong>Ini adalah OFI untuk EDM01</strong></p><p><br></p><p>Jadi EDM01 adalah....</p><ol><li>Sakj</li><li>Pdwp</li><li>Kkfjek</li></ol><p><br></p><p>OJdwn kej f euhuerbe bubfqeur osnfgeiofe bijfb</p><p>Oon efijwofng</p>','manual','medium','open',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-01-25 09:52:38','2026-01-25 09:52:38'),(3,10,1,'Rekomendasi Peningkatan Level 0 ke Level 4','<p><strong>Kesenjangan Kapabilitas: Level 0 → Level 4</strong></p><p>Untuk mencapai target level, disarankan untuk meningkatkan aktivitas berikut:</p><ul><li><strong>[EDM01.02.003]</strong> Menilai faktor internal dan eksternal yang memengaruhi desain tata kelola. (Level 2, Kepatuhan saat ini: %)</li><li><strong>[EDM01.02.001]</strong> Aku akan datang (Level 2, Kepatuhan saat ini: %)</li><li><strong>[EDM01.02.002]</strong> Coba adalah try (Level 2, Kepatuhan saat ini: %)</li><li><strong>[EDM01.01.A2]</strong> Menetapkan peran I&T dalam mendukung tujuan perusahaan. (Level 3, Kepatuhan saat ini: %)</li><li><strong>[EDM01.04.001]</strong> Si Doktor yang baik hati (Level 4, Kepatuhan saat ini: 0%)</li></ul>','auto','high','open','Process',NULL,NULL,0,4,4.00,4,NULL,'2026-01-25 21:23:36','2026-01-25 21:23:36');
/*!40000 ALTER TABLE `assessment_ofis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_team_members`
--

DROP TABLE IF EXISTS `assessment_team_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_team_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `role` enum('lead','assessor','reviewer','observer') NOT NULL DEFAULT 'assessor',
  `responsibilities` text DEFAULT NULL,
  `can_edit` tinyint(1) NOT NULL DEFAULT 1,
  `can_approve` tinyint(1) NOT NULL DEFAULT 0,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assessment_team_members_assessment_id_user_id_unique` (`assessment_id`,`user_id`),
  KEY `assessment_team_members_assigned_by_foreign` (`assigned_by`),
  KEY `assessment_team_members_assessment_id_index` (`assessment_id`),
  KEY `assessment_team_members_user_id_index` (`user_id`),
  CONSTRAINT `assessment_team_members_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_team_members_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`),
  CONSTRAINT `assessment_team_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_team_members`
--

LOCK TABLES `assessment_team_members` WRITE;
/*!40000 ALTER TABLE `assessment_team_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `assessment_team_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessments`
--

DROP TABLE IF EXISTS `assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `assessment_type` enum('initial','periodic','specific') NOT NULL DEFAULT 'initial',
  `scope_type` enum('full','tailored') NOT NULL DEFAULT 'tailored',
  `status` enum('draft','in_progress','completed') DEFAULT 'draft',
  `maturity_level` decimal(3,2) DEFAULT NULL COMMENT 'Maturity level 0-5',
  `assessment_period_start` date DEFAULT NULL,
  `assessment_period_end` date DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `progress_percentage` int(11) NOT NULL DEFAULT 0,
  `overall_maturity_level` decimal(3,2) DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assessments_code_unique` (`code`),
  KEY `assessments_created_by_foreign` (`created_by`),
  KEY `assessments_status_index` (`status`),
  KEY `assessments_company_id_index` (`company_id`),
  KEY `assessments_created_at_index` (`created_at`),
  CONSTRAINT `assessments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `assessments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessments`
--

LOCK TABLES `assessments` WRITE;
/*!40000 ALTER TABLE `assessments` DISABLE KEYS */;
INSERT INTO `assessments` VALUES (9,'ASM-000001','Asesmen IT Maturity 2026','Pelaksanaan Self Asesmen',4,'periodic','tailored','in_progress',NULL,'2026-01-22','2026-01-24',1,4,NULL,1,'2026-01-21 01:09:52','2026-01-25 05:08:56'),(10,'ASM-000002','Self Assessment IT Maturity 2026','Self Assessment IT Maturity Tahun 2026 dengan framework COBIT 2019',4,'periodic','tailored','in_progress',NULL,'2026-01-26','2026-02-26',4,45,3.50,1,'2026-01-25 20:25:38','2026-01-29 13:14:42');
/*!40000 ALTER TABLE `assessments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `entity_type` varchar(100) DEFAULT NULL,
  `entity_id` bigint(20) DEFAULT NULL,
  `status_code` int(11) DEFAULT NULL,
  `old_values` longtext DEFAULT NULL,
  `new_values` longtext DEFAULT NULL,
  `sensitive_data_accessed` tinyint(1) NOT NULL DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_index` (`user_id`),
  KEY `audit_logs_action_index` (`action`),
  KEY `audit_logs_created_at_index` (`created_at`),
  KEY `audit_logs_sensitive_data_accessed_index` (`sensitive_data_accessed`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=209 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'viewed','Assessment','Assessment',10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0','YQGBsvZbKCYUgwtD6bFoGiMnmRKvfP2CPppoqvNC',1,'2026-01-26 23:26:33'),(2,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-26 23:56:53'),(3,1,'viewed','Assessment',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-26 23:56:57'),(4,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-26 23:57:00'),(5,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-26 23:57:41'),(6,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-26 23:57:41'),(7,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 00:01:11'),(8,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 00:01:38'),(9,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 07:05:55'),(10,1,'updated','Assessment','App\\Models\\Assessment',10,NULL,'\"{\\\"id\\\":10,\\\"code\\\":\\\"ASM-000002\\\",\\\"title\\\":\\\"Self Assessment IT Maturity 2026\\\",\\\"description\\\":\\\"Self Assessment IT Maturity Tahun 2026 dengan framework COBIT 2019\\\",\\\"company_id\\\":4,\\\"assessment_type\\\":\\\"periodic\\\",\\\"scope_type\\\":\\\"tailored\\\",\\\"status\\\":\\\"in_progress\\\",\\\"maturity_level\\\":null,\\\"assessment_period_start\\\":\\\"2026-01-25T17:00:00.000000Z\\\",\\\"assessment_period_end\\\":\\\"2026-02-25T17:00:00.000000Z\\\",\\\"created_by\\\":4,\\\"reviewed_by\\\":null,\\\"approved_by\\\":null,\\\"progress_percentage\\\":9,\\\"overall_maturity_level\\\":null,\\\"is_encrypted\\\":true,\\\"created_at\\\":\\\"2026-01-25T20:25:38.000000Z\\\",\\\"updated_at\\\":\\\"2026-01-25T20:48:32.000000Z\\\"}\"','\"{\\\"overall_maturity_level\\\":0,\\\"updated_at\\\":\\\"2026-01-27 14:06:04\\\"}\"',0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 07:06:04'),(11,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 07:06:44'),(12,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 08:34:06'),(13,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 08:34:33'),(14,1,'viewed','Review-approval',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 08:35:16'),(15,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 08:39:35'),(16,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 08:50:02'),(17,1,'viewed','Assessment',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:05:52'),(18,1,'viewed','Assessment',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:14:00'),(19,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:14:12'),(20,1,'updated','User','App\\Models\\User',1,NULL,'\"{\\\"id\\\":1,\\\"name\\\":\\\"Super Administrator\\\",\\\"email\\\":\\\"superadmin@assessme.com\\\",\\\"phone\\\":null,\\\"bio\\\":null,\\\"timezone\\\":null,\\\"language\\\":\\\"id\\\",\\\"preferences\\\":null,\\\"email_verified_at\\\":null,\\\"avatar_path\\\":null,\\\"company_id\\\":null,\\\"is_active\\\":true,\\\"remember_token\\\":null,\\\"created_at\\\":\\\"2025-12-09T08:12:09.000000Z\\\",\\\"updated_at\\\":\\\"2025-12-09T08:12:09.000000Z\\\"}\"','\"{\\\"preferences\\\":\\\"{\\\\\\\"timezone\\\\\\\":\\\\\\\"Asia\\\\\\\\\\\\\\/Jakarta\\\\\\\",\\\\\\\"language\\\\\\\":\\\\\\\"id\\\\\\\",\\\\\\\"email_notifications\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"assessment_reminders\\\\\\\":\\\\\\\"1\\\\\\\"}\\\",\\\"updated_at\\\":\\\"2026-01-27 16:14:19\\\"}\"',0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:14:19'),(21,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:14:19'),(22,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:15:06'),(23,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:24:17'),(24,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:24:30'),(25,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:24:34'),(26,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:32:01'),(27,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:32:02'),(28,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:32:04'),(29,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:32:06'),(30,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:32:07'),(31,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:32:07'),(32,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:32:07'),(33,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:32:07'),(34,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:32:11'),(35,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:37:24'),(36,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:37:25'),(37,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','EJx33TFwVACYI6t85SJOqXWB87Zg36GAoqaSBsKw',1,'2026-01-27 09:37:48'),(38,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 11:58:33'),(39,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 11:58:43'),(40,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 11:58:50'),(41,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 11:59:55'),(42,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:01:19'),(43,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:05:07'),(44,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:05:08'),(45,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:05:11'),(46,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:05:12'),(47,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:07:08'),(48,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:07:43'),(49,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:07:44'),(50,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:07:45'),(51,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:07:45'),(52,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:07:48'),(53,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:09:39'),(54,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:09:53'),(55,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:09:55'),(56,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:10:00'),(57,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:10:08'),(58,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:10:30'),(59,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:10:31'),(60,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:10:42'),(61,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:10:43'),(62,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:10:57'),(63,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:10:58'),(64,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:10:58'),(65,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:10:58'),(66,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:10:58'),(67,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:11:36'),(68,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:11:37'),(69,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:11:48'),(70,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:11:49'),(71,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:11:49'),(72,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:11:49'),(73,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:11:49'),(74,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:12:13'),(75,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:12:13'),(76,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:12:13'),(77,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:12:13'),(78,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:12:25'),(79,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:12:25'),(80,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:12:25'),(81,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:12:25'),(82,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:12:26'),(83,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:12:35'),(84,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:12:35'),(85,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:12:35'),(86,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:34:01'),(87,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:34:03'),(88,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:34:21'),(89,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:34:43'),(90,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:34:44'),(91,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:34:44'),(92,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:34:44'),(93,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:34:44'),(94,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:34:44'),(95,1,'viewed','Assessment',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:35:19'),(96,1,'viewed','Assessment',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:35:26'),(97,1,'viewed','Assessment',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 12:42:32'),(98,1,'viewed','Assessment',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 13:01:36'),(99,1,'viewed','Assessment',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 13:01:38'),(100,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 13:01:39'),(101,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 13:25:48'),(102,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 13:26:10'),(103,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 13:33:18'),(104,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 13:33:24'),(105,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 13:33:39'),(106,1,'updated','Assessment','App\\Models\\Assessment',10,NULL,'\"{\\\"id\\\":10,\\\"code\\\":\\\"ASM-000002\\\",\\\"title\\\":\\\"Self Assessment IT Maturity 2026\\\",\\\"description\\\":\\\"Self Assessment IT Maturity Tahun 2026 dengan framework COBIT 2019\\\",\\\"company_id\\\":4,\\\"assessment_type\\\":\\\"periodic\\\",\\\"scope_type\\\":\\\"tailored\\\",\\\"status\\\":\\\"in_progress\\\",\\\"maturity_level\\\":null,\\\"assessment_period_start\\\":\\\"2026-01-25T17:00:00.000000Z\\\",\\\"assessment_period_end\\\":\\\"2026-02-25T17:00:00.000000Z\\\",\\\"created_by\\\":4,\\\"progress_percentage\\\":9,\\\"overall_maturity_level\\\":\\\"0.00\\\",\\\"is_encrypted\\\":true,\\\"created_at\\\":\\\"2026-01-25T20:25:38.000000Z\\\",\\\"updated_at\\\":\\\"2026-01-27T07:06:04.000000Z\\\"}\"','\"{\\\"progress_percentage\\\":18,\\\"updated_at\\\":\\\"2026-01-27 20:34:40\\\"}\"',0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 13:34:40'),(107,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 13:48:16'),(108,1,'updated','Assessment','App\\Models\\Assessment',10,NULL,'\"{\\\"id\\\":10,\\\"code\\\":\\\"ASM-000002\\\",\\\"title\\\":\\\"Self Assessment IT Maturity 2026\\\",\\\"description\\\":\\\"Self Assessment IT Maturity Tahun 2026 dengan framework COBIT 2019\\\",\\\"company_id\\\":4,\\\"assessment_type\\\":\\\"periodic\\\",\\\"scope_type\\\":\\\"tailored\\\",\\\"status\\\":\\\"in_progress\\\",\\\"maturity_level\\\":null,\\\"assessment_period_start\\\":\\\"2026-01-25T17:00:00.000000Z\\\",\\\"assessment_period_end\\\":\\\"2026-02-25T17:00:00.000000Z\\\",\\\"created_by\\\":4,\\\"progress_percentage\\\":18,\\\"overall_maturity_level\\\":\\\"0.00\\\",\\\"is_encrypted\\\":true,\\\"created_at\\\":\\\"2026-01-25T20:25:38.000000Z\\\",\\\"updated_at\\\":\\\"2026-01-27T13:34:40.000000Z\\\"}\"','\"{\\\"progress_percentage\\\":27,\\\"updated_at\\\":\\\"2026-01-27 20:48:44\\\"}\"',0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 13:48:44'),(109,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 13:50:55'),(110,1,'updated','Assessment','App\\Models\\Assessment',10,NULL,'\"{\\\"id\\\":10,\\\"code\\\":\\\"ASM-000002\\\",\\\"title\\\":\\\"Self Assessment IT Maturity 2026\\\",\\\"description\\\":\\\"Self Assessment IT Maturity Tahun 2026 dengan framework COBIT 2019\\\",\\\"company_id\\\":4,\\\"assessment_type\\\":\\\"periodic\\\",\\\"scope_type\\\":\\\"tailored\\\",\\\"status\\\":\\\"in_progress\\\",\\\"maturity_level\\\":null,\\\"assessment_period_start\\\":\\\"2026-01-25T17:00:00.000000Z\\\",\\\"assessment_period_end\\\":\\\"2026-02-25T17:00:00.000000Z\\\",\\\"created_by\\\":4,\\\"progress_percentage\\\":27,\\\"overall_maturity_level\\\":\\\"0.00\\\",\\\"is_encrypted\\\":true,\\\"created_at\\\":\\\"2026-01-25T20:25:38.000000Z\\\",\\\"updated_at\\\":\\\"2026-01-27T13:48:44.000000Z\\\"}\"','\"{\\\"progress_percentage\\\":36,\\\"updated_at\\\":\\\"2026-01-27 20:51:07\\\"}\"',0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','cBfIeJG7UZAIr3NMRtCQDQERnt4EIJKMiDcQ0P2a',1,'2026-01-27 13:51:07'),(111,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','VSPdU9thoyTGL3tBTEmApm7OYoAaHRLLArsiXNZd',1,'2026-01-27 23:21:36'),(112,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','VSPdU9thoyTGL3tBTEmApm7OYoAaHRLLArsiXNZd',1,'2026-01-27 23:24:49'),(113,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','VSPdU9thoyTGL3tBTEmApm7OYoAaHRLLArsiXNZd',1,'2026-01-27 23:27:15'),(114,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','VSPdU9thoyTGL3tBTEmApm7OYoAaHRLLArsiXNZd',1,'2026-01-27 23:28:21'),(115,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','VSPdU9thoyTGL3tBTEmApm7OYoAaHRLLArsiXNZd',1,'2026-01-27 23:28:35'),(116,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','VSPdU9thoyTGL3tBTEmApm7OYoAaHRLLArsiXNZd',1,'2026-01-27 23:28:36'),(117,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','VSPdU9thoyTGL3tBTEmApm7OYoAaHRLLArsiXNZd',1,'2026-01-27 23:28:41'),(118,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','VSPdU9thoyTGL3tBTEmApm7OYoAaHRLLArsiXNZd',1,'2026-01-27 23:28:52'),(119,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','VSPdU9thoyTGL3tBTEmApm7OYoAaHRLLArsiXNZd',1,'2026-01-27 23:29:09'),(120,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','VSPdU9thoyTGL3tBTEmApm7OYoAaHRLLArsiXNZd',1,'2026-01-27 23:53:28'),(121,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','VSPdU9thoyTGL3tBTEmApm7OYoAaHRLLArsiXNZd',1,'2026-01-27 23:53:29'),(122,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','VSPdU9thoyTGL3tBTEmApm7OYoAaHRLLArsiXNZd',1,'2026-01-27 23:53:35'),(123,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','VSPdU9thoyTGL3tBTEmApm7OYoAaHRLLArsiXNZd',1,'2026-01-28 00:03:50'),(124,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','VSPdU9thoyTGL3tBTEmApm7OYoAaHRLLArsiXNZd',1,'2026-01-28 00:03:52'),(125,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','VSPdU9thoyTGL3tBTEmApm7OYoAaHRLLArsiXNZd',1,'2026-01-28 01:20:29'),(126,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','QGdt56KrOWbzHF7pcRBaDu6Rkv59u67pGahVEdyK',1,'2026-01-28 04:51:07'),(127,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','QGdt56KrOWbzHF7pcRBaDu6Rkv59u67pGahVEdyK',1,'2026-01-28 04:51:24'),(128,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','QGdt56KrOWbzHF7pcRBaDu6Rkv59u67pGahVEdyK',1,'2026-01-28 05:12:12'),(129,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','QGdt56KrOWbzHF7pcRBaDu6Rkv59u67pGahVEdyK',1,'2026-01-28 05:17:08'),(130,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','hc3hTBTZ5nzVfa8f9WaOVYetfjv8TtGx8xUDG54f',1,'2026-01-28 07:31:20'),(131,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','hc3hTBTZ5nzVfa8f9WaOVYetfjv8TtGx8xUDG54f',1,'2026-01-28 07:31:28'),(132,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','hc3hTBTZ5nzVfa8f9WaOVYetfjv8TtGx8xUDG54f',1,'2026-01-28 07:31:32'),(133,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','hc3hTBTZ5nzVfa8f9WaOVYetfjv8TtGx8xUDG54f',1,'2026-01-28 07:31:37'),(134,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','hc3hTBTZ5nzVfa8f9WaOVYetfjv8TtGx8xUDG54f',1,'2026-01-28 07:31:40'),(135,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','hc3hTBTZ5nzVfa8f9WaOVYetfjv8TtGx8xUDG54f',1,'2026-01-28 07:31:43'),(136,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','hc3hTBTZ5nzVfa8f9WaOVYetfjv8TtGx8xUDG54f',1,'2026-01-28 07:39:00'),(137,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','hc3hTBTZ5nzVfa8f9WaOVYetfjv8TtGx8xUDG54f',1,'2026-01-28 08:29:01'),(138,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','hc3hTBTZ5nzVfa8f9WaOVYetfjv8TtGx8xUDG54f',1,'2026-01-28 08:30:04'),(139,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','hc3hTBTZ5nzVfa8f9WaOVYetfjv8TtGx8xUDG54f',1,'2026-01-28 09:22:46'),(140,1,'viewed','Assessment',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','hc3hTBTZ5nzVfa8f9WaOVYetfjv8TtGx8xUDG54f',1,'2026-01-28 09:22:50'),(141,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','hc3hTBTZ5nzVfa8f9WaOVYetfjv8TtGx8xUDG54f',1,'2026-01-28 09:22:53'),(142,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','hc3hTBTZ5nzVfa8f9WaOVYetfjv8TtGx8xUDG54f',1,'2026-01-28 09:22:55'),(143,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','rL2iYMuxOdrCrErgs724nB7xydUqTXzrpiHvo5TZ',1,'2026-01-29 01:34:19'),(144,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','rL2iYMuxOdrCrErgs724nB7xydUqTXzrpiHvo5TZ',1,'2026-01-29 01:43:02'),(145,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','rL2iYMuxOdrCrErgs724nB7xydUqTXzrpiHvo5TZ',1,'2026-01-29 01:43:51'),(146,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','rL2iYMuxOdrCrErgs724nB7xydUqTXzrpiHvo5TZ',1,'2026-01-29 01:45:15'),(147,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','rL2iYMuxOdrCrErgs724nB7xydUqTXzrpiHvo5TZ',1,'2026-01-29 01:54:13'),(148,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','rL2iYMuxOdrCrErgs724nB7xydUqTXzrpiHvo5TZ',1,'2026-01-29 01:56:16'),(149,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','rL2iYMuxOdrCrErgs724nB7xydUqTXzrpiHvo5TZ',1,'2026-01-29 01:59:17'),(150,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','rL2iYMuxOdrCrErgs724nB7xydUqTXzrpiHvo5TZ',1,'2026-01-29 01:59:18'),(151,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','rL2iYMuxOdrCrErgs724nB7xydUqTXzrpiHvo5TZ',1,'2026-01-29 01:59:21'),(152,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','rL2iYMuxOdrCrErgs724nB7xydUqTXzrpiHvo5TZ',1,'2026-01-29 02:15:50'),(153,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','rL2iYMuxOdrCrErgs724nB7xydUqTXzrpiHvo5TZ',1,'2026-01-29 02:21:01'),(154,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','rL2iYMuxOdrCrErgs724nB7xydUqTXzrpiHvo5TZ',1,'2026-01-29 02:21:04'),(155,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','opJi55L4ixwfkFu0SaE9jgJRVwk5tWLkohAlyQEv',1,'2026-01-29 07:07:44'),(156,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','opJi55L4ixwfkFu0SaE9jgJRVwk5tWLkohAlyQEv',1,'2026-01-29 07:12:00'),(157,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','opJi55L4ixwfkFu0SaE9jgJRVwk5tWLkohAlyQEv',1,'2026-01-29 07:12:01'),(158,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','opJi55L4ixwfkFu0SaE9jgJRVwk5tWLkohAlyQEv',1,'2026-01-29 07:16:51'),(159,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','opJi55L4ixwfkFu0SaE9jgJRVwk5tWLkohAlyQEv',1,'2026-01-29 07:18:36'),(160,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','opJi55L4ixwfkFu0SaE9jgJRVwk5tWLkohAlyQEv',1,'2026-01-29 07:18:38'),(161,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','opJi55L4ixwfkFu0SaE9jgJRVwk5tWLkohAlyQEv',1,'2026-01-29 07:18:44'),(162,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','opJi55L4ixwfkFu0SaE9jgJRVwk5tWLkohAlyQEv',1,'2026-01-29 07:34:26'),(163,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','opJi55L4ixwfkFu0SaE9jgJRVwk5tWLkohAlyQEv',1,'2026-01-29 07:34:55'),(164,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','sbnKszBJBewrY3bYVcayOIsLxa8ip41LfMvFkQVE',1,'2026-01-29 07:35:01'),(165,1,'viewed','Assessment',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','sbnKszBJBewrY3bYVcayOIsLxa8ip41LfMvFkQVE',1,'2026-01-29 07:35:06'),(166,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','sbnKszBJBewrY3bYVcayOIsLxa8ip41LfMvFkQVE',1,'2026-01-29 07:35:08'),(167,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','sbnKszBJBewrY3bYVcayOIsLxa8ip41LfMvFkQVE',1,'2026-01-29 07:35:14'),(168,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','sbnKszBJBewrY3bYVcayOIsLxa8ip41LfMvFkQVE',1,'2026-01-29 07:36:00'),(169,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','sbnKszBJBewrY3bYVcayOIsLxa8ip41LfMvFkQVE',1,'2026-01-29 07:38:03'),(170,1,'updated','Assessment','App\\Models\\Assessment',10,NULL,'\"{\\\"id\\\":10,\\\"code\\\":\\\"ASM-000002\\\",\\\"title\\\":\\\"Self Assessment IT Maturity 2026\\\",\\\"description\\\":\\\"Self Assessment IT Maturity Tahun 2026 dengan framework COBIT 2019\\\",\\\"company_id\\\":4,\\\"assessment_type\\\":\\\"periodic\\\",\\\"scope_type\\\":\\\"tailored\\\",\\\"status\\\":\\\"in_progress\\\",\\\"maturity_level\\\":null,\\\"assessment_period_start\\\":\\\"2026-01-25T17:00:00.000000Z\\\",\\\"assessment_period_end\\\":\\\"2026-02-25T17:00:00.000000Z\\\",\\\"created_by\\\":4,\\\"progress_percentage\\\":36,\\\"overall_maturity_level\\\":\\\"3.67\\\",\\\"is_encrypted\\\":true,\\\"created_at\\\":\\\"2026-01-25T20:25:38.000000Z\\\",\\\"updated_at\\\":\\\"2026-01-27T23:27:04.000000Z\\\"}\"','\"{\\\"status\\\":\\\"completed\\\",\\\"updated_at\\\":\\\"2026-01-29 14:38:07\\\"}\"',0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','sbnKszBJBewrY3bYVcayOIsLxa8ip41LfMvFkQVE',1,'2026-01-29 07:38:07'),(171,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','sbnKszBJBewrY3bYVcayOIsLxa8ip41LfMvFkQVE',1,'2026-01-29 07:38:09'),(172,1,'viewed','Assessment',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','sbnKszBJBewrY3bYVcayOIsLxa8ip41LfMvFkQVE',1,'2026-01-29 07:38:19'),(173,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','sbnKszBJBewrY3bYVcayOIsLxa8ip41LfMvFkQVE',1,'2026-01-29 07:38:26'),(174,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','sbnKszBJBewrY3bYVcayOIsLxa8ip41LfMvFkQVE',1,'2026-01-29 07:39:16'),(175,1,'viewed','Admin',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','sbnKszBJBewrY3bYVcayOIsLxa8ip41LfMvFkQVE',1,'2026-01-29 07:42:41'),(176,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 12:51:06'),(177,1,'viewed','Assessment',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:00:59'),(178,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:01:04'),(179,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:01:07'),(180,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:01:18'),(181,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:01:19'),(182,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:01:19'),(183,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:01:19'),(184,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:01:20'),(185,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:01:20'),(186,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:01:37'),(187,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:01:38'),(188,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:01:53'),(189,1,'updated','Assessment','App\\Models\\Assessment',10,NULL,'\"{\\\"id\\\":10,\\\"code\\\":\\\"ASM-000002\\\",\\\"title\\\":\\\"Self Assessment IT Maturity 2026\\\",\\\"description\\\":\\\"Self Assessment IT Maturity Tahun 2026 dengan framework COBIT 2019\\\",\\\"company_id\\\":4,\\\"assessment_type\\\":\\\"periodic\\\",\\\"scope_type\\\":\\\"tailored\\\",\\\"status\\\":\\\"completed\\\",\\\"maturity_level\\\":null,\\\"assessment_period_start\\\":\\\"2026-01-25T17:00:00.000000Z\\\",\\\"assessment_period_end\\\":\\\"2026-02-25T17:00:00.000000Z\\\",\\\"created_by\\\":4,\\\"progress_percentage\\\":36,\\\"overall_maturity_level\\\":\\\"3.67\\\",\\\"is_encrypted\\\":true,\\\"created_at\\\":\\\"2026-01-25T20:25:38.000000Z\\\",\\\"updated_at\\\":\\\"2026-01-29T13:01:46.000000Z\\\"}\"','\"{\\\"status\\\":\\\"in_progress\\\",\\\"updated_at\\\":\\\"2026-01-29 20:01:57\\\"}\"',0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:01:57'),(190,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:01:58'),(191,1,'updated','Assessment','App\\Models\\Assessment',10,NULL,'\"{\\\"id\\\":10,\\\"code\\\":\\\"ASM-000002\\\",\\\"title\\\":\\\"Self Assessment IT Maturity 2026\\\",\\\"description\\\":\\\"Self Assessment IT Maturity Tahun 2026 dengan framework COBIT 2019\\\",\\\"company_id\\\":4,\\\"assessment_type\\\":\\\"periodic\\\",\\\"scope_type\\\":\\\"tailored\\\",\\\"status\\\":\\\"in_progress\\\",\\\"maturity_level\\\":null,\\\"assessment_period_start\\\":\\\"2026-01-25T17:00:00.000000Z\\\",\\\"assessment_period_end\\\":\\\"2026-02-25T17:00:00.000000Z\\\",\\\"created_by\\\":4,\\\"progress_percentage\\\":36,\\\"overall_maturity_level\\\":\\\"3.67\\\",\\\"is_encrypted\\\":true,\\\"created_at\\\":\\\"2026-01-25T20:25:38.000000Z\\\",\\\"updated_at\\\":\\\"2026-01-29T13:01:57.000000Z\\\"}\"','\"{\\\"status\\\":\\\"completed\\\",\\\"updated_at\\\":\\\"2026-01-29 20:02:00\\\"}\"',0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:02:00'),(192,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:02:01'),(193,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:02:02'),(194,1,'updated','Assessment','App\\Models\\Assessment',10,NULL,'\"{\\\"id\\\":10,\\\"code\\\":\\\"ASM-000002\\\",\\\"title\\\":\\\"Self Assessment IT Maturity 2026\\\",\\\"description\\\":\\\"Self Assessment IT Maturity Tahun 2026 dengan framework COBIT 2019\\\",\\\"company_id\\\":4,\\\"assessment_type\\\":\\\"periodic\\\",\\\"scope_type\\\":\\\"tailored\\\",\\\"status\\\":\\\"completed\\\",\\\"maturity_level\\\":null,\\\"assessment_period_start\\\":\\\"2026-01-25T17:00:00.000000Z\\\",\\\"assessment_period_end\\\":\\\"2026-02-25T17:00:00.000000Z\\\",\\\"created_by\\\":4,\\\"progress_percentage\\\":36,\\\"overall_maturity_level\\\":\\\"3.67\\\",\\\"is_encrypted\\\":true,\\\"created_at\\\":\\\"2026-01-25T20:25:38.000000Z\\\",\\\"updated_at\\\":\\\"2026-01-29T13:02:00.000000Z\\\"}\"','\"{\\\"progress_percentage\\\":45,\\\"updated_at\\\":\\\"2026-01-29 20:02:45\\\"}\"',0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:02:45'),(195,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:14:18'),(196,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:14:19'),(197,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:14:19'),(198,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:14:20'),(199,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:14:39'),(200,1,'updated','Assessment','App\\Models\\Assessment',10,NULL,'\"{\\\"id\\\":10,\\\"code\\\":\\\"ASM-000002\\\",\\\"title\\\":\\\"Self Assessment IT Maturity 2026\\\",\\\"description\\\":\\\"Self Assessment IT Maturity Tahun 2026 dengan framework COBIT 2019\\\",\\\"company_id\\\":4,\\\"assessment_type\\\":\\\"periodic\\\",\\\"scope_type\\\":\\\"tailored\\\",\\\"status\\\":\\\"completed\\\",\\\"maturity_level\\\":null,\\\"assessment_period_start\\\":\\\"2026-01-25T17:00:00.000000Z\\\",\\\"assessment_period_end\\\":\\\"2026-02-25T17:00:00.000000Z\\\",\\\"created_by\\\":4,\\\"progress_percentage\\\":45,\\\"overall_maturity_level\\\":\\\"3.50\\\",\\\"is_encrypted\\\":true,\\\"created_at\\\":\\\"2026-01-25T20:25:38.000000Z\\\",\\\"updated_at\\\":\\\"2026-01-29T13:02:45.000000Z\\\"}\"','\"{\\\"status\\\":\\\"in_progress\\\",\\\"updated_at\\\":\\\"2026-01-29 20:14:42\\\"}\"',0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:14:42'),(201,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:14:43'),(202,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','yMHnGKQQHBuFkdcxHaIVgbu0y3HI120IfpZq8APP',1,'2026-01-29 13:14:45'),(203,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','ziNqHD30kdaBgw55Omyru7npS0bZOrhHdGynlMOh',1,'2026-01-30 02:01:23'),(204,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','ziNqHD30kdaBgw55Omyru7npS0bZOrhHdGynlMOh',1,'2026-01-30 02:43:33'),(205,1,'viewed','Assessment',NULL,10,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','8UraWvVkqJcXOqxTPEbQBhhCrQn0b83Fpzieuoey',1,'2026-02-02 04:43:03'),(206,1,'viewed','Dashboard',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','lyq8ly6cAmJEioIRcwANH6n3MyspYljIlOLO2Y4D',1,'2026-04-09 04:08:16'),(207,1,'viewed','Assessment',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','lyq8ly6cAmJEioIRcwANH6n3MyspYljIlOLO2Y4D',1,'2026-04-09 04:08:21'),(208,1,'viewed','Profile',NULL,NULL,NULL,NULL,NULL,0,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','lyq8ly6cAmJEioIRcwANH6n3MyspYljIlOLO2Y4D',1,'2026-04-09 04:08:30');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('cobit-2019-assessment-cache-spatie.permission.cache','a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:53:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"user.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:9:\"user.read\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:11:\"user.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:11:\"user.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:19:\"user.reset_password\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:11:\"role.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:17:\"permission.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:17:\"assessment.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:15:\"assessment.read\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:4;i:2;i:5;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:17:\"assessment.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:17:\"assessment.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:17:\"assessment.review\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:18:\"assessment.approve\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:18:\"assessment.archive\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:26:\"assessment.assign_assessor\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:20:\"design_factor.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:18:\"design_factor.read\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:21:\"gamo_objective.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:19:\"gamo_objective.read\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:15:\"question.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:13:\"question.read\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:15:\"question.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:15:\"question.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:20:\"question.bulk_import\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:13:\"answer.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:4;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:11:\"answer.read\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:4;i:2;i:5;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:13:\"answer.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:4;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:13:\"answer.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:11:\"answer.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:15:\"evidence.upload\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:4;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:15:\"evidence.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:15:\"report.generate\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:13:\"report.export\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:11:\"report.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:4;i:2;i:5;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:13:\"report.custom\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:10:\"audit.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:12:\"audit.export\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:16:\"system.configure\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:13:\"system.backup\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:14:\"system.restore\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:22:\"encryption.manage_keys\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:41;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:18:\"security.configure\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:14:\"company.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:14:\"dashboard.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:4;i:2;i:5;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:10:\"2fa.bypass\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:16:\"create questions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:16:\"update questions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:16:\"delete questions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:48;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:14:\"view questions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:49;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:16:\"view assessments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:6;}}i:50;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:20:\"view own assessments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:6;}}i:51;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:14:\"view companies\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:6;}}i:52;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:15:\"upload evidence\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:6;}}}s:5:\"roles\";a:4:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"Super Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:8:\"Assessor\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:6:\"Viewer\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:5:\"Asesi\";s:1:\"c\";s:3:\"web\";}}}',1775794096);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
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
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `size` enum('startup','sme','enterprise') NOT NULL DEFAULT 'sme',
  `established_year` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (4,'Perum Jasa Tirta II','Jl. Lurah Kawi No 1 Jatiluhur Purwakarta','085281800035','pjt2@jasatirta2.co.id','Jasa','enterprise',1960,'2025-12-15 18:33:23','2025-12-15 18:33:23');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `design_factors`
--

DROP TABLE IF EXISTS `design_factors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `design_factors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `factor_order` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `design_factors_code_unique` (`code`),
  KEY `design_factors_code_index` (`code`),
  KEY `design_factors_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `design_factors`
--

LOCK TABLES `design_factors` WRITE;
/*!40000 ALTER TABLE `design_factors` DISABLE KEYS */;
INSERT INTO `design_factors` VALUES (1,'ES','Enterprise Strategy','Visi, misi, dan strategi bisnis organisasi',1,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(2,'EG','Enterprise Goals','Tujuan perusahaan yang aligned dengan strategi',2,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(3,'RP','Risk Profile','Risk appetite dan tolerance level organisasi',3,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(4,'ITI','I&T Related Issues','Isu-isu yang berkaitan dengan IT',4,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(5,'TL','Threat Landscape','Ancaman internal dan eksternal',5,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(6,'CR','Compliance Requirements','Requirement regulasi dan compliance',6,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(7,'RIT','Role of IT','Peran IT dalam organisasi (Support/Defense/Factory/Strategic)',7,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(8,'SM','Sourcing Model for IT','Model sumber IT (Insourced/Outsourced/Co-sourced)',8,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(9,'IM','IT Implementation Methods','Metode implementasi IT (Waterfall/Agile/Hybrid/DevOps)',9,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(10,'TA','Technology Strategy Adoption','Strategi adopsi teknologi (Legacy/Steady/Progressive/Innovative)',10,1,'2025-12-09 15:12:08','2025-12-09 08:12:08');
/*!40000 ALTER TABLE `design_factors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `encryption_keys_log`
--

DROP TABLE IF EXISTS `encryption_keys_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encryption_keys_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key_version` int(11) NOT NULL,
  `key_algorithm` varchar(100) DEFAULT NULL,
  `key_size` int(11) DEFAULT NULL,
  `rotation_date` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive','compromised') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `encryption_keys_log`
--

LOCK TABLES `encryption_keys_log` WRITE;
/*!40000 ALTER TABLE `encryption_keys_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `encryption_keys_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evidence_access_logs`
--

DROP TABLE IF EXISTS `evidence_access_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `evidence_access_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_answer_id` bigint(20) unsigned NOT NULL,
  `evidence_version_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `action` enum('view','download','upload','delete','restore') NOT NULL DEFAULT 'view',
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `accessed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `evidence_access_logs_evidence_version_id_foreign` (`evidence_version_id`),
  KEY `evidence_access_logs_assessment_answer_id_accessed_at_index` (`assessment_answer_id`,`accessed_at`),
  KEY `evidence_access_logs_user_id_accessed_at_index` (`user_id`,`accessed_at`),
  CONSTRAINT `evidence_access_logs_assessment_answer_id_foreign` FOREIGN KEY (`assessment_answer_id`) REFERENCES `assessment_answers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evidence_access_logs_evidence_version_id_foreign` FOREIGN KEY (`evidence_version_id`) REFERENCES `evidence_versions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evidence_access_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evidence_access_logs`
--

LOCK TABLES `evidence_access_logs` WRITE;
/*!40000 ALTER TABLE `evidence_access_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `evidence_access_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evidence_versions`
--

DROP TABLE IF EXISTS `evidence_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `evidence_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_answer_id` bigint(20) unsigned NOT NULL,
  `version_number` int(11) NOT NULL DEFAULT 1,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `file_hash` varchar(64) DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 1,
  `version_notes` text DEFAULT NULL,
  `uploaded_by` bigint(20) unsigned NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evidence_versions_uploaded_by_foreign` (`uploaded_by`),
  KEY `evidence_versions_assessment_answer_id_version_number_index` (`assessment_answer_id`,`version_number`),
  KEY `evidence_versions_uploaded_at_index` (`uploaded_at`),
  CONSTRAINT `evidence_versions_assessment_answer_id_foreign` FOREIGN KEY (`assessment_answer_id`) REFERENCES `assessment_answers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evidence_versions_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evidence_versions`
--

LOCK TABLES `evidence_versions` WRITE;
/*!40000 ALTER TABLE `evidence_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `evidence_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
-- Table structure for table `gamo_capability_definitions`
--

DROP TABLE IF EXISTS `gamo_capability_definitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gamo_capability_definitions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gamo_objective_id` bigint(20) unsigned NOT NULL,
  `level` int(11) NOT NULL,
  `level_name` varchar(100) DEFAULT NULL,
  `compliance_score` decimal(5,2) DEFAULT NULL,
  `weight` int(11) NOT NULL DEFAULT 1,
  `min_questions` int(11) DEFAULT NULL,
  `max_questions` int(11) DEFAULT NULL,
  `required_evidence_count` int(11) DEFAULT NULL,
  `required_compliance_percentage` int(11) DEFAULT NULL,
  `additional_requirements` longtext DEFAULT NULL,
  `guidance_text` longtext DEFAULT NULL,
  `examples` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `gcd_gamo_level_unique` (`gamo_objective_id`,`level`),
  CONSTRAINT `gamo_capability_definitions_gamo_objective_id_foreign` FOREIGN KEY (`gamo_objective_id`) REFERENCES `gamo_objectives` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gamo_capability_definitions`
--

LOCK TABLES `gamo_capability_definitions` WRITE;
/*!40000 ALTER TABLE `gamo_capability_definitions` DISABLE KEYS */;
/*!40000 ALTER TABLE `gamo_capability_definitions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gamo_objectives`
--

DROP TABLE IF EXISTS `gamo_objectives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gamo_objectives` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_id` varchar(255) DEFAULT NULL,
  `description` longtext NOT NULL,
  `description_id` longtext DEFAULT NULL,
  `category` enum('EDM','APO','BAI','DSS','MEA') NOT NULL,
  `objective_order` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gamo_objectives_code_unique` (`code`),
  KEY `gamo_objectives_code_index` (`code`),
  KEY `gamo_objectives_category_index` (`category`),
  KEY `gamo_objectives_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gamo_objectives`
--

LOCK TABLES `gamo_objectives` WRITE;
/*!40000 ALTER TABLE `gamo_objectives` DISABLE KEYS */;
INSERT INTO `gamo_objectives` VALUES (1,'EDM01','Evaluate, Direct and Monitor the Set of Enterprise Goals','Evaluasi, Arahkan, dan Pantau Pemenuhan Tujuan Perusahaan','Ensure that business goals and objectives are understood, achieved, and monitored in alignment with IT strategy','Memastikan tujuan dan objektif bisnis dipahami, dicapai, dan dipantau sesuai dengan strategi IT','EDM',1,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(2,'EDM02','Evaluate, Direct and Monitor IT-Related Business Risk','Evaluasi, Arahkan, dan Pantau Risiko Bisnis Terkait IT','Manage and monitor IT-related business risks and ensure proper risk mitigation strategies are in place','Kelola dan pantau risiko bisnis terkait IT serta pastikan strategi mitigasi risiko yang tepat diterapkan','EDM',2,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(3,'EDM03','Evaluate, Direct and Monitor IT Compliance','Evaluasi, Arahkan, dan Pantau Kepatuhan IT','Ensure IT operations are compliant with laws, regulations, and contractual obligations','Pastikan operasi IT mematuhi hukum, regulasi, dan kewajiban kontraktual','EDM',3,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(4,'EDM04','Evaluate, Direct and Monitor IT Governance','Evaluasi, Arahkan, dan Pantau Governance IT','Establish and monitor IT governance framework to ensure effective management and oversight','Tetapkan dan pantau kerangka kerja governance IT untuk memastikan manajemen dan pengawasan yang efektif','EDM',4,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(5,'EDM05','Evaluate, Direct and Monitor IT Investments','Evaluasi, Arahkan, dan Pantau Investasi IT','Manage and optimize IT investments to ensure proper allocation and value realization','Kelola dan optimalkan investasi IT untuk memastikan alokasi yang tepat dan realisasi nilai','EDM',5,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(6,'APO01','Manage IT Management Framework','Kelola Kerangka Kerja Manajemen IT','Establish and maintain an integrated IT management framework aligned with business objectives','Tetapkan dan pertahankan kerangka kerja manajemen IT yang terintegrasi sesuai dengan tujuan bisnis','APO',1,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(7,'APO02','Manage Strategy','Kelola Strategi','Develop and maintain IT strategy aligned with business strategy and stakeholder needs','Kembangkan dan pertahankan strategi IT yang selaras dengan strategi bisnis dan kebutuhan pemangku kepentingan','APO',2,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(8,'APO03','Manage Enterprise Architecture','Kelola Arsitektur Enterprise','Define and maintain enterprise architecture to guide IT decision-making and transformation','Tentukan dan pertahankan arsitektur enterprise untuk membimbing pengambilan keputusan IT dan transformasi','APO',3,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(9,'APO04','Manage Innovation','Kelola Inovasi','Identify and evaluate IT innovations to maintain competitive advantage','Identifikasi dan evaluasi inovasi IT untuk mempertahankan keunggulan kompetitif','APO',4,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(10,'APO05','Manage Portfolio','Kelola Portfolio','Manage IT portfolio to ensure optimal allocation of resources and value delivery','Kelola portfolio IT untuk memastikan alokasi sumber daya yang optimal dan pengiriman nilai','APO',5,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(11,'APO06','Manage Budget and Costs','Kelola Budget dan Biaya','Plan, manage, and control IT budget and costs effectively','Rencanakan, kelola, dan kontrol budget dan biaya IT secara efektif','APO',6,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(12,'APO07','Manage Human Resources','Kelola Sumber Daya Manusia','Ensure IT department has appropriate skills, competencies, and organizational structure','Pastikan departemen IT memiliki keterampilan, kompetensi, dan struktur organisasi yang tepat','APO',7,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(13,'BAI01','Manage Programmes and Projects','Kelola Program dan Proyek','Plan and execute IT programmes and projects according to approved plans and governance','Rencanakan dan eksekusi program dan proyek IT sesuai rencana dan governance yang disetujui','BAI',1,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(14,'BAI02','Manage Requirements Definition','Kelola Definisi Requirement','Gather, document, and manage IT requirements from business stakeholders','Kumpulkan, dokumentasikan, dan kelola requirement IT dari pemangku kepentingan bisnis','BAI',2,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(15,'BAI03','Manage Solutions Identification and Build','Kelola Identifikasi dan Pembangunan Solusi','Identify, design, build, and implement IT solutions to address business requirements','Identifikasi, desain, bangun, dan implementasikan solusi IT untuk mengatasi requirement bisnis','BAI',3,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(16,'BAI04','Manage Availability and Capacity','Kelola Ketersediaan dan Kapasitas','Plan and manage IT availability and capacity to meet current and future business demands','Rencanakan dan kelola ketersediaan dan kapasitas IT untuk memenuhi permintaan bisnis saat ini dan masa depan','BAI',4,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(17,'DSS01','Manage Operations','Kelola Operasi','Execute and manage IT operations to ensure reliable and efficient delivery of IT services','Eksekusi dan kelola operasi IT untuk memastikan pengiriman layanan IT yang andal dan efisien','DSS',1,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(18,'DSS02','Manage Service Requests and Incidents','Kelola Permintaan Layanan dan Insiden','Process and manage IT service requests and incidents to minimize disruption','Proses dan kelola permintaan layanan IT dan insiden untuk meminimalkan gangguan','DSS',2,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(19,'DSS03','Manage Problems','Kelola Masalah','Identify, analyze, and resolve problems to prevent service disruptions','Identifikasi, analisis, dan selesaikan masalah untuk mencegah gangguan layanan','DSS',3,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(20,'DSS04','Manage Continuity','Kelola Kontinuitas','Plan and ensure business continuity of IT services during disruptions','Rencanakan dan pastikan kontinuitas bisnis layanan IT selama gangguan','DSS',4,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(21,'DSS05','Manage Security Services','Kelola Layanan Keamanan','Implement and maintain security controls to protect IT assets and data','Implementasikan dan pertahankan kontrol keamanan untuk melindungi aset dan data IT','DSS',5,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(22,'MEA01','Monitor, Evaluate and Assess Performance and Conformance','Pantau, Evaluasi, dan Asesmen Kinerja dan Kesesuaian','Monitor IT performance and conformance to ensure objectives are being met','Pantau kinerja IT dan kesesuaian untuk memastikan tujuan tercapai','MEA',1,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(23,'MEA02','Monitor, Evaluate and Assess the System of Internal Control','Pantau, Evaluasi, dan Asesmen Sistem Pengendalian Internal','Evaluate the effectiveness of IT internal control systems','Evaluasi efektivitas sistem pengendalian internal IT','MEA',2,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(24,'MEA03','Monitor, Evaluate and Assess Compliance with External Requirements','Pantau, Evaluasi, dan Asesmen Kepatuhan Terhadap Requirement Eksternal','Monitor IT compliance with external laws, regulations, and standards','Pantau kepatuhan IT terhadap hukum, regulasi, dan standar eksternal','MEA',3,1,'2025-12-09 15:12:08','2025-12-09 08:12:08'),(25,'APO08','Managed Relationships',NULL,'Managed Relationships',NULL,'APO',8,1,'2026-01-08 01:15:23','2026-01-08 01:15:23'),(26,'APO09','Managed Service Agreements',NULL,'Managed Service Agreements',NULL,'APO',9,1,'2026-01-08 01:15:23','2026-01-08 01:15:23'),(27,'APO10','Managed Vendors',NULL,'Managed Vendors',NULL,'APO',10,1,'2026-01-08 01:15:23','2026-01-08 01:15:23'),(28,'APO11','Managed Quality',NULL,'Managed Quality',NULL,'APO',11,1,'2026-01-08 01:15:23','2026-01-08 01:15:23'),(29,'APO12','Managed Risk',NULL,'Managed Risk',NULL,'APO',12,1,'2026-01-08 01:15:23','2026-01-08 01:15:23'),(30,'APO13','Managed Security',NULL,'Managed Security',NULL,'APO',13,1,'2026-01-08 01:15:23','2026-01-08 01:15:23'),(31,'APO14','Managed Data',NULL,'Managed Data',NULL,'APO',14,1,'2026-01-08 01:15:23','2026-01-08 01:15:23'),(32,'BAI05','Managed Organizational Change',NULL,'Managed Organizational Change',NULL,'BAI',5,1,'2026-01-08 01:15:23','2026-01-08 01:15:23'),(33,'BAI06','Managed IT Changes',NULL,'Managed IT Changes',NULL,'BAI',6,1,'2026-01-08 01:15:23','2026-01-08 01:15:23'),(34,'BAI07','Managed IT Change Acceptance and Transitioning',NULL,'Managed IT Change Acceptance and Transitioning',NULL,'BAI',7,1,'2026-01-08 01:15:23','2026-01-08 01:15:23'),(35,'BAI08','Managed Knowledge',NULL,'Managed Knowledge',NULL,'BAI',8,1,'2026-01-08 01:15:23','2026-01-08 01:15:23'),(36,'BAI09','Managed Assets',NULL,'Managed Assets',NULL,'BAI',9,1,'2026-01-08 01:15:23','2026-01-08 01:15:23'),(37,'BAI10','Managed Configuration',NULL,'Managed Configuration',NULL,'BAI',10,1,'2026-01-08 01:15:23','2026-01-08 01:15:23'),(38,'BAI11','Managed Projects',NULL,'Managed Projects',NULL,'BAI',11,1,'2026-01-08 01:15:23','2026-01-08 01:15:23'),(39,'DSS06','Managed Business Process Controls',NULL,'Managed Business Process Controls',NULL,'DSS',6,1,'2026-01-08 01:15:23','2026-01-08 01:15:23'),(40,'MEA04','Managed Assurance',NULL,'Managed Assurance',NULL,'MEA',4,1,'2026-01-08 01:15:23','2026-01-26 16:22:38');
/*!40000 ALTER TABLE `gamo_objectives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gamo_questions`
--

DROP TABLE IF EXISTS `gamo_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gamo_questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `gamo_objective_id` bigint(20) unsigned NOT NULL,
  `question_text` longtext NOT NULL,
  `document_requirements` text DEFAULT NULL,
  `guidance` text DEFAULT NULL,
  `evidence_requirement` text DEFAULT NULL,
  `question_type` enum('text','rating','multiple_choice','yes_no','evidence') NOT NULL DEFAULT 'text',
  `maturity_level` int(11) NOT NULL DEFAULT 1,
  `required` tinyint(1) NOT NULL DEFAULT 1,
  `question_order` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gamo_questions_code_unique` (`code`),
  KEY `gamo_questions_gamo_objective_id_index` (`gamo_objective_id`),
  KEY `gamo_questions_is_active_index` (`is_active`),
  CONSTRAINT `gamo_questions_gamo_objective_id_foreign` FOREIGN KEY (`gamo_objective_id`) REFERENCES `gamo_objectives` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1275 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gamo_questions`
--

LOCK TABLES `gamo_questions` WRITE;
/*!40000 ALTER TABLE `gamo_questions` DISABLE KEYS */;
INSERT INTO `gamo_questions` VALUES (1152,'EDM01.02.003',1,'Assess internal and external factors influencing governance design. | Menilai faktor internal dan eksternal yang memengaruhi desain tata kelola.','- Risk Assessment Report\\n- Compliance Matrix\\n- Governance Framework Documentation\\n- Board Meeting Minutes','Consider the following when assessing governance factors: 1) External regulations and compliance requirements, 2) Internal policies and standards, 3) Stakeholder expectations and business objectives',NULL,'rating',2,1,1,1,'2026-01-19 19:19:31','2026-01-26 20:06:08'),(1153,'EDM01.01.A2',1,'Define the role of I&T in supporting enterprise objectives. | Menetapkan peran I&T dalam mendukung tujuan perusahaan.',NULL,NULL,NULL,'rating',3,1,2,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1154,'EDM01.05.001',1,'Incorporate legal, regulatory and contractual requirements. | Mengintegrasikan persyaratan hukum, regulasi, dan kontrak.',NULL,NULL,NULL,'rating',5,1,3,1,'2026-01-19 19:19:31','2026-01-22 00:14:30'),(1156,'EDM02.01.A2',2,'Direct benefit realization practices. | Mengarahkan praktik realisasi manfaat.',NULL,NULL,NULL,'rating',3,1,5,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1157,'EDM02.01.A3',2,'Monitor achievement of expected benefits. | Memantau pencapaian manfaat yang diharapkan.',NULL,NULL,NULL,'rating',5,1,6,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1159,'EDM03.01.A2',3,'Direct risk management approach. | Mengarahkan pendekatan manajemen risiko.',NULL,NULL,NULL,'rating',3,1,8,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1160,'EDM03.01.A3',3,'Monitor risk exposure and responses. | Memantau eksposur dan respons risiko.',NULL,NULL,NULL,'rating',5,1,9,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1162,'EDM04.01.A2',4,'Direct optimal use of I&T resources. | Mengarahkan penggunaan optimal sumber daya I&T.',NULL,NULL,NULL,'rating',3,1,11,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1163,'EDM04.01.A3',4,'Monitor resource utilization and capacity. | Memantau pemanfaatan dan kapasitas sumber daya.',NULL,NULL,NULL,'rating',5,1,12,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1165,'EDM05.01.A2',5,'Direct stakeholder communication and reporting. | Mengarahkan komunikasi dan pelaporan pemangku kepentingan.',NULL,NULL,NULL,'rating',3,1,14,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1166,'EDM05.01.A3',5,'Monitor stakeholder satisfaction. | Memantau kepuasan pemangku kepentingan.',NULL,NULL,NULL,'rating',5,1,15,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1168,'APO01.01.A2',6,'Implement policies, standards and procedures. | Menerapkan kebijakan, standar, dan prosedur.',NULL,NULL,NULL,'rating',3,1,17,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1169,'APO01.01.A3',6,'Monitor effectiveness and compliance. | Memantau efektivitas dan kepatuhan.',NULL,NULL,NULL,'rating',5,1,18,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1171,'APO02.01.A2',7,'Implement policies, standards and procedures. | Menerapkan kebijakan, standar, dan prosedur.',NULL,NULL,NULL,'rating',3,1,20,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1172,'APO02.01.A3',7,'Monitor effectiveness and compliance. | Memantau efektivitas dan kepatuhan.',NULL,NULL,NULL,'rating',5,1,21,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1174,'APO03.01.A2',8,'Implement policies, standards and procedures. | Menerapkan kebijakan, standar, dan prosedur.',NULL,NULL,NULL,'rating',3,1,23,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1175,'APO03.01.A3',8,'Monitor effectiveness and compliance. | Memantau efektivitas dan kepatuhan.',NULL,NULL,NULL,'rating',5,1,24,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1177,'APO04.01.A2',9,'Implement policies, standards and procedures. | Menerapkan kebijakan, standar, dan prosedur.',NULL,NULL,NULL,'rating',3,1,26,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1178,'APO04.01.A3',9,'Monitor effectiveness and compliance. | Memantau efektivitas dan kepatuhan.',NULL,NULL,NULL,'rating',5,1,27,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1180,'APO05.01.A2',10,'Implement policies, standards and procedures. | Menerapkan kebijakan, standar, dan prosedur.',NULL,NULL,NULL,'rating',3,1,29,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1181,'APO05.01.A3',10,'Monitor effectiveness and compliance. | Memantau efektivitas dan kepatuhan.',NULL,NULL,NULL,'rating',5,1,30,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1183,'APO06.01.A2',11,'Implement policies, standards and procedures. | Menerapkan kebijakan, standar, dan prosedur.',NULL,NULL,NULL,'rating',3,1,32,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1184,'APO06.01.A3',11,'Monitor effectiveness and compliance. | Memantau efektivitas dan kepatuhan.',NULL,NULL,NULL,'rating',5,1,33,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1186,'APO07.01.A2',12,'Implement policies, standards and procedures. | Menerapkan kebijakan, standar, dan prosedur.',NULL,NULL,NULL,'rating',3,1,35,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1187,'APO07.01.A3',12,'Monitor effectiveness and compliance. | Memantau efektivitas dan kepatuhan.',NULL,NULL,NULL,'rating',5,1,36,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1189,'APO08.01.A2',25,'Implement policies, standards and procedures. | Menerapkan kebijakan, standar, dan prosedur.',NULL,NULL,NULL,'rating',3,1,38,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1190,'APO08.01.A3',25,'Monitor effectiveness and compliance. | Memantau efektivitas dan kepatuhan.',NULL,NULL,NULL,'rating',5,1,39,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1192,'APO09.01.A2',26,'Implement policies, standards and procedures. | Menerapkan kebijakan, standar, dan prosedur.',NULL,NULL,NULL,'rating',3,1,41,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1193,'APO09.01.A3',26,'Monitor effectiveness and compliance. | Memantau efektivitas dan kepatuhan.',NULL,NULL,NULL,'rating',5,1,42,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1195,'APO10.01.A2',27,'Implement policies, standards and procedures. | Menerapkan kebijakan, standar, dan prosedur.',NULL,NULL,NULL,'rating',3,1,44,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1196,'APO10.01.A3',27,'Monitor effectiveness and compliance. | Memantau efektivitas dan kepatuhan.',NULL,NULL,NULL,'rating',5,1,45,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1198,'APO11.01.A2',28,'Implement policies, standards and procedures. | Menerapkan kebijakan, standar, dan prosedur.',NULL,NULL,NULL,'rating',3,1,47,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1199,'APO11.01.A3',28,'Monitor effectiveness and compliance. | Memantau efektivitas dan kepatuhan.',NULL,NULL,NULL,'rating',5,1,48,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1201,'APO12.01.A2',29,'Implement policies, standards and procedures. | Menerapkan kebijakan, standar, dan prosedur.',NULL,NULL,NULL,'rating',3,1,50,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1202,'APO12.01.A3',29,'Monitor effectiveness and compliance. | Memantau efektivitas dan kepatuhan.',NULL,NULL,NULL,'rating',5,1,51,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1204,'APO13.01.A2',30,'Implement policies, standards and procedures. | Menerapkan kebijakan, standar, dan prosedur.',NULL,NULL,NULL,'rating',3,1,53,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1205,'APO13.01.A3',30,'Monitor effectiveness and compliance. | Memantau efektivitas dan kepatuhan.',NULL,NULL,NULL,'rating',5,1,54,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1207,'APO14.01.A2',31,'Implement policies, standards and procedures. | Menerapkan kebijakan, standar, dan prosedur.',NULL,NULL,NULL,'rating',3,1,56,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1208,'APO14.01.A3',31,'Monitor effectiveness and compliance. | Memantau efektivitas dan kepatuhan.',NULL,NULL,NULL,'rating',5,1,57,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1210,'BAI01.01.A2',13,'Ensure alignment with requirements and standards. | Memastikan keselarasan dengan kebutuhan dan standar.',NULL,NULL,NULL,'rating',3,1,59,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1211,'BAI01.01.A3',13,'Review outcomes and close activities. | Meninjau hasil dan menutup aktivitas.',NULL,NULL,NULL,'rating',5,1,60,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1213,'BAI02.01.A2',14,'Ensure alignment with requirements and standards. | Memastikan keselarasan dengan kebutuhan dan standar.',NULL,NULL,NULL,'rating',3,1,62,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1214,'BAI02.01.A3',14,'Review outcomes and close activities. | Meninjau hasil dan menutup aktivitas.',NULL,NULL,NULL,'rating',5,1,63,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1216,'BAI03.01.A2',15,'Ensure alignment with requirements and standards. | Memastikan keselarasan dengan kebutuhan dan standar.',NULL,NULL,NULL,'rating',3,1,65,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1217,'BAI03.01.A3',15,'Review outcomes and close activities. | Meninjau hasil dan menutup aktivitas.',NULL,NULL,NULL,'rating',5,1,66,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1219,'BAI04.01.A2',16,'Ensure alignment with requirements and standards. | Memastikan keselarasan dengan kebutuhan dan standar.',NULL,NULL,NULL,'rating',3,1,68,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1220,'BAI04.01.A3',16,'Review outcomes and close activities. | Meninjau hasil dan menutup aktivitas.',NULL,NULL,NULL,'rating',5,1,69,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1222,'BAI05.01.A2',32,'Ensure alignment with requirements and standards. | Memastikan keselarasan dengan kebutuhan dan standar.',NULL,NULL,NULL,'rating',3,1,71,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1223,'BAI05.01.A3',32,'Review outcomes and close activities. | Meninjau hasil dan menutup aktivitas.',NULL,NULL,NULL,'rating',5,1,72,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1225,'BAI06.01.A2',33,'Ensure alignment with requirements and standards. | Memastikan keselarasan dengan kebutuhan dan standar.',NULL,NULL,NULL,'rating',3,1,74,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1226,'BAI06.01.A3',33,'Review outcomes and close activities. | Meninjau hasil dan menutup aktivitas.',NULL,NULL,NULL,'rating',5,1,75,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1228,'BAI07.01.A2',34,'Ensure alignment with requirements and standards. | Memastikan keselarasan dengan kebutuhan dan standar.',NULL,NULL,NULL,'rating',3,1,77,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1229,'BAI07.01.A3',34,'Review outcomes and close activities. | Meninjau hasil dan menutup aktivitas.',NULL,NULL,NULL,'rating',5,1,78,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1231,'BAI08.01.A2',35,'Ensure alignment with requirements and standards. | Memastikan keselarasan dengan kebutuhan dan standar.',NULL,NULL,NULL,'rating',3,1,80,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1232,'BAI08.01.A3',35,'Review outcomes and close activities. | Meninjau hasil dan menutup aktivitas.',NULL,NULL,NULL,'rating',5,1,81,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1234,'BAI09.01.A2',36,'Ensure alignment with requirements and standards. | Memastikan keselarasan dengan kebutuhan dan standar.',NULL,NULL,NULL,'rating',3,1,83,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1235,'BAI09.01.A3',36,'Review outcomes and close activities. | Meninjau hasil dan menutup aktivitas.',NULL,NULL,NULL,'rating',5,1,84,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1237,'BAI10.01.A2',37,'Ensure alignment with requirements and standards. | Memastikan keselarasan dengan kebutuhan dan standar.',NULL,NULL,NULL,'rating',3,1,86,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1238,'BAI10.01.A3',37,'Review outcomes and close activities. | Meninjau hasil dan menutup aktivitas.',NULL,NULL,NULL,'rating',5,1,87,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1240,'BAI11.01.A2',38,'Ensure alignment with requirements and standards. | Memastikan keselarasan dengan kebutuhan dan standar.',NULL,NULL,NULL,'rating',3,1,89,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1241,'BAI11.01.A3',38,'Review outcomes and close activities. | Meninjau hasil dan menutup aktivitas.',NULL,NULL,NULL,'rating',5,1,90,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1243,'DSS01.01.A2',17,'Monitor service performance and issues. | Memantau kinerja layanan dan permasalahan.',NULL,NULL,NULL,'rating',3,1,92,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1244,'DSS01.01.A3',17,'Resolve and report outcomes. | Menyelesaikan dan melaporkan hasil.',NULL,NULL,NULL,'rating',5,1,93,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1246,'DSS02.01.A2',18,'Monitor service performance and issues. | Memantau kinerja layanan dan permasalahan.',NULL,NULL,NULL,'rating',3,1,95,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1247,'DSS02.01.A3',18,'Resolve and report outcomes. | Menyelesaikan dan melaporkan hasil.',NULL,NULL,NULL,'rating',5,1,96,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1249,'DSS03.01.A2',19,'Monitor service performance and issues. | Memantau kinerja layanan dan permasalahan.',NULL,NULL,NULL,'rating',3,1,98,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1250,'DSS03.01.A3',19,'Resolve and report outcomes. | Menyelesaikan dan melaporkan hasil.',NULL,NULL,NULL,'rating',5,1,99,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1252,'DSS04.01.A2',20,'Monitor service performance and issues. | Memantau kinerja layanan dan permasalahan.',NULL,NULL,NULL,'rating',3,1,101,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1253,'DSS04.01.A3',20,'Resolve and report outcomes. | Menyelesaikan dan melaporkan hasil.',NULL,NULL,NULL,'rating',5,1,102,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1255,'DSS05.01.A2',21,'Monitor service performance and issues. | Memantau kinerja layanan dan permasalahan.',NULL,NULL,NULL,'rating',3,1,104,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1256,'DSS05.01.A3',21,'Resolve and report outcomes. | Menyelesaikan dan melaporkan hasil.',NULL,NULL,NULL,'rating',5,1,105,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1258,'DSS06.01.A2',39,'Monitor service performance and issues. | Memantau kinerja layanan dan permasalahan.',NULL,NULL,NULL,'rating',3,1,107,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1259,'DSS06.01.A3',39,'Resolve and report outcomes. | Menyelesaikan dan melaporkan hasil.',NULL,NULL,NULL,'rating',5,1,108,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1261,'MEA01.01.A2',22,'Perform monitoring and assessments. | Melaksanakan pemantauan dan penilaian.',NULL,NULL,NULL,'rating',3,1,110,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1262,'MEA01.01.A3',22,'Report results and follow up improvements. | Melaporkan hasil dan menindaklanjuti perbaikan.',NULL,NULL,NULL,'rating',5,1,111,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1264,'MEA02.01.A2',23,'Perform monitoring and assessments. | Melaksanakan pemantauan dan penilaian.',NULL,NULL,NULL,'rating',3,1,113,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1265,'MEA02.01.A3',23,'Report results and follow up improvements. | Melaporkan hasil dan menindaklanjuti perbaikan.',NULL,NULL,NULL,'rating',5,1,114,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1267,'MEA03.01.A2',24,'Perform monitoring and assessments. | Melaksanakan pemantauan dan penilaian.',NULL,NULL,NULL,'rating',3,1,116,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1268,'MEA03.01.A3',24,'Report results and follow up improvements. | Melaporkan hasil dan menindaklanjuti perbaikan.',NULL,NULL,NULL,'rating',5,1,117,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1270,'MEA04.01.A2',40,'Perform monitoring and assessments. | Melaksanakan pemantauan dan penilaian.',NULL,NULL,NULL,'rating',3,1,119,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1271,'MEA04.01.A3',40,'Report results and follow up improvements. | Melaporkan hasil dan menindaklanjuti perbaikan.',NULL,NULL,NULL,'rating',5,1,120,1,'2026-01-19 19:19:31','2026-01-19 19:19:31'),(1272,'EDM01.02.001',1,'I will coming | Aku akan datang',NULL,NULL,NULL,'rating',2,1,NULL,1,'2026-01-21 21:51:34','2026-01-25 02:49:27'),(1273,'EDM01.04.001',1,'The Good Doctor | Si Doktor yang baik hati',NULL,NULL,NULL,'rating',4,1,NULL,1,'2026-01-21 23:23:20','2026-01-21 23:23:20'),(1274,'EDM01.02.002',1,'Try is coba | Coba adalah try',NULL,NULL,NULL,'rating',2,1,NULL,1,'2026-01-21 23:29:43','2026-01-21 23:54:02');
/*!40000 ALTER TABLE `gamo_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gamo_scores`
--

DROP TABLE IF EXISTS `gamo_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gamo_scores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint(20) unsigned NOT NULL,
  `gamo_objective_id` bigint(20) unsigned NOT NULL,
  `current_maturity_level` decimal(3,2) NOT NULL DEFAULT 0.00,
  `target_maturity_level` decimal(3,2) NOT NULL DEFAULT 3.00,
  `capability_score` decimal(5,2) DEFAULT NULL,
  `capability_level` decimal(3,2) DEFAULT NULL,
  `percentage_complete` int(11) NOT NULL DEFAULT 0,
  `status` enum('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gs_assessment_gamo_unique` (`assessment_id`,`gamo_objective_id`),
  KEY `gamo_scores_gamo_objective_id_foreign` (`gamo_objective_id`),
  KEY `gamo_scores_current_maturity_level_index` (`current_maturity_level`),
  CONSTRAINT `gamo_scores_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gamo_scores_gamo_objective_id_foreign` FOREIGN KEY (`gamo_objective_id`) REFERENCES `gamo_objectives` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gamo_scores`
--

LOCK TABLES `gamo_scores` WRITE;
/*!40000 ALTER TABLE `gamo_scores` DISABLE KEYS */;
INSERT INTO `gamo_scores` VALUES (1,10,1,2.89,4.00,0.95,5.00,100,'completed','2026-01-27 07:06:04','2026-01-29 13:01:46'),(2,10,3,2.33,4.00,0.67,3.00,100,'completed','2026-01-27 23:22:53','2026-01-27 23:25:23'),(3,10,18,1.50,3.00,0.50,3.00,100,'completed','2026-01-27 23:27:04','2026-01-27 23:27:04'),(4,10,17,3.00,4.00,1.00,3.00,50,'in_progress','2026-01-29 13:02:45','2026-01-29 13:02:45');
/*!40000 ALTER TABLE `gamo_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
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
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `status` enum('SUCCESS','FAILED') NOT NULL DEFAULT 'FAILED',
  `failure_reason` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `login_attempts_user_id_foreign` (`user_id`),
  KEY `login_attempts_email_ip_address_index` (`email`,`ip_address`),
  KEY `login_attempts_attempted_at_index` (`attempted_at`),
  CONSTRAINT `login_attempts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_12_09_110447_create_permission_tables',1),(5,'2025_12_09_114353_create_activity_log_table',1),(6,'2025_12_09_114354_add_event_column_to_activity_log_table',1),(7,'2025_12_09_114355_add_batch_uuid_column_to_activity_log_table',1),(8,'2025_12_09_114833_create_personal_access_tokens_table',1),(9,'2025_12_09_200000_create_cobit_assessment_schema',1),(10,'2025_12_10_022158_add_maturity_level_to_assessments_table',2),(11,'2025_12_15_035517_add_company_and_is_active_to_users_table',3),(12,'2025_12_15_035525_add_company_and_is_active_to_users_table',3),(13,'2025_12_15_232410_create_assessment_team_members_table',4),(14,'2025_12_16_021011_add_profile_fields_to_users_table',5),(16,'2026_01_07_000001_add_evidence_versioning',6),(17,'2026_01_07_083704_add_target_maturity_level_to_assessment_gamo_selections_table',7),(18,'2026_01_08_040927_create_assessment_evidence_table',8),(19,'2026_01_08_040944_create_assessment_audit_logs_table',8),(20,'2026_01_08_041133_create_assessment_notes_table',8),(21,'2026_01_08_041151_add_columns_to_assessment_answers_table',8),(22,'2026_01_08_041214_add_document_requirements_to_gamo_questions_table',8),(23,'2026_01_25_131818_create_assessment_ofis_table',9),(24,'2026_01_27_160735_update_assessment_status_enum',10),(25,'2026_01_27_160824_update_assessment_status_values',10),(26,'2026_01_28_070554_create_notifications_table',11),(27,'2026_01_28_071159_create_notifications_table',12);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
INSERT INTO `model_has_permissions` VALUES (46,'App\\Models\\User',1),(47,'App\\Models\\User',1),(48,'App\\Models\\User',1),(49,'App\\Models\\User',1);
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(4,'App\\Models\\User',4),(5,'App\\Models\\User',7),(6,'App\\Models\\User',8);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` text NOT NULL,
  `message` text NOT NULL,
  `assessment_id` bigint(20) unsigned DEFAULT NULL,
  `related_user_id` bigint(20) unsigned DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_assessment_id_foreign` (`assessment_id`),
  KEY `notifications_related_user_id_foreign` (`related_user_id`),
  KEY `notifications_user_id_is_read_index` (`user_id`,`is_read`),
  KEY `notifications_created_at_index` (`created_at`),
  CONSTRAINT `notifications_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_related_user_id_foreign` FOREIGN KEY (`related_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,7,'rating_updated','Penilaian Diperbarui','Super Administrator memperbarui penilaian aktivitas \'I will coming | Aku akan datang\' menjadi L',10,1,'{\"assessment_code\":\"ASM-000002\",\"assessment_title\":\"Self Assessment IT Maturity 2026\",\"activity_id\":1272,\"activity_text\":\"I will coming | Aku akan datang\",\"rating\":\"L\"}',0,NULL,'2026-01-29 07:38:40','2026-01-29 07:38:40'),(2,8,'rating_updated','Penilaian Diperbarui','Super Administrator memperbarui penilaian aktivitas \'I will coming | Aku akan datang\' menjadi L',10,1,'{\"assessment_code\":\"ASM-000002\",\"assessment_title\":\"Self Assessment IT Maturity 2026\",\"activity_id\":1272,\"activity_text\":\"I will coming | Aku akan datang\",\"rating\":\"L\"}',0,NULL,'2026-01-29 07:38:40','2026-01-29 07:38:40'),(3,7,'rating_updated','Penilaian Diperbarui','Super Administrator memperbarui penilaian aktivitas \'Assess internal and external factors influencing governance design. | Menilai faktor internal dan eksternal yang memengaruhi desain tata kelola.\' menjadi F',10,1,'{\"assessment_code\":\"ASM-000002\",\"assessment_title\":\"Self Assessment IT Maturity 2026\",\"activity_id\":1152,\"activity_text\":\"Assess internal and external factors influencing governance design. | Menilai faktor internal dan eksternal yang memengaruhi desain tata kelola.\",\"rating\":\"F\"}',0,NULL,'2026-01-29 07:39:03','2026-01-29 07:39:03'),(4,8,'rating_updated','Penilaian Diperbarui','Super Administrator memperbarui penilaian aktivitas \'Assess internal and external factors influencing governance design. | Menilai faktor internal dan eksternal yang memengaruhi desain tata kelola.\' menjadi F',10,1,'{\"assessment_code\":\"ASM-000002\",\"assessment_title\":\"Self Assessment IT Maturity 2026\",\"activity_id\":1152,\"activity_text\":\"Assess internal and external factors influencing governance design. | Menilai faktor internal dan eksternal yang memengaruhi desain tata kelola.\",\"rating\":\"F\"}',0,NULL,'2026-01-29 07:39:03','2026-01-29 07:39:03'),(5,7,'rating_updated','Penilaian Diperbarui','Super Administrator memperbarui penilaian aktivitas \'Assess internal and external factors influencing governance design. | Menilai faktor internal dan eksternal yang memengaruhi desain tata kelola.\' menjadi L',10,1,'{\"assessment_code\":\"ASM-000002\",\"assessment_title\":\"Self Assessment IT Maturity 2026\",\"activity_id\":1152,\"activity_text\":\"Assess internal and external factors influencing governance design. | Menilai faktor internal dan eksternal yang memengaruhi desain tata kelola.\",\"rating\":\"L\"}',0,NULL,'2026-01-29 13:01:29','2026-01-29 13:01:29'),(6,8,'rating_updated','Penilaian Diperbarui','Super Administrator memperbarui penilaian aktivitas \'Assess internal and external factors influencing governance design. | Menilai faktor internal dan eksternal yang memengaruhi desain tata kelola.\' menjadi L',10,1,'{\"assessment_code\":\"ASM-000002\",\"assessment_title\":\"Self Assessment IT Maturity 2026\",\"activity_id\":1152,\"activity_text\":\"Assess internal and external factors influencing governance design. | Menilai faktor internal dan eksternal yang memengaruhi desain tata kelola.\",\"rating\":\"L\"}',0,NULL,'2026-01-29 13:01:29','2026-01-29 13:01:29'),(7,7,'rating_updated','Penilaian Diperbarui','Super Administrator memperbarui penilaian aktivitas \'Try is coba | Coba adalah try\' menjadi F',10,1,'{\"assessment_code\":\"ASM-000002\",\"assessment_title\":\"Self Assessment IT Maturity 2026\",\"activity_id\":1274,\"activity_text\":\"Try is coba | Coba adalah try\",\"rating\":\"F\"}',0,NULL,'2026-01-29 13:01:35','2026-01-29 13:01:35'),(8,8,'rating_updated','Penilaian Diperbarui','Super Administrator memperbarui penilaian aktivitas \'Try is coba | Coba adalah try\' menjadi F',10,1,'{\"assessment_code\":\"ASM-000002\",\"assessment_title\":\"Self Assessment IT Maturity 2026\",\"activity_id\":1274,\"activity_text\":\"Try is coba | Coba adalah try\",\"rating\":\"F\"}',0,NULL,'2026-01-29 13:01:35','2026-01-29 13:01:35'),(9,7,'rating_updated','Penilaian Diperbarui','Super Administrator memperbarui penilaian aktivitas \'Assess internal and external factors influencing governance design. | Menilai faktor internal dan eksternal yang memengaruhi desain tata kelola.\' menjadi F',10,1,'{\"assessment_code\":\"ASM-000002\",\"assessment_title\":\"Self Assessment IT Maturity 2026\",\"activity_id\":1152,\"activity_text\":\"Assess internal and external factors influencing governance design. | Menilai faktor internal dan eksternal yang memengaruhi desain tata kelola.\",\"rating\":\"F\"}',0,NULL,'2026-01-29 13:01:46','2026-01-29 13:01:46'),(10,8,'rating_updated','Penilaian Diperbarui','Super Administrator memperbarui penilaian aktivitas \'Assess internal and external factors influencing governance design. | Menilai faktor internal dan eksternal yang memengaruhi desain tata kelola.\' menjadi F',10,1,'{\"assessment_code\":\"ASM-000002\",\"assessment_title\":\"Self Assessment IT Maturity 2026\",\"activity_id\":1152,\"activity_text\":\"Assess internal and external factors influencing governance design. | Menilai faktor internal dan eksternal yang memengaruhi desain tata kelola.\",\"rating\":\"F\"}',0,NULL,'2026-01-29 13:01:46','2026-01-29 13:01:46'),(11,7,'rating_updated','Penilaian Diperbarui','Super Administrator memperbarui penilaian aktivitas \'Monitor service performance and issues. | Memantau kinerja layanan dan permasalahan.\' menjadi F',10,1,'{\"assessment_code\":\"ASM-000002\",\"assessment_title\":\"Self Assessment IT Maturity 2026\",\"activity_id\":1243,\"activity_text\":\"Monitor service performance and issues. | Memantau kinerja layanan dan permasalahan.\",\"rating\":\"F\"}',0,NULL,'2026-01-29 13:02:45','2026-01-29 13:02:45'),(12,8,'rating_updated','Penilaian Diperbarui','Super Administrator memperbarui penilaian aktivitas \'Monitor service performance and issues. | Memantau kinerja layanan dan permasalahan.\' menjadi F',10,1,'{\"assessment_code\":\"ASM-000002\",\"assessment_title\":\"Self Assessment IT Maturity 2026\",\"activity_id\":1243,\"activity_text\":\"Monitor service performance and issues. | Memantau kinerja layanan dan permasalahan.\",\"rating\":\"F\"}',0,NULL,'2026-01-29 13:02:45','2026-01-29 13:02:45');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'user.create','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(2,'user.read','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(3,'user.update','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(4,'user.delete','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(5,'user.reset_password','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(6,'role.manage','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(7,'permission.manage','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(8,'assessment.create','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(9,'assessment.read','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(10,'assessment.update','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(11,'assessment.delete','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(12,'assessment.review','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(13,'assessment.approve','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(14,'assessment.archive','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(15,'assessment.assign_assessor','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(16,'design_factor.manage','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(17,'design_factor.read','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(18,'gamo_objective.manage','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(19,'gamo_objective.read','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(20,'question.create','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(21,'question.read','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(22,'question.update','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(23,'question.delete','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(24,'question.bulk_import','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(25,'answer.create','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(26,'answer.read','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(27,'answer.update','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(28,'answer.delete','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(29,'answer.edit','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(30,'evidence.upload','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(31,'evidence.delete','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(32,'report.generate','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(33,'report.export','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(34,'report.view','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(35,'report.custom','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(36,'audit.view','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(37,'audit.export','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(38,'system.configure','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(39,'system.backup','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(40,'system.restore','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(41,'encryption.manage_keys','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(42,'security.configure','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(43,'company.manage','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(44,'dashboard.view','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(45,'2fa.bypass','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(46,'create questions','web','2026-01-21 02:17:17','2026-01-21 02:17:17'),(47,'update questions','web','2026-01-21 02:17:17','2026-01-21 02:17:17'),(48,'delete questions','web','2026-01-21 02:17:17','2026-01-21 02:17:17'),(49,'view questions','web','2026-01-21 02:17:17','2026-01-21 02:17:17'),(50,'view assessments','web','2026-01-26 05:37:02','2026-01-26 05:37:02'),(51,'view own assessments','web','2026-01-26 05:37:02','2026-01-26 05:37:02'),(52,'view companies','web','2026-01-26 05:37:02','2026-01-26 05:37:02'),(53,'upload evidence','web','2026-01-26 05:37:02','2026-01-26 05:37:02');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(9,4),(9,5),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(25,1),(25,4),(26,1),(26,4),(26,5),(27,1),(27,4),(28,1),(29,1),(30,1),(30,4),(31,1),(32,1),(33,1),(34,1),(34,4),(34,5),(35,1),(36,1),(37,1),(38,1),(39,1),(40,1),(41,1),(42,1),(43,1),(44,1),(44,4),(44,5),(45,1),(46,1),(47,1),(48,1),(49,1),(50,6),(51,6),(52,6),(53,6);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Admin','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(4,'Assessor','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(5,'Viewer','web','2025-12-09 08:12:08','2025-12-09 08:12:08'),(6,'Asesi','web','2026-01-26 05:37:02','2026-01-26 05:37:02');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
INSERT INTO `sessions` VALUES ('8UraWvVkqJcXOqxTPEbQBhhCrQn0b83Fpzieuoey',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoibDRkNzVHemt5SUVBdEhxR1JtSmN2cEhNN2R4aXZ3R1p3Y0phOW5FNyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXNzZXNzbWVudHMvMTAiO3M6NToicm91dGUiO3M6MTY6ImFzc2Vzc21lbnRzLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1770007384),('lyq8ly6cAmJEioIRcwANH6n3MyspYljIlOLO2Y4D',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiV1ROdDRPRXNEYU5YZmtQSllUOHU2UmtQaHM4emdZb2VIRG9TM0IyZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9maWxlIjtzOjU6InJvdXRlIjtzOjEzOiJwcm9maWxlLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1775707710),('ziNqHD30kdaBgw55Omyru7npS0bZOrhHdGynlMOh',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoieFRTWXl5b01Ya3dzaUVSY2dFajdVUkg5RmVPd1FRRzQ5N2pWUTBrVyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXNzZXNzbWVudHMvMTAiO3M6NToicm91dGUiO3M6MTY6ImFzc2Vzc21lbnRzLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1769741014);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_tokens`
--

DROP TABLE IF EXISTS `user_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `token_type` enum('access','refresh','api') NOT NULL DEFAULT 'access',
  `token_hash` varchar(255) NOT NULL,
  `device_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`device_info`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `revoked_at` timestamp NULL DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_tokens_token_hash_unique` (`token_hash`),
  KEY `user_tokens_user_id_index` (`user_id`),
  KEY `user_tokens_expires_at_index` (`expires_at`),
  CONSTRAINT `user_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_tokens`
--

LOCK TABLES `user_tokens` WRITE;
/*!40000 ALTER TABLE `user_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `language` varchar(5) NOT NULL DEFAULT 'id',
  `preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`preferences`)),
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_company_id_foreign` (`company_id`),
  CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Administrator','superadmin@assessme.com',NULL,NULL,NULL,'id','{\"timezone\":\"Asia\\/Jakarta\",\"language\":\"id\",\"email_notifications\":\"1\",\"assessment_reminders\":\"1\"}',NULL,'$2y$12$9eoEyRzrDZjDN.09ugUUketi2c2h2xPM/nJ1S.sDJ3XATDZ8fiCh6',NULL,NULL,1,NULL,'2025-12-09 08:12:09','2026-01-27 09:14:19'),(4,'IT Assessor','assessor@assessme.com',NULL,NULL,NULL,'id',NULL,NULL,'$2y$12$9eoEyRzrDZjDN.09ugUUketi2c2h2xPM/nJ1S.sDJ3XATDZ8fiCh6',NULL,NULL,1,NULL,'2025-12-09 08:12:09','2025-12-09 08:12:09'),(7,'Ahmad Choirul Firdaus','ahmadchoirul55@gmail.com',NULL,NULL,NULL,'id',NULL,NULL,'$2y$12$dLy73ZPomMaiTVr2JBdpIeXhsSYYDuQ0a22vJiWDeWNT4YH0XJljm',NULL,4,1,NULL,'2026-01-25 00:43:54','2026-01-25 00:43:54'),(8,'Asesi Aini','aini@gmail.com',NULL,NULL,NULL,'id',NULL,NULL,'$2y$12$ZrnnQNB98WBZ6kjGUn/yW.rawQcIKuDtz51rJ5UMhlgmrLPjJDlm6',NULL,4,1,NULL,'2026-01-26 05:42:47','2026-01-26 23:23:21');
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

-- Dump completed on 2026-04-09 11:12:04
