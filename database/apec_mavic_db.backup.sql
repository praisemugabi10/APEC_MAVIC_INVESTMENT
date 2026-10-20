/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: mysql    Database: apec_mavic_db
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `client` (
  `client_id` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone_primary` varchar(15) NOT NULL,
  `phone_secondary` varchar(15) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `officer_id` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`client_id`),
  KEY `fk_client_officer` (`officer_id`),
  CONSTRAINT `fk_client_officer` FOREIGN KEY (`officer_id`) REFERENCES `field_officer` (`officer_id`),
  CONSTRAINT `chk_client_phone_primary` CHECK (regexp_like(`phone_primary`,_utf8mb4'^(0(7|1)[0-9]{8}|256(7|1)[0-9]{8})$')),
  CONSTRAINT `chk_client_phone_secondary` CHECK (((`phone_secondary` is null) or regexp_like(`phone_secondary`,_utf8mb4'^(0(7|1)[0-9]{8}|256(7|1)[0-9]{8})$')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `client` WRITE;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
/*!40000 ALTER TABLE `client` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `client_transaction`
--

DROP TABLE IF EXISTS `client_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_transaction` (
  `client_transaction_id` varchar(50) NOT NULL,
  `client_transaction_transaction_id` varchar(50) NOT NULL,
  `client_transaction_client_id` varchar(50) NOT NULL,
  PRIMARY KEY (`client_transaction_id`),
  UNIQUE KEY `uq_client_transaction_transaction` (`client_transaction_transaction_id`),
  KEY `fk_client_transaction_client` (`client_transaction_client_id`),
  CONSTRAINT `fk_client_transaction_client` FOREIGN KEY (`client_transaction_client_id`) REFERENCES `client` (`client_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_client_transaction_transaction` FOREIGN KEY (`client_transaction_transaction_id`) REFERENCES `transaction` (`transaction_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_transaction`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `client_transaction` WRITE;
/*!40000 ALTER TABLE `client_transaction` DISABLE KEYS */;
/*!40000 ALTER TABLE `client_transaction` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `email_verifications`
--

DROP TABLE IF EXISTS `email_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_verifications` (
  `verification_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `verified` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`verification_id`),
  KEY `fk_email_verification_user` (`user_id`),
  CONSTRAINT `fk_email_verification_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_verifications`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `email_verifications` WRITE;
