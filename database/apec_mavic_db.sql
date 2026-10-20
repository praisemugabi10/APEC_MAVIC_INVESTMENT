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
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff` (
  `staff_id` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` char(12) NOT NULL,
  `type` enum('field_officer','cashier','manager','boss','other') NOT NULL DEFAULT 'other',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`staff_id`),
  UNIQUE KEY `uq_staff_email` (`email`),
  UNIQUE KEY `uq_staff_phone` (`phone_number`),
  CONSTRAINT `chk_staff_phone` CHECK (
    regexp_like(
      `phone_number`,
      _utf8mb4'^(0(7|1)[0-9]{8}|256(7|1)[0-9]{8})$'
    )
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `user_id` varchar(50) NOT NULL,
  `staff_id` varchar(50) NOT NULL,
  `role` enum('field_officer','cashier','manager','other') NOT NULL DEFAULT 'other',
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_user_staff` (`staff_id`),
  KEY `fk_user_staff` (`staff_id`),
  CONSTRAINT `fk_user_staff`
    FOREIGN KEY (`staff_id`)
    REFERENCES `staff` (`staff_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  CONSTRAINT `fk_transaction_category`
    FOREIGN KEY (`transaction_category_id`)
    REFERENCES `transaction_category` (`transaction_category_id`),
  CONSTRAINT `fk_transaction_recorded_by`
    FOREIGN KEY (`transaction_recorded_by`)
    REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `staff_id` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`client_id`),
  KEY `fk_client_staff` (`staff_id`),
  CONSTRAINT `fk_client_staff`
    FOREIGN KEY (`staff_id`)
    REFERENCES `staff` (`staff_id`),
  CONSTRAINT `chk_client_phone_primary` CHECK (
    regexp_like(
      `phone_primary`,
      _utf8mb4'^(0(7|1)[0-9]{8}|256(7|1)[0-9]{8})$'
    )
  ),
  CONSTRAINT `chk_client_phone_secondary` CHECK (
    (
      `phone_secondary` IS NULL
      OR regexp_like(
        `phone_secondary`,
        _utf8mb4'^(0(7|1)[0-9]{8}|256(7|1)[0-9]{8})$'
      )
    )
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  CONSTRAINT `fk_client_transaction_client`
    FOREIGN KEY (`client_transaction_client_id`)
    REFERENCES `client` (`client_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_client_transaction_transaction`
    FOREIGN KEY (`client_transaction_transaction_id`)
    REFERENCES `transaction` (`transaction_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `staff_transaction`
--

DROP TABLE IF EXISTS `staff_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_transaction` (
  `staff_transaction_id` varchar(50) NOT NULL,
  `staff_transaction_transaction_id` varchar(50) NOT NULL,
  `staff_transaction_staff_id` varchar(50) NOT NULL,
  PRIMARY KEY (`staff_transaction_id`),
  UNIQUE KEY `uq_staff_transaction_transaction` (`staff_transaction_transaction_id`),
  KEY `fk_staff_transaction_staff` (`staff_transaction_staff_id`),
  CONSTRAINT `fk_staff_transaction_staff`
    FOREIGN KEY (`staff_transaction_staff_id`)
    REFERENCES `staff` (`staff_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_staff_transaction_transaction`
    FOREIGN KEY (`staff_transaction_transaction_id`)
    REFERENCES `transaction` (`transaction_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  CONSTRAINT `chk_guarantor_phone` CHECK (
    regexp_like(
      `phone_number`,
      _utf8mb4'^(0(7|1)[0-9]{8}|256(7|1)[0-9]{8})$'
    )
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  CONSTRAINT `fk_loan_client`
    FOREIGN KEY (`client_id`)
    REFERENCES `client` (`client_id`),
  CONSTRAINT `fk_loan_guarantor`
    FOREIGN KEY (`guarantor_id`)
    REFERENCES `guarantor` (`guarantor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment` (
  `payment_id` varchar(50) NOT NULL,
  `loan_id` varchar(50) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `collected_by` varchar(50) NOT NULL,
  `recorded_by` varchar(50) NOT NULL,
  `amount_paid` decimal(15,2) NOT NULL,
  `balance_at_time` decimal(15,2) NOT NULL,
  `date_paid` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  UNIQUE KEY `uq_payment_transaction` (`transaction_id`),
  KEY `fk_payment_loan` (`loan_id`),
  KEY `fk_payment_transaction` (`transaction_id`),
  KEY `fk_payment_collected_by` (`collected_by`),
  KEY `fk_payment_recorded_by` (`recorded_by`),
  CONSTRAINT `fk_payment_loan`
    FOREIGN KEY (`loan_id`)
    REFERENCES `loan` (`loan_id`),
  CONSTRAINT `fk_payment_transaction`
    FOREIGN KEY (`transaction_id`)
    REFERENCES `transaction` (`transaction_id`),
  CONSTRAINT `fk_payment_collected_by`
    FOREIGN KEY (`collected_by`)
    REFERENCES `staff` (`staff_id`),
  CONSTRAINT `fk_payment_recorded_by`
    FOREIGN KEY (`recorded_by`)
    REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  CONSTRAINT `fk_email_verification_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`user_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-09-26