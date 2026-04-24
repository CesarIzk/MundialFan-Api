-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: mundialfan
-- ------------------------------------------------------
-- Server version	9.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (6,'Noticias Generales','Actualidad diaria y noticias de última hora sobre el Mundial.','2026-04-23 18:51:11','2026-04-23 18:51:11'),(7,'Estadísticas','Datos, récords históricos y tablas de posiciones en tiempo real.','2026-04-23 18:51:11','2026-04-23 18:51:11'),(8,'Sedes y Estadios','Información sobre las ciudades anfitrionas y los recintos deportivos.','2026-04-23 18:51:11','2026-04-23 18:51:11'),(9,'UEFA','Todo sobre las selecciones europeas y su camino al título.','2026-04-23 18:51:11','2026-04-23 18:51:11'),(10,'CONMEBOL','Actualidad de las potencias sudamericanas y sus estrellas.','2026-04-23 18:51:11','2026-04-23 18:51:11'),(11,'Curiosidades','Historias increíbles, datos curiosos y el color de la afición.','2026-04-23 18:51:11','2026-04-23 18:51:11'),(12,'Multimedia','Contenido multimedia','2026-04-23 18:51:11','2026-04-23 18:51:11');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `championships`
--

DROP TABLE IF EXISTS `championships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `championships` (
  `id` int NOT NULL AUTO_INCREMENT,
  `year` year NOT NULL,
  `host_country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `flag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `champion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `runner_up` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_goals` int DEFAULT '0',
  `participating_teams` int DEFAULT '0',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `championships`
--

LOCK TABLES `championships` WRITE;
/*!40000 ALTER TABLE `championships` DISABLE KEYS */;
INSERT INTO `championships` VALUES (22,2022,'QATAR',NULL,'ARGENTINA','PARIS',21,11,NULL,'2026-04-24 04:29:18','2026-04-24 04:29:49');
/*!40000 ALTER TABLE `championships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (21,25,18,'que pro','2026-04-23 04:23:09'),(22,25,18,'waza','2026-04-23 04:23:22'),(24,25,18,'wazaaaa','2026-04-23 04:54:33'),(28,26,18,'muy bueno pije','2026-04-23 18:24:59');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `continent` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `history` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `titles` int DEFAULT '0',
  `participations` int DEFAULT '0',
  `coach` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `best_player` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured_video` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` VALUES (5,'Mexico','MX','AFC','countries/flag_1776970293.png',NULL,NULL,0,11,'Tuca','Chicaro',NULL,'2026-04-24 02:51:33','2026-04-24 02:51:55'),(6,'Argentina','AR','CONCACAF','countries/flag_1776970380.png',NULL,NULL,2,11,'Messi','Messi',NULL,'2026-04-24 02:53:00','2026-04-24 02:53:00'),(7,'JAPON','JP','CONCACAF',NULL,NULL,NULL,0,0,'Tuca','KEIJI',NULL,'2026-04-24 04:17:20','2026-04-24 04:17:20');
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `country_images`
--

DROP TABLE IF EXISTS `country_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `country_id` int DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`),
  CONSTRAINT `country_images_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country_images`
--

