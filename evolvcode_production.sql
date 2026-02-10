-- MySQL dump 10.13  Distrib 9.6.0, for macos26.2 (arm64)
--
-- Host: localhost    Database: evolvcode_cms
-- ------------------------------------------------------
-- Server version	9.6.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `otp_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','sales@evolvcode.com','$2y$12$O4xfzJzGb1l/eO35k14NSOnJ.XevotEWC/EiEupwPs6WvMElOLTqC','2026-02-07 07:11:28','2026-02-06 06:17:06',NULL,NULL);
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_categories`
--

DROP TABLE IF EXISTS `blog_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_categories`
--

LOCK TABLES `blog_categories` WRITE;
/*!40000 ALTER TABLE `blog_categories` DISABLE KEYS */;
INSERT INTO `blog_categories` VALUES (1,'Digital Marketing','digital-marketing','2026-02-06 06:17:06'),(2,'Web Design','web-design','2026-02-06 06:17:06'),(3,'Website Development','website-development','2026-02-06 06:17:06'),(4,'Consulting','consulting','2026-02-06 06:17:06'),(5,'Webflow Website','webflow-website','2026-02-06 06:17:06');
/*!40000 ALTER TABLE `blog_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_submissions`
--

DROP TABLE IF EXISTS `contact_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_submissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contact_read` (`is_read`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_submissions`
--

LOCK TABLES `contact_submissions` WRITE;
/*!40000 ALTER TABLE `contact_submissions` DISABLE KEYS */;
INSERT INTO `contact_submissions` VALUES (1,'Final Test','admin@test.com','123456789','test','this is test message','::1',0,'2026-02-06 08:16:11'),(2,'Test User','test@example.com','','','Hello','::1',0,'2026-02-06 08:20:26'),(3,'Verified User','verify@example.com','','','Testing fix','::1',0,'2026-02-06 08:22:02'),(4,'Verified User','verify@example.com','','','Testing fix 2','::1',1,'2026-02-06 08:24:10'),(5,'Lavkush','kushkrg@gmail.com','1234567890','Tst asdlfkjlj  asdf','Thi si sthe masdlfkjlkajs df','::1',1,'2026-02-06 08:28:46'),(6,'Lavkush kumar','kushkrg@gmail.com','07858004001','Website not working','hjk','::1',0,'2026-02-06 12:31:50'),(7,'Lavkush kumar','kushkrg@gmail.com','08678014338','Application: UI/UX Designer','This is the cover message\n\nResume: http://localhost:8000/assets/uploads/resumes/lavkush-kumar-1770447701.pdf','::1',1,'2026-02-07 07:01:41');
/*!40000 ALTER TABLE `contact_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_fields`
--

DROP TABLE IF EXISTS `form_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `form_fields` (
  `id` int NOT NULL AUTO_INCREMENT,
  `form_id` int NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('text','email','tel','textarea','select','checkbox','radio','hidden','number','date','url') COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `placeholder` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `options` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON array for select/radio/checkbox options',
  `is_required` tinyint(1) DEFAULT '0',
  `validation_rules` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Comma-separated rules like: min:3,max:100',
  `css_class` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_form_fields_form` (`form_id`,`sort_order`),
  CONSTRAINT `form_fields_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_fields`
--

LOCK TABLES `form_fields` WRITE;
/*!40000 ALTER TABLE `form_fields` DISABLE KEYS */;
INSERT INTO `form_fields` VALUES (1,1,'Your Name','name','text','John Doe',NULL,NULL,1,NULL,NULL,1,'2026-02-06 06:46:53'),(2,1,'Email Address','email','email','john@example.com',NULL,NULL,1,NULL,NULL,2,'2026-02-06 06:46:53'),(3,1,'Phone Number','phone','tel','+91 98765 43210',NULL,NULL,0,NULL,NULL,3,'2026-02-06 06:46:53'),(4,1,'Interested In','subject','select','',NULL,'[\"General Inquiry\", \"Web Development\", \"Digital Marketing\", \"SEO Services\", \"Other\"]',0,NULL,NULL,4,'2026-02-06 06:46:53'),(5,1,'Your Message','message','textarea','Tell us about your project...',NULL,NULL,1,NULL,NULL,5,'2026-02-06 06:46:53'),(6,2,'name','name','text','','','',0,NULL,NULL,1,'2026-02-07 07:03:10');
/*!40000 ALTER TABLE `form_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_submissions`
--

DROP TABLE IF EXISTS `form_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `form_submissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `form_id` int NOT NULL,
  `data` json NOT NULL COMMENT 'JSON object with field name => value pairs',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `referrer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('unread','read','replied','archived') COLLATE utf8mb4_unicode_ci DEFAULT 'unread',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_form_submissions_form` (`form_id`,`status`,`created_at`),
  CONSTRAINT `form_submissions_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_submissions`
--

LOCK TABLES `form_submissions` WRITE;
/*!40000 ALTER TABLE `form_submissions` DISABLE KEYS */;
INSERT INTO `form_submissions` VALUES (1,1,'{\"name\": \"Final Test\", \"email\": \"final@test.com\", \"phone\": \"\", \"message\": \"Test Message\", \"subject\": \"\"}','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','http://localhost:8000/services','unread',NULL,'2026-02-06 07:19:28');
/*!40000 ALTER TABLE `form_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forms`
--

DROP TABLE IF EXISTS `forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('popup','embedded','contact') COLLATE utf8mb4_unicode_ci DEFAULT 'popup',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `submit_button_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Submit',
  `success_message` text COLLATE utf8mb4_unicode_ci,
  `email_notification` tinyint(1) DEFAULT '1',
  `email_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_forms_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forms`
--

LOCK TABLES `forms` WRITE;
/*!40000 ALTER TABLE `forms` DISABLE KEYS */;
INSERT INTO `forms` VALUES (1,'Contact Form','contact','popup','Get a Free Consultation','Fill out the form below and we\'ll get back to you within 24 hours.','Send Message','Thank you! Your message has been sent successfully. We\'ll get back to you within 24 hours.',1,NULL,NULL,'active','2026-02-06 06:46:53','2026-02-06 06:46:53'),(2,'test','test-title','embedded','test title','test description','Submit','',1,'',NULL,'active','2026-02-07 07:02:46','2026-02-07 07:02:46');
/*!40000 ALTER TABLE `forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` int NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `dimensions` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES (3,'6986d6455701d_01bea218.webp','image-3-2048x1082.webp','image-3-2048x1082',NULL,'/assets/uploads/images/2026/02/6986d6455701d_01bea218.webp','image/webp',98522,NULL,NULL,'2026-02-07 06:05:57'),(4,'6986d6549b75e_21e3ee1b.webp','6970ead7f69d7020bd43723a_Gemini_Generated_Image_9ezd8q9ezd8q9ezd.webp','6970ead7f69d7020bd43723a_Gemini_Generated_Image_9ezd8q9ezd8q9ezd',NULL,'/assets/uploads/images/2026/02/6986d6549b75e_21e3ee1b.webp','image/webp',1070532,NULL,NULL,'2026-02-07 06:06:12'),(5,'6986d66a2cdc0_025c3a9a.jpg','Book1_page-0001.jpg','Book1_page-0001',NULL,'/assets/uploads/images/2026/02/6986d66a2cdc0_025c3a9a.jpg','image/jpeg',44385,NULL,NULL,'2026-02-07 06:06:34'),(6,'6986d6eac0f8f_37a91ed6.webp','image-3-2048x1082.webp','image-3-2048x1082',NULL,'/assets/uploads/images/2026/02/6986d6eac0f8f_37a91ed6.webp','image/webp',98522,NULL,NULL,'2026-02-07 06:08:42'),(7,'6986d75f536d1_c5f2f270.jpg','Book1_page-0001.jpg','Book1_page-0001',NULL,'/assets/uploads/images/2026/02/6986d75f536d1_c5f2f270.jpg','image/jpeg',44385,NULL,NULL,'2026-02-07 06:10:39');
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `menu_id` int NOT NULL,
  `parent_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '_self',
  `sort_order` int DEFAULT '0',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `menu_items_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` VALUES (7,2,NULL,'Home','/','_self',0,'active'),(8,2,NULL,'About Us','/about','_self',1,'active'),(9,2,NULL,'Services','/services','_self',2,'active'),(10,2,NULL,'Portfolio','/portfolio','_self',3,'active'),(11,2,NULL,'Blog','/blog','_self',4,'active'),(12,2,NULL,'Contact','/contact','_self',5,'active'),(14,1,NULL,'Home','/','_self',0,'active'),(15,1,NULL,'Digital Marketing','#','_self',1,'active'),(16,1,15,'SEO Services','/service/seo','_self',0,'active'),(17,1,15,'Social Media Marketing','/service/social-media','_self',1,'active'),(18,1,15,'PPC Advertising','/service/ppc','_self',2,'active'),(19,1,15,'Content Marketing','/service/content-marketing','_self',3,'active'),(20,1,NULL,'Web Development','#','_self',2,'active'),(21,1,20,'Custom Website Development','/service/custom-web-dev','_self',0,'active'),(22,1,20,'E-commerce Solutions','/service/ecommerce','_self',1,'active'),(23,1,20,'WordPress Development','/service/wordpress','_self',2,'active'),(24,1,NULL,'Company','#','_self',3,'active'),(25,1,24,'About Us','/about','_self',0,'active'),(26,1,24,'Blog','/blog','_self',1,'active'),(27,1,24,'Contact Us','/contact','_self',2,'active'),(28,1,24,'Career','/career','_self',3,'active'),(29,1,NULL,'Portfolio','/portfolio','_self',4,'active');
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `location` (`location`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES (1,'Header Menu','header','2026-02-07 04:42:39','2026-02-07 04:42:39'),(2,'Footer Quick Links','footer','2026-02-07 04:42:39','2026-02-07 04:42:39');
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text COLLATE utf8mb4_unicode_ci,
  `status` enum('published','draft') COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'About Us','about','<section class=\"who-we-are\">\r\n<h2>Who We Are</h2>\r\n<p>We\'re a team of creative minds, tech experts, and marketing pros who love helping businesses shine online. Based in India and serving clients worldwide, we bring a human touch to every digital project.</p>\r\n<p>Whether it\'s a website, a mobile app, or a digital campaign &mdash; we treat every project like our own.</p>\r\n</section>\r\n<section class=\"what-we-do\">\r\n<h2>What We Do</h2>\r\n<ul>\r\n<li>✨ Website Development &ndash; WordPress, Shopify, Custom</li>\r\n<li>📈 Digital Marketing &ndash; SEO, Google Ads, Social Media</li>\r\n<li>🛒 E-commerce Solutions &ndash; Online store setup, payments, tracking</li>\r\n<li>🔄 Migration &amp; Upgrades &ndash; From old to new, we make it easy</li>\r\n<li>📊 PPC &amp; Lead Generation &ndash; Get real leads that grow your business</li>\r\n<li>🎨 Design &amp; Branding &ndash; Logos, creatives, and clean UI/UX</li>\r\n<li>🧠 Strategy &amp; Consulting &ndash; Tech advice you can trust</li>\r\n</ul>\r\n</section>\r\n<section class=\"who-we-work-with\">\r\n<h2>Who We Work With</h2>\r\n<ul>\r\n<li>🏪 Local Shops</li>\r\n<li>🧑&zwj;💼 Startups</li>\r\n<li>🏢 Enterprises</li>\r\n<li>💻 Freelancers &amp; Coaches</li>\r\n<li>🏥 Clinics &amp; Medical Brands</li>\r\n<li>🧵 Fashion &amp; Lifestyle Stores</li>\r\n<li>🛠️ Service-Based Companies</li>\r\n</ul>\r\n<p>No matter your industry, we\'ll build what works best for your audience.</p>\r\n</section>\r\n<section class=\"how-we-work\">\r\n<h2>How We Work</h2>\r\n<ol>\r\n<li>Understand your needs 🤝</li>\r\n<li>Plan smart 📋</li>\r\n<li>Design with purpose 🎨</li>\r\n<li>Build with care 🛠️</li>\r\n<li>Launch with confidence 🚀</li>\r\n<li>Support you always ❤️</li>\r\n</ol>\r\n<p>We don\'t overcomplicate things. We keep you involved and informed at every step.</p>\r\n</section>\r\n<section class=\"trust-factors\">\r\n<h3>Transparency</h3>\r\n<p>Transparency is our promise, ensuring open communication and clear expectations every step of the way.</p>\r\n<h3>Experienced Team</h3>\r\n<p>Our experienced team brings a wealth of expertise and knowledge to deliver exceptional results for every project.</p>\r\n<h3>Result Guarantee</h3>\r\n<p>With our result guarantee, we ensure you receive the outcomes you expect, backed by our commitment to your success. done</p>\r\n</section>','Empowering Businesses with Smart Digital Solutions','Transform your business with our innovative digital solutions. Get a free consultation today and watch your growth soar!','ec','published','2026-02-06 06:17:06','2026-02-06 12:23:57'),(2,'Contact Us','contact','<h2>Send us a message</h2>\n<p>Ready to start your project? Get in touch with us today!</p>\n\n<div class=\"contact-info\">\n<p><strong>Email:</strong> sales@evolvcode.com</p>\n<p><strong>Phone:</strong> +91-9229045881</p>\n<p><strong>WhatsApp:</strong> <a href=\"https://api.whatsapp.com/send/?phone=919229045881\">Chat with us</a></p>\n</div>','Reliable Digital Marketing Team - Contact Us Today','Reach out for expert digital marketing solutions. Our team ensures your website is optimized for success. Send us a message now!',NULL,'published','2026-02-06 06:17:06','2026-02-06 06:17:06'),(3,'Career','career','\n<section class=\"section page-header\" style=\"padding-top: 150px; background: var(--color-gray-50);\">\n    <div class=\"container\">\n        <div class=\"section-header\">\n            <span class=\"section-label\">Join Our Team</span>\n            <h1 class=\"section-title\">Build the Future With Us</h1>\n            <p class=\"section-description lead\">\n                We are looking for passionate individuals to join our mission.\n            </p>\n        </div>\n    </div>\n</section>\n<section class=\"values-section\" style=\"padding: 80px 0;\">\n    <div class=\"container\">\n        <div class=\"grid grid-3\" style=\"gap: 40px;\">\n            <div class=\"value-card text-center\">\n                <div class=\"icon-box\" style=\"font-size: 2.5rem; color: var(--color-primary); margin-bottom: 20px;\">\n                    <i class=\"fas fa-rocket\"></i>\n                </div>\n                <h3>Innovation First</h3>\n                <p class=\"text-gray-600\">We push boundaries and embrace new technologies to deliver cutting-edge solutions.</p>\n            </div>\n            <div class=\"value-card text-center\">\n                <div class=\"icon-box\" style=\"font-size: 2.5rem; color: var(--color-primary); margin-bottom: 20px;\">\n                    <i class=\"fas fa-users\"></i>\n                </div>\n                <h3>Collaborative Culture</h3>\n                <p class=\"text-gray-600\">We believe in the power of teamwork and open communication to achieve great results.</p>\n            </div>\n            <div class=\"value-card text-center\">\n                <div class=\"icon-box\" style=\"font-size: 2.5rem; color: var(--color-primary); margin-bottom: 20px;\">\n                    <i class=\"fas fa-chart-line\"></i>\n                </div>\n                <h3>Growth & Learning</h3>\n                <p class=\"text-gray-600\">We invest in our people, providing opportunities for continuous learning and career advancement.</p>\n            </div>\n        </div>\n    </div>\n</section>','Career - Join Our Team','Join the Evolvcode team. We are hiring for Web Developers, Digital Marketers, and more.',NULL,'published','2026-02-07 05:23:02','2026-02-07 05:31:53');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portfolio_categories`
--

DROP TABLE IF EXISTS `portfolio_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `portfolio_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portfolio_categories`
--

LOCK TABLES `portfolio_categories` WRITE;
/*!40000 ALTER TABLE `portfolio_categories` DISABLE KEYS */;
INSERT INTO `portfolio_categories` VALUES (1,'Web Development','web-development','2026-02-06 06:17:06'),(2,'E-commerce','ecommerce','2026-02-06 06:17:06'),(3,'Digital Marketing','digital-marketing','2026-02-06 06:17:06'),(4,'App Development','app-development','2026-02-06 06:17:06');
/*!40000 ALTER TABLE `portfolio_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `author` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text COLLATE utf8mb4_unicode_ci,
  `views` int DEFAULT '0',
  `status` enum('published','draft') COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  KEY `idx_posts_status` (`status`,`published_at`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,'How to Create a Winning Digital Marketing Strategy in 2025 (Complete Playbook)','digital-marketing-strategy-playbook','A digital marketing strategy is a documented plan that connects your business goals to your channels, messages, budgets, timelines, and KPIs.','<h2>What Is a Digital Marketing Strategy (and Why It Fails Without a System)</h2>\r\n<p>A digital marketing strategy is a documented plan that connects your business goals to your channels, messages, budgets, timelines, and KPIs.</p>\r\n<p>Most strategies fail because they\'re:</p>\r\n<ul>\r\n<li>Goal-less (no revenue linkage)</li>\r\n<li>Channel-first (tactics without positioning)</li>\r\n<li>Under-measured (weak analytics)</li>\r\n<li>Or over-complicated (no weekly execution)</li>\r\n</ul>\r\n<p>This playbook will help you avoid these pitfalls and create a strategy that actually works.</p>','/assets/uploads/images/2026/02/6985dcfc86ce7_4d935f76.webp',1,'Kush Kumar','How to Create a Winning Digital Marketing Strategy in 2025 (Complete Playbook)','Learn how to create a winning digital marketing strategy with our complete playbook.',NULL,4,'published','2026-02-06 12:22:20','2026-02-06 06:17:06','2026-02-07 04:17:12'),(2,'Top Digital Marketing Agency in Patna: Evolvcode Solutions – Your #1 Choice','best-digital-marketing-agency-in-patna','Grow your business with simple, affordable, and effective digital marketing. From SEO and Social Media to PPC and Web Design.','<h2>Best Digital Marketing Agency in Patna – Why Evolvcode Solutions is #1 Choice</h2>\n<p>Patna\'s #1 Digital Growth Partner</p>\n\n<p>Grow your business with simple, affordable, and effective digital marketing. From SEO and Social Media to PPC and Web Design—we help Patna businesses get found and grow online.</p>\n\n<h3>Our Services</h3>\n<ul>\n<li>Search Engine Optimization (SEO)</li>\n<li>Social Media Marketing</li>\n<li>Pay-Per-Click Advertising</li>\n<li>Web Design & Development</li>\n<li>Content Marketing</li>\n</ul>',NULL,1,'Kush Kumar','Top Digital Marketing Agency in Patna: Evolvcode Solutions – Your #1 Choice','Best Digital Marketing Agency in Patna – Why Evolvcode Solutions is #1 Choice',NULL,14,'published','2025-08-28 18:30:00','2026-02-06 06:17:06','2026-02-07 04:15:25'),(3,'The Ultimate Webflow Website Development Handbook','the-ultimate-webflow-website-development-handbook','In the ever-evolving world of web design, finding the right tools and platforms to bring your vision to life is crucial.','<h2>The Ultimate Webflow Website Development Handbook</h2>\n<p>In the ever-evolving world of web design, finding the right tools and platforms to bring your vision to life is crucial. Webflow has emerged as one of the most powerful and versatile website development platforms available today.</p>\n\n<p>Whether you\'re a seasoned developer or a novice looking to dip your toes into web design, Webflow offers a unique combination of visual design tools and robust functionality.</p>',NULL,5,'Anand Bajrangi','The Ultimate Webflow Website Development Handbook','Complete guide to Webflow website development for designers and developers.',NULL,0,'published','2024-06-01 18:30:00','2026-02-06 06:17:06','2026-02-06 06:17:06'),(4,'Key Aspects of a Successful Website: Design, Content, SEO, and More','key-aspects-of-a-successful-website-design-content-seo-and-more','Discover the essential elements that make a website successful, from design and content to SEO and user experience.','<h2>Key Aspects of a Successful Website</h2>\n<p>A successful website is more than just an online presence—it\'s a powerful tool that can drive business growth, build brand credibility, and engage your target audience.</p>\n\n<h3>Essential Elements</h3>\n<ul>\n<li>Professional Design</li>\n<li>Quality Content</li>\n<li>SEO Optimization</li>\n<li>User Experience</li>\n<li>Mobile Responsiveness</li>\n<li>Fast Loading Speed</li>\n</ul>',NULL,4,'Kush Kumar','Key Aspects of a Successful Website: Design, Content, SEO, and More','Discover the essential elements that make a website successful.',NULL,1,'published','2024-05-14 18:30:00','2026-02-06 06:17:06','2026-02-06 10:51:30');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gallery` text COLLATE utf8mb4_unicode_ci,
  `tech_stack` text COLLATE utf8mb4_unicode_ci,
  `project_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text COLLATE utf8mb4_unicode_ci,
  `is_featured` tinyint(1) DEFAULT '0',
  `status` enum('published','draft') COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `completed_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  KEY `idx_projects_status` (`status`,`is_featured`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `portfolio_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,'Career Hike | Complete Website Development for Education Admission Consultancy','career-hike-complete-website-development-for-education-admission-consultancy','Career Hike','Evolvcode Solutions is excited to have partnered with Career Hike, delivering a comprehensive website for education admission consultancy.','<h2>Project Highlights</h2>\r\n<h3>University and College Listing Features</h3>\r\n<ul>\r\n<li>Created a comprehensive database for universities and colleges with detailed profiles, making it easier for students to explore their options.</li>\r\n<li>Implemented filters and search functionality to enable quick and accurate results based on location, ranking, and specialization.</li>\r\n</ul>\r\n<h3>Course Listing Features</h3>\r\n<ul>\r\n<li>Designed an organized structure for courses offered by various institutions with detailed descriptions, eligibility criteria, and application processes.</li>\r\n<li>Enabled course comparison to help students make informed decisions.</li>\r\n</ul>\r\n<h3>Custom Design for Education Consultancy</h3>\r\n<p>Developed a modern, intuitive design reflecting Career Hike\'s mission of guiding students and parents through the complex admission process.</p>\r\n<h3>Responsive and Mobile-Friendly Interface</h3>\r\n<p>Ensured a seamless browsing experience across all devices for both students and parents.</p>\r\n<h3>Service Pages</h3>\r\n<p>Highlighted key services, including career counseling, admission guidance, scholarship assistance, and visa consultation.</p>\r\n<h3>Student-Friendly Features</h3>\r\n<ul>\r\n<li>Integrated inquiry forms and appointment scheduling for one-on-one guidance.</li>\r\n<li>Developed a \"Success Stories\" section to inspire and motivate aspiring students.</li>\r\n</ul>\r\n<h3>Blog and Resource Section</h3>\r\n<p>Added a blog to share admission tips, study abroad guides, and the latest updates in the education industry.</p>\r\n<h3>SEO Optimization</h3>\r\n<p>Enhanced the website\'s online presence to ensure it reaches students and parents looking for reliable admission consultancy services.</p>\r\n<h3>Analytics Integration</h3>\r\n<p>Implemented tracking tools to monitor website engagement and user interactions for continuous improvement.</p>','/assets/uploads/images/2026/02/6985db0f0b6f6_58786f84.webp',NULL,'Wordpress','',1,'Career Hike Website Development | Evolvcode Solutions','Check out the new Career Hike website that simplifies student admissions!',NULL,1,'published',NULL,'2026-02-06 06:17:06','2026-02-06 12:22:00'),(2,'Benison IVF | Complete Business Website Development','benison-ivf-complete-business-website-development','Benison IVF','Evolvcode Solutions is proud to have successfully delivered a comprehensive business website for Benison IVF.','<h2>Project Overview</h2>\n<p>Evolvcode Solutions is proud to have successfully delivered a comprehensive business website for Benison IVF, a leading fertility clinic.</p>\n\n<h3>Key Features</h3>\n<ul>\n<li>Clean, professional design reflecting trust and medical expertise</li>\n<li>Service pages highlighting IVF treatments and fertility solutions</li>\n<li>Patient testimonials and success stories</li>\n<li>Online appointment booking system</li>\n<li>Doctor profiles and qualifications</li>\n<li>Blog section for fertility tips and information</li>\n<li>Mobile-responsive design for accessibility</li>\n<li>SEO optimization for local search visibility</li>\n</ul>',NULL,NULL,NULL,NULL,1,'Benison IVF | Complete Business Website Development','Comprehensive business website development for Benison IVF fertility clinic.',NULL,1,'published',NULL,'2026-02-06 06:17:06','2026-02-06 06:17:06'),(3,'Vvalyou | WordPress to Shopify Migration with Advanced Features','vvalyou-wordpress-to-shopify-migration-with-advanced-features','Vvalyou','Evolvcode Solutions proudly partnered with Vvalyou to migrate their e-commerce platform from WordPress to Shopify.','<h2>Project Overview</h2>\n<p>Evolvcode Solutions proudly partnered with Vvalyou to migrate their e-commerce platform from WordPress to Shopify with advanced features.</p>\n\n<h3>Migration Highlights</h3>\n<ul>\n<li>Complete product catalog migration with all variants and images</li>\n<li>Customer data and order history preservation</li>\n<li>SEO URL redirects to maintain search rankings</li>\n<li>Custom Shopify theme development matching brand identity</li>\n<li>Advanced filtering and search functionality</li>\n<li>Payment gateway integration</li>\n<li>Inventory management system setup</li>\n<li>Performance optimization for faster load times</li>\n</ul>',NULL,NULL,NULL,NULL,2,'Vvalyou | WordPress to Shopify Migration with Advanced Features','Complete WordPress to Shopify migration with advanced e-commerce features.',NULL,1,'published',NULL,'2026-02-06 06:17:06','2026-02-06 06:17:06');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `features` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int DEFAULT '0',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text COLLATE utf8mb4_unicode_ci,
  `status` enum('published','draft') COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_services_status` (`status`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'Digital Marketing Services','digital-marketing-services','Unlock your business potential with our expert digital marketing strategies designed for measurable growth.','<h2>Digital Marketing Services Designed for Measurable Growth</h2>\r\n<p>We keep things simple, clear, and results-focused&mdash;taking your brand from \"just online\" to \"growing online.\"</p>\r\n<h3>Our Credentials and Expertise</h3>\r\n<ul>\r\n<li><strong>Certified Professionals:</strong> Our team holds Google Ads, Meta Ads, and HubSpot certifications.</li>\r\n<li><strong>Proven Track Record:</strong> We\'ve helped 50+ businesses increase traffic, leads, and revenue.</li>\r\n<li><strong>Industry Experience:</strong> Over 5 years working across eCommerce, healthcare, tech, education, and local businesses.</li>\r\n<li><strong>Data-First Approach:</strong> Every recommendation is based on analytics and market research.</li>\r\n</ul>\r\n<h3>Why Businesses Trust Our Digital Marketing Services</h3>\r\n<ul>\r\n<li><strong>Full Visibility:</strong> We provide detailed monthly reports and dashboards so you always know where your money is going.</li>\r\n<li><strong>Ethical Practices:</strong> No black-hat tactics. We follow Google\'s guidelines to protect your site from penalties.</li>\r\n<li><strong>Client-Centric Approach:</strong> We listen to your business goals before building a custom strategy.</li>\r\n<li><strong>Long-Term Focus:</strong> We build campaigns that grow with your business, not just short-term hacks.</li>\r\n</ul>','fa-chart-line','/assets/uploads/images/2026/02/6985e02adf6f6_9ddcf11b.webp','SEO Services,PPC Advertising,Social Media Marketing,Content Marketing,Email Marketing,Analytics & Reporting',1,'Customized Digital Marketing Services for Business Growth','Unlock your business potential with our expert strategies!','','published','2026-02-06 06:17:06','2026-02-06 12:35:54'),(2,'Search Engine Optimization','search-engine-optimization-services','Improve your online visibility and drive organic traffic with our comprehensive SEO services.','<h2>Search Engine Optimization (SEO)</h2>\n<p>Get found on Google and other search engines with our proven SEO strategies.</p>\n\n<h3>Our SEO Services Include:</h3>\n<ul>\n<li><strong>Website Audit & Keyword Research:</strong> In-depth analysis of your site and competitors.</li>\n<li><strong>On-Page SEO:</strong> Optimizing content, meta tags, headings, and internal links.</li>\n<li><strong>Technical SEO:</strong> Fixing crawl issues, improving speed, and making your site mobile-friendly.</li>\n<li><strong>Link Building:</strong> Acquiring high-quality backlinks that boost domain authority.</li>\n<li><strong>Local SEO:</strong> Perfect for businesses targeting customers in a specific region.</li>\n</ul>','fa-search',NULL,'Keyword Research,On-Page SEO,Technical SEO,Link Building,Local SEO,SEO Audits',2,'Search Engine Optimization Services | Evolvcode','Improve your online visibility and drive organic traffic with our comprehensive SEO services.',NULL,'published','2026-02-06 06:17:06','2026-02-06 06:17:06'),(3,'Social Media Marketing','social-media-marketing','Build your brand presence and engage with your audience across all major social media platforms.','<h2>Social Media Marketing (SMM)</h2>\n<p>Connect with your audience where they spend their time.</p>\n\n<h3>Our Social Media Services Include:</h3>\n<ul>\n<li><strong>Content Calendar & Strategy:</strong> Professionally planned posts that align with your brand voice.</li>\n<li><strong>Community Building:</strong> Engaging followers and turning them into loyal advocates.</li>\n<li><strong>Paid Social Ads:</strong> Expanding reach and driving measurable ROI.</li>\n<li><strong>Platform Management:</strong> Facebook, Instagram, LinkedIn, Twitter, and more.</li>\n</ul>','fa-share-alt',NULL,'Content Strategy,Community Management,Paid Social Ads,Influencer Marketing,Analytics & Reporting',3,'Social Media Marketing Services | Evolvcode','Build your brand presence and engage with your audience across all major social media platforms.',NULL,'published','2026-02-06 06:17:06','2026-02-06 06:17:06'),(4,'PPC Services','ppc-services','Get immediate visibility and drive targeted traffic with our pay-per-click advertising services.','<h2>Pay-Per-Click (PPC) Advertising</h2>\n<p>Get immediate results with targeted paid advertising campaigns.</p>\n\n<h3>Our PPC Services Include:</h3>\n<ul>\n<li><strong>Google Ads:</strong> Search, Display, Shopping, and Remarketing campaigns.</li>\n<li><strong>Meta Ads:</strong> Targeted campaigns on Facebook and Instagram to drive sales.</li>\n<li><strong>Performance Monitoring:</strong> Continuous A/B testing to improve click-through rates and lower cost per lead.</li>\n</ul>\n<p>Our approach is fully transparent — you own your ad accounts, and we share full data access.</p>','fa-bullseye',NULL,'Google Ads,Facebook Ads,Instagram Ads,Remarketing,A/B Testing,ROI Optimization',4,'PPC Services | Evolvcode','Get immediate visibility and drive targeted traffic with our pay-per-click advertising services.',NULL,'published','2026-02-06 06:17:06','2026-02-06 06:17:06'),(5,'Shopify Store Development','shopify-store-development','Launch your online store with our expert Shopify development services.','<h2>Shopify Store Development</h2>\n<p>Transform your online store with our expert Shopify solutions.</p>\n\n<h3>Our Shopify Services Include:</h3>\n<ul>\n<li>Custom Shopify theme development</li>\n<li>Store setup and configuration</li>\n<li>Product catalog management</li>\n<li>Payment gateway integration</li>\n<li>Shopify app integration</li>\n<li>Performance optimization</li>\n<li>Migration from other platforms</li>\n</ul>','fa-shopping-bag',NULL,'Custom Theme Development,Store Setup,Payment Integration,App Integration,Migration Services',5,'Top Shopify Store Development Solutions for Success','Transform your online store with our expert Shopify solutions. Get your free consultation now!',NULL,'published','2026-02-06 06:17:06','2026-02-06 06:17:06'),(6,'WordPress Development','wordpress-development-services','Get a powerful, customizable website with our professional WordPress development services.','<h2>WordPress Development Services</h2>\n<p>Transform your business with our expert WordPress services!</p>\n\n<h3>Our WordPress Services Include:</h3>\n<ul>\n<li>Custom WordPress theme development</li>\n<li>Plugin development and customization</li>\n<li>WooCommerce setup and optimization</li>\n<li>Website speed optimization</li>\n<li>Security hardening</li>\n<li>Maintenance and support</li>\n</ul>\n\n<h3>Why Choose Us:</h3>\n<ul>\n<li><strong>5+ Years Experience</strong></li>\n<li><strong>90% Client Retention Rate</strong></li>\n<li><strong>100+ Projects Delivered</strong></li>\n</ul>','fa-wordpress',NULL,'Theme Development,Plugin Development,WooCommerce,Speed Optimization,Security,Maintenance',6,'Top WordPress Development Services for Your Business','Transform your business with our expert WordPress services!',NULL,'published','2026-02-06 06:17:06','2026-02-06 06:17:06'),(7,'Website Migration & Upgrade','website-migration-upgrade-services','Seamlessly migrate your website to a new platform or upgrade your existing site.','<h2>Website Migration & Upgrade Services</h2>\n<p>From old to new, we make it easy.</p>\n\n<h3>Our Migration Services Include:</h3>\n<ul>\n<li>Platform-to-platform migration</li>\n<li>Data and content migration</li>\n<li>SEO preservation and redirects</li>\n<li>Design refresh and modernization</li>\n<li>Performance improvements</li>\n<li>Post-migration support</li>\n</ul>','fa-exchange-alt',NULL,'Platform Migration,Data Migration,SEO Preservation,Design Refresh,Performance Upgrade',7,'Website Migration & Upgrade Services | Evolvcode','Seamlessly migrate your website to a new platform or upgrade your existing site.',NULL,'published','2026-02-06 06:17:06','2026-02-06 06:17:06'),(8,'Ecommerce Web Development','ecommerce-web-development-services','Build a powerful online store that converts visitors into customers.','<h2>Ecommerce Web Development Services</h2>\n<p>Build an online store that drives sales.</p>\n\n<h3>Our Ecommerce Services Include:</h3>\n<ul>\n<li>Custom ecommerce website development</li>\n<li>Shopping cart and checkout optimization</li>\n<li>Payment gateway integration</li>\n<li>Inventory management</li>\n<li>Order tracking systems</li>\n<li>Multi-vendor marketplace development</li>\n</ul>','fa-store',NULL,'Custom Development,Cart Optimization,Payment Integration,Inventory Management,Order Tracking',8,'Ecommerce Web Development Services | Evolvcode','Build a powerful online store that converts visitors into customers.',NULL,'published','2026-02-06 06:17:06','2026-02-06 06:17:06'),(9,'App Development Services','app-development-services','Transform your ideas into powerful mobile applications for iOS and Android.','<h2>App Development Services</h2>\n<p>Transform your ideas into powerful mobile applications.</p>\n\n<h3>Our App Development Services Include:</h3>\n<ul>\n<li>iOS and Android app development</li>\n<li>Cross-platform development (React Native, Flutter)</li>\n<li>UI/UX design for mobile</li>\n<li>API integration</li>\n<li>App store optimization</li>\n<li>Maintenance and updates</li>\n</ul>','fa-mobile-alt',NULL,'iOS Development,Android Development,Cross-Platform,UI/UX Design,API Integration,Maintenance',9,'App Development Services | Evolvcode','Transform your ideas into powerful mobile applications for iOS and Android.',NULL,'published','2026-02-06 06:17:06','2026-02-06 06:17:06');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'site_name','Evolvcode Solutions','2026-02-06 06:17:06'),(2,'site_tagline','Digital Marketing Agency | Best Web Development in Patna','2026-02-06 06:17:06'),(3,'site_description','Boost your business with our digital solutions. Join Evolvcode now!','2026-02-06 06:17:06'),(4,'contact_email','sales@evolvcode.com','2026-02-06 06:17:06'),(5,'contact_phone','+91-9229045881','2026-02-06 06:17:06'),(6,'whatsapp_number','7858004001','2026-02-07 06:25:03'),(7,'address','Patna, Bihar, India','2026-02-06 06:17:06'),(8,'facebook_url','','2026-02-06 06:17:06'),(9,'twitter_url','','2026-02-06 06:17:06'),(10,'linkedin_url','','2026-02-06 06:17:06'),(11,'instagram_url','','2026-02-06 06:17:06'),(12,'logo_path','/assets/uploads/logo/2026/02/6986e0b15ecfc_e261e63b.png','2026-02-07 06:50:25'),(13,'footer_text','© 2026 Evolvcode Solutions. All rights reserved.','2026-02-06 06:17:06'),(14,'home_hero_badge','Digital Marketing & Web Development Agency1','2026-02-07 04:34:58'),(15,'home_hero_title_1','Transform Your','2026-02-07 04:34:58'),(16,'home_hero_title_2','Digital Presence','2026-02-07 04:34:58'),(17,'home_hero_description','Design visuals that speak louder than words — turning ideas into creative designs that connect, inspire, and engage your audience.','2026-02-07 04:34:58'),(18,'home_hero_btn_primary_text','Get Started','2026-02-07 04:34:58'),(19,'home_hero_btn_primary_link','/contact','2026-02-07 04:34:58'),(20,'home_hero_btn_secondary_text','View Our Work','2026-02-07 04:34:58'),(21,'home_hero_btn_secondary_link','/portfolio','2026-02-07 04:34:58'),(22,'home_show_services','1','2026-02-07 04:34:58'),(23,'home_services_label','What We Do','2026-02-07 04:34:58'),(24,'home_services_title','Our Services','2026-02-07 04:34:58'),(25,'home_services_desc','We offer a comprehensive suite of digital solutions tailored to help your business grow and succeed online.','2026-02-07 04:34:58'),(26,'home_show_wcu','1','2026-02-07 04:34:58'),(27,'home_wcu_label','Why Choose Evolvcode','2026-02-07 04:34:58'),(28,'home_wcu_title','Your Growth, Our Mission','2026-02-07 04:34:58'),(29,'home_wcu_desc','We combine creativity with technical expertise to deliver digital solutions that drive real business results.','2026-02-07 04:34:58'),(30,'home_show_projects','1','2026-02-07 04:34:58'),(31,'home_projects_label','Our Work','2026-02-07 04:34:58'),(32,'home_projects_title','Featured Projects','2026-02-07 04:34:58'),(33,'home_projects_desc','Take a look at some of our recent work and see how we help businesses succeed online.','2026-02-07 04:34:58'),(34,'home_show_process','1','2026-02-07 04:34:58'),(35,'home_process_label','Our Process','2026-02-07 04:34:58'),(36,'home_process_title','How We Do Digital Marketing at Evolvcode','2026-02-07 04:34:58'),(37,'home_process_desc','We keep things simple, clear, and results-focused—taking your brand from \"just online\" to \"growing online.\"','2026-02-07 04:34:58'),(38,'home_show_trust','1','2026-02-07 04:34:58'),(39,'home_show_cta','1','2026-02-07 04:34:58'),(40,'home_cta_title','Ready to Transform Your Business?','2026-02-07 04:34:58'),(41,'home_cta_desc','Let\'s discuss how we can help you achieve your digital goals. Get a free consultation today!','2026-02-07 04:34:58'),(42,'home_cta_btn_text','Contact Us','2026-02-07 04:34:58'),(43,'home_cta_btn_link','/contact','2026-02-07 04:34:58'),(44,'youtube_url','','2026-02-07 06:24:14'),(45,'google_analytics','','2026-02-07 06:24:14'),(46,'smtp_host','','2026-02-07 06:24:14'),(47,'smtp_port','587','2026-02-07 06:24:14'),(48,'smtp_user','','2026-02-07 06:24:14'),(49,'smtp_encryption','tls','2026-02-07 06:24:14'),(50,'smtp_from_email','','2026-02-07 06:24:14'),(51,'smtp_from_name','','2026-02-07 06:24:14'),(52,'security_2fa_enabled','1','2026-02-07 07:17:39');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_members`
--

DROP TABLE IF EXISTS `team_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `team_members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_members`
--

LOCK TABLES `team_members` WRITE;
/*!40000 ALTER TABLE `team_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `team_members` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-07 13:05:37
