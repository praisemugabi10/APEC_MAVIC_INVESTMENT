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
  `mortgage` varchar(255) DEFAULT NULL,
  `staff_id` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`client_id`),
  KEY `fk_client_staff` (`staff_id`),
  CONSTRAINT `fk_client_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`),
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
INSERT INTO `client` VALUES
('CL_6ab84846e740a','Mugwanya','Anthony','256778788788','256778989098','mississipi','Boda Boda&#039;','STF_6ab845c346a4f','2026-09-26 22:33:42',1),
('CL_6ab848a264d92','Murokore','ivan','256774744744',NULL,'michgan',NULL,'STF_6ab845c346a4f','2026-09-26 22:35:14',1),
('CL_6ab84935d020d','wer','wer','256777777777',NULL,'wer','plot of land','STF_6ab845c346a4f','2026-09-26 22:37:41',1),
('CL_6ab84b00a4049','akampa','livingstone','256779799799','256778432345','mississipi','car','STF6ab845f430b2c','2026-09-26 22:45:20',1),
('CL_6ac2375db87b2','kalanzi','joseph','256778788788',NULL,'michigan','tractor','STF6ac236e27168c','2026-10-04 11:24:13',1),
('CL_6ac23778bb2f9','another','client','256778788788',NULL,'mississipi','plot of land','STF6ac236e27168c','2026-10-04 11:24:40',1),
('CL_6ac29fe3e7c08','fifth','client','256778788383',NULL,'michi',NULL,'STF6ac236e27168c','2026-10-04 18:50:11',1),
('CL_6ac2a968719ae','prosper','amuntuz','256787877877',NULL,'mishg','car','STF6ac236e27168c','2026-10-04 19:30:48',1),
('CL_6ac3bfcb81566','New','client','256789898765',NULL,'mississipi','car and plot','STF6ac236e27168c','2026-10-05 15:18:35',1),
('CL_6ac8f1a3b57eb','client','one','256773645343',NULL,'mississipi','car','STF6ac8e95ccf24f','2026-10-09 13:52:35',1),
('CL_6ac8f1cad38be','client','two','256765432123',NULL,'michigan','car','STF6ac8e95ccf24f','2026-10-09 13:53:14',1),
('CL_6acc067658cf5','MWESIGYE','Saphan','256782971335',NULL,'Mbiiko','Fridge','STF6ac8e95ccf24f','2026-10-11 21:58:14',1);
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
INSERT INTO `client_transaction` VALUES
('CTRXN_6ab849ab31667','TRXN_6ab849ab2f979','CL_6ab848a264d92'),
('CTRXN_6ab849fcec4ed','TRXN_6ab849fcec1a5','CL_6ab84846e740a'),
('CTRXN_6ab84a2513679','TRXN_6ab84a25133d2','CL_6ab84846e740a'),
('CTRXN_6ab84a8ea3aa5','TRXN_6ab84a8ea3754','CL_6ab848a264d92'),
('CTRXN_6abe53f06e285','TRXN_6abe53f06db43','CL_6ab848a264d92'),
('CTRXN_6abe53f87738e','TRXN_6abe53f876fd3','CL_6ab84846e740a'),
('CTRXN_6abe6d0a9eda5','TRXN_6abe6d0a9eadf','CL_6ab84b00a4049'),
('CTRXN_6abe732c16d71','TRXN_6abe732c16aca','CL_6ab848a264d92'),
('CTRXN_6ac163a642a34','TRXN_6ac163a6421ce','CL_6ab848a264d92'),
('CTRXN_6ac16437581be','TRXN_6ac1643757db1','CL_6ab848a264d92'),
('CTRXN_6ac2378bd2e88','TRXN_6ac2378bd1c9d','CL_6ac23778bb2f9'),
('CTRXN_6ac29f6e2d782','TRXN_6ac29f6e2cf77','CL_6ac2375db87b2'),
('CTRXN_6ac2a98340f5e','TRXN_6ac2a98340cf8','CL_6ac2a968719ae'),
('CTRXN_6ac2a9a1912c4','TRXN_6ac2a9a190ef4','CL_6ac29fe3e7c08'),
('CTRXN_6ac2ac74dd526','TRXN_6ac2ac74dd284','CL_6ab84b00a4049'),
('CTRXN_6ac3b0761cd9b','TRXN_6ac3b0761bb6c','CL_6ac29fe3e7c08'),
('CTRXN_6ac3b0e547cf3','TRXN_6ac3b0e547a8b','CL_6ac2a968719ae'),
('CTRXN_6ac3c1a67f3c7','TRXN_6ac3c1a67f0d1','CL_6ab84b00a4049'),
('CTRXN_6ac81aed12e16','TRXN_6ac81aed10870','CL_6ac23778bb2f9'),
('CTRXN_6ac8f1e5e2933','TRXN_6ac8f1e5e220c','CL_6ac8f1cad38be'),
('CTRXN_6ac8f2af82ba0','TRXN_6ac8f2af82930','CL_6ac8f1a3b57eb'),
('CTRXN_6acc05b198d5f','TRXN_6acc05b19851d','CL_6ac8f1cad38be'),
('CTRXN_6acc0a491de0a','TRXN_6acc0a491db49','CL_6acc067658cf5'),
('CTRXN_6acc0bd5eaed9','TRXN_6acc0bd5eabca','CL_6acc067658cf5'),
('CTRXN_6acc0e544017e','TRXN_6acc0e543fde7','CL_6acc067658cf5'),
('CTRXN_6acc0f1d7f8ff','TRXN_6acc0f1d7f648','CL_6acc067658cf5'),
('CTRXN_6ad1766ea0a53','TRXN_6ad1766e9f480','CL_6acc067658cf5');
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
INSERT INTO `email_verifications` VALUES
('VER6ab7d07bb2f83','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$Zk91b3YvUkNPZ24yQ2VESA$SBlWsmL+h8/t10Kez7HB3ekRm62xjskzTA6ECv+s7LE','2026-09-26 14:07:35',1,'2026-09-26 14:02:35'),
('VER6ab838e46fd3f','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$MmI0U2tQR1h2ZGlSMVdVcw$2pTqOXUV4909xBM265okY0XPzCuH+N7PZKjKdQnhnJ8','2026-09-26 21:33:04',1,'2026-09-26 21:28:04'),
('VER6abe536331395','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$bTRoTmxJNEo0SzU0TjFsaQ$5RJesCbLUZ/1lQwc7dQTfKszNbQsv9+RUPwfx5oM/ow','2026-10-01 12:39:43',1,'2026-10-01 12:34:43'),
('VER6abf4e5a58718','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$Z2JEZkw4NFZvVGdidDhpZA$TYpv4UyPabPj6IdpnzJH6kwQdFCt3/43SKx2b6XCOYo','2026-10-02 06:30:30',1,'2026-10-02 06:25:30'),
('VER6abf5021c6ce3','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$SzdWVDdmSC9wSmdDeVZYWQ$JwUuVi52TMfzRY3WhpAj+7iMxbHWt8sl6S9/Aq+Yd6I','2026-10-02 06:38:05',1,'2026-10-02 06:33:05'),
('VER6abf519e3f927','USR6abf510899546','$argon2id$v=19$m=65536,t=4,p=1$bDAuQnByenBEZkVMbWFJbg$E+0lwWyqUYaDfdS+eEqdMY8LxDZLX0LdJHpPzP03LXE','2026-10-02 06:44:26',1,'2026-10-02 06:39:26'),
('VER6abf531b3f778','USR6abf510899546','$argon2id$v=19$m=65536,t=4,p=1$QnE5LzVjemxZWGFJOEVDaQ$ep/9hBSJfQrPQ2SG+NxMvdGdyH8DIZX2JD/ph4aP2mc','2026-10-02 06:50:47',1,'2026-10-02 06:45:47'),
('VER6ac161b2f3d06','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$dGRtZUM4REI2ZnlPZllBaA$uqhP0dbp0Sg+/QGtXkg5lEMmbDWNgd7FQnPBxEifgGk','2026-10-03 20:17:34',1,'2026-10-03 20:12:35'),
('VER6ac21a3a78de1','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$YkxwUGE0NjJGdDZCZ25SMw$MSio+wOZraSMxsfN2K93avuG70l0+ATXK98Mq+82gSI','2026-10-04 09:24:54',0,'2026-10-04 09:19:54'),
('VER6ac21a5e8e467','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$c1FISVROaWRwTnJpaE5HRA$ObzBfinCNoNFWheRwrVbZFZmAXi7KGnd6nCNQUPEhYI','2026-10-04 09:25:30',1,'2026-10-04 09:20:30'),
('VER6ac234fe71e10','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$aDhZNDVnWmJnbm5QSHdWNw$i69dd7XFq7FXZr+aKN21nIw5e4nlWv4lm6AuGaKognE','2026-10-04 11:19:06',1,'2026-10-04 11:14:06'),
('VER6ac29e8840913','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$TUtSLjlreWpSbVlzVTlxZQ$+nmxBxUuVpQZnK9+sJUScU3rNm/Xe6v9Y0wYG3cfD8c','2026-10-04 18:49:24',1,'2026-10-04 18:44:24'),
('VER6ac29f3a5f8c6','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$WmdrMWxwbjQ1UEpLUnRsaw$4uXNywwI6bHEhkRS7MOn8BaCA0hQKNgrkBOmX81051Q','2026-10-04 18:52:22',1,'2026-10-04 18:47:22'),
('VER6ac2b307ed20d','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$YTl4dDV5WUdSajdTdWQ0Wg$iCGKtRx+TVB56PlWlavzBYL0Cfqb0j8IVIQbaHG9YJw','2026-10-04 20:16:51',1,'2026-10-04 20:11:51'),
('VER6ac35f29dcbf8','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$aDRhei5RLnRiamg0Y1NrOA$EJUyAIGFERToSdh3Yms+91GZ05vmzFUaW+6703xcDfk','2026-10-05 08:31:17',1,'2026-10-05 08:26:17'),
('VER6ac37df5f2aca','USR_789b61379bc6ff39f009c0d53d9c4869','$argon2id$v=19$m=65536,t=4,p=1$aTJyODh4UFVzTHkyL2xtQg$t/w6mHavqW+E44YBHmvMcZD0RxFD3J27FTEIi0rL3BY','2026-10-05 10:42:41',1,'2026-10-05 10:37:41'),
('VER6ac37e3f6167f','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$NlNXS3F4VlRTQk03ZkxYdQ$0xqmE+sC2rhvjTdionCylUvVYdgH50Sll0gx5QbG4WY','2026-10-05 10:43:55',1,'2026-10-05 10:38:55'),
('VER6ac3a0234f915','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$bnJuRXVmbmhlV1BkSkszNw$2ERxEwmeDYkCtnu3i7uXQI9Rn4gP+ZgAwOC8Dzubrfo','2026-10-05 13:08:31',1,'2026-10-05 13:03:31'),
('VER6ac3a1be23a8b','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$T3o2REtueThnMVk2ZlFiSg$DWkqGU2CUmgHKNOvvSKOm8pxx2nyrYumYydP/nNAR9Q','2026-10-05 13:15:22',0,'2026-10-05 13:10:22'),
('VER6ac3a22600064','USR_789b61379bc6ff39f009c0d53d9c4869','$argon2id$v=19$m=65536,t=4,p=1$Zi9KcXZmTW1hcXdVSE9HUQ$OUM7hBkl3rNM1gNv34HiY1VY3gCESMYXokuhCnEOk0Q','2026-10-05 13:17:06',1,'2026-10-05 13:12:06'),
('VER6ac5412ae7bf8','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$Tll6Zmx1aUo5b091ekZsMQ$eti459OjKpY7nok4Eg2aDJVCY46CrLr6Nj4ewBO03nA','2026-10-06 18:47:50',1,'2026-10-06 18:42:50'),
('VER6ac6288a0457b','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$blhucFpqYUwyV21BZVZqNA$WBwt1jtleJ2iygF4ZrGP6YQif7/nNwS9Cyj2HMbA3q0','2026-10-07 11:15:02',1,'2026-10-07 11:10:02'),
('VER6ac6321e95263','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$YnFyUTZ3VTgydFlnNWpyLg$UJVG223VLX5Glh/zD1jt/2IVNTHcSdseIEkuTc/bg2A','2026-10-07 11:55:54',1,'2026-10-07 11:50:54'),
('VER6ac6326adbeb6','USR_789b61379bc6ff39f009c0d53d9c4869','$argon2id$v=19$m=65536,t=4,p=1$cG9kZ0lwTEV1QTNheWU1Nw$o/P8xxQCUzhVp5GH/4FP877UqVS48zf6evuiub6b9Qg','2026-10-07 11:57:10',1,'2026-10-07 11:52:10'),
('VER6ac632a5824b6','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$cU1QaUJXeHJsMy9tZnc0MQ$BGpYMmZ8+aVBXNWVhPZbbJnVmECZz+DULmGU49ipNGc','2026-10-07 11:58:09',1,'2026-10-07 11:53:09'),
('VER6ac636f090d81','USR_28573afc0add81599705391e32eea405','$argon2id$v=19$m=65536,t=4,p=1$VkFHQ1NKMDlQQzFhcFhPeg$uQiK/Qdq2jv/uYgSMGRhFcaRAtJ+03hWHT95BelsBro','2026-10-07 12:16:28',0,'2026-10-07 12:11:28'),
('VER6ac63c67b2be3','USR_28573afc0add81599705391e32eea405','$argon2id$v=19$m=65536,t=4,p=1$ak5PYTdBSC5aSW1zQ2x1Sw$MihaE7CdHlJKiP4xGgxGpnftdDtHffjh8Qsrx6YsfC0','2026-10-07 12:39:47',1,'2026-10-07 12:34:47'),
('VER6ac63ebcca8a5','USR_28573afc0add81599705391e32eea405','$argon2id$v=19$m=65536,t=4,p=1$bnZaUkxIU2Uuay5qZnoyTQ$+DkEmZM4PRmJ9BCFvDWhdIPQngrPpSYlg8Q+JeActxM','2026-10-07 12:49:44',1,'2026-10-07 12:44:44'),
('VER6ac68af2171cb','USR_28573afc0add81599705391e32eea405','$argon2id$v=19$m=65536,t=4,p=1$RjgucU5VZjBHT2QxdnUzTQ$pjSwVS4TpBFBkIHR7TDeeKoU+6fRUmh/9JDPPy8UEbM','2026-10-07 18:14:54',0,'2026-10-07 18:09:54'),
('VER6ac68b54a0c1d','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$RE1KcjNnY1NjTk1JbkV1TQ$RVxhrkuAgCCFjqc92Jgj172LfNStEu4XZJ80wPkrJnM','2026-10-07 18:16:32',1,'2026-10-07 18:11:32'),
('VER6ac68b8c65647','USR_28573afc0add81599705391e32eea405','$argon2id$v=19$m=65536,t=4,p=1$OTZhMGNTZ1dHbC4wWC9qMQ$aFbuhM+Poadq65sT75oUNvkRTm0fpKMDB4ej0FSiKjI','2026-10-07 18:17:28',1,'2026-10-07 18:12:28'),
('VER6ac68da1dd999','USR_28573afc0add81599705391e32eea405','$argon2id$v=19$m=65536,t=4,p=1$NDY4ODJ5SUVFd1lCakFxLw$4SJrezMmLJKNKjPoubgmsX24FipBZNfOHxrRonRFagY','2026-10-07 18:26:21',1,'2026-10-07 18:21:21'),
('VER6ac68e0717ed9','USR_28573afc0add81599705391e32eea405','$argon2id$v=19$m=65536,t=4,p=1$SEFtcFdaLkt3c2NUcHFBNQ$qCwinZ5l6jL7jWTWw5zKYFhWTJBgyVRv9Nl4UIUasA4','2026-10-07 18:28:03',0,'2026-10-07 18:23:03'),
('VER6ac6abac0996e','USR_28573afc0add81599705391e32eea405','$argon2id$v=19$m=65536,t=4,p=1$TXZ1emhKZHhqYmExNVZ2cQ$eeszo7bZi5xUGJRuAPRCB4o6u519ZC2b/PWMHWrS0hg','2026-10-07 20:34:32',1,'2026-10-07 20:29:32'),
('VER6ac6abdb40a7c','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$WUVrZ3BGT3VHYUp0WWl6Vg$ck8ALPJGqtTXPu2WF2pQfhbrBoHWrol7anRKqI+XPp8','2026-10-07 20:35:19',1,'2026-10-07 20:30:19'),
('VER6ac803619d0c0','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$ekRpbWVEQWxJaDJ5NFdETA$/JMzegzHxsk3Io1DebbuIm0HNzjV+HaerhnQp00NNZ8','2026-10-08 21:01:01',1,'2026-10-08 20:56:01'),
('VER6ac8070240b53','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$Q0JSdkZ6MklDS1JpeXlYVw$obSZZkoNOf0SboVLN4ImShn3wFmd+/VjUbJZOQEudkw','2026-10-08 21:16:30',1,'2026-10-08 21:11:30'),
('VER6ac8073419049','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$aUNYeTBoUXY4SGttZHVLZg$D9tGmjdg+ipYWlVDAevJjy6wBT95f/+y4FpjtxD/u5w','2026-10-08 21:17:20',0,'2026-10-08 21:12:20'),
('VER6ac807709169f','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$Q3pDWVBsc29RL2hKdG4yNQ$Ds8M6re40r97lEDH4lo9BdovO01xOQ4Xqh7h7Wiysxk','2026-10-08 21:18:20',0,'2026-10-08 21:13:20'),
('VER6ac807d551764','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$NzFNMG9LWHQzUUpFL3cvLw$+hlJV/gt6MII8CTye+F5hbrvrUkLyik5h032ah6R6KU','2026-10-08 21:20:01',1,'2026-10-08 21:15:01'),
('VER6ac80f6c4b87d','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$RnNSam9FSkpKTE9uOHhTRA$rCuT0cZjPUr9mXmGBOI4mlHXuHELOKh4SgwDLQLU9SY','2026-10-08 21:52:24',1,'2026-10-08 21:47:24'),
('VER6ac810bcf346c','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$Y0NnT1hlRndmL3dGcFBVRw$UEdzdA5yFBP7a/PUnuwMaRsOA7gIMdxRp7ujn6PklvI','2026-10-08 21:58:00',1,'2026-10-08 21:53:00'),
('VER6ac81a8da5b6c','USR_789b61379bc6ff39f009c0d53d9c4869','$argon2id$v=19$m=65536,t=4,p=1$UkZCN0V0aExtZ09hUlVyWQ$O8Mgm2kzmuh/GL4dJ4JVwtnuAvLykE6NjJOQKPqW3RA','2026-10-08 22:39:53',1,'2026-10-08 22:34:53'),
('VER6ac8b0bc67ca2','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$bjFJcTM5NGJTUGViaXV2ag$klynh1H8FOtnoJEBT6jSp3DOePl1wNkbKJ9jgblCMnM','2026-10-09 09:20:40',1,'2026-10-09 09:15:40'),
('VER6ac8dc4a08881','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$bGRNV0pwRzRlRzAzTDRlWA$/P2e2OXszqopTTljX+4tVuHK76Xfp1Z4w1Y0wzxtvy0','2026-10-09 12:26:30',1,'2026-10-09 12:21:30'),
('VER6ac8dccdda936','USR_789b61379bc6ff39f009c0d53d9c4869','$argon2id$v=19$m=65536,t=4,p=1$TE8yRGp5ejh4ZVNvL1R5Rw$oUHjv6WgWPtT9mlZzURDBgDglsexpZeTZXR0rUSgEvI','2026-10-09 12:28:41',1,'2026-10-09 12:23:41'),
('VER6ac8f25c654b7','USR_789b61379bc6ff39f009c0d53d9c4869','$argon2id$v=19$m=65536,t=4,p=1$NUtiRW85VklHZFI1WWV0aw$84W9k+XXTV6mCbtEVXeZRQvwvbN/QG3RVsiGfT/Krk0','2026-10-09 14:00:40',1,'2026-10-09 13:55:40'),
('VER6acbe9e495d10','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$aWZpaGNycGs0WWUxM1l6TA$JZ1BEWMObDdgz0NOAnypv0VaVtUv3k5PckldVrsUQFA','2026-10-11 20:01:20',1,'2026-10-11 19:56:20'),
('VER6acbeaa57a60a','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$WEVzSmkuUlpXTGVqYTVubw$oYFrxhlSqmbK4t7NJIl3D4wrlP2FnVZnfgpwYKmxNxQ','2026-10-11 20:04:33',0,'2026-10-11 19:59:33'),
('VER6acbec81600ff','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$UnpOU3UxbEtWS0NNVlFkYQ$6I5q51YhPUj3UwQgoRWyiH+lxRWYmRKgSvSxpL+Fqss','2026-10-11 20:12:29',0,'2026-10-11 20:07:29'),
('VER6acbeca3c52b1','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$TWdjWGRlUmQuUVhmT3BYSA$fj5kVaVP57355lQu5cgdQlWAPOtNkWG6AD6+KSrJ4cg','2026-10-11 20:13:03',0,'2026-10-11 20:08:03'),
('VER6acbed9a8d38e','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$UTNJMzRNUmpSeWVMQXZiTw$7hWcEfMtlZiKGEdoYwKlYPnATac3zftU+uG2XIRRp5s','2026-10-11 20:17:10',1,'2026-10-11 20:12:10'),
('VER6acc0379c0c54','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$QmUxQTdLZHFFVi5hcEQ4WA$337Er5P19AxIoTiISC/+fedQbGiZa8QeP9ovw9DO+Zc','2026-10-11 21:50:29',1,'2026-10-11 21:45:29'),
('VER6acc054355fe6','USR6acc0371d41ce','$argon2id$v=19$m=65536,t=4,p=1$cWNyYmRZZEdkQWtaV3FGVQ$Rkah6nEoKd9Sg/S/3LYM6Hg/A87O5vK3So/B/lBJZPA','2026-10-11 21:58:07',1,'2026-10-11 21:53:07'),
('VER6acc09d719878','USR_40fe80d699aa1641884470f7e3b6f969','$argon2id$v=19$m=65536,t=4,p=1$WGNjLnlqUTV6SFZSYjJQSQ$V49NocVAvJWkzst96GdRUDIKXwBqVdVCqv2nqd+LA3s','2026-10-11 22:17:39',1,'2026-10-11 22:12:39'),
('VER6acc0b4cef94b','USR_40fe80d699aa1641884470f7e3b6f969','$argon2id$v=19$m=65536,t=4,p=1$RzM3ekNRTGpCbE1yMnM3bQ$K26GYd0zkLVLsK3IQ0vJkZLf1sfeKYps8XFkm5Z0FJo','2026-10-11 22:23:52',1,'2026-10-11 22:18:52'),
('VER6acf3c106b652','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$NXFIQ0V6dlZ2VExpelJkQg$pzPJwxdJOmpNZB5VoBDj+q7ggl7TZcMAAGhWDO0bj5Q','2026-10-14 08:28:44',1,'2026-10-14 08:23:44'),
('VER6acf3c6ee5f15','USR6abf510899546','$argon2id$v=19$m=65536,t=4,p=1$UXJrNThlM2FhaW5pS1psOQ$RYOAUwAumY5kdKE3S+Gwa6dFMW8FM2ioOx0LAIauGcE','2026-10-14 08:30:18',1,'2026-10-14 08:25:18'),
('VER6ad158799c9ae','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$VjVYdGo1cTZHbW41dllYMw$3/s3TiQ2zcZ2SKaOooVC03DrxIVl/CmISwI67z6Etfo','2026-10-15 22:54:29',1,'2026-10-15 22:49:29'),
('VER6ad16e7ceb31b','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$b1p5bkpmT2o4Vk5zLnVZNg$HTTcm1oL22nzVJGj6CSvTkQhrIOswarLGwRkPV0PjX4','2026-10-16 00:28:24',1,'2026-10-16 00:23:24'),
('VER6ad28e40ee4c8','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$QXh3dUVwODAuMUpjSnlHUg$X/QU/Dq8nbfvVtjcxeLI73R+G1x2OYAy22lvH0UheBc','2026-10-16 20:56:12',1,'2026-10-16 20:51:12'),
('VER6ad341a4e35c3','USR6ab7ced74d59a','$argon2id$v=19$m=65536,t=4,p=1$RmdyMHBmamhvdXhBRU9rLw$tjUm5EOOJ+jrsfURgL9c8ecQ3Txgc5mvW1rjfnW0wqc','2026-10-17 09:41:36',1,'2026-10-17 09:36:36');
/*!40000 ALTER TABLE `email_verifications` ENABLE KEYS */;
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
INSERT INTO `guarantor` VALUES
('GNT_6ab84846e6d50','mugwanya','malaika','256776789098'),
('GNT_6ab848a264c2e','musawo','ivan','256778788788'),
('GNT_6ab84935d0098','werw','er','256702133121'),
('GNT_6ab84b00a3eb3','MUGABI','PRAISE','256702133121'),
('GNT_6ac2375db8618','MUGABI','PRAISE','256702133121'),
('GNT_6ac23778bb0d0','MUGABI','PRAISE','256702133121'),
('GNT_6ac29fe3e7a5b','migano','alskdjf','256778277277'),
('GNT_6ac2a96871820','MUGABI','PRAISE','256702133121'),
('GNT_6ac3bfcb813e7','musawo','ivan','256786544632'),
('GNT_6ac8f1a3b55ef','MUGABI','PRAISE','256702133121'),
('GNT_6ac8f1cad376c','MUGABI','PRAISE','256702133121'),
('GNT_6acc067658b6b','KAMUNYIGA','Evan','256758274730'),
('GNT_6acc0d022fb93','KAMUNYIGA','Evan','256758274730');
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
INSERT INTO `loan` VALUES
('LOAN_6ab84846e75b8','CL_6ab84846e740a','GNT_6ab84846e6d50',100000.00,19.00,119000.00,'2026-09-27','active'),
('LOAN_6ab848a265014','CL_6ab848a264d92','GNT_6ab848a264c2e',200000.00,20.00,240000.00,'2026-09-27','cleared'),
('LOAN_6ab84935d0337','CL_6ab84935d020d','GNT_6ab84935d0098',100000.00,3.00,103000.00,'2026-09-27','active'),
('LOAN_6ab84b00a4208','CL_6ab84b00a4049','GNT_6ab84b00a3eb3',300000.00,20.00,360000.00,'2026-09-27','cleared'),
('LOAN_6ac2375db8902','CL_6ac2375db87b2','GNT_6ac2375db8618',100000.00,20.00,120000.00,'2026-10-04','active'),
('LOAN_6ac23778bb4f5','CL_6ac23778bb2f9','GNT_6ac23778bb0d0',100000.00,20.00,120000.00,'2026-10-04','active'),
('LOAN_6ac29fe3e7d2b','CL_6ac29fe3e7c08','GNT_6ac29fe3e7a5b',200000.00,20.00,240000.00,'2026-10-04','cleared'),
('LOAN_6ac2a96871b06','CL_6ac2a968719ae','GNT_6ac2a96871820',100000.00,20.00,120000.00,'2026-10-04','active'),
('LOAN_6ac3bfcb8169a','CL_6ac3bfcb81566','GNT_6ac3bfcb813e7',100000.00,20.00,120000.00,'2026-10-05','active'),
('LOAN_6ac8f1a3b5919','CL_6ac8f1a3b57eb','GNT_6ac8f1a3b55ef',100000.00,20.00,120000.00,'2026-10-09','active'),
('LOAN_6ac8f1cad39e3','CL_6ac8f1cad38be','GNT_6ac8f1cad376c',200000.00,20.00,240000.00,'2026-10-09','cleared'),
('LOAN_6acc067658e2f','CL_6acc067658cf5','GNT_6acc067658b6b',200000.00,20.00,240000.00,'2026-10-12','cleared'),
('LOAN_6acc0d0230245','CL_6acc067658cf5','GNT_6acc0d022fb93',1000000.00,20.00,1200000.00,'2026-10-12','active');
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
  CONSTRAINT `fk_payment_collected_by` FOREIGN KEY (`collected_by`) REFERENCES `staff` (`staff_id`),
  CONSTRAINT `fk_payment_loan` FOREIGN KEY (`loan_id`) REFERENCES `loan` (`loan_id`),
  CONSTRAINT `fk_payment_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `user` (`user_id`),
  CONSTRAINT `fk_payment_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `transaction` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `payment` WRITE;
