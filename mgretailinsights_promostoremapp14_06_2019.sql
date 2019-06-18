-- MySQL dump 10.13  Distrib 5.7.26, for Linux (x86_64)
--
-- Host: localhost    Database: kirana_qa_new
-- ------------------------------------------------------
-- Server version	5.7.26-0ubuntu0.18.04.1

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
-- Table structure for table `mgretailinsights_promostoremapp`
--

DROP TABLE IF EXISTS `mgretailinsights_promostoremapp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mgretailinsights_promostoremapp` (
  `p_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Post ID',
  `store_id` int(11) DEFAULT NULL COMMENT 'Store ID',
  `rule_id` int(11) DEFAULT NULL COMMENT 'Rule ID',
  `pstart_date` timestamp NULL DEFAULT NULL COMMENT 'Promotion Start Date',
  `pend_date` timestamp NULL DEFAULT NULL COMMENT 'Promotion End Date',
  `status` int(11) DEFAULT NULL COMMENT 'Promotion Status',
  `description` varchar(225) NOT NULL COMMENT 'description of rule',
  `store_name` varchar(225) NOT NULL COMMENT 'store name',
  `seller_type` int(11) DEFAULT NULL COMMENT 'seller type',
  `conditions_serialized` text NOT NULL COMMENT 'conditions of rule',
  `actions_serialized` text NOT NULL COMMENT 'actions of rule',
  `simple_action` text NOT NULL COMMENT 'simple action of rule',
  `discount_amount` varchar(225) NOT NULL COMMENT 'Discount Amount',
  `rule_type` int(11) NOT NULL COMMENT 'Rule type',
  PRIMARY KEY (`p_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='Promotion Store Mapping';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mgretailinsights_promostoremapp`
--

LOCK TABLES `mgretailinsights_promostoremapp` WRITE;
/*!40000 ALTER TABLE `mgretailinsights_promostoremapp` DISABLE KEYS */;
INSERT INTO `mgretailinsights_promostoremapp` VALUES (1,1161,2,'2019-10-10 00:00:00','2019-11-09 00:00:00',1,'Buy one Good Life Sella Rice get one free','',1,'{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Combine\",\"attribute\":null,\"operator\":null,\"value\":true,\"is_value_processed\":null,\"aggregator\":\"all\"}','{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Product\\\\Combine\",\"attribute\":null,\"operator\":null,\"value\":\"1\",\"is_value_processed\":null,\"aggregator\":\"all\",\"conditions\":[{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Product\",\"attribute\":\"sku\",\"operator\":\"==\",\"value\":\"SKU491185250\",\"is_value_processed\":false}]}','buy_x_get_y','1.0000',0),(2,910,4,'2019-09-10 00:00:00','2019-10-09 00:00:00',1,'','',0,'{\"type\":\"Magento\\CatalogRule\\Model\\Rule\\Condition\\Combine\",\"attribute\":null,\"operator\":null,\"value\":true,\"is_value_processed\":null,\"aggregator\":\"all\"}','{\"type\":\"Magento\\CatalogRule\\Model\\Rule\\Action\\Collection\",\"attribute\":null,\"operator\":\"=\",\"value\":null}','by_percent','5.0000',1),(3,1161,3,'2019-09-10 00:00:00','2019-10-09 00:00:00',1,'','',1,'{\"type\":\"Magento\\CatalogRule\\Model\\Rule\\Condition\\Combine\",\"attribute\":null,\"operator\":null,\"value\":true,\"is_value_processed\":null,\"aggregator\":\"all\"}','{\"type\":\"Magento\\CatalogRule\\Model\\Rule\\Action\\Collection\",\"attribute\":null,\"operator\":\"=\",\"value\":null}','by_fixed','10.0000',1),(4,461,5,'2019-10-10 00:00:00','2019-11-09 00:00:00',1,'Buy one Sunfeast Kesar Elaichi Cookies get one free','',1,'{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Combine\",\"attribute\":null,\"operator\":null,\"value\":true,\"is_value_processed\":null,\"aggregator\":\"all\"}','{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Product\\\\Combine\",\"attribute\":null,\"operator\":null,\"value\":\"1\",\"is_value_processed\":null,\"aggregator\":\"all\",\"conditions\":[{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Product\",\"attribute\":\"sku\",\"operator\":\"==\",\"value\":\"SKU490750758\",\"is_value_processed\":false}]}','buy_x_get_y','1.0000',0),(5,660,3,'2019-10-10 00:00:00','2019-11-09 00:00:00',1,'Get Flat ₹1 Off','',0,'{\"type\":\"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Combine\",\"attribute\":null,\"operator\":null,\"value\":true,\"is_value_processed\":null,\"aggregator\":\"all\"}','{\"type\":\"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Action\\\\Collection\",\"attribute\":null,\"operator\":\"=\",\"value\":null}','by_fixed','1.0000',1),(6,664,4,'2019-10-10 00:00:00','2019-11-09 00:00:00',1,'Save 5%','',0,'{\"type\":\"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Combine\",\"attribute\":null,\"operator\":null,\"value\":true,\"is_value_processed\":null,\"aggregator\":\"all\"}','{\"type\":\"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Action\\\\Collection\",\"attribute\":null,\"operator\":\"=\",\"value\":null}','by_percent','5.0000',1),(7,665,3,'2019-10-10 00:00:00','2019-11-09 00:00:00',1,'Get Flat ₹1 Off','',0,'{\"type\":\"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Combine\",\"attribute\":null,\"operator\":null,\"value\":true,\"is_value_processed\":null,\"aggregator\":\"all\"}','{\"type\":\"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Action\\\\Collection\",\"attribute\":null,\"operator\":\"=\",\"value\":null}','by_fixed','1.0000',1),(9,461,5,'2019-10-10 00:00:00','2019-11-09 00:00:00',1,'Buy one Britannia Butter Elaichiz Cookies get one free','',1,'{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Combine\",\"attribute\":null,\"operator\":null,\"value\":true,\"is_value_processed\":null,\"aggregator\":\"all\"}','{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Product\\\\Combine\",\"attribute\":null,\"operator\":null,\"value\":\"1\",\"is_value_processed\":null,\"aggregator\":\"all\",\"conditions\":[{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Product\",\"attribute\":\"sku\",\"operator\":\"==\",\"value\":\"SKU490675900\",\"is_value_processed\":false}]}','buy_x_get_y','1',0),(10,461,5,'2019-10-10 00:00:00','2019-11-09 00:00:00',1,'Buy one Sunfeast Special Creams Orange get one free','',1,'{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Combine\",\"attribute\":null,\"operator\":null,\"value\":true,\"is_value_processed\":null,\"aggregator\":\"all\"}','{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Product\\\\Combine\",\"attribute\":null,\"operator\":null,\"value\":\"1\",\"is_value_processed\":null,\"aggregator\":\"all\",\"conditions\":[{\"type\":\"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Product\",\"attribute\":\"sku\",\"operator\":\"==\",\"value\":\"SKU490675868\",\"is_value_processed\":false}]}','buy_x_get_y','1',0);
/*!40000 ALTER TABLE `mgretailinsights_promostoremapp` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-06-14  6:40:01
