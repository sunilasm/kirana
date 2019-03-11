-- MySQL dump 10.13  Distrib 5.7.25, for Linux (x86_64)
--
-- Host: localhost    Database: kirana_qa_new
-- ------------------------------------------------------
-- Server version	5.7.25-0ubuntu0.16.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `mglocality`
--

DROP TABLE IF EXISTS `mglocality`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mglocality` (
  `locality_id` int(11) NOT NULL AUTO_INCREMENT,
  `area_id` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`locality_id`)
) ENGINE=InnoDB AUTO_INCREMENT=331 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mglocality`
--

LOCK TABLES `mglocality` WRITE;
/*!40000 ALTER TABLE `mglocality` DISABLE KEYS */;
INSERT INTO `mglocality` VALUES (1,'1','Sector 1'),(2,'1','Sector 1A'),(3,'1','Sector 2'),(4,'1','Sector 3'),(5,'1','Sector 4'),(6,'1','Sector 5'),(7,'1','Sector 6'),(8,'1','Sector 7'),(9,'1','Sector 8'),(10,'1','Sector 9'),(11,'1','Sector 10'),(12,'1','Sector 11'),(13,'1','Sector 12'),(14,'1','Sector 13'),(15,'1','Sector 14'),(16,'1','Sector 15'),(17,'1','Sector 16'),(18,'1','Sector 17'),(19,'1','Sector 18'),(20,'1','Sector 19'),(21,'1','Sector 20'),(22,'2','Sector 1'),(23,'2','Sector 2'),(24,'2','Sector 3'),(25,'2','Sector 4'),(26,'2','Sector 5'),(27,'2','Sector 6'),(28,'2','Sector 7'),(29,'2','Sector 8'),(30,'2','Sector 9'),(31,'2','Sector 10'),(32,'2','Sector 11'),(33,'2','Sector 12'),(34,'2','Sector 12A'),(35,'2','Sector 13'),(36,'2','Sector 14'),(37,'2','Sector 15'),(38,'2','Sector 16'),(39,'2','Sector 17'),(40,'2','Sector 18'),(41,'2','Sector 19'),(42,'2','Sector 20'),(43,'2','Sector 21'),(44,'2','Sector 22'),(45,'2','Sector 23'),(46,'2','Sector 24'),(47,'2','Sector 25'),(48,'2','Sector 26'),(49,'2','Sector 27'),(50,'2','Sector 28'),(51,'2','Sector 29'),(52,'2','Sector 30'),(53,'3','Sector 1'),(54,'3','Sector 2'),(55,'3','Sector 3'),(56,'3','Sector 4'),(57,'3','Sector 5'),(58,'3','Sector 6'),(59,'3','Sector 7'),(60,'3','Sector 8'),(61,'3','Sector 9'),(62,'3','Sector 10'),(63,'3','Sector 11'),(64,'3','Sector 12A'),(65,'3','Sector 12B'),(66,'3','Sector 12C'),(67,'3','Sector 13'),(68,'3','Sector 14'),(69,'3','Sector 15'),(70,'3','Sector 16'),(71,'3','Sector 17'),(72,'3','Sector 18'),(73,'3','Sector 19'),(74,'3','Sector 19A'),(75,'3','Sector 20'),(76,'4','Sector 1'),(77,'4','Sector 1A'),(78,'4','Sector 2'),(79,'4','Sector 3'),(80,'4','Sector 4'),(81,'4','Sector 5'),(82,'4','Sector 6'),(83,'4','Sector 7'),(84,'4','Sector 8'),(85,'4','Sector 9'),(86,'4','Sector 9A'),(87,'4','Sector 10'),(88,'4','Sector 10A'),(89,'4','Sector 11'),(90,'4','Sector 12'),(91,'4','Sector 13'),(92,'4','Sector 14'),(93,'4','Sector 15'),(94,'4','Sector 16'),(95,'4','Sector 16A'),(96,'4','Sector 17'),(97,'4','Sector 18'),(98,'4','Sector 19A'),(99,'4','Sector 19B'),(100,'4','Sector 19C'),(101,'4','Sector 19D'),(102,'4','Sector 19E'),(103,'4','Sector 19F'),(104,'4','Sector 20'),(105,'4','Sector 21'),(106,'4','Sector 22'),(107,'4','Sector 23'),(108,'4','Sector 24'),(109,'4','Sector 25'),(110,'4','Sector 26'),(111,'4','Sector 27'),(112,'4','Sector 28'),(113,'4','Sector 29'),(114,'4','Sector 30'),(115,'4','Sector 31'),(116,'5','Sector 1'),(117,'5','Sector 2'),(118,'5','Sector 3'),(119,'5','Sector 4'),(120,'5','Sector 5'),(121,'5','Sector 6'),(122,'5','Sector 7'),(123,'5','Sector 8'),(124,'5','Sector 9'),(125,'5','Sector 10'),(126,'5','Sector 11'),(127,'5','Sector 12'),(128,'5','Sector 13'),(129,'5','Sector 14'),(130,'5','Sector 15'),(131,'5','Sector 16'),(132,'5','Sector 16A'),(133,'5','Sector 17'),(134,'5','Sector 18'),(135,'5','Sector 19'),(136,'5','Sector 20'),(137,'5','Sector 21'),(138,'5','Sector 22'),(139,'5','Sector 23'),(140,'5','Sector 24'),(141,'5','Sector 25'),(142,'6','Sector 1'),(143,'6','Sector 1A'),(144,'6','Sector 2'),(145,'6','Sector 3'),(146,'6','Sector 4'),(147,'6','Sector 5'),(148,'6','Sector 6'),(149,'6','Sector 7'),(150,'6','Sector 8'),(151,'6','Sector 9'),(152,'6','Sector 10'),(153,'6','Sector 11'),(154,'6','Sector 12'),(155,'6','Sector 13'),(156,'6','Sector 14'),(157,'6','Sector 15'),(158,'6','Sector 15A'),(159,'6','Sector 16'),(160,'6','Sector 16A'),(161,'6','Sector 17'),(162,'6','Sector 18'),(163,'6','Sector 19'),(164,'6','Sector 19A'),(165,'6','Sector 20'),(166,'6','Sector 21'),(167,'6','Sector 22'),(168,'6','Sector 23'),(169,'6','Sector 24'),(170,'6','Sector 25'),(171,'6','Sector 26'),(172,'6','Sector 27'),(173,'6','Sector 28'),(174,'6','Sector 29'),(175,'6','Sector 30'),(176,'6','Sector 32'),(177,'6','Sector 34'),(178,'6','Sector 36'),(179,'6','Sector 40'),(180,'6','Sector 42'),(181,'6','Sector 42A'),(182,'6','Sector 44'),(183,'6','Sector 44A'),(184,'6','Sector 46'),(185,'6','Sector 46A'),(186,'6','Sector 52'),(187,'6','Sector 54'),(188,'6','Sector 56'),(189,'6','Sector 58'),(190,'6','Sector 58A'),(191,'6','Sector 60'),(192,'7','Sector 1'),(193,'7','Sector 1A'),(194,'7','Sector 2'),(195,'7','Sector 3'),(196,'7','Sector 3A'),(197,'7','Sector 4'),(198,'7','Sector 5'),(199,'7','Sector 6'),(200,'7','Sector 7'),(201,'7','Sector 8'),(202,'7','Sector 8A'),(203,'7','Sector 8B'),(204,'7','Sector 9'),(205,'7','Sector 10'),(206,'7','Sector 11'),(207,'7','Sector 12'),(208,'7','Sector 13'),(209,'7','Sector 14'),(210,'7','Sector 15'),(211,'7','Sector 19'),(212,'7','Sector 20'),(213,'7','Sector 21'),(214,'7','Sector 22'),(215,'7','Sector 23'),(216,'7','Sector 24'),(217,'7','Sector 25'),(218,'7','Sector 26'),(219,'7','Sector 27'),(220,'7','Sector 28'),(221,'7','Sector 29'),(222,'7','Sector 30'),(223,'7','Sector 31'),(224,'8','Sector 1'),(225,'8','Sector 2'),(226,'8','Sector 3'),(227,'8','Sector 4'),(228,'8','Sector 5'),(229,'8','Sector 6'),(230,'8','Sector 7'),(231,'8','Sector 8'),(232,'8','Sector 9'),(233,'8','Sector 10'),(234,'8','Sector 11'),(235,'8','Sector 12'),(236,'8','Sector 13'),(237,'8','Sector 14'),(238,'8','Sector 15'),(239,'8','Sector 16'),(240,'8','Sector 17'),(241,'8','Sector 18'),(242,'8','Sector 19'),(243,'8','Sector 20'),(244,'8','Sector 21'),(245,'8','Sector 22'),(246,'8','Sector 23'),(247,'8','Sector 24'),(248,'8','Sector 25'),(249,'8','Sector 26'),(250,'8','Sector 27'),(251,'8','Sector 28'),(252,'8','Sector 29'),(253,'8','Sector 30'),(254,'8','Sector 31'),(255,'8','Sector 32'),(256,'8','Sector 33'),(257,'8','Sector 34'),(258,'8','Sector 35'),(259,'8','Sector 36'),(260,'8','Sector 37'),(261,'8','Sector 38'),(262,'8','Sector 39'),(263,'8','Sector 40'),(264,'9','Sector 1'),(265,'9','Sector 2'),(266,'9','Sector 3'),(267,'9','Sector 4'),(268,'9','Sector 5'),(269,'9','Sector 6'),(270,'9','Sector 7'),(271,'9','Sector 8'),(272,'9','Sector 9'),(273,'9','Sector 10'),(274,'9','Sector 11'),(275,'9','Sector 12'),(276,'9','Sector 13'),(277,'9','Sector 14'),(278,'9','Sector 15'),(279,'9','Sector 16'),(280,'9','Sector 17'),(281,'9','Sector 18'),(282,'9','Sector 19'),(283,'9','Sector 20'),(284,'9','Sector 21'),(285,'9','Sector 22'),(286,'10','Sector 1'),(287,'10','Sector 2'),(288,'10','Sector 3'),(289,'10','Sector 4'),(290,'10','Sector 5'),(291,'10','Sector 6'),(292,'10','Sector 7'),(293,'10','Sector 8'),(294,'10','Sector 9'),(295,'10','Sector 10'),(296,'10','Sector 11'),(297,'10','Sector 12'),(298,'10','Sector 13'),(299,'10','Sector 14'),(300,'10','Sector 15'),(301,'10','Sector 16'),(302,'10','Sector 17'),(303,'10','Sector 18'),(304,'10','Sector 19'),(305,'10','Sector 20'),(306,'10','Sector 21'),(307,'10','Sector 22'),(308,'10','Sector 23'),(309,'11','Sector 5'),(310,'12','Sector 1'),(311,'12','Sector 2'),(312,'12','Sector 3'),(313,'12','Sector 4'),(314,'12','Sector 5'),(315,'12','Sector 6'),(316,'12','Sector 7'),(317,'12','Sector 8'),(318,'12','Sector 9'),(319,'12','Sector 10'),(320,'12','Sector 11'),(321,'12','Sector 12'),(322,'12','Sector 13'),(323,'12','Sector 14'),(324,'12','Sector 15'),(325,'12','Sector 16'),(326,'12','Sector 17'),(327,'12','Sector 18'),(328,'12','Sector 19'),(329,'12','Sector 20'),(330,'12','Sector 21');
/*!40000 ALTER TABLE `mglocality` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-03-11 18:47:09
