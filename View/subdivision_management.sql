-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: subdivision_management
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `admin_logs`
--

DROP TABLE IF EXISTS `admin_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `details` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_logs`
--

LOCK TABLES `admin_logs` WRITE;
/*!40000 ALTER TABLE `admin_logs` DISABLE KEYS */;
INSERT INTO `admin_logs` VALUES (1,1,'CREATED','Added new resident: John Doe to Block 5 Lot 2','2026-03-20 10:01:07'),(2,1,'UPDATED','Updated utility bill for Account #10025','2026-03-20 10:01:07'),(3,1,'UPDATED','Modified: Andress Jose Bonifacio','2026-03-21 10:38:58'),(4,2,'UPDATED','Modified: Andress Jose Bonifacio','2026-03-25 08:47:49'),(5,2,'UPDATED','Modified: Andress Jose Bonifacio','2026-03-25 09:26:53'),(6,2,'CREATED','Added: Albert Poblacion','2026-03-25 09:43:10'),(7,2,'CREATED','Created new admin: William Reynolds','2026-03-25 11:16:06'),(8,2,'UPDATED','Updated admin profile: Kervie Balolong','2026-03-25 11:16:22'),(9,2,'UPDATED','Updated admin profile: William Reynold (Staff)','2026-03-25 11:38:48'),(10,2,'DELETE','Deleted ID: 2','2026-03-25 11:47:39'),(11,2,'ADD','Added: sampleresidents','2026-03-25 11:51:47'),(12,2,'UPDATED','Updated admin profile: MasterAdmin (Master)','2026-03-25 11:52:15'),(13,2,'DELETE','Deleted ID: 16','2026-03-25 11:52:56'),(14,2,'ADD','Added: John Doe','2026-03-25 11:58:26'),(15,2,'DELETED','Deleted Resident: John Doe (ID: 17)','2026-03-25 12:03:34'),(16,2,'UPDATED','Updated admin: MasterAdmin','2026-03-29 19:36:31'),(17,2,'UPDATED','Updated admin: MasterAdmin','2026-03-29 19:39:25'),(18,2,'CREATED','Added: Franz Matthew','2026-03-29 21:03:37'),(19,4,'CREATED','Added admin: hellnah as Master','2026-06-19 08:30:38'),(20,4,'UPDATED','Modified: Franz Matthew','2026-06-25 10:14:50'),(21,4,'UPDATED','Modified: Franz Matthew','2026-06-25 13:19:48'),(22,4,'UPDATED','Modified: Andress Jose Bonifacio','2026-06-25 13:20:18'),(23,4,'UPDATED','Modified: Franz Matthew','2026-06-25 13:20:48'),(24,4,'UPDATED','Modified: Franz Matthew','2026-06-25 13:21:26'),(25,4,'UPDATED','Modified: Franz Matthew','2026-06-25 13:21:43'),(26,4,'UPDATED','Modified: Franz Matthew','2026-06-25 13:24:30'),(27,4,'UPDATED','Modified: Franz Matthew','2026-06-25 13:25:16'),(28,4,'UPDATED','Modified: Franz Matthew','2026-06-25 13:25:48'),(29,4,'UPDATED','Modified: Franz Matthew','2026-06-25 13:47:43'),(30,4,'UPDATED','Modified: Franz Matthew','2026-06-25 13:48:13'),(31,4,'UPDATED','Modified: Franz Matthew','2026-06-25 13:50:00'),(32,4,'UPDATED','Modified: Franz Matthew','2026-06-25 13:50:16'),(33,4,'UPDATED','Modified: Andress Jose Bonifacio','2026-06-25 13:52:05'),(34,4,'UPDATED','Modified: Franz Matthew','2026-06-25 14:13:40'),(35,4,'UPDATED','Modified: Franz Matthew','2026-06-25 14:14:05'),(36,4,'UPDATED','Modified: Franz Matthew','2026-06-25 14:20:49'),(37,4,'UPDATED','Modified: Franz Matthew','2026-06-25 14:21:47'),(38,4,'UPDATED','Modified: Franz Matthew','2026-06-25 14:23:28'),(39,4,'UPDATED','Modified: Franz Matthew','2026-06-25 14:25:45'),(40,4,'CREATED','Added: Lalaine Tumagan','2026-06-26 11:07:44'),(41,4,'CREATED','Added: AFA','2026-06-26 11:22:04'),(42,4,'DELETED','Deleted Resident: AFA (ID: 20)','2026-06-26 11:33:21'),(43,4,'DELETED','Deleted Resident: Lalaine Tumagan (ID: 19)','2026-06-26 11:33:29'),(44,4,'CREATED','Added: HUEHUE','2026-06-26 11:53:45'),(45,4,'UPDATED','Modified: HUEHUE','2026-06-26 11:54:03'),(46,4,'UPDATED','Modified: HUEHUE','2026-06-26 12:02:30'),(47,4,'UPDATED','Modified: HUEHUE','2026-06-26 12:02:58'),(48,4,'CREATED','Added: jiji','2026-06-26 12:03:40'),(49,4,'UPDATED','Modified: jiji','2026-06-26 12:04:17'),(50,4,'UPDATED','Modified: Franz Matthew','2026-07-07 17:20:55'),(51,4,'UPDATED','Modified: Franz Matthew','2026-07-07 17:26:11'),(52,4,'UPDATED','Modified: Franz Matthew','2026-07-07 17:26:42'),(53,4,'UPDATED','Modified: jiji','2026-07-08 14:39:02'),(54,4,'UPDATED','Modified: jiji','2026-07-08 14:39:14'),(55,5,'UPDATED','Updated own profile','2026-07-09 10:00:40'),(56,4,'CREATED','Added admin: JanaDulog as Master','2026-07-09 14:18:26'),(57,5,'CREATED','Added admin: josh123 as Master','2026-07-24 09:16:05');
/*!40000 ALTER TABLE `admin_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(100) DEFAULT NULL,
  `authority_level` enum('Master','Staff') NOT NULL DEFAULT 'Staff',
  `auth_key` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password` varchar(255) NOT NULL,
  `admin_status` varchar(20) DEFAULT 'active',
  `profile_photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'Kervie Balolong','Master','delete123','2026-03-25 03:16:22','$2y$10$dxpLXcfRT5xro2lScRogUu5S4OBfLHp/4xkntzyTTocuv/8ThYMEO','active',NULL),(2,'MasterAdmin','Master','admin123','2026-04-21 01:22:27','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','active',NULL),(3,'William Reynold','Staff','william123','2026-03-29 11:41:54','$2y$10$IdRFDkeJ02430e7Oy0X6t.JHRIRORQ.2pCne7Yk8of4TlCWENcnai','active',NULL),(4,'Leyn','Master','leyn123','2026-07-08 00:04:14','$2b$12$mFmOGp8YTIR8hnMKLSCmBO7NWJBS9.M.WfAMxxZ9mny4VVD6SVE.W','active',NULL),(5,'Catherine','Master','cath123','2026-07-09 02:00:40','$2b$12$EvBy28xjbS.f6WU9.e2FYu3QD8iXP6Dq.7y2Sixl6f33n/qhkwPjq','active','catherine.jpg'),(6,'hellnah','Master','12345','2026-06-19 00:30:38','$2y$10$B4rS11up9hEOmvHGbYXsy.4KVsyCv0TjqACkKZwKq.QB5FwbScKNy','active',NULL),(7,'JanaDulog','Master','123456','2026-07-09 06:20:43','$2y$10$32Xlf1mX9x9/J9WaIdwn6eAoCQrbK3SRMfWv55bCXo.9j7VJ8Sg6u','active',NULL),(8,'josh123','Master','123','2026-07-24 01:20:19','$2y$10$BldYPp/j5K3Nm.W0cWANWO8fjOThVnjPPjdiC.nkIMnA0OZKHRHKa','active',NULL);
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `connovate_panels`
--

DROP TABLE IF EXISTS `connovate_panels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `connovate_panels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_name` varchar(100) NOT NULL,
  `block_no` varchar(50) NOT NULL,
  `lot_no` varchar(50) NOT NULL,
  `floor_name` varchar(50) NOT NULL,
  `panel_key` varchar(100) NOT NULL,
  `control_number` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `status` varchar(50) NOT NULL DEFAULT 'done',
  `completed_by_id` int(11) DEFAULT NULL,
  `completed_by` varchar(100) DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `connovate_part` varchar(255) DEFAULT NULL,
  `started_at` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_connovate_panel` (`project_name`,`block_no`,`lot_no`,`floor_name`,`panel_key`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `connovate_panels`
--

LOCK TABLES `connovate_panels` WRITE;
/*!40000 ALTER TABLE `connovate_panels` DISABLE KEYS */;
INSERT INTO `connovate_panels` VALUES (1,'PADRE GARCIA','18','63','GROUND FLOOR','gf-top-beam','TDX-002A',1,'finished',4,'Leyn','2026-07-09 14:19:29','2026-07-09 06:19:29','2026-07-09 06:19:29','TDX-002A',NULL),(2,'PADRE GARCIA','18','63','GROUND FLOOR','gf-center-left-red','TDX-004A',1,'finished',4,'Leyn','2026-07-09 14:19:39','2026-07-09 06:19:39','2026-07-09 06:19:39','TDX-004A',NULL),(3,'PADRE GARCIA','18','63','GROUND FLOOR','gf-center-right-red','TDX-006A',1,'finished',4,'Leyn','2026-07-09 14:19:46','2026-07-09 06:19:46','2026-07-09 06:19:46','TDX-003A',NULL),(4,'PADRE GARCIA','18','63','GROUND FLOOR','gf-center-purple','TDX-001A',1,'finished',4,'Leyn','2026-07-09 14:20:08','2026-07-09 06:20:08','2026-07-09 06:20:08','TDX-001A',NULL),(5,'PADRE GARCIA','18','63','GROUND FLOOR','gf-center-green','TIR-214A',1,'finished',4,'Leyn','2026-07-09 14:20:27','2026-07-09 06:20:22','2026-07-09 06:20:27','TIR-214A',NULL),(6,'PADRE GARCIA','18','63','GROUND FLOOR','gf-mid-beam-left','TDX-0219A',1,'finished',7,'JanaDulog','2026-07-09 14:21:17','2026-07-09 06:21:17','2026-07-09 06:21:17','TDX-219A',NULL),(7,'PADRE GARCIA','18','63','GROUND FLOOR','gf-mid-beam-right','TDX-0220A',1,'finished',7,'JanaDulog','2026-07-09 14:21:30','2026-07-09 06:21:30','2026-07-09 06:21:30','TDX-220A',NULL),(8,'PADRE GARCIA','18','63','GROUND FLOOR','gf-left-top-green','TIR-207-1',1,'finished',7,'JanaDulog','2026-07-09 14:21:53','2026-07-09 06:21:53','2026-07-09 06:21:53','TIR-207A',NULL),(9,'PADRE GARCIA','18','63','GROUND FLOOR','gf-right-top-green','TIR-207-2',1,'finished',7,'JanaDulog','2026-07-09 14:23:28','2026-07-09 06:23:28','2026-07-09 06:23:28','TIR-207A',NULL),(10,'PADRE GARCIA','18','63','GROUND FLOOR','gf-left-red-1','TDX-204A',1,'finished',7,'JanaDulog','2026-07-09 14:25:42','2026-07-09 06:25:42','2026-07-09 06:25:42','TDX-204A',NULL),(11,'PADRE GARCIA','18','63','GROUND FLOOR','gf-left-red-2','TDX-203A',1,'finished',7,'JanaDulog','2026-07-09 14:25:54','2026-07-09 06:25:54','2026-07-09 06:25:54','TDX-203A',NULL),(12,'PADRE GARCIA','18','63','GROUND FLOOR','gf-right-red-1','TDX-216A',1,'finished',7,'JanaDulog','2026-07-09 14:26:35','2026-07-09 06:26:11','2026-07-09 06:26:35','TDX-216A',NULL),(13,'PADRE GARCIA','18','63','GROUND FLOOR','gf-right-red-2','TDX-215A',1,'finished',7,'JanaDulog','2026-07-09 14:26:31','2026-07-09 06:26:27','2026-07-09 06:26:31','TDX-215A',NULL),(14,'PADRE GARCIA','18','63','GROUND FLOOR','gf-center-blue-1','TDX-206A',1,'finished',7,'JanaDulog','2026-07-09 14:26:51','2026-07-09 06:26:51','2026-07-09 06:26:51','TDX-206A',NULL),(15,'PADRE GARCIA','18','63','GROUND FLOOR','gf-center-blue-2','TDX-205A',1,'finished',7,'JanaDulog','2026-07-09 14:27:01','2026-07-09 06:27:01','2026-07-09 06:27:01','TDX-205A',NULL),(16,'PADRE GARCIA','18','63','GROUND FLOOR','gf-left-bottom-green','TIR-207-3',1,'finished',7,'JanaDulog','2026-07-09 14:27:16','2026-07-09 06:27:16','2026-07-09 06:27:16','TIR-207A',NULL),(17,'PADRE GARCIA','18','63','GROUND FLOOR','gf-right-bottom-green','TIR-207-4',1,'finished',7,'JanaDulog','2026-07-09 14:27:28','2026-07-09 06:27:28','2026-07-09 06:27:28','TIR-207A',NULL),(18,'PADRE GARCIA','18','63','GROUND FLOOR','gf-bottom-beam-left','TIR-201A',1,'finished',7,'JanaDulog','2026-07-09 14:27:48','2026-07-09 06:27:48','2026-07-09 06:27:48','TDX-201A',NULL),(19,'PADRE GARCIA','18','63','GROUND FLOOR','gf-bottom-beam-right','TDX-201A',1,'finished',7,'JanaDulog','2026-07-09 14:28:26','2026-07-09 06:28:26','2026-07-09 06:28:26','TDX-201A',NULL),(20,'PADRE GARCIA','18','63','GROUND FLOOR','gf-center-bottom-green','TIR-207-6',1,'finished',7,'JanaDulog','2026-07-09 14:29:28','2026-07-09 06:29:28','2026-07-09 06:29:28','TIR-207A',NULL),(21,'PADRE GARCIA','18','63','SECOND FLOOR','sf-center-green','CTRL-TESTSF-214A-1',1,'finished',7,'JanaDulog','2026-07-09 14:29:54','2026-07-09 06:29:54','2026-07-09 06:29:54','TIR-214A',NULL),(22,'PADRE GARCIA','18','63','SECOND FLOOR','sf-mid-beam-left','CTRL-TESTSF-209-1',1,'finished',7,'JanaDulog','2026-07-09 14:30:32','2026-07-09 06:30:32','2026-07-09 06:30:32','TIR-209A',NULL),(23,'PADRE GARCIA','18','63','SECOND FLOOR','sf-mid-beam-right','CTRL-TESTSF-209-2',1,'finished',7,'JanaDulog','2026-07-09 14:30:38','2026-07-09 06:30:38','2026-07-09 06:30:38','TIR-209A',NULL),(24,'PADRE GARCIA','18','63','SECOND FLOOR','sf-left-top-green','CTRL-TESTSF-214-2',1,'finished',7,'JanaDulog','2026-07-09 14:30:53','2026-07-09 06:30:53','2026-07-09 06:30:53','TIR-214A',NULL),(25,'PADRE GARCIA','18','63','SECOND FLOOR','sf-right-top-green','CTRL-TESTSF-214-3',1,'finished',7,'JanaDulog','2026-07-09 14:31:03','2026-07-09 06:31:03','2026-07-09 06:31:03','TIR-214A',NULL),(26,'PADRE GARCIA','18','63','SECOND FLOOR','sf-center-blue-1','CTRL-TESTSF-213',1,'finished',7,'JanaDulog','2026-07-09 14:33:39','2026-07-09 06:33:39','2026-07-09 06:33:39','TDX-213A',NULL);
/*!40000 ALTER TABLE `connovate_panels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `residents`
--

DROP TABLE IF EXISTS `residents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `residents` (
  `resident_id` int(11) NOT NULL AUTO_INCREMENT,
  `subdivision_id` int(11) DEFAULT NULL,
  `phase` varchar(50) DEFAULT NULL,
  `block_no` varchar(50) DEFAULT NULL,
  `lot_no` varchar(50) DEFAULT NULL,
  `tct_no` varchar(100) DEFAULT NULL,
  `tct_file` varchar(255) DEFAULT NULL,
  `buyer_name` varchar(255) DEFAULT NULL,
  `new_buyer_assumed` varchar(255) DEFAULT NULL,
  `buyer_representative` varchar(255) DEFAULT NULL,
  `contact_no` varchar(50) DEFAULT NULL,
  `social_media` varchar(255) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `account_address` text DEFAULT NULL,
  `resident_status` enum('Active','Inactive','Moved Out') DEFAULT 'Active',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`resident_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `residents`
--

LOCK TABLES `residents` WRITE;
/*!40000 ALTER TABLE `residents` DISABLE KEYS */;
INSERT INTO `residents` VALUES (3,1,'phase 1','18','62','','','Andress Jose Bonifacio','','','0917-123-4567','','andressjose@example.com','ACC-IM-2026-001','','Active','','2026-03-17 16:00:00'),(15,1,'phase 1','18','61','',NULL,'Albert Poblacion','','','0929-263-4567','','AlbertPoblacion@example.com','ACC-IM-2026-005','','Active','','2026-03-24 16:00:00'),(18,1,'phase 1','18','63','','','Franz Matthew','','','0917-148-4567','','FranzMatt@example.com','ACC-IM-2026-003','','Active','','2026-03-28 16:00:00'),(21,1,'phase 1','23','12','4234','','HUEHUE','','','09171484567','','huehue@example.com','ACC-IM-2026-003','','Active','','2026-06-25 16:00:00'),(22,1,'phase 1','23','11','4234','','jiji','','','09171484567','','huehue@example.com','ACC-IM-2026-003','','Active','','2026-06-25 16:00:00');
/*!40000 ALTER TABLE `residents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solar_panel_parts`
--

DROP TABLE IF EXISTS `solar_panel_parts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solar_panel_parts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resident_id` int(11) DEFAULT NULL,
  `project_name` varchar(100) NOT NULL,
  `block_no` varchar(50) NOT NULL,
  `lot_no` varchar(50) NOT NULL,
  `solar_type` enum('Hybrid','Grid-Tied') DEFAULT 'Grid-Tied',
  `part_name` varchar(100) NOT NULL,
  `solar_status` enum('Not Installed','Installed') DEFAULT 'Not Installed',
  `installation_date` date DEFAULT NULL,
  `provider` varchar(150) DEFAULT NULL,
  `capacity_details` varchar(255) DEFAULT NULL,
  `proof_file` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_solar_part` (`project_name`,`block_no`,`lot_no`,`part_name`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solar_panel_parts`
--

LOCK TABLES `solar_panel_parts` WRITE;
/*!40000 ALTER TABLE `solar_panel_parts` DISABLE KEYS */;
INSERT INTO `solar_panel_parts` VALUES (15,18,'PADRE GARCIA','18','63','Hybrid','Solar Inverter','Installed',NULL,'','','','','2026-07-08 06:32:32','2026-07-08 06:32:32'),(16,18,'PADRE GARCIA','18','63','Hybrid','Battery Inverter','Installed',NULL,'','','','','2026-07-08 06:32:38','2026-07-08 06:32:38'),(17,18,'PADRE GARCIA','18','63','Hybrid','Electrical Cables','Installed',NULL,'','','','','2026-07-08 06:32:44','2026-07-08 06:32:44'),(18,18,'PADRE GARCIA','18','63','Hybrid','Mounting Structure','Installed',NULL,'','','','','2026-07-08 06:32:51','2026-07-08 06:32:51'),(19,18,'PADRE GARCIA','18','63','Hybrid','Electrical Devices','Installed',NULL,'','','','','2026-07-08 06:32:58','2026-07-08 06:32:58'),(20,18,'PADRE GARCIA','18','63','Hybrid','Net Metering','Installed',NULL,'','','','','2026-07-08 06:34:02','2026-07-08 06:34:02');
/*!40000 ALTER TABLE `solar_panel_parts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solar_panels_backup`
--

DROP TABLE IF EXISTS `solar_panels_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solar_panels_backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resident_id` int(11) DEFAULT NULL,
  `project_name` varchar(100) DEFAULT NULL,
  `block_no` varchar(50) DEFAULT NULL,
  `lot_no` varchar(50) DEFAULT NULL,
  `solar_status` varchar(30) DEFAULT 'Not Installed',
  `installation_date` date DEFAULT NULL,
  `provider` varchar(150) DEFAULT NULL,
  `capacity_details` varchar(255) DEFAULT NULL,
  `proof_file` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solar_panels_backup`
--

LOCK TABLES `solar_panels_backup` WRITE;
/*!40000 ALTER TABLE `solar_panels_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `solar_panels_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subdivisions`
--

DROP TABLE IF EXISTS `subdivisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subdivisions` (
  `subdivision_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_name` varchar(100) NOT NULL,
  `map_path` varchar(255) DEFAULT NULL,
  `map_width` int(11) DEFAULT 2000,
  `map_height` int(11) DEFAULT 1500,
  PRIMARY KEY (`subdivision_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subdivisions`
--

LOCK TABLES `subdivisions` WRITE;
/*!40000 ALTER TABLE `subdivisions` DISABLE KEYS */;
INSERT INTO `subdivisions` VALUES (1,'PADRE GARCIA','subdivision1.png',2000,1500),(2,'VIA VERDE STO. TOMAS BATANGAS','VVST3-SDP_page-0001.jpg',2000,1500),(3,'Imperial Meadows','ISM SITE MAP.jpg',2000,1500),(4,'Brgy. Tartaria','Silang Cavite.jpg',2000,1500),(5,'Rancho Imperial','Rancho imperial de Silang-Model with color.jpg',2000,1500),(6,'Tagaytay Meridien','Tagaytay Meridien map 1.jpg',2000,1500),(7,'The Venetto Heights','The-Venetto-Heights-Updated-2014-Model.jpg',2000,1500),(8,'Trece Martires','W-Trece Martires.jpg',2000,1500),(10,'Priya Meridian','Priya Meridian.jpg',2000,1500),(11,'Brgy. STO.Domingo','BrgySTO.Domingo,IrigaCity.jpg',2000,1500),(12,'Brgy. Estanza','BRGY. ESTANZA LEGAZPI CITY.jpg',2000,1500),(13,'Homapon Legazpi City','HOMAPON LEGAZPI CITY.jpg',2000,1500),(14,'VHS PH 2','VHS PH 2.JPG',2000,1500),(15,'Sorsogon','Sorsogon - with alteration_page-0001.jpg',2000,1500),(16,'Buragwis','Buragwis_page-0001.jpg',2000,1500),(17,'Estanza PH 1 & 2','Estanza ph 1 & 2_page-0001.jpg',2000,1500),(18,'Estanza Phase 1','Estanza Phase 1_page-0001.jpg',2000,1500),(19,'Iriga Phase 1','Iriga Phase 1_page-0001.jpg',2000,1500),(20,'Labo','Labo_page-0001.jpg',2000,1500),(21,'LeGrand 1 & 2','LeGrand 1 & 2_page-0001.jpg',2000,1500),(22,'OLV Buragwis','OLV Buragwis_page-0001.jpg',2000,1500),(23,'Polangui','Polangui_page-0001.jpg',2000,1500),(24,'San Fernando','San Fernando_page-0001.jpg',2000,1500);
/*!40000 ALTER TABLE `subdivisions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utility_bills`
--

DROP TABLE IF EXISTS `utility_bills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utility_bills` (
  `bill_id` int(11) NOT NULL AUTO_INCREMENT,
  `resident_id` int(11) DEFAULT NULL,
  `subdivision_id` int(11) DEFAULT NULL,
  `electric_provider` varchar(100) DEFAULT NULL,
  `water_provider` varchar(100) DEFAULT NULL,
  `bill_date` date DEFAULT NULL,
  `billing_period` varchar(100) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `prev_reading` decimal(10,2) DEFAULT 0.00,
  `present_reading` decimal(10,2) DEFAULT 0.00,
  `consumption` decimal(10,2) DEFAULT 0.00,
  `cost_per_cubic_meter` decimal(10,2) DEFAULT 0.00,
  `current_bill` decimal(10,2) DEFAULT 0.00,
  `previous_bill_balance` decimal(10,2) DEFAULT 0.00,
  `total_bill` decimal(10,2) DEFAULT 0.00,
  `penalty_fee` decimal(10,2) DEFAULT 0.00,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `remaining_balance` decimal(10,2) DEFAULT 0.00,
  `bill_status` enum('Paid','Unpaid','Overdue','Pending') DEFAULT 'Unpaid',
  `payment_date` datetime DEFAULT NULL,
  PRIMARY KEY (`bill_id`),
  KEY `resident_id` (`resident_id`),
  KEY `fk_bill_subdivision` (`subdivision_id`),
  CONSTRAINT `fk_bill_subdivision` FOREIGN KEY (`subdivision_id`) REFERENCES `subdivisions` (`subdivision_id`),
  CONSTRAINT `utility_bills_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`resident_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utility_bills`
--

LOCK TABLES `utility_bills` WRITE;
/*!40000 ALTER TABLE `utility_bills` DISABLE KEYS */;
/*!40000 ALTER TABLE `utility_bills` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-24  9:40:50