/*!40000 ALTER TABLE `payment` DISABLE KEYS */;
INSERT INTO `payment` VALUES
('PAY_6ab849ab2f977','LOAN_6ab848a265014','TRXN_6ab849ab2f979','STF6ab7ced74d597','USR6ab7ced74d59a',8000.00,232000.00,'2026-09-27 21:00:00','2026-09-26 22:39:39','2026-09-26 22:40:34'),
('PAY_6ab849fcec1a3','LOAN_6ab84846e75b8','TRXN_6ab849fcec1a5','STF6ab7ced74d597','USR6ab7ced74d59a',4000.00,115000.00,'2026-09-26 21:00:00','2026-09-26 22:41:00','2026-09-26 22:41:00'),
('PAY_6ab84a25133d0','LOAN_6ab84846e75b8','TRXN_6ab84a25133d2','STF6ab7ced74d597','USR6ab7ced74d59a',4000.00,111000.00,'2026-09-27 21:00:00','2026-09-26 22:41:41','2026-09-26 22:41:41'),
('PAY_6ab84a8ea3753','LOAN_6ab848a265014','TRXN_6ab84a8ea3754','STF6ab7ced74d597','USR6ab7ced74d59a',8000.00,224000.00,'2026-09-26 21:00:00','2026-09-26 22:43:26','2026-09-26 22:43:26'),
('PAY_6abe53f06db42','LOAN_6ab848a265014','TRXN_6abe53f06db43','STF6ab7ced74d597','USR6ab7ced74d59a',4000.00,220000.00,'2026-09-30 21:00:00','2026-10-01 12:37:04','2026-10-01 12:37:04'),
('PAY_6abe53f876fd1','LOAN_6ab84846e75b8','TRXN_6abe53f876fd3','STF6ab7ced74d597','USR6ab7ced74d59a',4000.00,107000.00,'2026-09-30 21:00:00','2026-10-01 12:37:12','2026-10-01 12:37:12'),
('PAY_6abe6d0a9eadd','LOAN_6ab84b00a4208','TRXN_6abe6d0a9eadf','STF6ab7ced74d597','USR6ab7ced74d59a',4000.00,356000.00,'2026-09-30 21:00:00','2026-10-01 14:24:10','2026-10-01 14:24:10'),
('PAY_6abe732c16ac9','LOAN_6ab848a265014','TRXN_6abe732c16aca','STF_6ab845c346a4f','USR6ab7ced74d59a',4000.00,216000.00,'2026-10-01 21:00:00','2026-10-01 14:50:20','2026-10-01 14:50:20'),
('PAY_6ac163a6421cc','LOAN_6ab848a265014','TRXN_6ac163a6421ce','STF_6ab845c346a4f','USR6ab7ced74d59a',4000.00,212000.00,'2026-10-02 21:00:00','2026-10-03 20:20:54','2026-10-03 20:20:54'),
('PAY_6ac1643757db0','LOAN_6ab848a265014','TRXN_6ac1643757db1','STF_6ab845c346a4f','USR6ab7ced74d59a',212000.00,0.00,'2026-10-02 21:00:00','2026-10-03 20:23:19','2026-10-03 20:23:19'),
('PAY_6ac2378bd1c9b','LOAN_6ac23778bb4f5','TRXN_6ac2378bd1c9d','STF6ac236e27168c','USR6ab7ced74d59a',4000.00,116000.00,'2026-10-04 21:00:00','2026-10-04 11:24:59','2026-10-04 11:24:59'),
('PAY_6ac29f6e2cf75','LOAN_6ac2375db8902','TRXN_6ac29f6e2cf77','STF6ab7ced74d597','USR6ab7ced74d59a',4000.00,116000.00,'2026-10-04 21:00:00','2026-10-04 18:48:14','2026-10-04 18:48:14'),
('PAY_6ac2a98340cf7','LOAN_6ac2a96871b06','TRXN_6ac2a98340cf8','STF6ac236e27168c','USR6ab7ced74d59a',4000.00,116000.00,'2026-10-04 21:00:00','2026-10-04 19:31:15','2026-10-04 19:31:15'),
('PAY_6ac2a9a190ef2','LOAN_6ac29fe3e7d2b','TRXN_6ac2a9a190ef4','STF6ab7ced74d597','USR6ab7ced74d59a',8000.00,232000.00,'2026-10-04 21:00:00','2026-10-04 19:31:45','2026-10-04 19:31:45'),
('PAY_6ac2ac74dd283','LOAN_6ab84b00a4208','TRXN_6ac2ac74dd284','STF6ab7ced74d597','USR6ab7ced74d59a',10000.00,346000.00,'2026-10-04 21:00:00','2026-10-04 19:43:48','2026-10-04 19:43:48'),
('PAY_6ac3b0761bb6b','LOAN_6ac29fe3e7d2b','TRXN_6ac3b0761bb6c','STF6ac236e27168c','USR6ab7ced74d59a',232000.00,0.00,'2026-10-05 21:00:00','2026-10-05 14:13:10','2026-10-05 14:13:10'),
('PAY_6ac3b0e547a89','LOAN_6ac2a96871b06','TRXN_6ac3b0e547a8b','STF6ac236e27168c','USR6ab7ced74d59a',4000.00,112000.00,'2026-10-06 21:00:00','2026-10-05 14:15:01','2026-10-05 14:15:01'),
('PAY_6ac3c1a67f0cf','LOAN_6ab84b00a4208','TRXN_6ac3c1a67f0d1','STF6ab845f430b2c','USR6ab7ced74d59a',346000.00,0.00,'2026-10-04 21:00:00','2026-10-05 15:26:30','2026-10-05 15:26:30'),
('PAY_6ac81aed1086e','LOAN_6ac23778bb4f5','TRXN_6ac81aed10870','STF_6ac3604815211','USR_789b61379bc6ff39f009c0d53d9c4869',4000.00,112000.00,'2026-10-08 21:00:00','2026-10-08 22:36:29','2026-10-08 22:36:29'),
('PAY_6ac8f1e5e220a','LOAN_6ac8f1cad39e3','TRXN_6ac8f1e5e220c','STF6ac8e95ccf24f','USR6ab7ced74d59a',8000.00,232000.00,'2026-10-09 21:00:00','2026-10-09 13:53:41','2026-10-09 13:53:41'),
('PAY_6ac8f2af8292f','LOAN_6ac8f1a3b5919','TRXN_6ac8f2af82930','STF_6ac3604815211','USR_789b61379bc6ff39f009c0d53d9c4869',4000.00,116000.00,'2026-10-09 21:00:00','2026-10-09 13:57:03','2026-10-09 13:57:03'),
('PAY_6acc05b19851c','LOAN_6ac8f1cad39e3','TRXN_6acc05b19851d','STF6ac8e95ccf24f','USR6ab7ced74d59a',232000.00,0.00,'2026-10-11 21:00:00','2026-10-11 21:54:57','2026-10-11 21:54:57'),
('PAY_6acc0a491db47','LOAN_6acc067658e2f','TRXN_6acc0a491db49','STF_6acc03c2eb363','USR_40fe80d699aa1641884470f7e3b6f969',16000.00,224000.00,'2026-10-11 21:00:00','2026-10-11 22:14:33','2026-10-11 22:14:33'),
('PAY_6acc0bd5eabc8','LOAN_6acc067658e2f','TRXN_6acc0bd5eabca','STF6ac8e95ccf24f','USR_40fe80d699aa1641884470f7e3b6f969',224000.00,0.00,'2026-10-11 21:00:00','2026-10-11 22:21:09','2026-10-11 22:21:09'),
('PAY_6acc0e543fde6','LOAN_6acc0d0230245','TRXN_6acc0e543fde7','STF6ac8e95ccf24f','USR_40fe80d699aa1641884470f7e3b6f969',4000.00,1196000.00,'2026-10-12 21:00:00','2026-10-11 22:31:48','2026-10-11 22:33:43'),
('PAY_6acc0f1d7f647','LOAN_6acc0d0230245','TRXN_6acc0f1d7f648','STF_6acc03c2eb363','USR_40fe80d699aa1641884470f7e3b6f969',80000.00,1116000.00,'2026-10-12 21:00:00','2026-10-11 22:35:09','2026-10-11 22:35:09'),
('PAY_6ad1766e9f47e','LOAN_6acc0d0230245','TRXN_6ad1766e9f480','STF6ac8e95ccf24f','USR6ab7ced74d59a',4000.00,1112000.00,'2026-10-15 21:00:00','2026-10-16 00:57:18','2026-10-16 00:57:18');
/*!40000 ALTER TABLE `payment` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

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
  CONSTRAINT `chk_staff_phone` CHECK (regexp_like(`phone_number`,_utf8mb4'^(0(7|1)[0-9]{8}|256(7|1)[0-9]{8})$'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
INSERT INTO `staff` VALUES
('STF_6ab845c346a4f','Mugabi','Praise','praisemugabi10@gmail.com','256703344567','field_officer','2026-09-26 22:22:59',1),
('STF_6ac2c32cde4be','kayaga','wiston','kayagawiston@gmail.com','256778788766','field_officer','2026-10-04 21:20:44',1),
('STF_6ac35fb11544b','kadoto','william','william@gmail.com','256776766766','field_officer','2026-10-05 08:28:33',0),
('STF_6ac35fcd0b435','magonya','marvin','marvin@gmail.com','256776766767','field_officer','2026-10-05 08:29:01',1),
('STF_6ac35fe28866f','musiru','victor','victor@gmail.com','256776544321','field_officer','2026-10-05 08:29:22',1),
('STF_6ac3600408d3f','mutamba','shaize','shaize@gmail.com','256776757436','field_officer','2026-10-05 08:29:56',1),
('STF_6ac3604815211','mugabi','praise','mugabipraise01@gmail.com','256703318543','cashier','2026-10-05 08:31:04',1),
('STF_6ac62a82e8f78','KISAKYE','JOSHUA','kayagawiston1@gmail.com','256776543212','field_officer','2026-10-07 11:18:26',1),
('STF_6ac62c81785fb','sample','staff','sample@gmail.com','256778765432','field_officer','2026-10-07 11:26:57',0),
('STF_6acc03c2eb363','Mwesigye','Saphan','msaphan6@gmail.com','256778987890','cashier','2026-10-11 21:46:42',1),
('STF6ab7ced74d597','MUGABI','PRAISE','mugabipraise4@gmail.com','256702133121','manager','2026-09-26 13:55:35',1),
('STF6ab845f430b2c','Tatitwe','Dansonz','danson@apec.comj','256789900988','field_officer','2026-09-26 22:23:48',1),
('STF6abf510899544','Tatitwe','Danson','tatitwedanson2@gmail.com','256777877877','cashier','2026-10-02 06:36:56',1),
('STF6ac236e27168c','Arayo','totoka','monica@gmail.com','256778788988','field_officer','2026-10-04 11:22:10',1),
('STF6ac237248c812','tats','john','aldskjf@aldjkf.com','256778788788','field_officer','2026-10-04 11:23:16',1),
('STF6ac2a0173dc0e','another','one','aldjfk@alsdkfj.alsdjkf','256746366467','field_officer','2026-10-04 18:51:03',1),
('STF6ac2a172d7620','new','one','new@apec.com','256765366377','field_officer','2026-10-04 18:56:50',1),
('STF6ac3c03c4fbd7','muganzi','john','john@gmail.com','256765432123','field_officer','2026-10-05 15:20:28',1),
('STF6ac3c18115f85','natamba','mugabi','mugabi@apec.com','256789477477','field_officer','2026-10-05 15:25:53',1),
('STF6ac8e95ccf24f','kadonya','ivanz','alsdkj@lafjdd.com','256778654321','field_officer','2026-10-09 13:17:16',1),
('STF6acc0371d41c8','Mwesigye','Saphn','mwesigyesaphan6@gmail.com','256765432456','manager','2026-10-11 21:45:22',1);
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

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
  CONSTRAINT `fk_staff_transaction_staff` FOREIGN KEY (`staff_transaction_staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_staff_transaction_transaction` FOREIGN KEY (`staff_transaction_transaction_id`) REFERENCES `transaction` (`transaction_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_transaction`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `staff_transaction` WRITE;
/*!40000 ALTER TABLE `staff_transaction` DISABLE KEYS */;
INSERT INTO `staff_transaction` VALUES
('STRXN_6ac29f6e2d8c8','TRXN_6ac29f6e2cf77','STF6ab7ced74d597'),
('STRXN_6ac2a9a191423','TRXN_6ac2a9a190ef4','STF6ab7ced74d597'),
('STRXN_6ac2ac74dd649','TRXN_6ac2ac74dd284','STF6ab7ced74d597'),
('STRXN_6ac81aed1331c','TRXN_6ac81aed10870','STF_6ac3604815211'),
('STRXN_6ac8f2af82c82','TRXN_6ac8f2af82930','STF_6ac3604815211'),
('STRXN_6acc0a491df52','TRXN_6acc0a491db49','STF_6acc03c2eb363'),
('STRXN_6acc0f1d7fa2d','TRXN_6acc0f1d7f648','STF_6acc03c2eb363');
/*!40000 ALTER TABLE `staff_transaction` ENABLE KEYS */;
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
INSERT INTO `transaction` VALUES
('TRXN_6ab849ab2f979','cash_in','TRXNCAT6ab83e8256eda',8000.00,'Loan payment from client','USR6ab7ced74d59a','2026-09-28 00:00:00','2026-09-26 22:39:39','2026-09-26 22:40:34'),
('TRXN_6ab849fcec1a5','cash_in','TRXNCAT6ab83e8256eda',4000.00,'Loan payment from client','USR6ab7ced74d59a','2026-09-27 00:00:00','2026-09-26 22:41:00','2026-09-26 22:41:00'),
('TRXN_6ab84a25133d2','cash_in','TRXNCAT6ab83e8256eda',4000.00,'Loan payment from client','USR6ab7ced74d59a','2026-09-28 00:00:00','2026-09-26 22:41:41','2026-09-26 22:41:41'),
('TRXN_6ab84a8ea3754','cash_in','TRXNCAT6ab83e8256eda',8000.00,'Loan payment from client','USR6ab7ced74d59a','2026-09-27 00:00:00','2026-09-26 22:43:26','2026-09-26 22:43:26'),
('TRXN_6abe53f06db43','cash_in','TRXNCAT6ab83e8256eda',4000.00,'Loan payment from client','USR6ab7ced74d59a','2026-10-01 00:00:00','2026-10-01 12:37:04','2026-10-01 12:37:04'),
('TRXN_6abe53f876fd3','cash_in','TRXNCAT6ab83e8256eda',4000.00,'Loan payment from client','USR6ab7ced74d59a','2026-10-01 00:00:00','2026-10-01 12:37:12','2026-10-01 12:37:12'),
('TRXN_6abe6d0a9eadf','cash_in','TRXNCAT6ab83e8256eda',4000.00,'Loan payment from client','USR6ab7ced74d59a','2026-10-01 00:00:00','2026-10-01 14:24:10','2026-10-01 14:24:10'),
('TRXN_6abe732c16aca','cash_in','TRXNCAT6ab83e8256eda',4000.00,'Loan payment from client','USR6ab7ced74d59a','2026-10-02 00:00:00','2026-10-01 14:50:20','2026-10-01 14:50:20'),
('TRXN_6ac163a6421ce','cash_in','TRXNCAT6ab83e8256eda',4000.00,'Loan payment from client','USR6ab7ced74d59a','2026-10-03 00:00:00','2026-10-03 20:20:54','2026-10-03 20:20:54'),
('TRXN_6ac1643757db1','cash_in','TRXNCAT6ab83e8256eda',212000.00,'Loan payment from client','USR6ab7ced74d59a','2026-10-03 00:00:00','2026-10-03 20:23:19','2026-10-03 20:23:19'),
('TRXN_6ac2378bd1c9d','cash_in','TRXNCAT6ab83e8256eda',4000.00,'Loan payment from client','USR6ab7ced74d59a','2026-10-05 00:00:00','2026-10-04 11:24:59','2026-10-04 11:24:59'),
('TRXN_6ac29f6e2cf77','cash_in','TRXNCAT6ab83e8256eda',4000.00,'Loan payment from client at office','USR6ab7ced74d59a','2026-10-05 00:00:00','2026-10-04 18:48:14','2026-10-04 18:48:14'),
('TRXN_6ac2a98340cf8','cash_in','TRXNCAT6ab83e8256eda',4000.00,'Loan payment from client','USR6ab7ced74d59a','2026-10-05 00:00:00','2026-10-04 19:31:15','2026-10-04 19:31:15'),
('TRXN_6ac2a9a190ef4','cash_in','TRXNCAT6ab83e8256eda',8000.00,'Loan payment from client at office','USR6ab7ced74d59a','2026-10-05 00:00:00','2026-10-04 19:31:45','2026-10-04 19:31:45'),
('TRXN_6ac2ac74dd284','cash_in','TRXNCAT6ab83e8256eda',10000.00,'Loan payment from client at office','USR6ab7ced74d59a','2026-10-05 00:00:00','2026-10-04 19:43:48','2026-10-04 19:43:48'),
('TRXN_6ac3b0761bb6c','cash_in','TRXNCAT6ab83e8256eda',232000.00,'Loan payment from client','USR6ab7ced74d59a','2026-10-06 00:00:00','2026-10-05 14:13:10','2026-10-05 14:13:10'),
('TRXN_6ac3b0e547a8b','cash_in','TRXNCAT6ab83e8256eda',4000.00,'Loan payment from client','USR6ab7ced74d59a','2026-10-07 00:00:00','2026-10-05 14:15:01','2026-10-05 14:15:01'),
('TRXN_6ac3c1a67f0d1','cash_in','TRXNCAT6ab83e8256eda',346000.00,'Loan payment from client','USR6ab7ced74d59a','2026-10-05 00:00:00','2026-10-05 15:26:30','2026-10-05 15:26:30'),
('TRXN_6ac81aed10870','cash_in','TRXNCAT6ab83e8256eda',4000.00,'Loan payment from client at office','USR_789b61379bc6ff39f009c0d53d9c4869','2026-10-09 00:00:00','2026-10-08 22:36:29','2026-10-08 22:36:29'),
('TRXN_6ac8f1e5e220c','cash_in','TRXNCAT6ab83e8256eda',8000.00,'Loan payment from client','USR6ab7ced74d59a','2026-10-10 00:00:00','2026-10-09 13:53:41','2026-10-09 13:53:41'),
('TRXN_6ac8f2af82930','cash_in','TRXNCAT6ab83e8256eda',4000.00,'Loan payment from client at office','USR_789b61379bc6ff39f009c0d53d9c4869','2026-10-10 00:00:00','2026-10-09 13:57:03','2026-10-09 13:57:03'),
('TRXN_6acc05b19851d','cash_in','TRXNCAT6ab83e8256eda',232000.00,'Loan payment from client','USR6ab7ced74d59a','2026-10-12 00:00:00','2026-10-11 21:54:57','2026-10-11 21:54:57'),
('TRXN_6acc0a491db49','cash_in','TRXNCAT6ab83e8256eda',16000.00,'Loan payment from client at office','USR_40fe80d699aa1641884470f7e3b6f969','2026-10-12 00:00:00','2026-10-11 22:14:33','2026-10-11 22:14:33'),
('TRXN_6acc0bd5eabca','cash_in','TRXNCAT6ab83e8256eda',224000.00,'Loan payment from client','USR_40fe80d699aa1641884470f7e3b6f969','2026-10-12 00:00:00','2026-10-11 22:21:09','2026-10-11 22:21:09'),
('TRXN_6acc0e543fde7','cash_in','TRXNCAT6ab83e8256eda',4000.00,'Loan payment from client','USR_40fe80d699aa1641884470f7e3b6f969','2026-10-13 00:00:00','2026-10-11 22:31:48','2026-10-11 22:33:43'),
('TRXN_6acc0f1d7f648','cash_in','TRXNCAT6ab83e8256eda',80000.00,'Loan payment from client at office','USR_40fe80d699aa1641884470f7e3b6f969','2026-10-13 00:00:00','2026-10-11 22:35:09','2026-10-11 22:35:09'),
('TRXN_6ad1766e9f480','cash_in','TRXNCAT6ab83e8256eda',4000.00,'Loan payment from client','USR6ab7ced74d59a','2026-10-16 00:00:00','2026-10-16 00:57:18','2026-10-16 00:57:18'),
('TRXN6ab83f1314e9f','cash_in','TRXNCAT6ab83ecdd6c33',1000000.00,'boss paid the money','USR6ab7ced74d59a','2026-09-27 00:54:26','2026-09-26 21:54:27','2026-09-26 21:54:27'),
('TRXN6ab83f414520d','cash_out','TRXNCAT6ab83e9a3c78b',100000.00,'today','USR6ab7ced74d59a','2026-09-27 00:55:12','2026-09-26 21:55:13','2026-09-26 21:55:13'),
('TRXN6ad15b63c691f','cash_in','TRXNCAT6ab83ecdd6c33',3000.00,'money from boss','USR6ab7ced74d59a','2026-10-16 02:01:55','2026-10-15 23:01:55','2026-10-15 23:01:55'),
('TRXN6ad15e2205265','cash_out','TRXNCAT6ab83eaf5f1a7',4000.00,'money spent at office','USR6ab7ced74d59a','2026-10-16 02:13:30','2026-10-15 23:13:38','2026-10-15 23:13:38'),
('TRXN6ad1665415f5c','cash_in','TRXNCAT6ac8e2bd17ee0',23.00,NULL,'USR6ab7ced74d59a','2026-10-16 02:48:35','2026-10-15 23:48:36','2026-10-15 23:48:36'),
('TRXN6ad176d02b9a8','cash_in','TRXNCAT6ab83ecdd6c33',100000.00,NULL,'USR6ab7ced74d59a','2026-10-16 03:58:55','2026-10-16 00:58:56','2026-10-16 00:58:56'),
('TRXN6ad176e2d420a','cash_in','TRXNCAT6ab83ecdd6c33',100000000.00,NULL,'USR6ab7ced74d59a','2026-10-16 03:59:14','2026-10-16 00:59:14','2026-10-16 00:59:14'),
('TRXN6ad292a309281','cash_in','TRXNCAT6ac8e2bd17ee0',3000.00,'from dividends','USR6ab7ced74d59a','2026-10-17 00:09:54','2026-10-16 21:09:55','2026-10-16 21:09:55'),
('TRXN6ad342c5d45ba','cash_out','TRXNCAT6ab83e9a3c78b',450000.00,NULL,'USR6ab7ced74d59a','2026-10-17 12:41:25','2026-10-17 09:41:25','2026-10-17 09:41:25');
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
('TRXNCAT6ab83e8256eda','client payment','money collected from clients','cash_in','0','2026-09-26 21:52:02','2026-10-16 20:53:40'),
('TRXNCAT6ab83e9a3c78b','Bank Deposit','money deposited at the bank after balancing in a day','cash_out','1','2026-09-26 21:52:26','2026-09-26 21:52:26'),
('TRXNCAT6ab83eaf5f1a7','Office expense','expenses at the office','cash_out','1','2026-09-26 21:52:47','2026-09-26 21:52:47'),
('TRXNCAT6ab83ecdd6c33','from boss','money from boss','cash_in','0','2026-09-26 21:53:17','2026-10-17 09:42:21'),
('TRXNCAT6ac8e2bd17ee0','dividend','dividend from companies','cash_in','0','2026-10-09 12:49:01','2026-10-17 09:42:16');
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
  `staff_id` varchar(50) NOT NULL,
  `role` enum('field_officer','cashier','manager','other') NOT NULL DEFAULT 'other',
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_user_staff` (`staff_id`),
  KEY `fk_user_staff` (`staff_id`),
  CONSTRAINT `fk_user_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES
