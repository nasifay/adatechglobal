-- MariaDB dump 10.19  Distrib 10.4.21-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: adatech_cms
-- ------------------------------------------------------
-- Server version	10.4.21-MariaDB

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
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('published','draft') COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `content` WRITE;
INSERT INTO `content` VALUES (4,'landing','We deliver innovative software, embedded systems, and digital solutions.','','2025-11-26 23:16:27',NULL,NULL,NULL,NULL,'published',NULL),(11,'index','We deliver innovative software, embedded systems, and digital solutions.','<h2>We deliver innovative software, embedded systems, and digital solutions.</h2>\n<p>We empower businesses, communities and innovators with web &amp; mobile apps, IoT and embedded systems, and cloud-hosted platforms tailored for the Ethiopian market.</p>','2025-11-27 00:14:47',NULL,NULL,NULL,NULL,'published','2025-11-27 00:14:47'),(12,'about','About','<h2>About Adatech Solutions</h2>\n<p>Adatech Solutions is a forward-thinking technology company dedicated to developing innovative digital solutions for businesses and communities. With expertise in software development, embedded systems, and IT consulting, we help organizations unlock their full potential through technology.</p>\n<h3>Mission</h3>\n<p>To empower businesses and communities through cutting-edge technology, fostering innovation, efficiency, and sustainable growth.</p>\n<h3>Vision</h3>\n<p>To become the most trusted technology partner in Ethiopia and across Africa, driving digital transformation for a better future.</p>','2025-11-27 00:14:47',NULL,NULL,NULL,NULL,'published','2025-11-27 00:14:47');

UNLOCK TABLES;

-- (rest of dump omitted for brevity in repo copy — full dump present in backups/)
