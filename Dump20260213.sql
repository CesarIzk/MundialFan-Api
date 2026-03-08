-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: railway
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
-- Table structure for table `campeonatos`
--

DROP TABLE IF EXISTS `campeonatos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campeonatos` (
  `idCampeonato` int NOT NULL AUTO_INCREMENT,
  `anio` year NOT NULL,
  `paisSede` varchar(100) NOT NULL,
  `bandera` varchar(255) DEFAULT NULL,
  `campeon` varchar(100) DEFAULT NULL,
  `subcampeon` varchar(100) DEFAULT NULL,
  `golesTotales` int DEFAULT NULL,
  `equiposParticipantes` int DEFAULT NULL,
  `descripcion` text,
  PRIMARY KEY (`idCampeonato`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campeonatos`
--

LOCK TABLES `campeonatos` WRITE;
/*!40000 ALTER TABLE `campeonatos` DISABLE KEYS */;
INSERT INTO `campeonatos` VALUES (1,1930,'Uruguay','https://flagicons.lipis.dev/flags/4x3/uy.svg','Uruguay','Argentina',70,13,'Primera Copa del Mundo organizada por la FIFA.'),(2,1934,'Italia','https://flagicons.lipis.dev/flags/4x3/it.svg','Italia','Checoslovaquia',70,16,'Segunda edición, la primera con formato eliminatorio.'),(3,1938,'Francia','https://flagicons.lipis.dev/flags/4x3/fr.svg','Italia','Hungría',84,15,'Último Mundial antes de la Segunda Guerra Mundial.'),(4,1950,'Brasil','https://flagicons.lipis.dev/flags/4x3/br.svg','Uruguay','Brasil',88,13,'Famoso por el Maracanazo.'),(5,1954,'Suiza','https://flagicons.lipis.dev/flags/4x3/ch.svg','Alemania','Hungría',140,16,'Primera final televisada.'),(6,1958,'Suecia','https://flagicons.lipis.dev/flags/4x3/se.svg','Brasil','Suecia',126,16,'El primer Mundial ganado por Brasil con Pelé.'),(7,1962,'Chile','https://flagicons.lipis.dev/flags/4x3/cl.svg','Brasil','Checoslovaquia',89,16,'Brasil repitió título mundial.'),(8,1966,'Inglaterra','https://flagicons.lipis.dev/flags/4x3/gb.svg','Inglaterra','Alemania',89,16,'Inglaterra ganó en casa.'),(9,1970,'México','https://flagicons.lipis.dev/flags/4x3/mx.svg','Brasil','Italia',95,16,'Pelé consiguió su tercer título mundial.'),(10,1978,'Argentina','https://flagicons.lipis.dev/flags/4x3/ar.svg','Argentina','Holanda',102,16,'Primera Copa del Mundo ganada por Argentina.'),(11,1982,'España','https://flagicons.lipis.dev/flags/4x3/es.svg','Italia','Alemania',146,24,'Primera Copa con 24 equipos. Italia ganó su tercer título.'),(12,1986,'México','https://flagicons.lipis.dev/flags/4x3/mx.svg','Argentina','Alemania',132,24,'Maradona brilló con el “Gol del Siglo” y la “Mano de Dios”.'),(13,1990,'Italia','https://flagicons.lipis.dev/flags/4x3/it.svg','Alemania','Argentina',115,24,'Alemania ganó su tercer título mundial.'),(14,1994,'Estados Unidos','https://flagicons.lipis.dev/flags/4x3/us.svg','Brasil','Italia',141,24,'Brasil ganó por penales; primer Mundial en EE.UU.'),(15,1998,'Francia','https://flagicons.lipis.dev/flags/4x3/fr.svg','Francia','Brasil',171,32,'Francia ganó su primer título en casa.'),(16,2002,'Corea del Sur / Japón','https://flagicons.lipis.dev/flags/4x3/kr.svg','Brasil','Alemania',161,32,'Primera Copa en Asia, quinta corona para Brasil.'),(17,2006,'Alemania','https://flagicons.lipis.dev/flags/4x3/de.svg','Italia','Francia',147,32,'Italia conquistó su cuarto título mundial.'),(18,2010,'Sudáfrica','https://flagicons.lipis.dev/flags/4x3/za.svg','España','Holanda',145,32,'España ganó su primer Mundial con gol de Iniesta.'),(19,2014,'Brasil','https://flagicons.lipis.dev/flags/4x3/br.svg','Alemania','Argentina',171,32,'Alemania ganó su cuarto título con el gol de Götze.'),(20,2018,'Rusia','https://flagicons.lipis.dev/flags/4x3/ru.svg','Francia','Croacia',169,32,'Francia repitió título con un equipo joven y dominante.'),(21,2022,'Qatar','https://flagicons.lipis.dev/flags/4x3/qa.svg','Argentina','Francia',172,32,'Messi lideró a Argentina al tricampeonato en una final histórica.');
/*!40000 ALTER TABLE `campeonatos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `idCategoria` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  PRIMARY KEY (`idCategoria`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Noticias','Publicaciones sobre actualidad del mundial y equipos'),(2,'Jugadores','Contenido relacionado con futbolistas destacados'),(3,'Partidos','Publicaciones sobre resultados y análisis'),(4,'Opinión','Análisis y comentarios de los usuarios'),(5,'Multimedia','Videos, imágenes o clips del mundial');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comentarios`
--

DROP TABLE IF EXISTS `comentarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comentarios` (
  `idComentario` int NOT NULL AUTO_INCREMENT,
  `idPublicacion` int NOT NULL,
  `idUsuario` int NOT NULL,
  `contenido` varchar(500) NOT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idComentario`),
  KEY `idPublicacion` (`idPublicacion`),
  KEY `idUsuario` (`idUsuario`),
  CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`idPublicacion`) REFERENCES `publicaciones` (`idPublicacion`) ON DELETE CASCADE,
  CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`idUsuario`) REFERENCES `users` (`idUsuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comentarios`
--

LOCK TABLES `comentarios` WRITE;
/*!40000 ALTER TABLE `comentarios` DISABLE KEYS */;
INSERT INTO `comentarios` VALUES (1,2,1,'hola que tal','2025-11-06 02:43:40'),(2,7,3,'sdww','2025-11-07 03:19:55'),(3,3,3,'dwf4f','2025-11-07 03:20:02'),(4,3,3,'32','2025-11-07 03:20:07'),(5,3,3,'dwdw','2025-11-07 03:20:15'),(6,3,3,'wwww','2025-11-07 03:20:18'),(7,3,3,'aña','2025-11-07 04:16:34'),(8,3,3,'no puede ser','2025-11-07 04:17:10'),(9,7,3,'wfe','2025-11-07 04:17:29'),(10,3,3,'buenas','2025-11-07 04:19:33');
/*!40000 ALTER TABLE `comentarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipos_exitosos`
--

DROP TABLE IF EXISTS `equipos_exitosos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipos_exitosos` (
  `idEquipo` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `bandera` varchar(255) DEFAULT NULL,
  `titulos` int DEFAULT '0',
  PRIMARY KEY (`idEquipo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipos_exitosos`
--

LOCK TABLES `equipos_exitosos` WRITE;
/*!40000 ALTER TABLE `equipos_exitosos` DISABLE KEYS */;
INSERT INTO `equipos_exitosos` VALUES (1,'Brasil','https://flagicons.lipis.dev/flags/4x3/br.svg',5),(2,'Alemania','https://flagicons.lipis.dev/flags/4x3/de.svg',4),(3,'Italia','https://flagicons.lipis.dev/flags/4x3/it.svg',4),(4,'Argentina','https://flagicons.lipis.dev/flags/4x3/ar.svg',3),(5,'Francia','https://flagicons.lipis.dev/flags/4x3/fr.svg',2),(6,'Uruguay','https://flagicons.lipis.dev/flags/4x3/uy.svg',2),(7,'Inglaterra','https://flagicons.lipis.dev/flags/4x3/gb.svg',1),(8,'España','https://flagicons.lipis.dev/flags/4x3/es.svg',1);
/*!40000 ALTER TABLE `equipos_exitosos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jugadores_destacados`
--

DROP TABLE IF EXISTS `jugadores_destacados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jugadores_destacados` (
  `idJugador` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `pais` varchar(100) DEFAULT NULL,
  `logros` text,
  `foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idJugador`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jugadores_destacados`
--

LOCK TABLES `jugadores_destacados` WRITE;
/*!40000 ALTER TABLE `jugadores_destacados` DISABLE KEYS */;
INSERT INTO `jugadores_destacados` VALUES (1,'Pelé','Brasil','Único jugador con 3 títulos mundiales (1958, 1962, 1970).','imagenes/pele.jpg'),(2,'Diego Maradona','Argentina','Líder y campeón del Mundial 1986 con el \"Gol del Siglo\".','imagenes/diego.jpg'),(3,'Zinedine Zidane','Francia','Campeón del Mundo 1998 y subcampeón en 2006.','imagenes/zinedine.jpg'),(4,'Ronaldo Nazário','Brasil','Máximo goleador del Mundial 2002 con 8 goles.','imagenes/ronaldo.jpg'),(5,'Miroslav Klose','Alemania','Máximo goleador histórico de los Mundiales (16 goles).','imagenes/miroslav.jpg'),(6,'Lionel Messi','Argentina','Campeón del Mundo 2022 y Balón de Oro del torneo.','imagenes/messi.jpg');
/*!40000 ALTER TABLE `jugadores_destacados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `likes` (
  `idLike` int NOT NULL AUTO_INCREMENT,
  `idUsuario` int NOT NULL,
  `idPublicacion` int NOT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idLike`),
  UNIQUE KEY `unique_like` (`idUsuario`,`idPublicacion`),
  KEY `idPublicacion` (`idPublicacion`),
  KEY `idx_usuario_like` (`idUsuario`),
  CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `users` (`idUsuario`) ON DELETE CASCADE,
  CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`idPublicacion`) REFERENCES `publicaciones` (`idPublicacion`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
INSERT INTO `likes` VALUES (3,3,3,'2025-10-21 06:20:19'),(5,1,5,'2025-11-06 05:51:17'),(6,1,4,'2025-11-06 05:51:19'),(7,1,2,'2025-11-06 05:51:23'),(8,1,3,'2025-11-06 05:51:24'),(9,3,7,'2025-11-07 04:15:37'),(10,3,6,'2025-11-07 04:15:38'),(11,3,2,'2025-11-07 04:15:43'),(12,3,8,'2025-11-07 04:25:36');
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pais_imagenes`
--

DROP TABLE IF EXISTS `pais_imagenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pais_imagenes` (
  `idImagen` int NOT NULL AUTO_INCREMENT,
  `idPais` int DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idImagen`),
  KEY `idPais` (`idPais`),
  CONSTRAINT `pais_imagenes_ibfk_1` FOREIGN KEY (`idPais`) REFERENCES `paises` (`idPais`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pais_imagenes`
--

LOCK TABLES `pais_imagenes` WRITE;
/*!40000 ALTER TABLE `pais_imagenes` DISABLE KEYS */;
/*!40000 ALTER TABLE `pais_imagenes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pais_videos`
--

DROP TABLE IF EXISTS `pais_videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pais_videos` (
  `idVideo` int NOT NULL AUTO_INCREMENT,
  `idPais` int DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idVideo`),
  KEY `idPais` (`idPais`),
  CONSTRAINT `pais_videos_ibfk_1` FOREIGN KEY (`idPais`) REFERENCES `paises` (`idPais`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pais_videos`
--

LOCK TABLES `pais_videos` WRITE;
/*!40000 ALTER TABLE `pais_videos` DISABLE KEYS */;
/*!40000 ALTER TABLE `pais_videos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paises`
--

DROP TABLE IF EXISTS `paises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paises` (
  `idPais` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `bandera` varchar(255) DEFAULT NULL,
  `descripcion` text,
  `historia` text,
  `titulos` int DEFAULT '0',
  `participaciones` int DEFAULT '0',
  `continente` varchar(50) DEFAULT NULL,
  `entrenador` varchar(100) DEFAULT NULL,
  `mejorJugador` varchar(100) DEFAULT NULL,
  `videoDestacado` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idPais`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paises`
--

LOCK TABLES `paises` WRITE;
/*!40000 ALTER TABLE `paises` DISABLE KEYS */;
INSERT INTO `paises` VALUES (1,'Argentina','argentina','https://flagcdn.com/w320/ar.png','Selección nacional de fútbol de Argentina.','Argentina debutó en 1930...',3,18,'América del Sur','Lionel Scaloni','Lionel Messi',NULL),(2,'Brasil','brasil','https://flagcdn.com/w320/br.png','Selección nacional de fútbol de Brasil.','Brasil es el único país...',5,22,'América del Sur','Dorival Júnior','Pelé',NULL),(3,'México','mexico','https://flagcdn.com/w320/mx.png','Selección nacional de México.','México ha participado en 17 ediciones...',0,17,'América del Norte','Jaime Lozano','Hugo Sánchez',NULL),(4,'Puerto Rico','puertorico','https://upload.wikimedia.org/wikipedia/commons/2/28/Flag_of_Puerto_Rico.svg','','',0,0,'America del Sur','','',NULL);
/*!40000 ALTER TABLE `paises` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publicaciones`
--

DROP TABLE IF EXISTS `publicaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `publicaciones` (
  `idPublicacion` int NOT NULL AUTO_INCREMENT,
  `idUsuario` int NOT NULL,
  `texto` varchar(500) DEFAULT NULL,
  `tipoContenido` varchar(10) DEFAULT NULL,
  `rutamulti` varchar(255) DEFAULT NULL,
  `likes` int DEFAULT '0',
  `estado` varchar(20) NOT NULL DEFAULT 'publico',
  `postdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `comentarios` int DEFAULT '0',
  `idCategoria` int DEFAULT NULL,
  PRIMARY KEY (`idPublicacion`),
  KEY `idx_usuario_publicacion` (`idUsuario`),
  KEY `idCategoria` (`idCategoria`),
  CONSTRAINT `publicaciones_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `users` (`idUsuario`) ON DELETE CASCADE,
  CONSTRAINT `publicaciones_ibfk_2` FOREIGN KEY (`idCategoria`) REFERENCES `categorias` (`idCategoria`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publicaciones`
--

LOCK TABLES `publicaciones` WRITE;
/*!40000 ALTER TABLE `publicaciones` DISABLE KEYS */;
INSERT INTO `publicaciones` VALUES (2,3,'Buenas noches','texto',NULL,2,'publico','2025-10-21 06:13:41',1,NULL),(3,3,'Buenas noches','video','videos/1761027309_Rashica.mp4',2,'publico','2025-10-21 06:15:09',7,NULL),(4,1,'Buenas noches','texto',NULL,1,'publico','2025-11-06 02:43:13',0,NULL),(5,1,'gus nnait','texto',NULL,1,'publico','2025-11-06 02:43:31',0,NULL),(6,1,'hola','imagen','imagenes/1762408319_miroslav.jpg',1,'publico','2025-11-06 05:51:59',0,5),(7,1,'dedef','imagen','post/1762410507_ronaldo.jpg',1,'publico','2025-11-06 06:28:27',2,5),(8,3,'wdwdwdw','imagen','/uploads/publics/imagenes/post_1762489230_690d738e0193b.jpg',1,'publico','2025-11-07 04:20:30',0,5),(9,3,'nonknk','imagen','/uploads/publics/imagenes/post_1762536614_690e2ca6e90a4.jpg',0,'publico','2025-11-07 17:30:14',0,2);
/*!40000 ALTER TABLE `publicaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `idUsuario` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('usuario','admin') DEFAULT 'usuario',
  `fechaNacimiento` date DEFAULT NULL,
  `genero` varchar(20) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `pais` varchar(100) DEFAULT NULL,
  `fotoPerfil` varchar(255) DEFAULT NULL,
  `biografia` text,
  `estado` enum('activo','inactivo','suspendido') DEFAULT 'activo',
  `fechaRegistro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimaActividad` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `totalPublicaciones` int DEFAULT '0',
  PRIMARY KEY (`idUsuario`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_email` (`email`),
  KEY `idx_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@mundialfan.com','admin','$2y$10$ZBj7o5FL0MRablRqqdRf5epptTHerhKwlkC7LWTc3.CG8625C80hW','admin','2004-10-14','Masculino','Monterrey','México','/uploads/avatars/user_1_1762398268.png','hola','activo','2025-10-20 17:46:53','2026-02-14 04:44:20',4),(3,'Cesar Isaac Peña Mendoza','cesarisaac2004@gmail.com','izaak','1442','usuario','2004-10-14','Masculino','Monterrey','México','/uploads/users/3/avatar/avatar_1762465935.png','Hola','activo','2025-10-20 18:05:06','2025-11-07 17:30:14',2),(4,'Cesar','cesar@gmail.com','cesar575','1442','usuario',NULL,NULL,NULL,'Mexico',NULL,NULL,'activo','2025-10-20 23:38:23','2025-10-20 23:38:23',0),(5,'Abigail Palacios','abby@admin.com','Abby','$2y$10$EN8mxXq9CnARDQrTNT1z5.GOm1gJ.KOOSYK0YwDzI3FIg98/Y9N0a','admin',NULL,NULL,NULL,NULL,NULL,NULL,'activo','2025-11-11 04:28:01','2025-11-11 04:28:01',0),(6,'Abigail Palacios','abby@admin.net','Abby01','$2y$10$eHc8deberZlHxEagqgM9BO60QlkORx5mPCcGP0DXBpcY.rAyhJmMW','admin',NULL,NULL,NULL,NULL,NULL,NULL,'activo','2025-11-11 04:31:10','2025-11-11 04:31:10',0),(7,'Abigail Palacios','abby@mail.com','Abby02','$2y$10$meWTvaelOBZg.LZm3yi.w.3o8.5Sd2OBxookMUHKCGN6ntb/Qkcce','admin',NULL,NULL,NULL,NULL,NULL,NULL,'activo','2025-11-11 04:43:49','2025-11-11 04:47:47',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `vw_estadisticas_generales`
--

DROP TABLE IF EXISTS `vw_estadisticas_generales`;
/*!50001 DROP VIEW IF EXISTS `vw_estadisticas_generales`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_estadisticas_generales` AS SELECT 
 1 AS `totalUsuarios`,
 1 AS `totalPublicaciones`,
 1 AS `totalComentarios`,
 1 AS `totalLikes`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_promedio_interacciones`
--

DROP TABLE IF EXISTS `vw_promedio_interacciones`;
/*!50001 DROP VIEW IF EXISTS `vw_promedio_interacciones`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_promedio_interacciones` AS SELECT 
 1 AS `idUsuario`,
 1 AS `username`,
 1 AS `totalPublicaciones`,
 1 AS `promedioInteraccion`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_publicaciones_comentarios`
--

DROP TABLE IF EXISTS `vw_publicaciones_comentarios`;
/*!50001 DROP VIEW IF EXISTS `vw_publicaciones_comentarios`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_publicaciones_comentarios` AS SELECT 
 1 AS `idPublicacion`,
 1 AS `texto`,
 1 AS `totalComentarios`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_publicaciones_detalle`
--

DROP TABLE IF EXISTS `vw_publicaciones_detalle`;
/*!50001 DROP VIEW IF EXISTS `vw_publicaciones_detalle`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_publicaciones_detalle` AS SELECT 
 1 AS `idPublicacion`,
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
-- Temporary view structure for view `vw_reporte_diario`
--

DROP TABLE IF EXISTS `vw_reporte_diario`;
/*!50001 DROP VIEW IF EXISTS `vw_reporte_diario`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_reporte_diario` AS SELECT 
 1 AS `fecha`,
 1 AS `nuevasPublicaciones`,
 1 AS `totalLikes`,
 1 AS `nuevosUsuarios`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_top_publicaciones`
--

DROP TABLE IF EXISTS `vw_top_publicaciones`;
/*!50001 DROP VIEW IF EXISTS `vw_top_publicaciones`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_top_publicaciones` AS SELECT 
 1 AS `idPublicacion`,
 1 AS `username`,
 1 AS `texto`,
 1 AS `likes`,
 1 AS `comentarios`,
 1 AS `postdate`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_usuarios_activos`
--

DROP TABLE IF EXISTS `vw_usuarios_activos`;
/*!50001 DROP VIEW IF EXISTS `vw_usuarios_activos`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_usuarios_activos` AS SELECT 
 1 AS `idUsuario`,
 1 AS `username`,
 1 AS `Nombre`,
 1 AS `ultimaActividad`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_usuarios_por_pais`
--

DROP TABLE IF EXISTS `vw_usuarios_por_pais`;
/*!50001 DROP VIEW IF EXISTS `vw_usuarios_por_pais`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_usuarios_por_pais` AS SELECT 
 1 AS `pais`,
 1 AS `totalUsuarios`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vw_estadisticas_generales`
--

/*!50001 DROP VIEW IF EXISTS `vw_estadisticas_generales`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_estadisticas_generales` AS select (select count(0) from `users`) AS `totalUsuarios`,(select count(0) from `publicaciones`) AS `totalPublicaciones`,(select count(0) from `comentarios`) AS `totalComentarios`,(select sum(`publicaciones`.`likes`) from `publicaciones`) AS `totalLikes` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_promedio_interacciones`
--

/*!50001 DROP VIEW IF EXISTS `vw_promedio_interacciones`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_promedio_interacciones` AS select `u`.`idUsuario` AS `idUsuario`,`u`.`username` AS `username`,count(`p`.`idPublicacion`) AS `totalPublicaciones`,ifnull(avg((`p`.`likes` + `p`.`comentarios`)),0) AS `promedioInteraccion` from (`users` `u` left join `publicaciones` `p` on((`u`.`idUsuario` = `p`.`idUsuario`))) group by `u`.`idUsuario` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_publicaciones_comentarios`
--

/*!50001 DROP VIEW IF EXISTS `vw_publicaciones_comentarios`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_publicaciones_comentarios` AS select `p`.`idPublicacion` AS `idPublicacion`,`p`.`texto` AS `texto`,count(`c`.`idComentario`) AS `totalComentarios` from (`publicaciones` `p` left join `comentarios` `c` on((`p`.`idPublicacion` = `c`.`idPublicacion`))) group by `p`.`idPublicacion` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_publicaciones_detalle`
--

/*!50001 DROP VIEW IF EXISTS `vw_publicaciones_detalle`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_publicaciones_detalle` AS select `p`.`idPublicacion` AS `idPublicacion`,`u`.`username` AS `username`,`u`.`Nombre` AS `autor`,`u`.`pais` AS `pais`,`p`.`texto` AS `texto`,`p`.`tipoContenido` AS `tipoContenido`,`p`.`rutamulti` AS `rutamulti`,`p`.`likes` AS `likes`,`p`.`comentarios` AS `comentarios`,`p`.`estado` AS `estado`,`p`.`postdate` AS `postdate` from (`publicaciones` `p` join `users` `u` on((`u`.`idUsuario` = `p`.`idUsuario`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_reporte_diario`
--

/*!50001 DROP VIEW IF EXISTS `vw_reporte_diario`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_reporte_diario` AS select cast(`p`.`postdate` as date) AS `fecha`,count(`p`.`idPublicacion`) AS `nuevasPublicaciones`,sum(`p`.`likes`) AS `totalLikes`,(select count(0) from `users` `u` where (cast(`u`.`fechaRegistro` as date) = cast(`p`.`postdate` as date))) AS `nuevosUsuarios` from `publicaciones` `p` group by cast(`p`.`postdate` as date) order by `fecha` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_top_publicaciones`
--

/*!50001 DROP VIEW IF EXISTS `vw_top_publicaciones`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_top_publicaciones` AS select `p`.`idPublicacion` AS `idPublicacion`,`u`.`username` AS `username`,`p`.`texto` AS `texto`,`p`.`likes` AS `likes`,`p`.`comentarios` AS `comentarios`,`p`.`postdate` AS `postdate` from (`publicaciones` `p` join `users` `u` on((`u`.`idUsuario` = `p`.`idUsuario`))) where (`p`.`estado` = 'publico') order by `p`.`likes` desc limit 10 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_usuarios_activos`
--

/*!50001 DROP VIEW IF EXISTS `vw_usuarios_activos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_usuarios_activos` AS select `users`.`idUsuario` AS `idUsuario`,`users`.`username` AS `username`,`users`.`Nombre` AS `Nombre`,`users`.`ultimaActividad` AS `ultimaActividad` from `users` where (`users`.`ultimaActividad` >= (now() - interval 7 day)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_usuarios_por_pais`
--

/*!50001 DROP VIEW IF EXISTS `vw_usuarios_por_pais`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_usuarios_por_pais` AS select `users`.`pais` AS `pais`,count(0) AS `totalUsuarios` from `users` group by `users`.`pais` order by `totalUsuarios` desc */;
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

-- Dump completed on 2026-02-13 23:04:39