('USR_28573afc0add81599705391e32eea405','STF_6ab845c346a4f','field_officer','$argon2id$v=19$m=65536,t=4,p=1$Yk9wVTNiVjZ1d1g4L2t4WA$zC1GILkgMgE3JPC+ln2FXouet0DdUVRTimI7yVnluuE','2026-10-05 13:07:18'),
('USR_40fe80d699aa1641884470f7e3b6f969','STF_6acc03c2eb363','cashier','$argon2id$v=19$m=65536,t=4,p=1$Qmd1NW54Y2Muc2p4bVFIQg$kWAxkckRIZStfEde47nBkvH8Ra8cPQgAvuj7GzTHQY0','2026-10-11 21:47:34'),
('USR_789b61379bc6ff39f009c0d53d9c4869','STF_6ac3604815211','cashier','$argon2id$v=19$m=65536,t=4,p=1$VDAuT0ZLVm5jZVNIRVNWYQ$gRlUQbEs1dGh6AAojRtueXsNfN5ttQkeJNNElGiTwTU','2026-10-05 10:12:37'),
('USR6ab7ced74d59a','STF6ab7ced74d597','manager','$argon2id$v=19$m=65536,t=4,p=1$d21OcjhhZ3YxbEFtbndhaw$KKmLxEQuUzlIGlRkU2OVlEPKJwOq640N2lqrLRkmqls','2026-09-26 13:55:35'),
('USR6abf510899546','STF6abf510899544','cashier','$argon2id$v=19$m=65536,t=4,p=1$RjhZWmgzU3VZTDgybEFwMg$Vzs300eK704MBWNRZYcoBqHisv8Mnr6AMvA+lyw8RYY','2026-10-02 06:36:57'),
('USR6acc0371d41ce','STF6acc0371d41c8','manager','$argon2id$v=19$m=65536,t=4,p=1$RW1QTDZuSy5uZmVVaFFMeg$v+D4P+2cc/qf3Z4tyYBebVWLJmGl9yRdBs38/9v3lqk','2026-10-11 21:45:22');
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

-- Dump completed on 2026-10-17 10:22:16