LOCK TABLES `country_images` WRITE;
/*!40000 ALTER TABLE `country_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `country_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `country_videos`
--

DROP TABLE IF EXISTS `country_videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country_videos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `country_id` int DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`),
  CONSTRAINT `country_videos_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country_videos`
--

LOCK TABLES `country_videos` WRITE;
/*!40000 ALTER TABLE `country_videos` DISABLE KEYS */;
/*!40000 ALTER TABLE `country_videos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `featured_players`
--

DROP TABLE IF EXISTS `featured_players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `featured_players` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `achievements` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `featured_players`
--

LOCK TABLES `featured_players` WRITE;
/*!40000 ALTER TABLE `featured_players` DISABLE KEYS */;
INSERT INTO `featured_players` VALUES (7,'Lionel Messi',NULL,NULL,NULL,'2026-04-24 04:30:27','2026-04-24 04:30:27');
/*!40000 ALTER TABLE `featured_players` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `friendships`
--

DROP TABLE IF EXISTS `friendships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `friendships` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT 'User who sent the request',
  `friend_id` int NOT NULL COMMENT 'User who received the request',
  `status` enum('pending','accepted','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_friendship` (`user_id`,`friend_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_friend` (`friend_id`),
  CONSTRAINT `fk_friendships_friend` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_friendships_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `friendships`
--

LOCK TABLES `friendships` WRITE;
/*!40000 ALTER TABLE `friendships` DISABLE KEYS */;
INSERT INTO `friendships` VALUES (5,18,12,'accepted','2026-04-22 01:41:30','2026-04-22 01:42:17');
/*!40000 ALTER TABLE `friendships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `likes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `post_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`user_id`,`post_id`),
  KEY `post_id` (`post_id`),
  KEY `idx_user_like` (`user_id`),
  CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
INSERT INTO `likes` VALUES (43,12,25,'2026-04-21 20:17:40'),(47,18,26,'2026-04-23 18:24:51'),(48,18,25,'2026-04-23 18:25:07');
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `media_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `media_type` enum('image','video','audio','document') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('sent','delivered','read') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'sent',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sender` (`sender_id`),
  KEY `idx_receiver` (`receiver_id`),
  CONSTRAINT `fk_msg_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (13,12,18,'Hola',NULL,NULL,'read','2026-04-22 09:42:42','2026-04-22 01:43:23'),(14,12,18,'Que haces',NULL,NULL,'read','2026-04-22 09:42:47','2026-04-22 01:43:23'),(15,18,12,'Nada aqui nomas',NULL,NULL,'read','2026-04-22 09:43:28','2026-04-22 01:43:29'),(16,12,18,'ah bueno jaja',NULL,NULL,'read','2026-04-22 09:43:36','2026-04-22 01:43:38'),(17,12,18,'Hola',NULL,NULL,'read','2026-04-23 12:17:18','2026-04-23 04:17:28'),(18,12,18,'hola',NULL,NULL,'read','2026-04-23 12:42:25','2026-04-23 04:55:47'),(19,18,12,'que hcaes',NULL,NULL,'read','2026-04-23 12:55:53','2026-04-23 04:56:04');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `actor_id` int DEFAULT NULL,
  `type` enum('friend_request','friend_accepted','post_like','post_comment','message','group_message','group_added','system') NOT NULL,
  `entity_id` int DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `body` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_read` (`user_id`,`is_read`),
  KEY `idx_user_date` (`user_id`,`created_at`),
  KEY `actor_id` (`actor_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,12,18,'post_like',25,'post',NULL,1,'2026-04-23 06:22:10'),(2,12,18,'post_comment',25,'post',NULL,1,'2026-04-23 06:23:09'),(3,18,12,'message',18,'message',NULL,0,'2026-04-23 06:42:25'),(4,12,18,'post_comment',25,'post',NULL,1,'2026-04-23 06:54:04'),(5,12,18,'post_like',25,'post',NULL,1,'2026-04-23 06:54:15'),(6,12,18,'post_comment',25,'post',NULL,1,'2026-04-23 06:55:23'),(7,12,18,'message',19,'message',NULL,1,'2026-04-23 06:55:53'),(8,12,18,'post_like',26,'post',NULL,1,'2026-04-23 20:24:51'),(9,12,18,'post_comment',26,'post',NULL,1,'2026-04-23 20:24:59'),(10,12,18,'post_like',25,'post',NULL,1,'2026-04-23 20:25:07');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `content` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `media_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `likes` int DEFAULT '0',
  `comments_count` int DEFAULT '0',
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_post` (`user_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (25,12,12,'asas','image','uploads/post/images/post_69e7da591d19b8.93254785.gif',2,3,'public','2026-04-21 20:13:13'),(26,12,12,'eeeeeeeeeeeeeeeeee','image','uploads/post/images/post_69ea44888ceda8.07879186.jpg',1,1,'public','2026-04-23 16:10:48');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `successful_teams`
--

DROP TABLE IF EXISTS `successful_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `successful_teams` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `flag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `titles` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `successful_teams`
--

LOCK TABLES `successful_teams` WRITE;
/*!40000 ALTER TABLE `successful_teams` DISABLE KEYS */;
INSERT INTO `successful_teams` VALUES (9,'Real Madrid',NULL,1,'2026-04-24 04:31:09','2026-04-24 04:31:09');
/*!40000 ALTER TABLE `successful_teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `birth_date` date DEFAULT NULL,
  `gender` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive','suspended') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `total_posts` int DEFAULT '0',
  `last_activity` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_email` (`email`),
  KEY `idx_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (12,'Cesar Isaac Peña Mendoza','Izaak','cesarisaac2004@gmail.com','101442','admin','2004-10-13','M','Monterrey','Mexico','uploads/avatars/avatar_12_1776887763.gif','uploads/covers/cover_12_1776823144.png','Hola','active',0,'2026-04-23 05:12:26','2026-04-21 20:13:01'),(13,'Alejandro García','aleg_99','alejandro@email.com','hash_password_1','user','1990-05-15','Masculino','Madrid','España',NULL,NULL,'Entusiasta de la tecnología.','active',0,'2026-04-22 01:31:07','2026-04-22 01:31:07'),(14,'María López','mery_jane','mery@email.com','hash_password_2','user','1995-08-22','Femenino','Bogotá','Colombia',NULL,NULL,'Amo viajar y leer.','active',5,'2026-04-22 01:31:07','2026-04-22 01:31:07'),(15,'John Doe','jdoe_dev','john@email.com','hash_password_3','user','1988-12-01','Masculino','New York','USA',NULL,NULL,'Software Engineer.','active',12,'2026-04-22 01:31:07','2026-04-22 01:31:07'),(16,'Lucía Fernández','lu_fer','lucia@email.com','hash_password_4','user','2001-02-10','Femenino','Buenos Aires','Argentina',NULL,NULL,'Estudiante de diseño.','active',2,'2026-04-22 01:31:07','2026-04-22 01:31:07'),(17,'Kenji Tanaka','kenji_t','kenji@email.com','hash_password_5','user','1993-11-30','Masculino','Tokyo','Japan',NULL,NULL,'Gaming is life.','inactive',0,'2026-04-22 01:31:07','2026-04-22 01:31:07'),(18,'Abril Bonilla','Absita','abril@gmail.com','101442','user','2004-04-06','F','Cadereyta','México','uploads/avatars/avatar_18_1776968562.png','uploads/covers/cover_18_1776968608.png','','active',0,'2026-04-23 18:23:28','2026-04-22 01:41:10');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `vw_active_users`
--

DROP TABLE IF EXISTS `vw_active_users`;
/*!50001 DROP VIEW IF EXISTS `vw_active_users`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_active_users` AS SELECT 
 1 AS `idUsuario`,
 1 AS `username`,
 1 AS `Nombre`,
 1 AS `ultimaActividad`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_avg_interactions`
--

DROP TABLE IF EXISTS `vw_avg_interactions`;
/*!50001 DROP VIEW IF EXISTS `vw_avg_interactions`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_avg_interactions` AS SELECT 
 1 AS `idUsuario`,
 1 AS `username`,
 1 AS `totalPublicaciones`,
 1 AS `promedioInteraccion`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_chat_sidebar`
--

DROP TABLE IF EXISTS `vw_chat_sidebar`;
/*!50001 DROP VIEW IF EXISTS `vw_chat_sidebar`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_chat_sidebar` AS SELECT 
 1 AS `owner_id`,
 1 AS `friend_id`,
 1 AS `friend_name`,
 1 AS `friend_username`,
 1 AS `friend_avatar`,
 1 AS `last_message_id`,
 1 AS `last_message_content`,
 1 AS `last_message_media`,
 1 AS `last_message_status`,
 1 AS `last_message_date`,
 1 AS `last_message_sender_id`,
 1 AS `unread_count`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_conversation_last_message`
--

DROP TABLE IF EXISTS `vw_conversation_last_message`;
/*!50001 DROP VIEW IF EXISTS `vw_conversation_last_message`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_conversation_last_message` AS SELECT 
 1 AS `user1_id`,
 1 AS `user2_id`,
 1 AS `last_message_id`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_daily_report`
--

DROP TABLE IF EXISTS `vw_daily_report`;
/*!50001 DROP VIEW IF EXISTS `vw_daily_report`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_daily_report` AS SELECT 
 1 AS `fecha`,
 1 AS `nuevasPublicaciones`,
 1 AS `totalLikes`,
 1 AS `nuevosUsuarios`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_friends_list`
--

DROP TABLE IF EXISTS `vw_friends_list`;
/*!50001 DROP VIEW IF EXISTS `vw_friends_list`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_friends_list` AS SELECT 
 1 AS `friendship_id`,
 1 AS `user_id`,
 1 AS `friend_id`,
 1 AS `friend_name`,
 1 AS `friend_username`,
 1 AS `friend_avatar`,
 1 AS `status`,
 1 AS `requested_at`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_general_stats`
--

DROP TABLE IF EXISTS `vw_general_stats`;
/*!50001 DROP VIEW IF EXISTS `vw_general_stats`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_general_stats` AS SELECT 
 1 AS `totalUsuarios`,
 1 AS `totalPublicaciones`,
 1 AS `totalComentarios`,
 1 AS `totalLikes`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_post_comments`
--

DROP TABLE IF EXISTS `vw_post_comments`;
/*!50001 DROP VIEW IF EXISTS `vw_post_comments`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_post_comments` AS SELECT 
 1 AS `id`,
 1 AS `texto`,
 1 AS `totalComentarios`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_post_detail`
--

DROP TABLE IF EXISTS `vw_post_detail`;
/*!50001 DROP VIEW IF EXISTS `vw_post_detail`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_post_detail` AS SELECT 
 1 AS `id`,
 1 AS `username`,
 1 AS `autor`,
 1 AS `pais`,
 1 AS `texto`,
 1 AS `tipoContenido`,
 1 AS `rutamulti`,
 1 AS `likes`,
 1 AS `comentarios`,
 1 AS `estado`,
 1 AS `postdate`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_top_posts`
--

DROP TABLE IF EXISTS `vw_top_posts`;
/*!50001 DROP VIEW IF EXISTS `vw_top_posts`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_top_posts` AS SELECT 
 1 AS `id`,
 1 AS `username`,
 1 AS `texto`,
 1 AS `likes`,
 1 AS `comentarios`,
 1 AS `postdate`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_users_by_country`
--

DROP TABLE IF EXISTS `vw_users_by_country`;
/*!50001 DROP VIEW IF EXISTS `vw_users_by_country`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_users_by_country` AS SELECT 
 1 AS `pais`,
 1 AS `totalUsuarios`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vw_active_users`
--

/*!50001 DROP VIEW IF EXISTS `vw_active_users`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_active_users` AS select `users`.`id` AS `idUsuario`,`users`.`username` AS `username`,`users`.`name` AS `Nombre`,`users`.`last_activity` AS `ultimaActividad` from `users` where (`users`.`last_activity` >= (now() - interval 7 day)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_avg_interactions`
--

/*!50001 DROP VIEW IF EXISTS `vw_avg_interactions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_avg_interactions` AS select `u`.`id` AS `idUsuario`,`u`.`username` AS `username`,count(`p`.`id`) AS `totalPublicaciones`,ifnull(avg((`p`.`likes` + `p`.`comments_count`)),0) AS `promedioInteraccion` from (`users` `u` left join `posts` `p` on((`u`.`id` = `p`.`user_id`))) group by `u`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_chat_sidebar`
--

/*!50001 DROP VIEW IF EXISTS `vw_chat_sidebar`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_chat_sidebar` AS select `u`.`id` AS `owner_id`,`f`.`id` AS `friend_id`,`f`.`name` AS `friend_name`,`f`.`username` AS `friend_username`,`f`.`profile_picture` AS `friend_avatar`,`m`.`id` AS `last_message_id`,`m`.`content` AS `last_message_content`,`m`.`media_type` AS `last_message_media`,`m`.`status` AS `last_message_status`,`m`.`created_at` AS `last_message_date`,`m`.`sender_id` AS `last_message_sender_id`,(select count(0) from `messages` where ((`messages`.`sender_id` = `f`.`id`) and (`messages`.`receiver_id` = `u`.`id`) and (`messages`.`status` <> 'read'))) AS `unread_count` from ((((`friendships` `fr` join `users` `u` on(((`u`.`id` = `fr`.`user_id`) or (`u`.`id` = `fr`.`friend_id`)))) join `users` `f` on((((`f`.`id` = `fr`.`friend_id`) and (`u`.`id` = `fr`.`user_id`)) or ((`f`.`id` = `fr`.`user_id`) and (`u`.`id` = `fr`.`friend_id`))))) left join `vw_conversation_last_message` `clm` on(((`clm`.`user1_id` = least(`u`.`id`,`f`.`id`)) and (`clm`.`user2_id` = greatest(`u`.`id`,`f`.`id`))))) left join `messages` `m` on((`m`.`id` = `clm`.`last_message_id`))) where (`fr`.`status` = 'accepted') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_conversation_last_message`
--

/*!50001 DROP VIEW IF EXISTS `vw_conversation_last_message`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_conversation_last_message` AS select least(`messages`.`sender_id`,`messages`.`receiver_id`) AS `user1_id`,greatest(`messages`.`sender_id`,`messages`.`receiver_id`) AS `user2_id`,max(`messages`.`id`) AS `last_message_id` from `messages` group by least(`messages`.`sender_id`,`messages`.`receiver_id`),greatest(`messages`.`sender_id`,`messages`.`receiver_id`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_daily_report`
--

/*!50001 DROP VIEW IF EXISTS `vw_daily_report`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_daily_report` AS select cast(`p`.`created_at` as date) AS `fecha`,count(`p`.`id`) AS `nuevasPublicaciones`,coalesce(sum(`p`.`likes`),0) AS `totalLikes`,0 AS `nuevosUsuarios` from `posts` `p` group by cast(`p`.`created_at` as date) order by `fecha` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_friends_list`
--

/*!50001 DROP VIEW IF EXISTS `vw_friends_list`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_friends_list` AS select `f`.`id` AS `friendship_id`,`f`.`user_id` AS `user_id`,`f`.`friend_id` AS `friend_id`,`u`.`name` AS `friend_name`,`u`.`username` AS `friend_username`,`u`.`profile_picture` AS `friend_avatar`,`f`.`status` AS `status`,`f`.`created_at` AS `requested_at` from (`friendships` `f` join `users` `u` on((`f`.`friend_id` = `u`.`id`))) union select `f`.`id` AS `friendship_id`,`f`.`friend_id` AS `user_id`,`f`.`user_id` AS `friend_id`,`u`.`name` AS `friend_name`,`u`.`username` AS `friend_username`,`u`.`profile_picture` AS `friend_avatar`,`f`.`status` AS `status`,`f`.`created_at` AS `requested_at` from (`friendships` `f` join `users` `u` on((`f`.`user_id` = `u`.`id`))) where (`f`.`status` = 'accepted') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_general_stats`
--

/*!50001 DROP VIEW IF EXISTS `vw_general_stats`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_general_stats` AS select (select count(0) from `users`) AS `totalUsuarios`,(select count(0) from `posts`) AS `totalPublicaciones`,(select count(0) from `comments`) AS `totalComentarios`,(select sum(`posts`.`likes`) from `posts`) AS `totalLikes` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_post_comments`
--

/*!50001 DROP VIEW IF EXISTS `vw_post_comments`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_post_comments` AS select `p`.`id` AS `id`,`p`.`content` AS `texto`,count(`c`.`id`) AS `totalComentarios` from (`posts` `p` left join `comments` `c` on((`p`.`id` = `c`.`post_id`))) group by `p`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_post_detail`
--

/*!50001 DROP VIEW IF EXISTS `vw_post_detail`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_post_detail` AS select `p`.`id` AS `id`,`u`.`username` AS `username`,`u`.`name` AS `autor`,`u`.`country` AS `pais`,`p`.`content` AS `texto`,`p`.`content_type` AS `tipoContenido`,`p`.`media_path` AS `rutamulti`,`p`.`likes` AS `likes`,`p`.`comments_count` AS `comentarios`,`p`.`status` AS `estado`,`p`.`created_at` AS `postdate` from (`posts` `p` join `users` `u` on((`u`.`id` = `p`.`user_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_top_posts`
--

/*!50001 DROP VIEW IF EXISTS `vw_top_posts`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_top_posts` AS select `p`.`id` AS `id`,`u`.`username` AS `username`,`p`.`content` AS `texto`,`p`.`likes` AS `likes`,`p`.`comments_count` AS `comentarios`,`p`.`created_at` AS `postdate` from (`posts` `p` join `users` `u` on((`u`.`id` = `p`.`user_id`))) where (`p`.`status` = 'public') order by `p`.`likes` desc limit 10 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_users_by_country`
--

/*!50001 DROP VIEW IF EXISTS `vw_users_by_country`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_users_by_country` AS select `users`.`country` AS `pais`,count(0) AS `totalUsuarios` from `users` group by `users`.`country` order by `totalUsuarios` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-23 23:36:28