/*!40000 ALTER TABLE `email_verifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_verifications` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `field_officer`
--

DROP TABLE IF EXISTS `field_officer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `field_officer` (
  `officer_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `phone_number` char(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`officer_id`),
  UNIQUE KEY `uq_field_officers_email` (`email`),
  UNIQUE KEY `uq_field_officers_phone` (`phone_number`),
  CONSTRAINT `chk_field_officers_phone` CHECK (regexp_like(`phone_number`,_utf8mb4'^(0(7|1)[0-9]{8}|256(7|1)[0-9]{8})$'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `field_officer`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `field_officer` WRITE;
/*!40000 ALTER TABLE `field_officer` DISABLE KEYS */;
/*!40000 ALTER TABLE `field_officer` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `field_officer_transaction`
--

DROP TABLE IF EXISTS `field_officer_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `field_officer_transaction` (
  `field_officer_transaction_id` varchar(50) NOT NULL,
  `field_officer_transaction_transaction_id` varchar(50) NOT NULL,
  `field_officer_transaction_officer_id` varchar(50) NOT NULL,
  PRIMARY KEY (`field_officer_transaction_id`),
  UNIQUE KEY `uq_field_officer_transaction_transaction` (`field_officer_transaction_transaction_id`),
  KEY `fk_field_officer_transaction_officer` (`field_officer_transaction_officer_id`),
  CONSTRAINT `fk_field_officer_transaction_officer` FOREIGN KEY (`field_officer_transaction_officer_id`) REFERENCES `field_officer` (`officer_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_field_officer_transaction_transaction` FOREIGN KEY (`field_officer_transaction_transaction_id`) REFERENCES `transaction` (`transaction_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `field_officer_transaction`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `field_officer_transaction` WRITE;
/*!40000 ALTER TABLE `field_officer_transaction` DISABLE KEYS */;
/*!40000 ALTER TABLE `field_officer_transaction` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `guarantor`
--

DROP TABLE IF EXISTS `guarantor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `guarantor` (
  `guarantor_id` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  PRIMARY KEY (`guarantor_id`),
  CONSTRAINT `chk_guarantor_phone` CHECK (regexp_like(`phone_number`,_utf8mb4'^(0(7|1)[0-9]{8}|256(7|1)[0-9]{8})$'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guarantor`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `guarantor` WRITE;
/*!40000 ALTER TABLE `guarantor` DISABLE KEYS */;
/*!40000 ALTER TABLE `guarantor` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `loan`
--

DROP TABLE IF EXISTS `loan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `loan` (
  `loan_id` varchar(50) NOT NULL,
  `client_id` varchar(50) NOT NULL,
  `guarantor_id` varchar(50) NOT NULL,
  `principal_amount` decimal(15,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `total_repayable` decimal(15,2) NOT NULL,
  `date_taken` date NOT NULL,
  `loan_status` enum('active','cleared','defaulted') DEFAULT 'active',
  PRIMARY KEY (`loan_id`),
  KEY `fk_loan_client` (`client_id`),
  KEY `fk_loan_guarantor` (`guarantor_id`),
  CONSTRAINT `fk_loan_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`),
  CONSTRAINT `fk_loan_guarantor` FOREIGN KEY (`guarantor_id`) REFERENCES `guarantor` (`guarantor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loan`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `loan` WRITE;
/*!40000 ALTER TABLE `loan` DISABLE KEYS */;
/*!40000 ALTER TABLE `loan` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment` (
  `payment_id` varchar(50) NOT NULL,
  `loan_id` varchar(50) NOT NULL,
  `amount_paid` decimal(15,2) NOT NULL,
  `balance_at_time` decimal(15,2) NOT NULL,
  `date_paid` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  KEY `fk_payment_loan` (`loan_id`),
  CONSTRAINT `fk_payment_loan` FOREIGN KEY (`loan_id`) REFERENCES `loan` (`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `payment` WRITE;
/*!40000 ALTER TABLE `payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `transaction`
--

DROP TABLE IF EXISTS `transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaction` (
  `transaction_id` varchar(50) NOT NULL,
  `transaction_type` enum('cash_in','cash_out') NOT NULL,
  `transaction_category_id` varchar(50) NOT NULL,
  `transaction_amount` decimal(15,2) NOT NULL,
  `transaction_description` text,
  `transaction_recorded_by` varchar(50) NOT NULL,
  `transaction_trxn_date` datetime NOT NULL,
  `transaction_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `transaction_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_id`),
  KEY `fk_transaction_category` (`transaction_category_id`),
  KEY `fk_transaction_recorded_by` (`transaction_recorded_by`),
  CONSTRAINT `fk_transaction_category` FOREIGN KEY (`transaction_category_id`) REFERENCES `transaction_category` (`transaction_category_id`),
  CONSTRAINT `fk_transaction_recorded_by` FOREIGN KEY (`transaction_recorded_by`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `transaction` WRITE;
/*!40000 ALTER TABLE `transaction` DISABLE KEYS */;
/*!40000 ALTER TABLE `transaction` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `transaction_category`
--

DROP TABLE IF EXISTS `transaction_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaction_category` (
  `transaction_category_id` varchar(50) NOT NULL,
  `transaction_category_name` varchar(100) NOT NULL,
  `transaction_category_description` text,
  `transaction_category_type` enum('cash_in','cash_out') NOT NULL,
  `transaction_category_is_active` enum('1','0') NOT NULL DEFAULT '1',
  `transaction_category_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `transaction_category_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_category_id`),
  UNIQUE KEY `uq_transaction_category_name` (`transaction_category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_category`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `transaction_category` WRITE;
/*!40000 ALTER TABLE `transaction_category` DISABLE KEYS */;
INSERT INTO `transaction_category` VALUES
('TRXNCAT6a8b5787611be','Bank Depositt','money taken at the bank at the end of the day&amp;amp;apos;','cash_out','0','2026-08-23 20:26:47','2026-08-28 17:42:25'),
('TRXNCAT6a8b57c32642b','field officer','money taken by field officer','cash_out','0','2026-08-23 20:27:47','2026-09-08 21:48:21'),
('TRXNCAT6a8b57d1bcf14','Bank Deposit&apos;',NULL,'cash_out','0','2026-08-23 20:28:01','2026-08-28 17:42:25'),
('TRXNCAT6a8b594add50c','Bank Deposit','money deposited in the bank at the end of the day','cash_out','1','2026-08-23 20:34:18','2026-08-28 17:42:25'),
('TRXNCAT6a8b59626bb2d','Office Expense','money spent on matters in the office','cash_out','1','2026-08-23 20:34:42','2026-08-28 17:42:25'),
('TRXNCAT6a8b59863f52c','client payment','money received from clients','cash_in','1','2026-08-23 20:35:18','2026-08-23 20:41:35'),
('TRXNCAT6a8b59998db47','capital from boss','money put to business from the manager/boss','cash_in','1','2026-08-23 20:35:37','2026-08-23 20:41:20'),
('TRXNCAT6a8b59f6d426a','will be deleted',NULL,'cash_in','0','2026-08-23 20:37:10','2026-08-23 20:37:18'),
('TRXNCAT6a91cbc70b8bb','loan repayment','loan repaid from client','cash_out','0','2026-08-28 17:56:23','2026-08-28 17:56:46'),
('TRXNCAT6aa082e1c8912','Client cash out','Client has taken the loan','cash_out','0','2026-09-08 21:49:21','2026-09-12 20:13:40'),
('TRXNCAT6aa087e23d891','Payment of demand','In case of a file officer or manager','cash_in','1','2026-09-08 22:10:42','2026-09-08 22:11:31'),
('TRXNCAT6aa5b595e0dda','client cashout','money taken by client.','cash_out','1','2026-09-12 20:27:01','2026-09-12 20:27:01');
/*!40000 ALTER TABLE `transaction_category` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `user_id` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `role` enum('cashier','manager','boss') DEFAULT 'cashier',
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone_number` (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES
('USR6a6b4a3d4839f','mugabi','praise','mugabipraise4@gmail.com','256764433174','cashier','$argon2id$v=19$m=65536,t=4,p=1$UmVVV0xMTHdwSTguUVFhTQ$kodypLgsc/XspWk1RMNIbT0cE5OfEtLIaZlMcFt30Z8','2026-07-30 12:57:33'),
('USR6a96aa51bd855','MUGABI','PRAISE','praisemugabi10@gmail.com','256702133121','cashier','$argon2id$v=19$m=65536,t=4,p=1$bDFtMlltL3dqVlZMZjdyeQ$mIDnaDaFV/lHeMidZOKwmzKCK0R2pekgMRDTAe1hi7U','2026-09-01 10:34:57'),
('USR6a96aae3b726e','Tatitwe','Danson','tatitwedanson2@gmail.com','256790204502','manager','$argon2id$v=19$m=65536,t=4,p=1$NUhXSi43d2FoY29uZE5iTQ$epLir42Z9rGRCna3nd4tjwmQM1OafVCANoppstFZLFE','2026-09-01 10:37:23');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-09-14 13:27:22
