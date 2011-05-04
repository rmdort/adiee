# Sequel Pro dump
# Version 2492
# http://code.google.com/p/sequel-pro
#
# Host: localhost (MySQL 5.1.44)
# Database: adi
# Generation Time: 2011-05-04 18:02:10 +0800
# ************************************************************

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table exp_accessories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_accessories`;

CREATE TABLE `exp_accessories` (
  `accessory_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(75) NOT NULL DEFAULT '',
  `member_groups` varchar(50) NOT NULL DEFAULT 'all',
  `controllers` text,
  `accessory_version` varchar(12) NOT NULL,
  PRIMARY KEY (`accessory_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_accessories` WRITE;
/*!40000 ALTER TABLE `exp_accessories` DISABLE KEYS */;
INSERT INTO `exp_accessories` (`accessory_id`,`class`,`member_groups`,`controllers`,`accessory_version`)
VALUES
	(1,'Expressionengine_info_acc','1|5','addons|addons_accessories|addons_extensions|addons_fieldtypes|addons_modules|addons_plugins|admin_content|admin_system|content|content_edit|content_files|content_publish|design|homepage|members|myaccount|tools|tools_communicate|tools_data|tools_logs|tools_utilities','1.0'),
	(2,'Nsm_morphine_theme_acc','1|5','addons|addons_accessories|addons_extensions|addons_fieldtypes|addons_modules|addons_plugins|admin_content|admin_system|content|content_edit|content_files|content_publish|design|homepage|members|myaccount|tools|tools_communicate|tools_data|tools_logs|tools_utilities','1.0.0'),
	(3,'Cp_menu_master_acc','1|5','addons|addons_accessories|addons_extensions|addons_fieldtypes|addons_modules|addons_plugins|admin_content|admin_system|content|content_edit|content_files|content_publish|design|homepage|members|myaccount|tools|tools_communicate|tools_data|tools_logs|tools_utilities','1.0.1');

/*!40000 ALTER TABLE `exp_accessories` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_actions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_actions`;

CREATE TABLE `exp_actions` (
  `action_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(50) NOT NULL,
  `method` varchar(50) NOT NULL,
  PRIMARY KEY (`action_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_actions` WRITE;
/*!40000 ALTER TABLE `exp_actions` DISABLE KEYS */;
INSERT INTO `exp_actions` (`action_id`,`class`,`method`)
VALUES
	(1,'Comment','insert_new_comment'),
	(2,'Comment_mcp','delete_comment_notification'),
	(3,'Comment','comment_subscribe'),
	(4,'Comment','edit_comment'),
	(5,'Email','send_email'),
	(6,'Search','do_search'),
	(7,'Channel','insert_new_entry'),
	(8,'Channel','filemanager_endpoint'),
	(9,'Channel','smiley_pop'),
	(10,'Member','registration_form'),
	(11,'Member','register_member'),
	(12,'Member','activate_member'),
	(13,'Member','member_login'),
	(14,'Member','member_logout'),
	(15,'Member','retrieve_password'),
	(16,'Member','reset_password'),
	(17,'Member','send_member_email'),
	(18,'Member','update_un_pw'),
	(19,'Member','member_search'),
	(20,'Member','member_delete'),
	(21,'Playa_mcp','filter_entries');

/*!40000 ALTER TABLE `exp_actions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_captcha
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_captcha`;

CREATE TABLE `exp_captcha` (
  `captcha_id` bigint(13) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(10) unsigned NOT NULL,
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `word` varchar(20) NOT NULL,
  PRIMARY KEY (`captcha_id`),
  KEY `word` (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_categories`;

CREATE TABLE `exp_categories` (
  `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_id` int(6) unsigned NOT NULL,
  `parent_id` int(4) unsigned NOT NULL,
  `cat_name` varchar(100) NOT NULL,
  `cat_url_title` varchar(75) NOT NULL,
  `cat_description` text,
  `cat_image` varchar(120) DEFAULT NULL,
  `cat_order` int(4) unsigned NOT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `group_id` (`group_id`),
  KEY `cat_name` (`cat_name`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_categories` WRITE;
/*!40000 ALTER TABLE `exp_categories` DISABLE KEYS */;
INSERT INTO `exp_categories` (`cat_id`,`site_id`,`group_id`,`parent_id`,`cat_name`,`cat_url_title`,`cat_description`,`cat_image`,`cat_order`)
VALUES
	(1,1,1,0,'Person','person','','',2),
	(4,1,2,0,'Iphone App','iphone-app','','',3),
	(2,1,1,0,'Company','company','','',1),
	(5,1,2,0,'Ipad App','ipad-app','','',2),
	(6,1,2,0,'Android App','android-app','','',1),
	(7,1,2,0,'Website','website','','',4);

/*!40000 ALTER TABLE `exp_categories` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_category_field_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_category_field_data`;

CREATE TABLE `exp_category_field_data` (
  `cat_id` int(4) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_id` int(4) unsigned NOT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `site_id` (`site_id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `exp_category_field_data` WRITE;
/*!40000 ALTER TABLE `exp_category_field_data` DISABLE KEYS */;
INSERT INTO `exp_category_field_data` (`cat_id`,`site_id`,`group_id`)
VALUES
	(1,1,1),
	(2,1,1),
	(4,1,2),
	(5,1,2),
	(6,1,2),
	(7,1,2);

/*!40000 ALTER TABLE `exp_category_field_data` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_category_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_category_fields`;

CREATE TABLE `exp_category_fields` (
  `field_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_id` int(4) unsigned NOT NULL,
  `field_name` varchar(32) NOT NULL DEFAULT '',
  `field_label` varchar(50) NOT NULL DEFAULT '',
  `field_type` varchar(12) NOT NULL DEFAULT 'text',
  `field_list_items` text NOT NULL,
  `field_maxl` smallint(3) NOT NULL DEFAULT '128',
  `field_ta_rows` tinyint(2) NOT NULL DEFAULT '8',
  `field_default_fmt` varchar(40) NOT NULL DEFAULT 'none',
  `field_show_fmt` char(1) NOT NULL DEFAULT 'y',
  `field_text_direction` char(3) NOT NULL DEFAULT 'ltr',
  `field_required` char(1) NOT NULL DEFAULT 'n',
  `field_order` int(3) unsigned NOT NULL,
  PRIMARY KEY (`field_id`),
  KEY `site_id` (`site_id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_category_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_category_groups`;

CREATE TABLE `exp_category_groups` (
  `group_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_name` varchar(50) NOT NULL,
  `sort_order` char(1) NOT NULL DEFAULT 'a',
  `field_html_formatting` char(4) NOT NULL DEFAULT 'all',
  `can_edit_categories` text,
  `can_delete_categories` text,
  PRIMARY KEY (`group_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_category_groups` WRITE;
/*!40000 ALTER TABLE `exp_category_groups` DISABLE KEYS */;
INSERT INTO `exp_category_groups` (`group_id`,`site_id`,`group_name`,`sort_order`,`field_html_formatting`,`can_edit_categories`,`can_delete_categories`)
VALUES
	(1,1,'Profile','a','all','',''),
	(2,1,'Projects','a','all','','');

/*!40000 ALTER TABLE `exp_category_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_category_posts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_category_posts`;

CREATE TABLE `exp_category_posts` (
  `entry_id` int(10) unsigned NOT NULL,
  `cat_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`entry_id`,`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `exp_category_posts` WRITE;
/*!40000 ALTER TABLE `exp_category_posts` DISABLE KEYS */;
INSERT INTO `exp_category_posts` (`entry_id`,`cat_id`)
VALUES
	(1,1),
	(2,2),
	(3,7);

/*!40000 ALTER TABLE `exp_category_posts` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_channel_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_channel_data`;

CREATE TABLE `exp_channel_data` (
  `entry_id` int(10) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `channel_id` int(4) unsigned NOT NULL,
  `field_id_1` text,
  `field_ft_1` tinytext,
  `field_id_2` text,
  `field_ft_2` tinytext,
  `field_id_3` text,
  `field_ft_3` tinytext,
  `field_id_4` text,
  `field_ft_4` tinytext,
  `field_id_5` text,
  `field_ft_5` tinytext,
  `field_id_6` text,
  `field_ft_6` tinytext,
  `field_id_7` text,
  `field_ft_7` tinytext,
  `field_id_8` text,
  `field_ft_8` tinytext,
  `field_id_9` text,
  `field_ft_9` tinytext,
  `field_id_10` text,
  `field_ft_10` tinytext,
  `field_id_12` text,
  `field_ft_12` tinytext,
  `field_id_13` text,
  `field_ft_13` tinytext,
  `field_id_14` text,
  `field_ft_14` tinytext,
  `field_id_15` text,
  `field_ft_15` tinytext,
  `field_id_16` text,
  `field_ft_16` tinytext,
  `field_id_17` text,
  `field_ft_17` tinytext,
  `field_id_18` text,
  `field_ft_18` tinytext,
  PRIMARY KEY (`entry_id`),
  KEY `channel_id` (`channel_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `exp_channel_data` WRITE;
/*!40000 ALTER TABLE `exp_channel_data` DISABLE KEYS */;
INSERT INTO `exp_channel_data` (`entry_id`,`site_id`,`channel_id`,`field_id_1`,`field_ft_1`,`field_id_2`,`field_ft_2`,`field_id_3`,`field_ft_3`,`field_id_4`,`field_ft_4`,`field_id_5`,`field_ft_5`,`field_id_6`,`field_ft_6`,`field_id_7`,`field_ft_7`,`field_id_8`,`field_ft_8`,`field_id_9`,`field_ft_9`,`field_id_10`,`field_ft_10`,`field_id_12`,`field_ft_12`,`field_id_13`,`field_ft_13`,`field_id_14`,`field_ft_14`,`field_id_15`,`field_ft_15`,`field_id_16`,`field_ft_16`,`field_id_17`,`field_ft_17`,`field_id_18`,`field_ft_18`)
VALUES
	(1,1,1,'Born and raised in The Netherlands and schooled as a Master of Arts in Film Studies. During these studies he was sidetracked by something called the World Wide Web in 1995 and it has been both his work- and playfield ever since.\n\nYears of self teaching and working for several companies eventually lead to landing a position at Eden, now Edenspiekermann before moving to Singapore in 2007.','xhtml','nilshendriks.com','none','Front End Web Developer','none','HTML\nCSS\nunobtrusive Javascript (jQuery)\nWeb Standards Consulting','xhtml','1','none','Singapore','none','1','none','','none','1','none','','none','1','none',NULL,'none',NULL,'none',NULL,'xhtml',NULL,'xhtml',NULL,'xhtml',NULL,'none'),
	(2,1,1,'We are a small design consultancy located in Singapore. We create gorgeous, engaging and accessible web sites.','xhtml','artminister.com','none','Founder','none','Front-end Developer\nhtml, css, jQuery\nExpression Engine Development','xhtml','1','none','Singapore','none','','none','','none','1','none','','none','1','none',NULL,'none',NULL,'none',NULL,'xhtml',NULL,'xhtml',NULL,'xhtml',NULL,'none'),
	(3,1,2,'Contact Singapore is an alliance of the Singapore Economic Development Board and Ministry of Manpower.\n\nIt aims to attract global talent to work, invest and live in Singapore.','xhtml','http://www.contactsingapore.sg/','none','','none','','xhtml','','none','Afghanistan','none','1','none','','none','','none','','none','1','none','Singapore Economic Development Board\nhttp://www.sedb.com/\nSingapore Economic Development Board\nhttp://www.sedb.com/\nMinistry of Manpower\nhttp://www.mom.gov.sg/\nMinistry of Manpower\nhttp://www.mom.gov.sg/\n','none',NULL,'none',NULL,'xhtml',NULL,'xhtml',NULL,'xhtml',NULL,'none'),
	(4,1,3,'',NULL,'',NULL,'',NULL,'',NULL,'',NULL,'',NULL,'',NULL,'',NULL,'',NULL,'',NULL,'',NULL,'',NULL,'',NULL,'',NULL,'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.','xhtml','Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\n\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.','xhtml','1','none');

/*!40000 ALTER TABLE `exp_channel_data` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_channel_entries_autosave
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_channel_entries_autosave`;

CREATE TABLE `exp_channel_entries_autosave` (
  `entry_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `original_entry_id` int(10) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `channel_id` int(4) unsigned NOT NULL,
  `author_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pentry_id` int(10) NOT NULL DEFAULT '0',
  `forum_topic_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(16) NOT NULL,
  `title` varchar(100) NOT NULL,
  `url_title` varchar(75) NOT NULL,
  `status` varchar(50) NOT NULL,
  `versioning_enabled` char(1) NOT NULL DEFAULT 'n',
  `view_count_one` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count_two` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count_three` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count_four` int(10) unsigned NOT NULL DEFAULT '0',
  `allow_comments` varchar(1) NOT NULL DEFAULT 'y',
  `sticky` varchar(1) NOT NULL DEFAULT 'n',
  `entry_date` int(10) NOT NULL,
  `dst_enabled` varchar(1) NOT NULL DEFAULT 'n',
  `year` char(4) NOT NULL,
  `month` char(2) NOT NULL,
  `day` char(3) NOT NULL,
  `expiration_date` int(10) NOT NULL DEFAULT '0',
  `comment_expiration_date` int(10) NOT NULL DEFAULT '0',
  `edit_date` bigint(14) DEFAULT NULL,
  `recent_comment_date` int(10) DEFAULT NULL,
  `comment_total` int(4) unsigned NOT NULL DEFAULT '0',
  `entry_data` text,
  PRIMARY KEY (`entry_id`),
  KEY `channel_id` (`channel_id`),
  KEY `author_id` (`author_id`),
  KEY `url_title` (`url_title`),
  KEY `status` (`status`),
  KEY `entry_date` (`entry_date`),
  KEY `expiration_date` (`expiration_date`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_channel_entries_autosave` WRITE;
/*!40000 ALTER TABLE `exp_channel_entries_autosave` DISABLE KEYS */;
INSERT INTO `exp_channel_entries_autosave` (`entry_id`,`original_entry_id`,`site_id`,`channel_id`,`author_id`,`pentry_id`,`forum_topic_id`,`ip_address`,`title`,`url_title`,`status`,`versioning_enabled`,`view_count_one`,`view_count_two`,`view_count_three`,`view_count_four`,`allow_comments`,`sticky`,`entry_date`,`dst_enabled`,`year`,`month`,`day`,`expiration_date`,`comment_expiration_date`,`edit_date`,`recent_comment_date`,`comment_total`,`entry_data`)
VALUES
	(9,0,1,1,1,0,NULL,'127.0.0.1','autosave_1304418730','autosave_1304418730','open','y',0,0,0,0,'y','n',1304418609,'n','2011','05','03',0,0,20110503183210,0,0,'a:21:{s:8:\"entry_id\";i:9;s:10:\"channel_id\";s:1:\"1\";s:7:\"site_id\";s:1:\"1\";s:10:\"field_id_1\";s:0:\"\";s:10:\"field_id_2\";s:0:\"\";s:10:\"field_id_3\";s:0:\"\";s:10:\"field_id_4\";s:0:\"\";s:10:\"field_id_5\";s:0:\"\";s:10:\"field_id_6\";s:11:\"Afghanistan\";s:10:\"field_id_7\";s:0:\"\";s:10:\"field_id_8\";s:0:\"\";s:10:\"field_id_9\";s:0:\"\";s:11:\"field_id_10\";s:0:\"\";s:11:\"field_id_12\";s:1:\"1\";s:11:\"field_id_13\";s:0:\"\";s:11:\"field_id_14\";s:0:\"\";s:11:\"field_id_15\";s:0:\"\";s:17:\"original_entry_id\";i:0;s:24:\"seo_lite__seo_lite_title\";s:0:\"\";s:27:\"seo_lite__seo_lite_keywords\";s:0:\"\";s:30:\"seo_lite__seo_lite_description\";s:0:\"\";}'),
	(7,1,1,1,1,0,NULL,'127.0.0.1','Nils Hendriks','nils-hendriks','open','y',0,0,0,0,'y','n',1301998406,'n','2011','04','05',0,0,20110503181127,0,0,'a:20:{s:10:\"channel_id\";s:1:\"1\";s:10:\"field_id_1\";s:389:\"Born and raised in The Netherlands and schooled as a Master of Arts in Film Studies. During these studies he was sidetracked by something called the World Wide Web in 1995 and it has been both his work- and playfield ever since.\n\nYears of self teaching and working for several companies eventually lead to landing a position at Eden, now Edenspiekermann before moving to Singapore in 2007.\";s:10:\"field_id_2\";s:16:\"nilshendriks.com\";s:10:\"field_id_3\";s:23:\"Front End Web Developer\";s:10:\"field_id_4\";s:65:\"HTML\nCSS\nunobtrusive Javascript (jQuery)\nWeb Standards Consulting\";s:10:\"field_id_5\";s:1:\"1\";s:10:\"field_id_6\";s:9:\"Singapore\";s:10:\"field_id_7\";s:1:\"1\";s:10:\"field_id_8\";s:0:\"\";s:10:\"field_id_9\";s:1:\"1\";s:11:\"field_id_10\";s:0:\"\";s:11:\"field_id_12\";s:1:\"1\";s:11:\"field_id_13\";s:0:\"\";s:11:\"field_id_14\";s:1:\"y\";s:8:\"entry_id\";s:1:\"1\";s:17:\"original_entry_id\";s:1:\"1\";s:24:\"seo_lite__seo_lite_title\";s:0:\"\";s:27:\"seo_lite__seo_lite_keywords\";s:0:\"\";s:30:\"seo_lite__seo_lite_description\";s:0:\"\";s:8:\"category\";a:1:{i:0;s:1:\"1\";}}'),
	(8,3,1,2,1,0,NULL,'127.0.0.1','Contact Singapore','contact-singapore','open','y',0,0,0,0,'y','n',1304416466,'n','2011','05','03',0,0,20110503182827,0,0,'a:21:{s:10:\"channel_id\";s:1:\"2\";s:10:\"field_id_1\";s:175:\"Contact Singapore is an alliance of the Singapore Economic Development Board and Ministry of Manpower.\n\nIt aims to attract global talent to work, invest and live in Singapore.\";s:10:\"field_id_2\";s:31:\"http://www.contactsingapore.sg/\";s:10:\"field_id_3\";s:0:\"\";s:10:\"field_id_4\";s:0:\"\";s:10:\"field_id_5\";s:0:\"\";s:10:\"field_id_6\";s:11:\"Afghanistan\";s:10:\"field_id_7\";s:1:\"1\";s:10:\"field_id_8\";s:0:\"\";s:10:\"field_id_9\";s:0:\"\";s:11:\"field_id_10\";s:1:\"1\";s:11:\"field_id_12\";s:1:\"1\";s:11:\"field_id_13\";s:204:\"Singapore Economic Development Board\nhttp://www.sedb.com/\nSingapore Economic Development Board\nhttp://www.sedb.com/\nMinistry of Manpower\nhttp://www.mom.gov.sg/\nMinistry of Manpower\nhttp://www.mom.gov.sg/\n\";s:11:\"field_id_14\";s:0:\"\";s:11:\"field_id_15\";s:0:\"\";s:8:\"entry_id\";s:1:\"3\";s:17:\"original_entry_id\";s:1:\"3\";s:24:\"seo_lite__seo_lite_title\";s:0:\"\";s:27:\"seo_lite__seo_lite_keywords\";s:0:\"\";s:30:\"seo_lite__seo_lite_description\";s:0:\"\";s:8:\"category\";a:1:{i:0;s:1:\"7\";}}');

/*!40000 ALTER TABLE `exp_channel_entries_autosave` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_channel_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_channel_fields`;

CREATE TABLE `exp_channel_fields` (
  `field_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_id` int(4) unsigned NOT NULL,
  `field_name` varchar(32) NOT NULL,
  `field_label` varchar(50) NOT NULL,
  `field_instructions` text,
  `field_type` varchar(50) NOT NULL DEFAULT 'text',
  `field_list_items` text NOT NULL,
  `field_pre_populate` char(1) NOT NULL DEFAULT 'n',
  `field_pre_channel_id` int(6) unsigned DEFAULT NULL,
  `field_pre_field_id` int(6) unsigned DEFAULT NULL,
  `field_related_to` varchar(12) NOT NULL DEFAULT 'channel',
  `field_related_id` int(6) unsigned NOT NULL DEFAULT '0',
  `field_related_orderby` varchar(12) NOT NULL DEFAULT 'date',
  `field_related_sort` varchar(4) NOT NULL DEFAULT 'desc',
  `field_related_max` smallint(4) NOT NULL DEFAULT '0',
  `field_ta_rows` tinyint(2) DEFAULT '8',
  `field_maxl` smallint(3) DEFAULT NULL,
  `field_required` char(1) NOT NULL DEFAULT 'n',
  `field_text_direction` char(3) NOT NULL DEFAULT 'ltr',
  `field_search` char(1) NOT NULL DEFAULT 'n',
  `field_is_hidden` char(1) NOT NULL DEFAULT 'n',
  `field_fmt` varchar(40) NOT NULL DEFAULT 'xhtml',
  `field_show_fmt` char(1) NOT NULL DEFAULT 'y',
  `field_order` int(3) unsigned NOT NULL,
  `field_content_type` varchar(20) NOT NULL DEFAULT 'any',
  `field_settings` text,
  PRIMARY KEY (`field_id`),
  KEY `group_id` (`group_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_channel_fields` WRITE;
/*!40000 ALTER TABLE `exp_channel_fields` DISABLE KEYS */;
INSERT INTO `exp_channel_fields` (`field_id`,`site_id`,`group_id`,`field_name`,`field_label`,`field_instructions`,`field_type`,`field_list_items`,`field_pre_populate`,`field_pre_channel_id`,`field_pre_field_id`,`field_related_to`,`field_related_id`,`field_related_orderby`,`field_related_sort`,`field_related_max`,`field_ta_rows`,`field_maxl`,`field_required`,`field_text_direction`,`field_search`,`field_is_hidden`,`field_fmt`,`field_show_fmt`,`field_order`,`field_content_type`,`field_settings`)
VALUES
	(1,1,1,'background','Background','','textarea','','0',0,0,'channel',1,'title','desc',0,6,128,'n','ltr','y','n','xhtml','n',1,'any','YTo2OntzOjE4OiJmaWVsZF9zaG93X3NtaWxleXMiO3M6MToibiI7czoxOToiZmllbGRfc2hvd19nbG9zc2FyeSI7czoxOiJuIjtzOjIxOiJmaWVsZF9zaG93X3NwZWxsY2hlY2siO3M6MToibiI7czoyNjoiZmllbGRfc2hvd19mb3JtYXR0aW5nX2J0bnMiO3M6MToibiI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtzOjE6Im4iO3M6MjA6ImZpZWxkX3Nob3dfd3JpdGVtb2RlIjtzOjE6Im4iO30='),
	(2,1,1,'portfolio','Portfolio URL','','text','','0',0,0,'channel',1,'title','desc',0,6,512,'n','ltr','n','n','none','n',2,'any','YTo3OntzOjE4OiJmaWVsZF9jb250ZW50X3RleHQiO2I6MDtzOjE4OiJmaWVsZF9zaG93X3NtaWxleXMiO3M6MToibiI7czoxOToiZmllbGRfc2hvd19nbG9zc2FyeSI7czoxOiJuIjtzOjIxOiJmaWVsZF9zaG93X3NwZWxsY2hlY2siO3M6MToibiI7czoyNjoiZmllbGRfc2hvd19mb3JtYXR0aW5nX2J0bnMiO3M6MToibiI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtzOjE6Im4iO3M6MjA6ImZpZWxkX3Nob3dfd3JpdGVtb2RlIjtzOjE6Im4iO30='),
	(3,1,1,'jobtitle','Job Title','','text','','0',0,0,'channel',1,'title','desc',0,6,512,'n','ltr','n','n','none','n',3,'any','YTo3OntzOjE4OiJmaWVsZF9jb250ZW50X3RleHQiO2I6MDtzOjE4OiJmaWVsZF9zaG93X3NtaWxleXMiO3M6MToibiI7czoxOToiZmllbGRfc2hvd19nbG9zc2FyeSI7czoxOiJuIjtzOjIxOiJmaWVsZF9zaG93X3NwZWxsY2hlY2siO3M6MToibiI7czoyNjoiZmllbGRfc2hvd19mb3JtYXR0aW5nX2J0bnMiO3M6MToibiI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtzOjE6Im4iO3M6MjA6ImZpZWxkX3Nob3dfd3JpdGVtb2RlIjtzOjE6Im4iO30='),
	(4,1,1,'skills','Skillset','','pt_list','','0',0,0,'channel',1,'title','desc',0,6,128,'n','ltr','y','n','xhtml','n',4,'any','YTo2OntzOjE4OiJmaWVsZF9zaG93X3NtaWxleXMiO3M6MToibiI7czoxOToiZmllbGRfc2hvd19nbG9zc2FyeSI7czoxOiJuIjtzOjIxOiJmaWVsZF9zaG93X3NwZWxsY2hlY2siO3M6MToibiI7czoyNjoiZmllbGRfc2hvd19mb3JtYXR0aW5nX2J0bnMiO3M6MToibiI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtzOjE6Im4iO3M6MjA6ImZpZWxkX3Nob3dfd3JpdGVtb2RlIjtzOjE6Im4iO30='),
	(5,1,1,'projects','Projects','','matrix','','0',0,0,'channel',1,'title','desc',0,6,128,'n','ltr','n','n','none','n',5,'any','YTo4OntzOjg6Im1heF9yb3dzIjtzOjA6IiI7czo3OiJjb2xfaWRzIjthOjQ6e2k6MDtzOjE6IjEiO2k6MTtzOjE6IjIiO2k6MjtzOjE6IjMiO2k6MztzOjE6IjQiO31zOjE4OiJmaWVsZF9zaG93X3NtaWxleXMiO3M6MToibiI7czoxOToiZmllbGRfc2hvd19nbG9zc2FyeSI7czoxOiJuIjtzOjIxOiJmaWVsZF9zaG93X3NwZWxsY2hlY2siO3M6MToibiI7czoyNjoiZmllbGRfc2hvd19mb3JtYXR0aW5nX2J0bnMiO3M6MToibiI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtzOjE6Im4iO3M6MjA6ImZpZWxkX3Nob3dfd3JpdGVtb2RlIjtzOjE6Im4iO30='),
	(6,1,1,'country','Country','','select','Afghanistan\nAlbania\nAlgeria\nAndorra\nAngola\nAntigua & Deps\nArgentina\nArmenia\nAustralia\nAustria\nAzerbaijan\nBahamas\nBahrain\nBangladesh\nBarbados\nBelarus\nBelgium\nBelize\nBenin\nBhutan\nBolivia\nBosnia Herzegovina\nBotswana\nBrazil\nBrunei\nBulgaria\nBurkina\nBurundi\nCambodia\nCameroon\nCanada\nCape Verde\nCentral African Rep\nChad\nChile\nChina\nColombia\nComoros\nCongo\nCongo {Democratic Rep}\nCosta Rica\nCroatia\nCuba\nCyprus\nCzech Republic\nDenmark\nDjibouti\nDominica\nDominican Republic\nEast Timor\nEcuador\nEgypt\nEl Salvador\nEquatorial Guinea\nEritrea\nEstonia\nEthiopia\nFiji\nFinland\nFrance\nGabon\nGambia\nGeorgia\nGermany\nGhana\nGreece\nGrenada\nGuatemala\nGuinea\nGuinea-Bissau\nGuyana\nHaiti\nHonduras\nHungary\nIceland\nIndia\nIndonesia\nIran\nIraq\nIreland {Republic}\nIsrael\nItaly\nIvory Coast\nJamaica\nJapan\nJordan\nKazakhstan\nKenya\nKiribati\nKorea North\nKorea South\nKosovo\nKuwait\nKyrgyzstan\nLaos\nLatvia\nLebanon\nLesotho\nLiberia\nLibya\nLiechtenstein\nLithuania\nLuxembourg\nMacedonia\nMadagascar\nMalawi\nMalaysia\nMaldives\nMali\nMalta\nMarshall Islands\nMauritania\nMauritius\nMexico\nMicronesia\nMoldova\nMonaco\nMongolia\nMontenegro\nMorocco\nMozambique\nMyanmar, {Burma}\nNamibia\nNauru\nNepal\nNetherlands\nNew Zealand\nNicaragua\nNiger\nNigeria\nNorway\nOman\nPakistan\nPalau\nPanama\nPapua New Guinea\nParaguay\nPeru\nPhilippines\nPoland\nPortugal\nQatar\nRomania\nRussian Federation\nRwanda\nSt Kitts & Nevis\nSt Lucia\nSaint Vincent & the Grenadines\nSamoa\nSan Marino\nSao Tome & Principe\nSaudi Arabia\nSenegal\nSerbia\nSeychelles\nSierra Leone\nSingapore\nSlovakia\nSlovenia\nSolomon Islands\nSomalia\nSouth Africa\nSpain\nSri Lanka\nSudan\nSuriname\nSwaziland\nSweden\nSwitzerland\nSyria\nTaiwan\nTajikistan\nTanzania\nThailand\nTogo\nTonga\nTrinidad & Tobago\nTunisia\nTurkey\nTurkmenistan\nTuvalu\nUganda\nUkraine\nUnited Arab Emirates\nUnited Kingdom\nUnited States\nUruguay\nUzbekistan\nVanuatu\nVatican City\nVenezuela\nVietnam\nYemen\nZambia\nZimbabwe','n',0,0,'channel',1,'title','desc',0,6,128,'n','ltr','n','n','none','n',6,'any','YTo2OntzOjE4OiJmaWVsZF9zaG93X3NtaWxleXMiO3M6MToibiI7czoxOToiZmllbGRfc2hvd19nbG9zc2FyeSI7czoxOiJuIjtzOjIxOiJmaWVsZF9zaG93X3NwZWxsY2hlY2siO3M6MToibiI7czoyNjoiZmllbGRfc2hvd19mb3JtYXR0aW5nX2J0bnMiO3M6MToibiI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtzOjE6Im4iO3M6MjA6ImZpZWxkX3Nob3dfd3JpdGVtb2RlIjtzOjE6Im4iO30='),
	(7,1,1,'images','Images','','matrix','','0',0,0,'channel',1,'title','desc',0,6,128,'n','ltr','n','n','none','n',7,'any','YTo4OntzOjg6Im1heF9yb3dzIjtzOjE6IjUiO3M6NzoiY29sX2lkcyI7YToyOntpOjA7czoxOiI1IjtpOjE7czoxOiI2Ijt9czoxODoiZmllbGRfc2hvd19zbWlsZXlzIjtzOjE6Im4iO3M6MTk6ImZpZWxkX3Nob3dfZ2xvc3NhcnkiO3M6MToibiI7czoyMToiZmllbGRfc2hvd19zcGVsbGNoZWNrIjtzOjE6Im4iO3M6MjY6ImZpZWxkX3Nob3dfZm9ybWF0dGluZ19idG5zIjtzOjE6Im4iO3M6MjQ6ImZpZWxkX3Nob3dfZmlsZV9zZWxlY3RvciI7czoxOiJuIjtzOjIwOiJmaWVsZF9zaG93X3dyaXRlbW9kZSI7czoxOiJuIjt9'),
	(8,1,1,'offices','Offices (Applicable to Companies)','','matrix','','0',0,0,'channel',1,'title','desc',0,6,128,'n','ltr','n','n','none','n',8,'any','YTo4OntzOjg6Im1heF9yb3dzIjtzOjA6IiI7czo3OiJjb2xfaWRzIjthOjI6e2k6MDtzOjE6IjciO2k6MTtzOjE6IjgiO31zOjE4OiJmaWVsZF9zaG93X3NtaWxleXMiO3M6MToibiI7czoxOToiZmllbGRfc2hvd19nbG9zc2FyeSI7czoxOiJuIjtzOjIxOiJmaWVsZF9zaG93X3NwZWxsY2hlY2siO3M6MToibiI7czoyNjoiZmllbGRfc2hvd19mb3JtYXR0aW5nX2J0bnMiO3M6MToibiI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtzOjE6Im4iO3M6MjA6ImZpZWxkX3Nob3dfd3JpdGVtb2RlIjtzOjE6Im4iO30='),
	(9,1,1,'address','Address','','matrix','','0',0,0,'channel',1,'title','desc',0,6,128,'n','ltr','n','n','none','n',9,'any','YTo4OntzOjg6Im1heF9yb3dzIjtzOjE6IjEiO3M6NzoiY29sX2lkcyI7YTozOntpOjA7czoxOiI5IjtpOjE7czoyOiIxMCI7aToyO3M6MjoiMTEiO31zOjE4OiJmaWVsZF9zaG93X3NtaWxleXMiO3M6MToibiI7czoxOToiZmllbGRfc2hvd19nbG9zc2FyeSI7czoxOiJuIjtzOjIxOiJmaWVsZF9zaG93X3NwZWxsY2hlY2siO3M6MToibiI7czoyNjoiZmllbGRfc2hvd19mb3JtYXR0aW5nX2J0bnMiO3M6MToibiI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtzOjE6Im4iO3M6MjA6ImZpZWxkX3Nob3dfd3JpdGVtb2RlIjtzOjE6Im4iO30='),
	(10,1,1,'contributors','Contributors (Applicable to Websites)','','matrix','','0',0,0,'channel',1,'title','desc',0,6,128,'n','ltr','n','n','none','n',10,'any','YTo4OntzOjg6Im1heF9yb3dzIjtzOjA6IiI7czo3OiJjb2xfaWRzIjthOjQ6e2k6MDtzOjI6IjEyIjtpOjE7czoyOiIxMyI7aToyO3M6MjoiMTQiO2k6MztzOjI6IjE5Ijt9czoxODoiZmllbGRfc2hvd19zbWlsZXlzIjtzOjE6Im4iO3M6MTk6ImZpZWxkX3Nob3dfZ2xvc3NhcnkiO3M6MToibiI7czoyMToiZmllbGRfc2hvd19zcGVsbGNoZWNrIjtzOjE6Im4iO3M6MjY6ImZpZWxkX3Nob3dfZm9ybWF0dGluZ19idG5zIjtzOjE6Im4iO3M6MjQ6ImZpZWxkX3Nob3dfZmlsZV9zZWxlY3RvciI7czoxOiJuIjtzOjIwOiJmaWVsZF9zaG93X3dyaXRlbW9kZSI7czoxOiJuIjt9'),
	(12,1,1,'contact','Contact Method','','matrix','','0',0,0,'channel',1,'title','desc',0,6,128,'n','ltr','n','n','none','n',11,'any','YTo4OntzOjg6Im1heF9yb3dzIjtzOjE6IjUiO3M6NzoiY29sX2lkcyI7YToyOntpOjA7czoyOiIxNSI7aToxO3M6MjoiMTYiO31zOjE4OiJmaWVsZF9zaG93X3NtaWxleXMiO3M6MToibiI7czoxOToiZmllbGRfc2hvd19nbG9zc2FyeSI7czoxOiJuIjtzOjIxOiJmaWVsZF9zaG93X3NwZWxsY2hlY2siO3M6MToibiI7czoyNjoiZmllbGRfc2hvd19mb3JtYXR0aW5nX2J0bnMiO3M6MToibiI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtzOjE6Im4iO3M6MjA6ImZpZWxkX3Nob3dfd3JpdGVtb2RlIjtzOjE6Im4iO30='),
	(13,1,1,'client','Client','By whom was this project commissioned?','matrix','','0',0,0,'channel',1,'title','desc',0,6,128,'n','ltr','n','n','none','n',12,'any','YTo4OntzOjg6Im1heF9yb3dzIjtzOjA6IiI7czo3OiJjb2xfaWRzIjthOjI6e2k6MDtzOjI6IjE3IjtpOjE7czoyOiIxOCI7fXM6MTg6ImZpZWxkX3Nob3dfc21pbGV5cyI7czoxOiJuIjtzOjE5OiJmaWVsZF9zaG93X2dsb3NzYXJ5IjtzOjE6Im4iO3M6MjE6ImZpZWxkX3Nob3dfc3BlbGxjaGVjayI7czoxOiJuIjtzOjI2OiJmaWVsZF9zaG93X2Zvcm1hdHRpbmdfYnRucyI7czoxOiJuIjtzOjI0OiJmaWVsZF9zaG93X2ZpbGVfc2VsZWN0b3IiO3M6MToibiI7czoyMDoiZmllbGRfc2hvd193cml0ZW1vZGUiO3M6MToibiI7fQ=='),
	(14,1,1,'related-projects','Related Projects','','playa','','0',0,0,'channel',1,'title','desc',0,6,128,'n','ltr','n','n','none','n',13,'any','YToxMDp7czo1OiJtdWx0aSI7czoxOiJ5IjtzOjg6ImNoYW5uZWxzIjthOjE6e2k6MDtzOjE6IjIiO31zOjc6Im9yZGVyYnkiO3M6NToidGl0bGUiO3M6NDoic29ydCI7czozOiJBU0MiO3M6MTg6ImZpZWxkX3Nob3dfc21pbGV5cyI7czoxOiJuIjtzOjE5OiJmaWVsZF9zaG93X2dsb3NzYXJ5IjtzOjE6Im4iO3M6MjE6ImZpZWxkX3Nob3dfc3BlbGxjaGVjayI7czoxOiJuIjtzOjI2OiJmaWVsZF9zaG93X2Zvcm1hdHRpbmdfYnRucyI7czoxOiJuIjtzOjI0OiJmaWVsZF9zaG93X2ZpbGVfc2VsZWN0b3IiO3M6MToibiI7czoyMDoiZmllbGRfc2hvd193cml0ZW1vZGUiO3M6MToibiI7fQ=='),
	(15,1,1,'site-different','Site different from Original ?','','checkboxes','Is the live project different from the delivered project','n',0,0,'channel',1,'title','desc',0,6,128,'n','ltr','n','n','xhtml','n',14,'any','YTo2OntzOjE4OiJmaWVsZF9zaG93X3NtaWxleXMiO3M6MToibiI7czoxOToiZmllbGRfc2hvd19nbG9zc2FyeSI7czoxOiJuIjtzOjIxOiJmaWVsZF9zaG93X3NwZWxsY2hlY2siO3M6MToibiI7czoyNjoiZmllbGRfc2hvd19mb3JtYXR0aW5nX2J0bnMiO3M6MToibiI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtzOjE6Im4iO3M6MjA6ImZpZWxkX3Nob3dfd3JpdGVtb2RlIjtzOjE6Im4iO30='),
	(16,1,3,'summary','Summary','','textarea','','0',0,0,'channel',3,'title','desc',0,6,128,'n','ltr','n','n','xhtml','n',1,'any','YTo2OntzOjE4OiJmaWVsZF9zaG93X3NtaWxleXMiO3M6MToibiI7czoxOToiZmllbGRfc2hvd19nbG9zc2FyeSI7czoxOiJuIjtzOjIxOiJmaWVsZF9zaG93X3NwZWxsY2hlY2siO3M6MToibiI7czoyNjoiZmllbGRfc2hvd19mb3JtYXR0aW5nX2J0bnMiO3M6MToibiI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtzOjE6Im4iO3M6MjA6ImZpZWxkX3Nob3dfd3JpdGVtb2RlIjtzOjE6Im4iO30='),
	(17,1,3,'body','Body','','textarea','','0',0,0,'channel',3,'title','desc',0,12,128,'n','ltr','n','n','xhtml','n',2,'any','YTo2OntzOjE4OiJmaWVsZF9zaG93X3NtaWxleXMiO3M6MToibiI7czoxOToiZmllbGRfc2hvd19nbG9zc2FyeSI7czoxOiJuIjtzOjIxOiJmaWVsZF9zaG93X3NwZWxsY2hlY2siO3M6MToibiI7czoyNjoiZmllbGRfc2hvd19mb3JtYXR0aW5nX2J0bnMiO3M6MToibiI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtzOjE6Im4iO3M6MjA6ImZpZWxkX3Nob3dfd3JpdGVtb2RlIjtzOjE6Im4iO30='),
	(18,1,3,'date-time','Date, Time and Location of the Event','','matrix','','0',0,0,'channel',3,'title','desc',0,6,128,'n','ltr','n','n','none','n',3,'any','YTo4OntzOjg6Im1heF9yb3dzIjtzOjE6IjEiO3M6NzoiY29sX2lkcyI7YToyOntpOjA7czoyOiIyMCI7aToxO3M6MjoiMjEiO31zOjE4OiJmaWVsZF9zaG93X3NtaWxleXMiO3M6MToibiI7czoxOToiZmllbGRfc2hvd19nbG9zc2FyeSI7czoxOiJuIjtzOjIxOiJmaWVsZF9zaG93X3NwZWxsY2hlY2siO3M6MToibiI7czoyNjoiZmllbGRfc2hvd19mb3JtYXR0aW5nX2J0bnMiO3M6MToibiI7czoyNDoiZmllbGRfc2hvd19maWxlX3NlbGVjdG9yIjtzOjE6Im4iO3M6MjA6ImZpZWxkX3Nob3dfd3JpdGVtb2RlIjtzOjE6Im4iO30=');

/*!40000 ALTER TABLE `exp_channel_fields` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_channel_member_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_channel_member_groups`;

CREATE TABLE `exp_channel_member_groups` (
  `group_id` smallint(4) unsigned NOT NULL,
  `channel_id` int(6) unsigned NOT NULL,
  PRIMARY KEY (`group_id`,`channel_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_channel_titles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_channel_titles`;

CREATE TABLE `exp_channel_titles` (
  `entry_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `channel_id` int(4) unsigned NOT NULL,
  `author_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pentry_id` int(10) NOT NULL DEFAULT '0',
  `forum_topic_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(16) NOT NULL,
  `title` varchar(100) NOT NULL,
  `url_title` varchar(75) NOT NULL,
  `status` varchar(50) NOT NULL,
  `versioning_enabled` char(1) NOT NULL DEFAULT 'n',
  `view_count_one` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count_two` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count_three` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count_four` int(10) unsigned NOT NULL DEFAULT '0',
  `allow_comments` varchar(1) NOT NULL DEFAULT 'y',
  `sticky` varchar(1) NOT NULL DEFAULT 'n',
  `entry_date` int(10) NOT NULL,
  `dst_enabled` varchar(1) NOT NULL DEFAULT 'n',
  `year` char(4) NOT NULL,
  `month` char(2) NOT NULL,
  `day` char(3) NOT NULL,
  `expiration_date` int(10) NOT NULL DEFAULT '0',
  `comment_expiration_date` int(10) NOT NULL DEFAULT '0',
  `edit_date` bigint(14) DEFAULT NULL,
  `recent_comment_date` int(10) DEFAULT NULL,
  `comment_total` int(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`entry_id`),
  KEY `channel_id` (`channel_id`),
  KEY `author_id` (`author_id`),
  KEY `url_title` (`url_title`),
  KEY `status` (`status`),
  KEY `entry_date` (`entry_date`),
  KEY `expiration_date` (`expiration_date`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_channel_titles` WRITE;
/*!40000 ALTER TABLE `exp_channel_titles` DISABLE KEYS */;
INSERT INTO `exp_channel_titles` (`entry_id`,`site_id`,`channel_id`,`author_id`,`pentry_id`,`forum_topic_id`,`ip_address`,`title`,`url_title`,`status`,`versioning_enabled`,`view_count_one`,`view_count_two`,`view_count_three`,`view_count_four`,`allow_comments`,`sticky`,`entry_date`,`dst_enabled`,`year`,`month`,`day`,`expiration_date`,`comment_expiration_date`,`edit_date`,`recent_comment_date`,`comment_total`)
VALUES
	(1,1,1,1,0,NULL,'127.0.0.1','Nils Hendriks','nils-hendriks','open','y',0,0,0,0,'y','n',1301998387,'n','2011','04','05',0,0,20110405184308,0,0),
	(2,1,1,1,0,NULL,'127.0.0.1','Artminister','artminister','open','y',0,0,0,0,'y','n',1301999093,'n','2011','04','05',0,0,20110405182854,0,0),
	(3,1,2,1,0,NULL,'127.0.0.1','Contact Singapore','contact-singapore','open','y',0,0,0,0,'y','n',1304416446,'n','2011','05','03',0,0,20110503180907,0,0),
	(4,1,3,1,0,NULL,'127.0.0.1','Webshite Meetup 27th May','webshite-meetup-27th-may','open','y',0,0,0,0,'y','n',1304420281,'n','2011','05','03',0,0,20110503190002,0,0);

/*!40000 ALTER TABLE `exp_channel_titles` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_channels
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_channels`;

CREATE TABLE `exp_channels` (
  `channel_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `channel_name` varchar(40) NOT NULL,
  `channel_title` varchar(100) NOT NULL,
  `channel_url` varchar(100) NOT NULL,
  `channel_description` varchar(225) DEFAULT NULL,
  `channel_lang` varchar(12) NOT NULL,
  `total_entries` mediumint(8) NOT NULL DEFAULT '0',
  `total_comments` mediumint(8) NOT NULL DEFAULT '0',
  `last_entry_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_comment_date` int(10) unsigned NOT NULL DEFAULT '0',
  `cat_group` varchar(255) DEFAULT NULL,
  `status_group` int(4) unsigned DEFAULT NULL,
  `deft_status` varchar(50) NOT NULL DEFAULT 'open',
  `field_group` int(4) unsigned DEFAULT NULL,
  `search_excerpt` int(4) unsigned DEFAULT NULL,
  `deft_category` varchar(60) DEFAULT NULL,
  `deft_comments` char(1) NOT NULL DEFAULT 'y',
  `channel_require_membership` char(1) NOT NULL DEFAULT 'y',
  `channel_max_chars` int(5) unsigned DEFAULT NULL,
  `channel_html_formatting` char(4) NOT NULL DEFAULT 'all',
  `channel_allow_img_urls` char(1) NOT NULL DEFAULT 'y',
  `channel_auto_link_urls` char(1) NOT NULL DEFAULT 'y',
  `channel_notify` char(1) NOT NULL DEFAULT 'n',
  `channel_notify_emails` varchar(255) DEFAULT NULL,
  `comment_url` varchar(80) DEFAULT NULL,
  `comment_system_enabled` char(1) NOT NULL DEFAULT 'y',
  `comment_require_membership` char(1) NOT NULL DEFAULT 'n',
  `comment_use_captcha` char(1) NOT NULL DEFAULT 'n',
  `comment_moderate` char(1) NOT NULL DEFAULT 'n',
  `comment_max_chars` int(5) unsigned DEFAULT '5000',
  `comment_timelock` int(5) unsigned NOT NULL DEFAULT '0',
  `comment_require_email` char(1) NOT NULL DEFAULT 'y',
  `comment_text_formatting` char(5) NOT NULL DEFAULT 'xhtml',
  `comment_html_formatting` char(4) NOT NULL DEFAULT 'safe',
  `comment_allow_img_urls` char(1) NOT NULL DEFAULT 'n',
  `comment_auto_link_urls` char(1) NOT NULL DEFAULT 'y',
  `comment_notify` char(1) NOT NULL DEFAULT 'n',
  `comment_notify_authors` char(1) NOT NULL DEFAULT 'n',
  `comment_notify_emails` varchar(255) DEFAULT NULL,
  `comment_expiration` int(4) unsigned NOT NULL DEFAULT '0',
  `search_results_url` varchar(80) DEFAULT NULL,
  `ping_return_url` varchar(80) DEFAULT NULL,
  `show_button_cluster` char(1) NOT NULL DEFAULT 'y',
  `rss_url` varchar(80) DEFAULT NULL,
  `enable_versioning` char(1) NOT NULL DEFAULT 'n',
  `max_revisions` smallint(4) unsigned NOT NULL DEFAULT '10',
  `default_entry_title` varchar(100) DEFAULT NULL,
  `url_title_prefix` varchar(80) DEFAULT NULL,
  `live_look_template` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`channel_id`),
  KEY `cat_group` (`cat_group`),
  KEY `status_group` (`status_group`),
  KEY `field_group` (`field_group`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_channels` WRITE;
/*!40000 ALTER TABLE `exp_channels` DISABLE KEYS */;
INSERT INTO `exp_channels` (`channel_id`,`site_id`,`channel_name`,`channel_title`,`channel_url`,`channel_description`,`channel_lang`,`total_entries`,`total_comments`,`last_entry_date`,`last_comment_date`,`cat_group`,`status_group`,`deft_status`,`field_group`,`search_excerpt`,`deft_category`,`deft_comments`,`channel_require_membership`,`channel_max_chars`,`channel_html_formatting`,`channel_allow_img_urls`,`channel_auto_link_urls`,`channel_notify`,`channel_notify_emails`,`comment_url`,`comment_system_enabled`,`comment_require_membership`,`comment_use_captcha`,`comment_moderate`,`comment_max_chars`,`comment_timelock`,`comment_require_email`,`comment_text_formatting`,`comment_html_formatting`,`comment_allow_img_urls`,`comment_auto_link_urls`,`comment_notify`,`comment_notify_authors`,`comment_notify_emails`,`comment_expiration`,`search_results_url`,`ping_return_url`,`show_button_cluster`,`rss_url`,`enable_versioning`,`max_revisions`,`default_entry_title`,`url_title_prefix`,`live_look_template`)
VALUES
	(1,1,'profile','Profile','http://adi.vinay:8888/index.php','','en',2,0,1301999093,0,'1',1,'open',1,1,'','y','y',NULL,'all','y','n','n','','','y','n','n','n',5000,0,'y','xhtml','safe','n','y','n','n','',0,'','','y','','n',10,'','',0),
	(2,1,'projects','Projects','http://adi.vinay:8888/index.php',NULL,'en',1,0,1304416446,0,'2',1,'open',1,NULL,NULL,'y','y',NULL,'all','y','y','n',NULL,'','y','n','n','n',5000,0,'y','xhtml','safe','n','y','n','n',NULL,0,'',NULL,'y',NULL,'n',10,'','',0),
	(3,1,'meetups','Meetups','http://adi.vinay:8888/',NULL,'en',1,0,1304420281,0,NULL,1,'open',3,NULL,NULL,'y','y',NULL,'all','y','y','n',NULL,'','y','n','n','n',5000,0,'y','xhtml','safe','n','y','n','n',NULL,0,'',NULL,'y',NULL,'n',10,'','',0);

/*!40000 ALTER TABLE `exp_channels` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_comment_subscriptions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_comment_subscriptions`;

CREATE TABLE `exp_comment_subscriptions` (
  `subscription_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(10) unsigned DEFAULT NULL,
  `member_id` int(10) DEFAULT '0',
  `email` varchar(50) DEFAULT NULL,
  `subscription_date` varchar(10) DEFAULT NULL,
  `notification_sent` char(1) DEFAULT 'n',
  `hash` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`subscription_id`),
  KEY `entry_id` (`entry_id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_comments`;

CREATE TABLE `exp_comments` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) DEFAULT '1',
  `entry_id` int(10) unsigned DEFAULT '0',
  `channel_id` int(4) unsigned DEFAULT '1',
  `author_id` int(10) unsigned DEFAULT '0',
  `status` char(1) DEFAULT '0',
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `url` varchar(75) DEFAULT NULL,
  `location` varchar(50) DEFAULT NULL,
  `ip_address` varchar(16) DEFAULT NULL,
  `comment_date` int(10) DEFAULT NULL,
  `edit_date` int(10) DEFAULT NULL,
  `comment` text,
  PRIMARY KEY (`comment_id`),
  KEY `entry_id` (`entry_id`),
  KEY `channel_id` (`channel_id`),
  KEY `author_id` (`author_id`),
  KEY `status` (`status`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_cp_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_cp_log`;

CREATE TABLE `exp_cp_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `member_id` int(10) unsigned NOT NULL,
  `username` varchar(32) NOT NULL,
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `act_date` int(10) NOT NULL,
  `action` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_cp_log` WRITE;
/*!40000 ALTER TABLE `exp_cp_log` DISABLE KEYS */;
INSERT INTO `exp_cp_log` (`id`,`site_id`,`member_id`,`username`,`ip_address`,`act_date`,`action`)
VALUES
	(1,1,1,'rmdort','127.0.0.1',1301989675,'Logged in'),
	(2,1,1,'rmdort','127.0.0.1',1301989740,'Channel Created:&nbsp;&nbsp;Profile'),
	(3,1,1,'rmdort','127.0.0.1',1301989873,'Field Group Created:&nbsp;Profile'),
	(4,1,1,'rmdort','127.0.0.1',1301995936,'Logged in'),
	(5,1,1,'rmdort','127.0.0.1',1301997368,'Category Group Created:&nbsp;&nbsp;Profile'),
	(6,1,1,'rmdort','127.0.0.1',1301998067,'Channel Created:&nbsp;&nbsp;Projects'),
	(7,1,1,'rmdort','127.0.0.1',1301998076,'Field Group Created:&nbsp;Projects'),
	(8,1,1,'rmdort','127.0.0.1',1301998165,'Custom Field Deleted:&nbsp;Image'),
	(9,1,1,'rmdort','127.0.0.1',1301998674,'Field group Deleted:&nbsp;&nbsp;Projects'),
	(10,1,1,'rmdort','127.0.0.1',1302007743,'Logged in'),
	(11,1,1,'rmdort','127.0.0.1',1302023136,'Logged in'),
	(12,1,1,'rmdort','127.0.0.1',1303374972,'Logged in'),
	(13,1,1,'rmdort','127.0.0.1',1303375010,'Logged in'),
	(14,1,1,'rmdort','127.0.0.1',1303922299,'Logged in'),
	(15,1,1,'rmdort','127.0.0.1',1303922409,'Member profile created:&nbsp;&nbsp;Nils'),
	(16,1,1,'rmdort','127.0.0.1',1304415970,'Logged in'),
	(17,1,1,'rmdort','127.0.0.1',1304417274,'Category Group Created:&nbsp;&nbsp;Projects'),
	(18,1,1,'rmdort','127.0.0.1',1304420163,'Channel Created:&nbsp;&nbsp;Meetups'),
	(19,1,1,'rmdort','127.0.0.1',1304420171,'Field Group Created:&nbsp;Meetups'),
	(20,1,1,'rmdort','127.0.0.1',1304500803,'Logged in');

/*!40000 ALTER TABLE `exp_cp_log` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_cp_search_index
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_cp_search_index`;

CREATE TABLE `exp_cp_search_index` (
  `search_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `controller` varchar(20) DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `language` varchar(20) DEFAULT NULL,
  `access` varchar(50) DEFAULT NULL,
  `keywords` text,
  PRIMARY KEY (`search_id`),
  FULLTEXT KEY `keywords` (`keywords`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_email_cache
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_email_cache`;

CREATE TABLE `exp_email_cache` (
  `cache_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `cache_date` int(10) unsigned NOT NULL DEFAULT '0',
  `total_sent` int(6) unsigned NOT NULL,
  `from_name` varchar(70) NOT NULL,
  `from_email` varchar(70) NOT NULL,
  `recipient` text NOT NULL,
  `cc` text NOT NULL,
  `bcc` text NOT NULL,
  `recipient_array` mediumtext NOT NULL,
  `subject` varchar(120) NOT NULL,
  `message` mediumtext NOT NULL,
  `plaintext_alt` mediumtext NOT NULL,
  `mailinglist` char(1) NOT NULL DEFAULT 'n',
  `mailtype` varchar(6) NOT NULL,
  `text_fmt` varchar(40) NOT NULL,
  `wordwrap` char(1) NOT NULL DEFAULT 'y',
  `priority` char(1) NOT NULL DEFAULT '3',
  PRIMARY KEY (`cache_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_email_cache_mg
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_email_cache_mg`;

CREATE TABLE `exp_email_cache_mg` (
  `cache_id` int(6) unsigned NOT NULL,
  `group_id` smallint(4) NOT NULL,
  PRIMARY KEY (`cache_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_email_cache_ml
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_email_cache_ml`;

CREATE TABLE `exp_email_cache_ml` (
  `cache_id` int(6) unsigned NOT NULL,
  `list_id` smallint(4) NOT NULL,
  PRIMARY KEY (`cache_id`,`list_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_email_console_cache
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_email_console_cache`;

CREATE TABLE `exp_email_console_cache` (
  `cache_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `cache_date` int(10) unsigned NOT NULL DEFAULT '0',
  `member_id` int(10) unsigned NOT NULL,
  `member_name` varchar(50) NOT NULL,
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `recipient` varchar(70) NOT NULL,
  `recipient_name` varchar(50) NOT NULL,
  `subject` varchar(120) NOT NULL,
  `message` mediumtext NOT NULL,
  PRIMARY KEY (`cache_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_email_tracker
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_email_tracker`;

CREATE TABLE `exp_email_tracker` (
  `email_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_date` int(10) unsigned NOT NULL DEFAULT '0',
  `sender_ip` varchar(16) NOT NULL,
  `sender_email` varchar(75) NOT NULL,
  `sender_username` varchar(50) NOT NULL,
  `number_recipients` int(4) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`email_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_entry_ping_status
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_entry_ping_status`;

CREATE TABLE `exp_entry_ping_status` (
  `entry_id` int(10) unsigned NOT NULL,
  `ping_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`entry_id`,`ping_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_entry_versioning
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_entry_versioning`;

CREATE TABLE `exp_entry_versioning` (
  `version_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(10) unsigned NOT NULL,
  `channel_id` int(4) unsigned NOT NULL,
  `author_id` int(10) unsigned NOT NULL,
  `version_date` int(10) NOT NULL,
  `version_data` mediumtext NOT NULL,
  PRIMARY KEY (`version_id`),
  KEY `entry_id` (`entry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_extensions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_extensions`;

CREATE TABLE `exp_extensions` (
  `extension_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(50) NOT NULL DEFAULT '',
  `method` varchar(50) NOT NULL DEFAULT '',
  `hook` varchar(50) NOT NULL DEFAULT '',
  `settings` text NOT NULL,
  `priority` int(2) NOT NULL DEFAULT '10',
  `version` varchar(10) NOT NULL DEFAULT '',
  `enabled` char(1) NOT NULL DEFAULT 'y',
  PRIMARY KEY (`extension_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_extensions` WRITE;
/*!40000 ALTER TABLE `exp_extensions` DISABLE KEYS */;
INSERT INTO `exp_extensions` (`extension_id`,`class`,`method`,`hook`,`settings`,`priority`,`version`,`enabled`)
VALUES
	(1,'Matrix_ext','channel_entries_tagdata','channel_entries_tagdata','',10,'2.1.3','y'),
	(2,'Playa_ext','channel_entries_tagdata','channel_entries_tagdata','',9,'4.0.2.1','y');

/*!40000 ALTER TABLE `exp_extensions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_field_formatting
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_field_formatting`;

CREATE TABLE `exp_field_formatting` (
  `formatting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(10) unsigned NOT NULL,
  `field_fmt` varchar(40) NOT NULL,
  PRIMARY KEY (`formatting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_field_formatting` WRITE;
/*!40000 ALTER TABLE `exp_field_formatting` DISABLE KEYS */;
INSERT INTO `exp_field_formatting` (`formatting_id`,`field_id`,`field_fmt`)
VALUES
	(1,1,'none'),
	(2,1,'br'),
	(3,1,'xhtml'),
	(4,2,'none'),
	(5,2,'br'),
	(6,2,'xhtml'),
	(7,3,'none'),
	(8,3,'br'),
	(9,3,'xhtml'),
	(10,4,'none'),
	(11,4,'br'),
	(12,4,'xhtml'),
	(13,5,'none'),
	(14,5,'br'),
	(15,5,'xhtml'),
	(16,6,'none'),
	(17,6,'br'),
	(18,6,'xhtml'),
	(19,7,'none'),
	(20,7,'br'),
	(21,7,'xhtml'),
	(22,8,'none'),
	(23,8,'br'),
	(24,8,'xhtml'),
	(25,9,'none'),
	(26,9,'br'),
	(27,9,'xhtml'),
	(28,10,'none'),
	(29,10,'br'),
	(30,10,'xhtml'),
	(36,12,'xhtml'),
	(35,12,'br'),
	(34,12,'none'),
	(37,13,'none'),
	(38,13,'br'),
	(39,13,'xhtml'),
	(40,14,'none'),
	(41,14,'br'),
	(42,14,'xhtml'),
	(43,15,'none'),
	(44,15,'br'),
	(45,15,'xhtml'),
	(46,16,'none'),
	(47,16,'br'),
	(48,16,'xhtml'),
	(49,17,'none'),
	(50,17,'br'),
	(51,17,'xhtml'),
	(52,18,'none'),
	(53,18,'br'),
	(54,18,'xhtml');

/*!40000 ALTER TABLE `exp_field_formatting` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_field_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_field_groups`;

CREATE TABLE `exp_field_groups` (
  `group_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_name` varchar(50) NOT NULL,
  PRIMARY KEY (`group_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_field_groups` WRITE;
/*!40000 ALTER TABLE `exp_field_groups` DISABLE KEYS */;
INSERT INTO `exp_field_groups` (`group_id`,`site_id`,`group_name`)
VALUES
	(1,1,'Profile'),
	(3,1,'Meetups');

/*!40000 ALTER TABLE `exp_field_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_fieldtypes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_fieldtypes`;

CREATE TABLE `exp_fieldtypes` (
  `fieldtype_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `version` varchar(12) NOT NULL,
  `settings` text,
  `has_global_settings` char(1) DEFAULT 'n',
  PRIMARY KEY (`fieldtype_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_fieldtypes` WRITE;
/*!40000 ALTER TABLE `exp_fieldtypes` DISABLE KEYS */;
INSERT INTO `exp_fieldtypes` (`fieldtype_id`,`name`,`version`,`settings`,`has_global_settings`)
VALUES
	(1,'select','1.0','YTowOnt9','n'),
	(2,'text','1.0','YTowOnt9','n'),
	(3,'textarea','1.0','YTowOnt9','n'),
	(4,'date','1.0','YTowOnt9','n'),
	(5,'file','1.0','YTowOnt9','n'),
	(6,'multi_select','1.0','YTowOnt9','n'),
	(7,'checkboxes','1.0','YTowOnt9','n'),
	(8,'radio','1.0','YTowOnt9','n'),
	(9,'rel','1.0','YTowOnt9','n'),
	(10,'matrix','2.1.3','YTowOnt9','y'),
	(11,'nsm_morphine_theme','1.0.0','YTowOnt9','n'),
	(12,'playa','4.0.2.1','YTowOnt9','y'),
	(13,'pt_list','1.0.3','YTowOnt9','n'),
	(14,'pt_dropdown','1.0.3','Tjs=','n');

/*!40000 ALTER TABLE `exp_fieldtypes` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_global_variables
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_global_variables`;

CREATE TABLE `exp_global_variables` (
  `variable_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `variable_name` varchar(50) NOT NULL,
  `variable_data` text NOT NULL,
  PRIMARY KEY (`variable_id`),
  KEY `variable_name` (`variable_name`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_global_variables` WRITE;
/*!40000 ALTER TABLE `exp_global_variables` DISABLE KEYS */;
INSERT INTO `exp_global_variables` (`variable_id`,`site_id`,`variable_name`,`variable_data`)
VALUES
	(1,1,'sitename','ADI asdadasdasdads');

/*!40000 ALTER TABLE `exp_global_variables` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_html_buttons
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_html_buttons`;

CREATE TABLE `exp_html_buttons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `member_id` int(10) NOT NULL DEFAULT '0',
  `tag_name` varchar(32) NOT NULL,
  `tag_open` varchar(120) NOT NULL,
  `tag_close` varchar(120) NOT NULL,
  `accesskey` varchar(32) NOT NULL,
  `tag_order` int(3) unsigned NOT NULL,
  `tag_row` char(1) NOT NULL DEFAULT '1',
  `classname` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_html_buttons` WRITE;
/*!40000 ALTER TABLE `exp_html_buttons` DISABLE KEYS */;
INSERT INTO `exp_html_buttons` (`id`,`site_id`,`member_id`,`tag_name`,`tag_open`,`tag_close`,`accesskey`,`tag_order`,`tag_row`,`classname`)
VALUES
	(1,1,0,'b','<strong>','</strong>','b',1,'1','btn_b'),
	(2,1,0,'i','<em>','</em>','i',2,'1','btn_i'),
	(3,1,0,'blockquote','<blockquote>','</blockquote>','q',3,'1','btn_blockquote'),
	(4,1,0,'a','<a href=\"[![Link:!:http://]!]\"(!( title=\"[![Title]!]\")!)>','</a>','a',4,'1','btn_a'),
	(5,1,0,'img','<img src=\"[![Link:!:http://]!]\" alt=\"[![Alternative text]!]\" />','','',5,'1','btn_img');

/*!40000 ALTER TABLE `exp_html_buttons` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_layout_publish
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_layout_publish`;

CREATE TABLE `exp_layout_publish` (
  `layout_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `member_group` int(4) unsigned NOT NULL DEFAULT '0',
  `channel_id` int(4) unsigned NOT NULL DEFAULT '0',
  `field_layout` text,
  PRIMARY KEY (`layout_id`),
  KEY `site_id` (`site_id`),
  KEY `member_group` (`member_group`),
  KEY `channel_id` (`channel_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_layout_publish` WRITE;
/*!40000 ALTER TABLE `exp_layout_publish` DISABLE KEYS */;
INSERT INTO `exp_layout_publish` (`layout_id`,`site_id`,`member_group`,`channel_id`,`field_layout`)
VALUES
	(3,1,1,1,'a:5:{s:7:\"publish\";a:17:{s:10:\"_tab_label\";s:7:\"Publish\";s:5:\"title\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:9:\"url_title\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:1;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:2;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:3;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:4;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:5;a:4:{s:7:\"visible\";b:0;s:8:\"collapse\";b:1;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:6;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:7;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:8;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:9;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:10;a:4:{s:7:\"visible\";b:0;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:0;s:5:\"width\";s:4:\"100%\";}i:12;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:13;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:14;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:15;a:4:{s:7:\"visible\";s:4:\"true\";s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:0;s:5:\"width\";s:4:\"100%\";}}s:4:\"date\";a:4:{s:10:\"_tab_label\";s:4:\"Date\";s:10:\"entry_date\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:15:\"expiration_date\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:23:\"comment_expiration_date\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}}s:10:\"categories\";a:2:{s:10:\"_tab_label\";s:10:\"Categories\";s:8:\"category\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}}s:7:\"options\";a:6:{s:10:\"_tab_label\";s:7:\"Options\";s:11:\"new_channel\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:6:\"status\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:6:\"author\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:7:\"options\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:4:\"ping\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}}s:8:\"seo_lite\";a:4:{s:10:\"_tab_label\";s:8:\"seo_lite\";s:24:\"seo_lite__seo_lite_title\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:1;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:27:\"seo_lite__seo_lite_keywords\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:1;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:30:\"seo_lite__seo_lite_description\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:1;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}}}'),
	(4,1,1,2,'a:5:{s:7:\"publish\";a:17:{s:10:\"_tab_label\";s:7:\"Publish\";s:5:\"title\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:9:\"url_title\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:1;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:2;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:3;a:4:{s:7:\"visible\";b:0;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:4;a:4:{s:7:\"visible\";b:0;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:5;a:4:{s:7:\"visible\";b:0;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:6;a:4:{s:7:\"visible\";b:0;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:7;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:8;a:4:{s:7:\"visible\";b:0;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:9;a:4:{s:7:\"visible\";b:0;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:10;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:0;s:5:\"width\";s:4:\"100%\";}i:12;a:4:{s:7:\"visible\";b:0;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:13;a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:14;a:4:{s:7:\"visible\";b:0;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}i:15;a:4:{s:7:\"visible\";s:4:\"true\";s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:0;s:5:\"width\";s:4:\"100%\";}}s:4:\"date\";a:4:{s:10:\"_tab_label\";s:4:\"Date\";s:10:\"entry_date\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:15:\"expiration_date\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:23:\"comment_expiration_date\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}}s:10:\"categories\";a:2:{s:10:\"_tab_label\";s:10:\"Categories\";s:8:\"category\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}}s:7:\"options\";a:6:{s:10:\"_tab_label\";s:7:\"Options\";s:11:\"new_channel\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:6:\"status\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:6:\"author\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:7:\"options\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:4:\"ping\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:0;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}}s:8:\"seo_lite\";a:4:{s:10:\"_tab_label\";s:8:\"seo_lite\";s:24:\"seo_lite__seo_lite_title\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:1;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:27:\"seo_lite__seo_lite_keywords\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:1;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}s:30:\"seo_lite__seo_lite_description\";a:4:{s:7:\"visible\";b:1;s:8:\"collapse\";b:1;s:11:\"htmlbuttons\";b:1;s:5:\"width\";s:4:\"100%\";}}}');

/*!40000 ALTER TABLE `exp_layout_publish` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_matrix_cols
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_matrix_cols`;

CREATE TABLE `exp_matrix_cols` (
  `col_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned DEFAULT '1',
  `field_id` int(6) unsigned DEFAULT NULL,
  `col_name` varchar(32) DEFAULT NULL,
  `col_label` varchar(50) DEFAULT NULL,
  `col_instructions` text,
  `col_type` varchar(50) DEFAULT 'text',
  `col_required` char(1) DEFAULT 'n',
  `col_search` char(1) DEFAULT 'n',
  `col_order` int(3) unsigned DEFAULT NULL,
  `col_width` varchar(4) DEFAULT NULL,
  `col_settings` text,
  PRIMARY KEY (`col_id`),
  KEY `site_id` (`site_id`),
  KEY `field_id` (`field_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_matrix_cols` WRITE;
/*!40000 ALTER TABLE `exp_matrix_cols` DISABLE KEYS */;
INSERT INTO `exp_matrix_cols` (`col_id`,`site_id`,`field_id`,`col_name`,`col_label`,`col_instructions`,`col_type`,`col_required`,`col_search`,`col_order`,`col_width`,`col_settings`)
VALUES
	(1,1,NULL,'project-name','Project Name','','text','n','n',0,'','YTozOntzOjQ6Im1heGwiO3M6MDoiIjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(2,1,NULL,'project-url','Project URL','','text','n','n',1,'','YTozOntzOjQ6Im1heGwiO3M6MDoiIjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(3,1,NULL,'client-name','Client Name','','text','n','n',2,'','YTozOntzOjQ6Im1heGwiO3M6MDoiIjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(4,1,NULL,'client-url','Client URL','','text','n','n',3,'','YTozOntzOjQ6Im1heGwiO3M6MDoiIjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(5,1,NULL,'url','Upload Image','','file','n','n',0,'33%','YToxOntzOjEyOiJjb250ZW50X3R5cGUiO3M6MzoiYW55Ijt9'),
	(6,1,NULL,'desc','Description','','text','n','n',1,'','YTo0OntzOjQ6Im1heGwiO3M6MzoiMTQwIjtzOjk6Im11bHRpbGluZSI7czoxOiJ5IjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(7,1,NULL,'name','Name','','text','n','n',0,'33%','YTozOntzOjQ6Im1heGwiO3M6MDoiIjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(8,1,NULL,'url','URL','','text','n','n',1,'','YTo0OntzOjQ6Im1heGwiO3M6MzoiMTQwIjtzOjk6Im11bHRpbGluZSI7czoxOiJ5IjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(9,1,NULL,'add','Address','','text','n','n',0,'33%','YTo0OntzOjQ6Im1heGwiO3M6MDoiIjtzOjk6Im11bHRpbGluZSI7czoxOiJ5IjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(10,1,NULL,'tel','Tel','','text','n','n',1,'','YTozOntzOjQ6Im1heGwiO3M6MDoiIjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(11,1,NULL,'fax','Fax','','text','n','n',2,'','YTozOntzOjQ6Im1heGwiO3M6MDoiIjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(12,1,NULL,'name','Name','','text','n','n',0,'','YTozOntzOjQ6Im1heGwiO3M6MDoiIjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(13,1,NULL,'url','URL','','text','n','n',1,'','YTo0OntzOjQ6Im1heGwiO3M6MzoiMTQwIjtzOjk6Im11bHRpbGluZSI7czoxOiJ5IjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(14,1,NULL,'role','Project Role','','text','n','n',2,'','YTozOntzOjQ6Im1heGwiO3M6MDoiIjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(15,1,NULL,'method','Method','','pt_dropdown','n','n',0,'','YToxOntzOjc6Im9wdGlvbnMiO2E6NTp7czo1OiJFbWFpbCI7czo1OiJFbWFpbCI7czozOiJBSU0iO3M6MzoiQUlNIjtzOjM6IllJTSI7czozOiJZSU0iO3M6MzoiTVNOIjtzOjM6Ik1TTiI7czo2OiJKYWJiZXIiO3M6NjoiSmFiYmVyIjt9fQ=='),
	(16,1,NULL,'detail','Details','','text','n','n',1,'','YTo0OntzOjQ6Im1heGwiO3M6MzoiMTQwIjtzOjk6Im11bHRpbGluZSI7czoxOiJ5IjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(17,1,NULL,'client-name','Client Name','','text','n','y',0,'','YTozOntzOjQ6Im1heGwiO3M6MDoiIjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(18,1,NULL,'client-url','Client url','','text','n','y',1,'','YTozOntzOjQ6Im1heGwiO3M6MzoiMTQwIjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9'),
	(19,1,NULL,'profile','Profile','','pt_dropdown','n','n',3,'','YToxOntzOjc6Im9wdGlvbnMiO2E6MTp7czowOiIiO3M6MDoiIjt9fQ=='),
	(20,1,NULL,'date','Date','','date','n','n',0,'33%','YTowOnt9'),
	(21,1,NULL,'location','Location','','text','n','n',1,'','YTozOntzOjQ6Im1heGwiO3M6MzoiMTQwIjtzOjM6ImZtdCI7czo0OiJub25lIjtzOjc6ImNvbnRlbnQiO3M6MzoiYW55Ijt9');

/*!40000 ALTER TABLE `exp_matrix_cols` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_matrix_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_matrix_data`;

CREATE TABLE `exp_matrix_data` (
  `row_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned DEFAULT '1',
  `entry_id` int(10) unsigned DEFAULT NULL,
  `field_id` int(6) unsigned DEFAULT NULL,
  `row_order` int(4) unsigned DEFAULT NULL,
  `col_id_1` text,
  `col_id_2` text,
  `col_id_3` text,
  `col_id_4` text,
  `col_id_5` text,
  `col_id_6` text,
  `col_id_7` text,
  `col_id_8` text,
  `col_id_9` text,
  `col_id_10` text,
  `col_id_11` text,
  `col_id_12` text,
  `col_id_13` text,
  `col_id_14` text,
  `col_id_15` text,
  `col_id_16` text,
  `col_id_17` text,
  `col_id_18` text,
  `col_id_19` text,
  `col_id_20` int(10) unsigned DEFAULT '0',
  `col_id_21` text,
  PRIMARY KEY (`row_id`),
  KEY `site_id` (`site_id`),
  KEY `entry_id` (`entry_id`),
  KEY `field_id` (`field_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_matrix_data` WRITE;
/*!40000 ALTER TABLE `exp_matrix_data` DISABLE KEYS */;
INSERT INTO `exp_matrix_data` (`row_id`,`site_id`,`entry_id`,`field_id`,`row_order`,`col_id_1`,`col_id_2`,`col_id_3`,`col_id_4`,`col_id_5`,`col_id_6`,`col_id_7`,`col_id_8`,`col_id_9`,`col_id_10`,`col_id_11`,`col_id_12`,`col_id_13`,`col_id_14`,`col_id_15`,`col_id_16`,`col_id_17`,`col_id_18`,`col_id_19`,`col_id_20`,`col_id_21`)
VALUES
	(1,1,1,5,0,'ICS','https://www.icscards.nl/','Edenspiekermann','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),
	(2,1,1,5,1,'PNH','http://www.noord-holland.nl/','Edenspiekermann','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),
	(3,1,1,5,2,'ABNAMRO','','Edenspiekermann','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),
	(4,1,1,5,3,'Contact SG','http://www.contactsingapore.sg/','Convertium','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),
	(5,1,1,5,4,'Vodafone India Mobile','','Qais Consulting','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),
	(6,1,1,5,5,'CAAS','','Qais Consulting','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),
	(7,1,1,9,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'205 River Valley Road, Singapore , 238274 Singapore ','+65 8113 4709','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),
	(8,1,1,12,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Email','nils@nilshendriks.com',NULL,NULL,NULL,0,NULL),
	(9,1,1,12,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'AIM','aimsomename',NULL,NULL,NULL,0,NULL),
	(10,1,1,12,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'MSN','ymsgr:sendIM?yim',NULL,NULL,NULL,0,NULL),
	(11,1,1,12,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jabber','Jabber',NULL,NULL,NULL,0,NULL),
	(12,1,2,5,0,'Southeast Asia','http://southeastasia.org','Qais Consulting','http://qaisconsulting.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),
	(13,1,2,5,1,'Southpointe Academy','http://www.southpointeacademy.ca/','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),
	(14,1,2,5,2,'Scud Industries','http://scud.com.sg/','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),
	(15,1,2,5,3,'Climate Justice Fast','http://www.climatejusticefast.com/','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),
	(16,1,2,9,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'121 Lorong K Telok Kurau, #02-06 Silahis Apartments','+65 90291420','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),
	(17,1,2,12,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Email','hi@artminister.com',NULL,NULL,NULL,0,NULL),
	(18,1,2,12,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'MSN','vinay@live.com',NULL,NULL,NULL,0,NULL),
	(19,1,1,7,0,NULL,NULL,NULL,NULL,'{filedir_2}nilshendriks.jpg','Photograph by Nils Hendriks',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),
	(20,1,3,7,0,NULL,NULL,NULL,NULL,'{filedir_1}contactsg.png','Contac',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL),
	(21,1,3,12,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Email','',NULL,NULL,NULL,0,NULL),
	(22,1,3,13,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Singapore Economic Development Board','http://www.sedb.com/',NULL,0,NULL),
	(23,1,3,13,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ministry of Manpower','http://www.mom.gov.sg/',NULL,0,NULL),
	(24,1,3,13,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Singapore Economic Development Board','http://www.sedb.com/',NULL,0,NULL),
	(25,1,3,13,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ministry of Manpower','http://www.mom.gov.sg/',NULL,0,NULL),
	(26,1,4,18,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1306522801,'TBA');

/*!40000 ALTER TABLE `exp_matrix_data` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_member_bulletin_board
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_member_bulletin_board`;

CREATE TABLE `exp_member_bulletin_board` (
  `bulletin_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int(10) unsigned NOT NULL,
  `bulletin_group` int(8) unsigned NOT NULL,
  `bulletin_date` int(10) unsigned NOT NULL,
  `hash` varchar(10) NOT NULL DEFAULT '',
  `bulletin_expires` int(10) unsigned NOT NULL DEFAULT '0',
  `bulletin_message` text NOT NULL,
  PRIMARY KEY (`bulletin_id`),
  KEY `sender_id` (`sender_id`),
  KEY `hash` (`hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_member_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_member_data`;

CREATE TABLE `exp_member_data` (
  `member_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `exp_member_data` WRITE;
/*!40000 ALTER TABLE `exp_member_data` DISABLE KEYS */;
INSERT INTO `exp_member_data` (`member_id`)
VALUES
	(1),
	(2);

/*!40000 ALTER TABLE `exp_member_data` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_member_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_member_fields`;

CREATE TABLE `exp_member_fields` (
  `m_field_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `m_field_name` varchar(32) NOT NULL,
  `m_field_label` varchar(50) NOT NULL,
  `m_field_description` text NOT NULL,
  `m_field_type` varchar(12) NOT NULL DEFAULT 'text',
  `m_field_list_items` text NOT NULL,
  `m_field_ta_rows` tinyint(2) DEFAULT '8',
  `m_field_maxl` smallint(3) NOT NULL,
  `m_field_width` varchar(6) NOT NULL,
  `m_field_search` char(1) NOT NULL DEFAULT 'y',
  `m_field_required` char(1) NOT NULL DEFAULT 'n',
  `m_field_public` char(1) NOT NULL DEFAULT 'y',
  `m_field_reg` char(1) NOT NULL DEFAULT 'n',
  `m_field_cp_reg` char(1) NOT NULL DEFAULT 'n',
  `m_field_fmt` char(5) NOT NULL DEFAULT 'none',
  `m_field_order` int(3) unsigned NOT NULL,
  PRIMARY KEY (`m_field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_member_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_member_groups`;

CREATE TABLE `exp_member_groups` (
  `group_id` smallint(4) unsigned NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_title` varchar(100) NOT NULL,
  `group_description` text NOT NULL,
  `is_locked` char(1) NOT NULL DEFAULT 'y',
  `can_view_offline_system` char(1) NOT NULL DEFAULT 'n',
  `can_view_online_system` char(1) NOT NULL DEFAULT 'y',
  `can_access_cp` char(1) NOT NULL DEFAULT 'y',
  `can_access_content` char(1) NOT NULL DEFAULT 'n',
  `can_access_publish` char(1) NOT NULL DEFAULT 'n',
  `can_access_edit` char(1) NOT NULL DEFAULT 'n',
  `can_access_files` char(1) NOT NULL DEFAULT 'n',
  `can_access_design` char(1) NOT NULL DEFAULT 'n',
  `can_access_addons` char(1) NOT NULL DEFAULT 'n',
  `can_access_modules` char(1) NOT NULL DEFAULT 'n',
  `can_access_extensions` char(1) NOT NULL DEFAULT 'n',
  `can_access_accessories` char(1) NOT NULL DEFAULT 'n',
  `can_access_plugins` char(1) NOT NULL DEFAULT 'n',
  `can_access_members` char(1) NOT NULL DEFAULT 'n',
  `can_access_admin` char(1) NOT NULL DEFAULT 'n',
  `can_access_sys_prefs` char(1) NOT NULL DEFAULT 'n',
  `can_access_content_prefs` char(1) NOT NULL DEFAULT 'n',
  `can_access_tools` char(1) NOT NULL DEFAULT 'n',
  `can_access_comm` char(1) NOT NULL DEFAULT 'n',
  `can_access_utilities` char(1) NOT NULL DEFAULT 'n',
  `can_access_data` char(1) NOT NULL DEFAULT 'n',
  `can_access_logs` char(1) NOT NULL DEFAULT 'n',
  `can_admin_channels` char(1) NOT NULL DEFAULT 'n',
  `can_admin_design` char(1) NOT NULL DEFAULT 'n',
  `can_admin_members` char(1) NOT NULL DEFAULT 'n',
  `can_delete_members` char(1) NOT NULL DEFAULT 'n',
  `can_admin_mbr_groups` char(1) NOT NULL DEFAULT 'n',
  `can_admin_mbr_templates` char(1) NOT NULL DEFAULT 'n',
  `can_ban_users` char(1) NOT NULL DEFAULT 'n',
  `can_admin_modules` char(1) NOT NULL DEFAULT 'n',
  `can_admin_templates` char(1) NOT NULL DEFAULT 'n',
  `can_admin_accessories` char(1) NOT NULL DEFAULT 'n',
  `can_edit_categories` char(1) NOT NULL DEFAULT 'n',
  `can_delete_categories` char(1) NOT NULL DEFAULT 'n',
  `can_view_other_entries` char(1) NOT NULL DEFAULT 'n',
  `can_edit_other_entries` char(1) NOT NULL DEFAULT 'n',
  `can_assign_post_authors` char(1) NOT NULL DEFAULT 'n',
  `can_delete_self_entries` char(1) NOT NULL DEFAULT 'n',
  `can_delete_all_entries` char(1) NOT NULL DEFAULT 'n',
  `can_view_other_comments` char(1) NOT NULL DEFAULT 'n',
  `can_edit_own_comments` char(1) NOT NULL DEFAULT 'n',
  `can_delete_own_comments` char(1) NOT NULL DEFAULT 'n',
  `can_edit_all_comments` char(1) NOT NULL DEFAULT 'n',
  `can_delete_all_comments` char(1) NOT NULL DEFAULT 'n',
  `can_moderate_comments` char(1) NOT NULL DEFAULT 'n',
  `can_send_email` char(1) NOT NULL DEFAULT 'n',
  `can_send_cached_email` char(1) NOT NULL DEFAULT 'n',
  `can_email_member_groups` char(1) NOT NULL DEFAULT 'n',
  `can_email_mailinglist` char(1) NOT NULL DEFAULT 'n',
  `can_email_from_profile` char(1) NOT NULL DEFAULT 'n',
  `can_view_profiles` char(1) NOT NULL DEFAULT 'n',
  `can_edit_html_buttons` char(1) NOT NULL DEFAULT 'n',
  `can_delete_self` char(1) NOT NULL DEFAULT 'n',
  `mbr_delete_notify_emails` varchar(255) DEFAULT NULL,
  `can_post_comments` char(1) NOT NULL DEFAULT 'n',
  `exclude_from_moderation` char(1) NOT NULL DEFAULT 'n',
  `can_search` char(1) NOT NULL DEFAULT 'n',
  `search_flood_control` mediumint(5) unsigned NOT NULL,
  `can_send_private_messages` char(1) NOT NULL DEFAULT 'n',
  `prv_msg_send_limit` smallint(5) unsigned NOT NULL DEFAULT '20',
  `prv_msg_storage_limit` smallint(5) unsigned NOT NULL DEFAULT '60',
  `can_attach_in_private_messages` char(1) NOT NULL DEFAULT 'n',
  `can_send_bulletins` char(1) NOT NULL DEFAULT 'n',
  `include_in_authorlist` char(1) NOT NULL DEFAULT 'n',
  `include_in_memberlist` char(1) NOT NULL DEFAULT 'y',
  `include_in_mailinglists` char(1) NOT NULL DEFAULT 'y',
  PRIMARY KEY (`group_id`,`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `exp_member_groups` WRITE;
/*!40000 ALTER TABLE `exp_member_groups` DISABLE KEYS */;
INSERT INTO `exp_member_groups` (`group_id`,`site_id`,`group_title`,`group_description`,`is_locked`,`can_view_offline_system`,`can_view_online_system`,`can_access_cp`,`can_access_content`,`can_access_publish`,`can_access_edit`,`can_access_files`,`can_access_design`,`can_access_addons`,`can_access_modules`,`can_access_extensions`,`can_access_accessories`,`can_access_plugins`,`can_access_members`,`can_access_admin`,`can_access_sys_prefs`,`can_access_content_prefs`,`can_access_tools`,`can_access_comm`,`can_access_utilities`,`can_access_data`,`can_access_logs`,`can_admin_channels`,`can_admin_design`,`can_admin_members`,`can_delete_members`,`can_admin_mbr_groups`,`can_admin_mbr_templates`,`can_ban_users`,`can_admin_modules`,`can_admin_templates`,`can_admin_accessories`,`can_edit_categories`,`can_delete_categories`,`can_view_other_entries`,`can_edit_other_entries`,`can_assign_post_authors`,`can_delete_self_entries`,`can_delete_all_entries`,`can_view_other_comments`,`can_edit_own_comments`,`can_delete_own_comments`,`can_edit_all_comments`,`can_delete_all_comments`,`can_moderate_comments`,`can_send_email`,`can_send_cached_email`,`can_email_member_groups`,`can_email_mailinglist`,`can_email_from_profile`,`can_view_profiles`,`can_edit_html_buttons`,`can_delete_self`,`mbr_delete_notify_emails`,`can_post_comments`,`exclude_from_moderation`,`can_search`,`search_flood_control`,`can_send_private_messages`,`prv_msg_send_limit`,`prv_msg_storage_limit`,`can_attach_in_private_messages`,`can_send_bulletins`,`include_in_authorlist`,`include_in_memberlist`,`include_in_mailinglists`)
VALUES
	(1,1,'Super Admins','','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','y','','y','y','y',0,'y',20,60,'y','y','y','y','y'),
	(2,1,'Banned','','y','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','','n','n','n',60,'n',20,60,'n','n','n','n','n'),
	(3,1,'Guests','','y','n','y','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','y','n','n','','y','n','y',15,'n',20,60,'n','n','n','n','n'),
	(4,1,'Pending','','y','n','y','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','y','n','n','','y','n','y',15,'n',20,60,'n','n','n','n','n'),
	(5,1,'Members','','y','n','y','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','n','y','y','y','n','','y','n','y',10,'y',20,60,'y','n','n','y','y');

/*!40000 ALTER TABLE `exp_member_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_member_homepage
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_member_homepage`;

CREATE TABLE `exp_member_homepage` (
  `member_id` int(10) unsigned NOT NULL,
  `recent_entries` char(1) NOT NULL DEFAULT 'l',
  `recent_entries_order` int(3) unsigned NOT NULL DEFAULT '0',
  `recent_comments` char(1) NOT NULL DEFAULT 'l',
  `recent_comments_order` int(3) unsigned NOT NULL DEFAULT '0',
  `recent_members` char(1) NOT NULL DEFAULT 'n',
  `recent_members_order` int(3) unsigned NOT NULL DEFAULT '0',
  `site_statistics` char(1) NOT NULL DEFAULT 'r',
  `site_statistics_order` int(3) unsigned NOT NULL DEFAULT '0',
  `member_search_form` char(1) NOT NULL DEFAULT 'n',
  `member_search_form_order` int(3) unsigned NOT NULL DEFAULT '0',
  `notepad` char(1) NOT NULL DEFAULT 'r',
  `notepad_order` int(3) unsigned NOT NULL DEFAULT '0',
  `bulletin_board` char(1) NOT NULL DEFAULT 'r',
  `bulletin_board_order` int(3) unsigned NOT NULL DEFAULT '0',
  `pmachine_news_feed` char(1) NOT NULL DEFAULT 'n',
  `pmachine_news_feed_order` int(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `exp_member_homepage` WRITE;
/*!40000 ALTER TABLE `exp_member_homepage` DISABLE KEYS */;
INSERT INTO `exp_member_homepage` (`member_id`,`recent_entries`,`recent_entries_order`,`recent_comments`,`recent_comments_order`,`recent_members`,`recent_members_order`,`site_statistics`,`site_statistics_order`,`member_search_form`,`member_search_form_order`,`notepad`,`notepad_order`,`bulletin_board`,`bulletin_board_order`,`pmachine_news_feed`,`pmachine_news_feed_order`)
VALUES
	(1,'l',1,'l',2,'n',0,'r',1,'n',0,'r',2,'r',0,'l',0),
	(2,'l',0,'l',0,'n',0,'r',0,'n',0,'r',0,'r',0,'n',0);

/*!40000 ALTER TABLE `exp_member_homepage` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_member_search
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_member_search`;

CREATE TABLE `exp_member_search` (
  `search_id` varchar(32) NOT NULL,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `search_date` int(10) unsigned NOT NULL,
  `keywords` varchar(200) NOT NULL,
  `fields` varchar(200) NOT NULL,
  `member_id` int(10) unsigned NOT NULL,
  `ip_address` varchar(16) NOT NULL,
  `total_results` int(8) unsigned NOT NULL,
  `query` text NOT NULL,
  PRIMARY KEY (`search_id`),
  KEY `member_id` (`member_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_members
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_members`;

CREATE TABLE `exp_members` (
  `member_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` smallint(4) NOT NULL DEFAULT '0',
  `username` varchar(50) NOT NULL,
  `screen_name` varchar(50) NOT NULL,
  `password` varchar(40) NOT NULL,
  `unique_id` varchar(40) NOT NULL,
  `crypt_key` varchar(40) DEFAULT NULL,
  `authcode` varchar(10) DEFAULT NULL,
  `email` varchar(72) NOT NULL,
  `url` varchar(150) DEFAULT NULL,
  `location` varchar(50) DEFAULT NULL,
  `occupation` varchar(80) DEFAULT NULL,
  `interests` varchar(120) DEFAULT NULL,
  `bday_d` int(2) DEFAULT NULL,
  `bday_m` int(2) DEFAULT NULL,
  `bday_y` int(4) DEFAULT NULL,
  `aol_im` varchar(50) DEFAULT NULL,
  `yahoo_im` varchar(50) DEFAULT NULL,
  `msn_im` varchar(50) DEFAULT NULL,
  `icq` varchar(50) DEFAULT NULL,
  `bio` text,
  `signature` text,
  `avatar_filename` varchar(120) DEFAULT NULL,
  `avatar_width` int(4) unsigned DEFAULT NULL,
  `avatar_height` int(4) unsigned DEFAULT NULL,
  `photo_filename` varchar(120) DEFAULT NULL,
  `photo_width` int(4) unsigned DEFAULT NULL,
  `photo_height` int(4) unsigned DEFAULT NULL,
  `sig_img_filename` varchar(120) DEFAULT NULL,
  `sig_img_width` int(4) unsigned DEFAULT NULL,
  `sig_img_height` int(4) unsigned DEFAULT NULL,
  `ignore_list` text,
  `private_messages` int(4) unsigned NOT NULL DEFAULT '0',
  `accept_messages` char(1) NOT NULL DEFAULT 'y',
  `last_view_bulletins` int(10) NOT NULL DEFAULT '0',
  `last_bulletin_date` int(10) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `join_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_visit` int(10) unsigned NOT NULL DEFAULT '0',
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `total_entries` smallint(5) unsigned NOT NULL DEFAULT '0',
  `total_comments` smallint(5) unsigned NOT NULL DEFAULT '0',
  `total_forum_topics` mediumint(8) NOT NULL DEFAULT '0',
  `total_forum_posts` mediumint(8) NOT NULL DEFAULT '0',
  `last_entry_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_comment_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_forum_post_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_email_date` int(10) unsigned NOT NULL DEFAULT '0',
  `in_authorlist` char(1) NOT NULL DEFAULT 'n',
  `accept_admin_email` char(1) NOT NULL DEFAULT 'y',
  `accept_user_email` char(1) NOT NULL DEFAULT 'y',
  `notify_by_default` char(1) NOT NULL DEFAULT 'y',
  `notify_of_pm` char(1) NOT NULL DEFAULT 'y',
  `display_avatars` char(1) NOT NULL DEFAULT 'y',
  `display_signatures` char(1) NOT NULL DEFAULT 'y',
  `parse_smileys` char(1) NOT NULL DEFAULT 'y',
  `smart_notifications` char(1) NOT NULL DEFAULT 'y',
  `language` varchar(50) NOT NULL,
  `timezone` varchar(8) NOT NULL,
  `daylight_savings` char(1) NOT NULL DEFAULT 'n',
  `localization_is_site_default` char(1) NOT NULL DEFAULT 'n',
  `time_format` char(2) NOT NULL DEFAULT 'us',
  `cp_theme` varchar(32) DEFAULT NULL,
  `profile_theme` varchar(32) DEFAULT NULL,
  `forum_theme` varchar(32) DEFAULT NULL,
  `tracker` text,
  `template_size` varchar(2) NOT NULL DEFAULT '20',
  `notepad` text,
  `notepad_size` varchar(2) NOT NULL DEFAULT '18',
  `quick_links` text,
  `quick_tabs` text,
  `show_sidebar` char(1) NOT NULL DEFAULT 'y',
  `pmember_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`member_id`),
  KEY `group_id` (`group_id`),
  KEY `unique_id` (`unique_id`),
  KEY `password` (`password`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_members` WRITE;
/*!40000 ALTER TABLE `exp_members` DISABLE KEYS */;
INSERT INTO `exp_members` (`member_id`,`group_id`,`username`,`screen_name`,`password`,`unique_id`,`crypt_key`,`authcode`,`email`,`url`,`location`,`occupation`,`interests`,`bday_d`,`bday_m`,`bday_y`,`aol_im`,`yahoo_im`,`msn_im`,`icq`,`bio`,`signature`,`avatar_filename`,`avatar_width`,`avatar_height`,`photo_filename`,`photo_width`,`photo_height`,`sig_img_filename`,`sig_img_width`,`sig_img_height`,`ignore_list`,`private_messages`,`accept_messages`,`last_view_bulletins`,`last_bulletin_date`,`ip_address`,`join_date`,`last_visit`,`last_activity`,`total_entries`,`total_comments`,`total_forum_topics`,`total_forum_posts`,`last_entry_date`,`last_comment_date`,`last_forum_post_date`,`last_email_date`,`in_authorlist`,`accept_admin_email`,`accept_user_email`,`notify_by_default`,`notify_of_pm`,`display_avatars`,`display_signatures`,`parse_smileys`,`smart_notifications`,`language`,`timezone`,`daylight_savings`,`localization_is_site_default`,`time_format`,`cp_theme`,`profile_theme`,`forum_theme`,`tracker`,`template_size`,`notepad`,`notepad_size`,`quick_links`,`quick_tabs`,`show_sidebar`,`pmember_id`)
VALUES
	(1,1,'rmdort','Vinay','6d4213ff2b440560e12d7a2b442948c7cd1c8764','d800f59f389402367d3bba8937678c02bdf4b61b','ca26292f7f0c65f9edc9e99e2138e2445533a4b3',NULL,'rmdort@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'y',0,0,'127.0.0.1',1301989655,1304420470,1304502424,5,0,0,0,1304420333,0,0,0,'n','y','y','y','y','y','y','y','y','english','UTC','n','n','us',NULL,NULL,NULL,NULL,'20',NULL,'18','','Custom Channel Fields|index.php?S=84e519fd302bfa1d277b32a247ccd678eca1f4b6&amp;D=cp&amp;C=admin_content&M=field_group_management|1','y',0),
	(2,1,'Nils','Nils','ebfc7910077770c8340f63cd2dca2ac1f120444f','ec4d03a59474cdda9130f6d9f87121187249aff3',NULL,NULL,'nils@nilshendriks.com','','','','',0,0,0,'','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'y',0,0,'127.0.0.1',1303922409,0,0,0,0,0,0,0,0,0,0,'n','y','y','y','y','y','y','y','y','english','UTC','n','n','us',NULL,NULL,NULL,NULL,'20',NULL,'18',NULL,NULL,'y',0);

/*!40000 ALTER TABLE `exp_members` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_message_attachments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_message_attachments`;

CREATE TABLE `exp_message_attachments` (
  `attachment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int(10) unsigned NOT NULL DEFAULT '0',
  `message_id` int(10) unsigned NOT NULL DEFAULT '0',
  `attachment_name` varchar(50) NOT NULL DEFAULT '',
  `attachment_hash` varchar(40) NOT NULL DEFAULT '',
  `attachment_extension` varchar(20) NOT NULL DEFAULT '',
  `attachment_location` varchar(150) NOT NULL DEFAULT '',
  `attachment_date` int(10) unsigned NOT NULL DEFAULT '0',
  `attachment_size` int(10) unsigned NOT NULL DEFAULT '0',
  `is_temp` char(1) NOT NULL DEFAULT 'y',
  PRIMARY KEY (`attachment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_message_copies
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_message_copies`;

CREATE TABLE `exp_message_copies` (
  `copy_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `message_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sender_id` int(10) unsigned NOT NULL DEFAULT '0',
  `recipient_id` int(10) unsigned NOT NULL DEFAULT '0',
  `message_received` char(1) NOT NULL DEFAULT 'n',
  `message_read` char(1) NOT NULL DEFAULT 'n',
  `message_time_read` int(10) unsigned NOT NULL DEFAULT '0',
  `attachment_downloaded` char(1) NOT NULL DEFAULT 'n',
  `message_folder` int(10) unsigned NOT NULL DEFAULT '1',
  `message_authcode` varchar(10) NOT NULL DEFAULT '',
  `message_deleted` char(1) NOT NULL DEFAULT 'n',
  `message_status` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`copy_id`),
  KEY `message_id` (`message_id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `sender_id` (`sender_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_message_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_message_data`;

CREATE TABLE `exp_message_data` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int(10) unsigned NOT NULL DEFAULT '0',
  `message_date` int(10) unsigned NOT NULL DEFAULT '0',
  `message_subject` varchar(255) NOT NULL DEFAULT '',
  `message_body` text NOT NULL,
  `message_tracking` char(1) NOT NULL DEFAULT 'y',
  `message_attachments` char(1) NOT NULL DEFAULT 'n',
  `message_recipients` varchar(200) NOT NULL DEFAULT '',
  `message_cc` varchar(200) NOT NULL DEFAULT '',
  `message_hide_cc` char(1) NOT NULL DEFAULT 'n',
  `message_sent_copy` char(1) NOT NULL DEFAULT 'n',
  `total_recipients` int(5) unsigned NOT NULL DEFAULT '0',
  `message_status` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_message_folders
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_message_folders`;

CREATE TABLE `exp_message_folders` (
  `member_id` int(10) unsigned NOT NULL DEFAULT '0',
  `folder1_name` varchar(50) NOT NULL DEFAULT 'InBox',
  `folder2_name` varchar(50) NOT NULL DEFAULT 'Sent',
  `folder3_name` varchar(50) NOT NULL DEFAULT '',
  `folder4_name` varchar(50) NOT NULL DEFAULT '',
  `folder5_name` varchar(50) NOT NULL DEFAULT '',
  `folder6_name` varchar(50) NOT NULL DEFAULT '',
  `folder7_name` varchar(50) NOT NULL DEFAULT '',
  `folder8_name` varchar(50) NOT NULL DEFAULT '',
  `folder9_name` varchar(50) NOT NULL DEFAULT '',
  `folder10_name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `exp_message_folders` WRITE;
/*!40000 ALTER TABLE `exp_message_folders` DISABLE KEYS */;
INSERT INTO `exp_message_folders` (`member_id`,`folder1_name`,`folder2_name`,`folder3_name`,`folder4_name`,`folder5_name`,`folder6_name`,`folder7_name`,`folder8_name`,`folder9_name`,`folder10_name`)
VALUES
	(1,'InBox','Sent','','','','','','','','');

/*!40000 ALTER TABLE `exp_message_folders` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_message_listed
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_message_listed`;

CREATE TABLE `exp_message_listed` (
  `listed_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL DEFAULT '0',
  `listed_member` int(10) unsigned NOT NULL DEFAULT '0',
  `listed_description` varchar(100) NOT NULL DEFAULT '',
  `listed_type` varchar(10) NOT NULL DEFAULT 'blocked',
  PRIMARY KEY (`listed_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_module_member_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_module_member_groups`;

CREATE TABLE `exp_module_member_groups` (
  `group_id` smallint(4) unsigned NOT NULL,
  `module_id` mediumint(5) unsigned NOT NULL,
  PRIMARY KEY (`group_id`,`module_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_modules
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_modules`;

CREATE TABLE `exp_modules` (
  `module_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(50) NOT NULL,
  `module_version` varchar(12) NOT NULL,
  `has_cp_backend` char(1) NOT NULL DEFAULT 'n',
  `has_publish_fields` char(1) NOT NULL DEFAULT 'n',
  `settings` text,
  PRIMARY KEY (`module_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_modules` WRITE;
/*!40000 ALTER TABLE `exp_modules` DISABLE KEYS */;
INSERT INTO `exp_modules` (`module_id`,`module_name`,`module_version`,`has_cp_backend`,`has_publish_fields`,`settings`)
VALUES
	(1,'Comment','2.2','y','n',NULL),
	(2,'Email','2.0','n','n',NULL),
	(3,'Emoticon','2.0','n','n',NULL),
	(4,'Jquery','1.0','n','n',NULL),
	(5,'Rss','2.0','n','n',NULL),
	(6,'Search','2.1','n','n',NULL),
	(7,'Channel','2.0.1','n','n',NULL),
	(8,'Member','2.0','n','n',NULL),
	(9,'Stats','2.0','n','n',NULL),
	(10,'Nsm_morphine_theme','1.0.0','n','n',NULL),
	(11,'Cp_menu_master','1.0.1','y','n','YToxOntzOjEyOiJlZGl0X3N1Ym1lbnUiO3M6MToieSI7fQ=='),
	(12,'Playa','4.0.2.1','n','n',NULL),
	(13,'Seo_lite','1.2.2','y','y',NULL),
	(15,'Devkit','1.0','y','n',NULL),
	(16,'Categories','1.0','n','n',NULL),
	(17,'Query','2.0','n','n',NULL);

/*!40000 ALTER TABLE `exp_modules` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_online_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_online_users`;

CREATE TABLE `exp_online_users` (
  `online_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `member_id` int(10) NOT NULL DEFAULT '0',
  `in_forum` char(1) NOT NULL DEFAULT 'n',
  `name` varchar(50) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  `anon` char(1) NOT NULL,
  PRIMARY KEY (`online_id`),
  KEY `date` (`date`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_online_users` WRITE;
/*!40000 ALTER TABLE `exp_online_users` DISABLE KEYS */;
INSERT INTO `exp_online_users` (`online_id`,`site_id`,`member_id`,`in_forum`,`name`,`ip_address`,`date`,`anon`)
VALUES
	(13,1,0,'n','','127.0.0.1',1304500792,''),
	(12,1,1,'n','Vinay','127.0.0.1',1304502430,'y'),
	(9,1,0,'n','','127.0.0.1',1304500792,''),
	(10,1,1,'n','Vinay','127.0.0.1',1304502430,'y'),
	(11,1,0,'n','','127.0.0.1',1304500792,''),
	(14,1,1,'n','Vinay','127.0.0.1',1304502430,'y'),
	(15,1,1,'n','Vinay','127.0.0.1',1304502430,'y'),
	(16,1,0,'n','','127.0.0.1',1304500792,''),
	(17,1,1,'n','Vinay','127.0.0.1',1304502430,'y');

/*!40000 ALTER TABLE `exp_online_users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_password_lockout
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_password_lockout`;

CREATE TABLE `exp_password_lockout` (
  `lockout_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login_date` int(10) unsigned NOT NULL,
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  PRIMARY KEY (`lockout_id`),
  KEY `login_date` (`login_date`),
  KEY `ip_address` (`ip_address`),
  KEY `user_agent` (`user_agent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_ping_servers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_ping_servers`;

CREATE TABLE `exp_ping_servers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `member_id` int(10) NOT NULL DEFAULT '0',
  `server_name` varchar(32) NOT NULL,
  `server_url` varchar(150) NOT NULL,
  `port` varchar(4) NOT NULL DEFAULT '80',
  `ping_protocol` varchar(12) NOT NULL DEFAULT 'xmlrpc',
  `is_default` char(1) NOT NULL DEFAULT 'y',
  `server_order` int(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_playa_relationships
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_playa_relationships`;

CREATE TABLE `exp_playa_relationships` (
  `rel_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_entry_id` int(10) unsigned DEFAULT NULL,
  `parent_field_id` int(6) unsigned DEFAULT NULL,
  `parent_col_id` int(6) unsigned DEFAULT NULL,
  `parent_row_id` int(10) unsigned DEFAULT NULL,
  `child_entry_id` int(10) unsigned DEFAULT NULL,
  `rel_order` int(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`rel_id`),
  KEY `parent_entry_id` (`parent_entry_id`),
  KEY `parent_field_id` (`parent_field_id`),
  KEY `parent_col_id` (`parent_col_id`),
  KEY `parent_row_id` (`parent_row_id`),
  KEY `child_entry_id` (`child_entry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_relationships
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_relationships`;

CREATE TABLE `exp_relationships` (
  `rel_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `rel_parent_id` int(10) NOT NULL DEFAULT '0',
  `rel_child_id` int(10) NOT NULL DEFAULT '0',
  `rel_type` varchar(12) NOT NULL,
  `rel_data` mediumtext NOT NULL,
  `reverse_rel_data` mediumtext NOT NULL,
  PRIMARY KEY (`rel_id`),
  KEY `rel_parent_id` (`rel_parent_id`),
  KEY `rel_child_id` (`rel_child_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_reset_password
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_reset_password`;

CREATE TABLE `exp_reset_password` (
  `reset_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL,
  `resetcode` varchar(12) NOT NULL,
  `date` int(10) NOT NULL,
  PRIMARY KEY (`reset_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_revision_tracker
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_revision_tracker`;

CREATE TABLE `exp_revision_tracker` (
  `tracker_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `item_table` varchar(20) NOT NULL,
  `item_field` varchar(20) NOT NULL,
  `item_date` int(10) NOT NULL,
  `item_author_id` int(10) unsigned NOT NULL,
  `item_data` mediumtext NOT NULL,
  PRIMARY KEY (`tracker_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_revision_tracker` WRITE;
/*!40000 ALTER TABLE `exp_revision_tracker` DISABLE KEYS */;
INSERT INTO `exp_revision_tracker` (`tracker_id`,`item_id`,`item_table`,`item_field`,`item_date`,`item_author_id`,`item_data`)
VALUES
	(27,17,'exp_templates','template_data',1301996349,1,'<!doctype html>\n<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->\n<!--[if lt IE 7 ]> <html class=\"no-js ie6\" lang=\"en\"> <![endif]-->\n<!--[if IE 7 ]>    <html class=\"no-js ie7\" lang=\"en\"> <![endif]-->\n<!--[if IE 8 ]>    <html class=\"no-js ie8\" lang=\"en\"> <![endif]-->\n<!--[if (gte IE 9)|!(IE)]><!--> <html class=\"no-js\" lang=\"en\"> <!--<![endif]-->\n<head>\n  <meta charset=\"utf-8\">\n\n  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame\n       Remove this if you use the .htaccess -->\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">\n\n  <title>Site template</title>\n  <meta name=\"description\" content=\"\">\n  <meta name=\"author\" content=\"\">\n\n  <!-- Mobile viewport optimized: j.mp/bplateviewport -->\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n\n  <!-- Place favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->\n  <link rel=\"shortcut icon\" href=\"/favicon.ico\">\n  <link rel=\"apple-touch-icon\" href=\"/apple-touch-icon.png\">\n\n\n  <!-- CSS: implied media=\"all\" -->\n  <link rel=\"stylesheet\" href=\"{site_url}css/style.css?v=2\">\n	<link rel=\"stylesheet/less\" href=\"{site_url}css/adifferentindex.css\">\n	<link rel=\"stylesheet\" href=\"{site_url}css/jquery-ui.css\">	\n\n  <!-- Uncomment if you are specifically targeting less enabled mobile browsers\n  <link rel=\"stylesheet\" media=\"handheld\" href=\"css/handheld.css?v=2\">  -->\n\n  <!-- All JavaScript at the bottom, except for Modernizr which enables HTML5 elements & feature detects -->\n  <script src=\"{site_url}js/libs/modernizr-1.7.min.js\"></script>\n		<script src=\"{site_url}js/libs/less-1.0.41.min.js\"></script>\n</head>\n\n<body id=\"home\">\n<div id=\"outer\">\n  <div id=\"container\">'),
	(26,16,'exp_templates','template_data',1301996349,1,'<footer>\n	<p>&copy; 2011 – A Different Index is an experimental project by Vinay M <span class=\"amp\">&amp;</span> Nils Hendriks</p>\n</footer>\n\n	</div> <!--! end of #container -->\n</div>\n\n<!-- JavaScript at the bottom for fast page loading -->\n\n<!-- Grab Google CDN\'s jQuery, with a protocol relative URL; fall back to local if necessary -->\n<script src=\"//ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.js\"></script>\n<script>window.jQuery || document.write(\"<script src=\'{site_url}js/libs/jquery-1.5.1.min.js\'>\\x3C/script>\")</script>\n\n<script src=\"{site_url}js/mylibs/jquery.ui.core.js\"></script>	\n<script src=\"{site_url}js/mylibs/jquery.ui.widget.js\"></script>\n<script src=\"{site_url}js/mylibs/jquery.ui.mouse.js\"></script>		\n<script src=\"{site_url}js/mylibs/jquery.ui.slider.js\"></script>		\n	\n\n<!-- scripts concatenated and minified via ant build script-->\n<script src=\"{site_url}js/plugins.js\"></script>\n<script src=\"{site_url}js/script.js\"></script>\n<!-- end scripts-->\n\n\n<!--[if lt IE 7 ]>\n  <script src=\"js/libs/dd_belatedpng.js\"></script>\n  <script>DD_belatedPNG.fix(\"img, .png_bg\"); // Fix any <img> or .png_bg bg-images. Also, please read goo.gl/mZiyb </script>\n<![endif]-->\n\n\n<!-- mathiasbynens.be/notes/async-analytics-snippet Change UA-XXXXX-X to be your site\'s ID -->\n<!--<script>\n  var _gaq=[[\"_setAccount\",\"UA-XXXXX-X\"],[\"_trackPageview\"]];\n  (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;\n  g.src=(\"https:\"==location.protocol?\"//ssl\":\"//www\")+\".google-analytics.com/ga.js\";\n  s.parentNode.insertBefore(g,s)}(document,\"script\"));\n</script>-->\n\n</body>\n</html>'),
	(16,30,'exp_templates','template_data',1301996008,1,'<!doctype html>  \n<!--[if lt IE 7 ]> <html class=\"no-js ie6\"> <![endif]-->\n<!--[if IE 7 ]>    <html class=\"no-js ie7\"> <![endif]-->\n<!--[if IE 8 ]>    <html class=\"no-js ie8\"> <![endif]-->\n<!--[if (gte IE 9)|!(IE)]><!--> <html class=\"no-js\"> <!--<![endif]-->\n<head>\n  <meta charset=\"utf-8\">\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">\n  {exp:seo_lite url_title=\"{embed:url_title}\" default_title=\"{embed:title}\" default_keywords=\"{embed:keywords}\" default_description=\"{embed:description}\" category_title=\"{embed:category_title}\"}  \n  <meta name=\"viewport\" content=\"width=960\">\n  <link rel=\"shortcut icon\" href=\"/favicon.ico\">\n  <link rel=\"apple-touch-icon\" href=\"/apple-touch-icon.png\">\n  <link rel=\"stylesheet\" href=\"{site_url}css/style.css?v=2\">\n	<link rel=\"stylesheet/less\" href=\"{site_url}css/nlba.less\" media=\"all\">\n	<script src=\"{site_url}js/less-1.0.41.min.js\"></script>\n  <script src=\"{site_url}js/libs/modernizr-1.6.min.js\"></script>\n</head>\n<body class=\"print\">\n{exp:channel:entries channel=\"news|course|pages\" limit=\"1\" entry_id=\"{segment_3}\"}\n	<h1>{title}</h1>\n	{{segment_2}-body}\n	\n	{exp:eei_tcpdf:params cache=\"y\"	language=\"eng\"	title=\"{title}\"	author=\"National Library Board Academy\" font-family=\"arialunicid0\" keywords=\"nlba\"}\n	\n{/exp:channel:entries}\n</body>\n</html>'),
	(17,31,'exp_templates','template_data',1301996008,1,'{embed=\"inc/header\" title=\"Search\"}\n\n<div id=\"LeftContent\">\n	\n	{embed=\"inc/print-icon\"}\n	\n	<ul id=\"Crumbs\">\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{site_url}\" itemprop=\"url\"><span itemprop=\"title\">Home</span></a></li>\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{path={segment_1}}\" itemprop=\"url\"><span itemprop=\"title\">Search</span></a></li>\n	</ul>\n\n	<h1>Search</h1>\n		\n	\n	{exp:search:advanced_form channel=\"news|course|trainers|about\" no_result_page=\"search/noresults\" results_page=\"search/results\" results=\"10\" where=\"all\" status=\"not Closed\" search_in=\"everywhere\" show_expired=\"yes\" show_future_entries=\"yes\" form_id=\"SearchForm\"} \n		<fieldset>\n			<label for=\"Advkeywords\">Search by Keyword</label>\n			<input type=\"text\" class=\"input\" maxlength=\"100\" size=\"40\" name=\"keywords\" id=\"Advkeywords\" /> \n			<select name=\"where\" id=\"Where\" size=\"4\"> \n				<option value=\"exact\" selected=\"selected\">{lang:exact_phrase_match}</option> \n				<option value=\"any\">{lang:search_any_words}</option> \n				<option value=\"all\" >{lang:search_all_words}</option> \n				<option value=\"word\" >{lang:search_exact_word}</option> \n				</select>\n		</fieldset>\n		<fieldset>\n			<select id=\"channel_id\" name=\'channel_id[]\' class=\'multiselect\' onchange=\'changemenu(this.selectedIndex);\' size=\"4\">\n				<option selected=\"selected\" value=\"null\">Everywhere</option>\n				<option value=\"4\">Courses</option>\n				<option value=\"1\">News</option>\n				<option value=\"3\">Trainer Profile</option>\n			</select> \n			<select name=\'cat_id[]\' class=\'multiselect\' size=\"6\"> <option value=\'all\' selected=\"selected\">{lang:any_category}</option> </select> \n		</fieldset>\n		<fieldset class=\"forms\">\n			<input type=\"submit\" value=\"Search\" class=\"submit\">\n		</fieldset>\n	{/exp:search:advanced_form}\n\n\n</div>\n\n<aside id=\"Sidebar\">\n</aside>\n\n{embed=\"inc/footer\"}'),
	(18,32,'exp_templates','template_data',1301996008,1,'{embed=\"inc/header\" title=\"Search results for {exp:search:keywords}\"}\n\n<div id=\"LeftContent\">\n	\n	{embed=\"inc/print-icon\"}\n	\n	<!-- Breadcrumbs -->\n	<ul id=\"Crumbs\">\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{site_url}\" itemprop=\"url\"><span itemprop=\"title\">Home</span></a></li>\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\" class=\"active\"><a href=\"{path=search}\" itemprop=\"url\"><span itemprop=\"title\">Search</span></a></li>\n	</ul>\n	<!-- / Breadcrumbs -->\n\n\n	<h1>Search results for {exp:search:keywords}</h1>\n\n	<p>Your search did not return any results. Try <a href=\"{path=search}\">Advanced Search</a></p>\n\n</div>\n\n<aside id=\"Sidebar\">\n</aside>\n\n\n{embed=\"inc/footer\"}'),
	(19,33,'exp_templates','template_data',1301996008,1,'{embed=\"inc/header\" title=\"Search results for {exp:search:keywords}\"}\n\n<div id=\"LeftContent\">\n	\n	{embed=\"inc/print-icon\"}\n	\n	<!-- Breadcrumbs -->\n	<ul id=\"Crumbs\">\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{site_url}\" itemprop=\"url\"><span itemprop=\"title\">Home</span></a></li>\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\" class=\"active\"><a href=\"{path=search}\" itemprop=\"url\"><span itemprop=\"title\">Search</span></a></li>\n	</ul>\n	<!-- / Breadcrumbs -->\n\n\n	<h1>Search results for {exp:search:keywords}</h1>\n\n	<ol class=\"searchresults\">		\n	\n	{exp:search:search_results}\n	\n	<li>\n		{if channel_short_name==\"course\"}\n			<h2><a href=\"{path=/}{exp:nlbaformat:getcaturl entry_id=\"{entry_id}\"} {/exp:nlbaformat:getcaturl}view/{url_title}\">{title}</a></h2>\n			<p>{exp:eehive_hacksaw words=\"40\" append=\"...\" allow=\"<br />\"}{course-summary}{/exp:eehive_hacksaw}</p>\n		{/if}\n		{if channel_short_name==\"trainers\"}\n			<h2><a href=\"{title_permalink=about/trainers}\">{title}</a></h2>\n			<p>{exp:eehive_hacksaw words=\"40\" append=\"...\" allow=\"<br />\"}{trainer-bio}{/exp:eehive_hacksaw}</p>\n		{/if}\n		\n		{if channel_short_name==\"about\"}\n			<h2><a href=\"{title_permalink=about}\">{title}</a></h2>\n			<p>{exp:eehive_hacksaw words=\"40\" append=\"...\" allow=\"<br />\"}{pages-body}{/exp:eehive_hacksaw}</p>			\n		{/if}\n		\n	</li>\n\n	{/exp:search:search_results}\n	</ol>\n\n	</table>\n\n	\n	{if paginate}\n\n	<div class=\'paginate\'>\n	<span class=\'pagecount\'>{page_count}</span>&nbsp; {paginate}\n	</div>\n\n	{/if}\n\n</div>\n\n<aside id=\"Sidebar\">\n</aside>\n\n\n{embed=\"inc/footer\"}'),
	(20,34,'exp_templates','template_data',1301996008,1,'{preload_replace:channel=\"about\"}\n{embed=\"inc/header\" title=\"Sitemap\"}\n\n<div id=\"LeftContent\">\n	\n	{embed=\"inc/print-icon\"}\n	\n	<!-- Breadcrumbs -->\n	<ul id=\"Crumbs\">\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{site_url}\" itemprop=\"url\"><span itemprop=\"title\">Home</span></a></li>\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{path=sitemap}\" itemprop=\"url\"><span itemprop=\"title\">Sitemap</span></a></li>\n	</ul>\n	<!-- / Breadcrumbs -->\n\n\n	<h1>Sitemap</h1>\n	\n	<ol class=\"sitemap\">\n		<li><h2><a href=\"{path=library-professionals}\">Library Professionals</a></h2>\n			<ol>\n				{exp:query sql=\"SELECT DISTINCT A.title, A.url_title, A.entry_id as target_entry_id FROM exp_channel_titles A, exp_category_posts B where channel_id=4 and A.entry_id=B.entry_id and B.cat_id=20 and A.entry_id NOT IN (SELECT C.entry_id from exp_category_posts C WHERE cat_id IN (29)) ORDER BY A.title ASC\"}\n					<li><a href=\"{path=library-professionals/view}/{url_title}\">{title}</a></li>\n				{/exp:query}\n				<li><ol>\n				{exp:query sql=\"SELECT cat_id, cat_name, cat_url_title, cat_description, cat_image FROM exp_categories WHERE parent_id =20\"}\n					<li><h3><a href=\"{path=library-professionals/{cat_url_title}}\">{cat_name}</a></h3>\n						\n				{/exp:query}\n					<ol>\n					{exp:channel:entries channel=\"course\" disable=\"{global-disable}\" dynamic=\"off\" category=\'29\' status=\"not Closed\"}\n						<li><a href=\"{title_permalink=library-professionals/seminars/view}\">{title}</a></li>\n					{/exp:channel:entries}\n					</ol>\n					</li>	\n				</ol></li>\n			</ol>\n		</li>\n		\n		<li class=\"cat\"><h2><a href=\"{path=students}\">Students</a></h2>\n				<ol>\n					{exp:query sql=\"SELECT cat_id, cat_name from exp_categories where parent_id=21\"}	\n					<li><h3>{cat_name}</h3>\n						{embed=\"sitemap/sitemap-course\" cat_id=\"{cat_id}\"}\n					</li>\n					{/exp:query}\n				</ol>\n		</li>\n		\n		<li class=\"cat\"><h2><a href=\"{path=adults-educators}\">Adults &amp; Educators</a></h2>\n				<ol>\n					{exp:query sql=\"SELECT cat_id, cat_name from exp_categories where parent_id=25\"}	\n					<li><h3>{cat_name}</h3>\n						{embed=\"sitemap/sitemap-course\" cat_id=\"{cat_id}\"}\n					</li>\n					{/exp:query}\n				</ol>\n		</li>\n		<li><h2><a href=\"{path=about}\">About NLBA</a></h2>\n			<ol>\n				{exp:query sql=\"SELECT title, url_title, entry_id FROM exp_channel_titles WHERE channel_id=9 and entry_id!=41 ORDER BY title ASC\"}\n					<li {if segment_2==url_title}class=\"active\"{/if}><a href=\"{path=about}/{url_title}\">{title}</a></li>\n				{/exp:query}\n				<li><a href=\"{path=about/clients}\">Clients</a></li>\n				<li {if segment_2==\"trainers\"}class=\"active\"{/if}><a href=\"{path=about/trainers}\">Trainers</a></li>\n			</ol>\n		</li>\n	</ol>\n	\n	\n\n</div>\n\n<aside id=\"Sidebar\">\n	\n	\n	\n	{testimonial-module}\n	\n</aside>\n\n\n\n{embed=\"inc/footer\"}'),
	(21,35,'exp_templates','template_data',1301996008,1,'{exp:channel:entries channel=\"course\" disable=\"{global-disable}\" category=\"{embed:cat_id}\" dynamic=\"off\" status=\"not Closed\" orderby=\"title\" sort=\"asc\"}\n{if count==1}<ol>{/if}\n<li><a href=\"{title_permalink=students/view}\">{title}</a></li>\n{if count==total_results}</ol>{/if}\n{/exp:channel:entries}'),
	(22,36,'exp_templates','template_data',1301996008,1,'{exp:channel:entries channel=\"pages\" disable=\"{global-disable}\" limit=\"1\"}\n{embed=\"inc/header\" title=\"{title}\"}\n\n<div id=\"LeftContent\">\n	\n	{embed=\"inc/print-icon\"}\n	\n	<!-- Breadcrumbs -->\n	<ul id=\"Crumbs\">\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{site_url}\" itemprop=\"url\"><span itemprop=\"title\">Home</span></a></li>\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\" class=\"active\"><a href=\"{path={segment_1}}\" itemprop=\"url\"><span itemprop=\"title\">{title}</span></a></li>\n	</ul>\n	<!-- / Breadcrumbs -->\n\n\n	<h1>{title}</h1>\n\n	{pages-body}		\n	\n	{if entry_id==\"25\"}\n	\n	{exp:email:contact_form user_recipients=\"false\" recipients=\"admin@example.com\" charset=\"utf-8\" form_class=\"forms\"} \n	<fieldset>\n	<p><label for=\"from\">Your Email:</label>\n			<input type=\"text\" id=\"from\" name=\"from\" size=\"40\" maxlength=\"35\" value=\"{member_email}\" />\n	</p> \n	<p>\n		<label for=\"subject\">Subject:</label> \n		<input type=\"text\" id=\"subject\" name=\"subject\" size=\"40\" value=\"Contact Form\" />\n	</p> \n	<p>\n		<label for=\"message\">Message:</label>\n		<textarea id=\"message\" name=\"message\" rows=\"12\" cols=\"40\"></textarea>\n	</p> \n	<p>\n		<input name=\"submit\" type=\'submit\' class=\"submit orange\" value=\'Send\' />\n	</p> \n	</fieldset>\n	{/exp:email:contact_form}\n\n		\n	{/if}\n\n</div>\n\n<aside id=\"Sidebar\">\n	{if entry_id==\"25\"}\n	<div id=\"GMap\">\n		\n	</div>\n		<script type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=false\"></script>			\n		<script type=\"text/javascript\">\n			var myLatlng = new google.maps.LatLng(1.333674,103.850445),\n	    myOptions = {\n	      zoom: 16,\n	      center: myLatlng,\n	      mapTypeId: google.maps.MapTypeId.ROADMAP\n	    },\n	    map = new google.maps.Map(document.getElementById(\"GMap\"), myOptions),\n			contentString = \'<div id=\"content\">\'+\n			        \'<p><strong>National Library Board Academy</strong></p>\'+\n							\'<p>Toa Payoh Public Library, Level 3<br>6 Toa Payoh Central<br>Singapore 319191</p>\'+\n							\'<p>Tel: +65 6354 5131 / 5123 / 5060 <br>Email: <a href=\"mailto:nlbacademy@nlb.gov.sg\">nlbacademy@nlb.gov.sg</a></p>\'+\n			        \'</div>\',\n\n		 infowindow = new google.maps.InfoWindow({\n		        content: contentString\n		    }),\n\n	   marker = new google.maps.Marker({\n	       position: myLatlng, \n	       map: map,\n	       title:\"National Library Board Academy\",\n				icon:\'/images/site/marker-library.png\'\n	   });\n\n		google.maps.event.addListener(marker, \'click\', function() {\n		      infowindow.open(map,marker);\n		});\n\n\n		</script>\n		\n	{/if}\n</aside>\n\n{/exp:channel:entries}\n\n{embed=\"inc/footer\"}'),
	(23,37,'exp_templates','template_data',1301996008,1,'{exp:channel:entries channel=\"pages\" disable=\"{global-disable}\" limit=\"1\"}\n{embed=\"inc/header\" title=\"{title}\"}\n\n<div id=\"LeftContent\">\n	\n	{embed=\"inc/print-icon\"}\n	\n	<!-- Breadcrumbs -->\n	<ul id=\"Crumbs\">\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{site_url}\" itemprop=\"url\"><span itemprop=\"title\">Home</span></a></li>\n		<li><a href=\"{path={segment_1}}\" itemtype=\"http://data-vocabulary.org/Breadcrumb\" itemprop=\"url\">{segment_1_category_name}</a></li>		\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\" class=\"active\"><a href=\"{page_url}\" itemprop=\"url\"><span itemprop=\"title\">Why Choose NLBA</span></a></li>\n	</ul>\n	<!-- / Breadcrumbs -->\n\n\n	<h1>Why Choose NLBA</h1>\n\n	{pages-body}			\n\n</div>\n\n<aside id=\"Sidebar\">\n	{embed=\"inc/sidebar\"}\n</aside>\n\n{/exp:channel:entries}\n\n{embed=\"inc/footer\"}'),
	(28,37,'exp_templates','template_data',1301996349,1,'{exp:channel:entries channel=\"pages\" disable=\"{global-disable}\" limit=\"1\"}\n{embed=\"inc/header\" title=\"{title}\"}\n\n<div id=\"LeftContent\">\n	\n	{embed=\"inc/print-icon\"}\n	\n	<!-- Breadcrumbs -->\n	<ul id=\"Crumbs\">\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{site_url}\" itemprop=\"url\"><span itemprop=\"title\">Home</span></a></li>\n		<li><a href=\"{path={segment_1}}\" itemtype=\"http://data-vocabulary.org/Breadcrumb\" itemprop=\"url\">{segment_1_category_name}</a></li>		\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\" class=\"active\"><a href=\"{page_url}\" itemprop=\"url\"><span itemprop=\"title\">Why Choose NLBA</span></a></li>\n	</ul>\n	<!-- / Breadcrumbs -->\n\n\n	<h1>Why Choose NLBA</h1>\n\n	{pages-body}			\n\n</div>\n\n<aside id=\"Sidebar\">\n	{embed=\"inc/sidebar\"}\n</aside>\n\n{/exp:channel:entries}\n\n{embed=\"inc/footer\"}'),
	(29,1,'exp_templates','template_data',1301996384,1,'');

/*!40000 ALTER TABLE `exp_revision_tracker` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_search
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_search`;

CREATE TABLE `exp_search` (
  `search_id` varchar(32) NOT NULL,
  `site_id` int(4) NOT NULL DEFAULT '1',
  `search_date` int(10) NOT NULL,
  `keywords` varchar(60) NOT NULL,
  `member_id` int(10) unsigned NOT NULL,
  `ip_address` varchar(16) NOT NULL,
  `total_results` int(6) NOT NULL,
  `per_page` tinyint(3) unsigned NOT NULL,
  `query` mediumtext,
  `custom_fields` mediumtext,
  `result_page` varchar(70) NOT NULL,
  PRIMARY KEY (`search_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_search_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_search_log`;

CREATE TABLE `exp_search_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `member_id` int(10) unsigned NOT NULL,
  `screen_name` varchar(50) NOT NULL,
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `search_date` int(10) NOT NULL,
  `search_type` varchar(32) NOT NULL,
  `search_terms` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_security_hashes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_security_hashes`;

CREATE TABLE `exp_security_hashes` (
  `hash_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(10) unsigned NOT NULL,
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `hash` varchar(40) NOT NULL,
  PRIMARY KEY (`hash_id`),
  KEY `hash` (`hash`)
) ENGINE=MyISAM AUTO_INCREMENT=619 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_security_hashes` WRITE;
/*!40000 ALTER TABLE `exp_security_hashes` DISABLE KEYS */;
INSERT INTO `exp_security_hashes` (`hash_id`,`date`,`ip_address`,`hash`)
VALUES
	(603,1304500802,'127.0.0.1','3917fd1514b6c0df68003446bf7a78b0ea7f0fad'),
	(618,1304501359,'127.0.0.1','b1e615de1be8f58766d0e83f275463bfac4b54a3'),
	(617,1304501358,'127.0.0.1','e78e78ccc82a013a6f9c0414488f657920bf39e5'),
	(616,1304501320,'127.0.0.1','ec375be6e1da9778fcf88b13612326a2ec612ca5'),
	(615,1304501319,'127.0.0.1','342ab1422cdbfba27d1cacf3cf331a69f41a54b5'),
	(614,1304501318,'127.0.0.1','a79bb2130ae6b97d7964b6ca90a7fdaa08dbd0e9'),
	(613,1304501306,'127.0.0.1','22a06e80923a44a8506fe953c159d913b02bc934'),
	(612,1304501300,'127.0.0.1','4bb1cbd364a696e07b028ac95a90673e18670aa0'),
	(611,1304501295,'127.0.0.1','760eacedef72c41622f31fa3b444f59495cf20d8'),
	(610,1304501250,'127.0.0.1','d3eca88a2b7318d6f2590ae8dd7d69e6470ca3c2'),
	(609,1304501197,'127.0.0.1','ddff0aae1996e6ce70888feeb071553479fefbb5'),
	(608,1304501161,'127.0.0.1','e126099febf566dfa4b9c0b24b3d9377f6ae1aea'),
	(607,1304500870,'127.0.0.1','7dcf5f10d50c5f8d946a39ee4cca8e0393ecd46c'),
	(606,1304500805,'127.0.0.1','7b2c410487897ee58f977f73537ad0cae21a19f1'),
	(605,1304500803,'127.0.0.1','31d99794d9372f4897ef68be554ef6d1732061c4'),
	(604,1304500803,'127.0.0.1','c98967fceab212a72be4b507a102773fbc0d1e26');

/*!40000 ALTER TABLE `exp_security_hashes` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_seolite_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_seolite_config`;

CREATE TABLE `exp_seolite_config` (
  `seolite_config_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(10) unsigned DEFAULT NULL,
  `template` text,
  `default_keywords` varchar(255) NOT NULL,
  `default_description` varchar(255) NOT NULL,
  `default_title_postfix` char(60) NOT NULL,
  PRIMARY KEY (`seolite_config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_seolite_config` WRITE;
/*!40000 ALTER TABLE `exp_seolite_config` DISABLE KEYS */;
INSERT INTO `exp_seolite_config` (`seolite_config_id`,`site_id`,`template`,`default_keywords`,`default_description`,`default_title_postfix`)
VALUES
	(1,1,'<title>{title}{site_name}</title>\n<meta name=\'keywords\' content=\'{meta_keywords}\' />\n<meta name=\'description\' content=\'{meta_description}\' />\n<!-- generated by seo_lite -->','your, default, keywords, here','Your default description here',' |&nbsp;');

/*!40000 ALTER TABLE `exp_seolite_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_seolite_content
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_seolite_content`;

CREATE TABLE `exp_seolite_content` (
  `seolite_content_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(10) NOT NULL,
  `entry_id` int(10) NOT NULL,
  `title` varchar(1024) DEFAULT NULL,
  `keywords` varchar(1024) NOT NULL,
  `description` text,
  PRIMARY KEY (`seolite_content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_seolite_content` WRITE;
/*!40000 ALTER TABLE `exp_seolite_content` DISABLE KEYS */;
INSERT INTO `exp_seolite_content` (`seolite_content_id`,`site_id`,`entry_id`,`title`,`keywords`,`description`)
VALUES
	(1,1,3,'','',''),
	(2,1,4,'','','');

/*!40000 ALTER TABLE `exp_seolite_content` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_sessions`;

CREATE TABLE `exp_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `member_id` int(10) NOT NULL DEFAULT '0',
  `admin_sess` tinyint(1) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`),
  KEY `member_id` (`member_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `exp_sessions` WRITE;
/*!40000 ALTER TABLE `exp_sessions` DISABLE KEYS */;
INSERT INTO `exp_sessions` (`session_id`,`site_id`,`member_id`,`admin_sess`,`ip_address`,`user_agent`,`last_activity`)
VALUES
	('8089d61f21c018812dba419bf75bf2085002c380',1,1,1,'127.0.0.1','Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en',1304502430);

/*!40000 ALTER TABLE `exp_sessions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_sites
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_sites`;

CREATE TABLE `exp_sites` (
  `site_id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `site_label` varchar(100) NOT NULL DEFAULT '',
  `site_name` varchar(50) NOT NULL DEFAULT '',
  `site_description` text,
  `site_system_preferences` text NOT NULL,
  `site_mailinglist_preferences` text NOT NULL,
  `site_member_preferences` text NOT NULL,
  `site_template_preferences` text NOT NULL,
  `site_channel_preferences` text NOT NULL,
  `site_bootstrap_checksums` text NOT NULL,
  PRIMARY KEY (`site_id`),
  KEY `site_name` (`site_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_sites` WRITE;
/*!40000 ALTER TABLE `exp_sites` DISABLE KEYS */;
INSERT INTO `exp_sites` (`site_id`,`site_label`,`site_name`,`site_description`,`site_system_preferences`,`site_mailinglist_preferences`,`site_member_preferences`,`site_template_preferences`,`site_channel_preferences`,`site_bootstrap_checksums`)
VALUES
	(1,'A Different Index','default_site',NULL,'YTo5MDp7czoxMDoic2l0ZV9pbmRleCI7czowOiIiO3M6ODoic2l0ZV91cmwiO3M6MjM6Imh0dHA6Ly9hZGkudmluYXlzOjg4ODgvIjtzOjE2OiJ0aGVtZV9mb2xkZXJfdXJsIjtzOjI5OiJodHRwOi8vYWRpLnZpbmF5Ojg4ODgvdGhlbWVzLyI7czoxNToid2VibWFzdGVyX2VtYWlsIjtzOjE2OiJybWRvcnRAZ21haWwuY29tIjtzOjE0OiJ3ZWJtYXN0ZXJfbmFtZSI7czowOiIiO3M6MjA6ImNoYW5uZWxfbm9tZW5jbGF0dXJlIjtzOjc6ImNoYW5uZWwiO3M6MTA6Im1heF9jYWNoZXMiO3M6MzoiMTUwIjtzOjExOiJjYXB0Y2hhX3VybCI7czozODoiaHR0cDovL2FkaS52aW5heTo4ODg4L2ltYWdlcy9jYXB0Y2hhcy8iO3M6MTI6ImNhcHRjaGFfcGF0aCI7czo0NjoiL0FwcGxpY2F0aW9ucy9NQU1QL2h0ZG9jcy9hZGkvaW1hZ2VzL2NhcHRjaGFzLyI7czoxMjoiY2FwdGNoYV9mb250IjtzOjE6InkiO3M6MTI6ImNhcHRjaGFfcmFuZCI7czoxOiJ5IjtzOjIzOiJjYXB0Y2hhX3JlcXVpcmVfbWVtYmVycyI7czoxOiJuIjtzOjE3OiJlbmFibGVfZGJfY2FjaGluZyI7czoxOiJuIjtzOjE4OiJlbmFibGVfc3FsX2NhY2hpbmciO3M6MToibiI7czoxODoiZm9yY2VfcXVlcnlfc3RyaW5nIjtzOjE6Im4iO3M6MTM6InNob3dfcHJvZmlsZXIiO3M6MToibiI7czoxODoidGVtcGxhdGVfZGVidWdnaW5nIjtzOjE6Im4iO3M6MTU6ImluY2x1ZGVfc2Vjb25kcyI7czoxOiJuIjtzOjEzOiJjb29raWVfZG9tYWluIjtzOjA6IiI7czoxMToiY29va2llX3BhdGgiO3M6MDoiIjtzOjE3OiJ1c2VyX3Nlc3Npb25fdHlwZSI7czoxOiJjIjtzOjE4OiJhZG1pbl9zZXNzaW9uX3R5cGUiO3M6MjoiY3MiO3M6MjE6ImFsbG93X3VzZXJuYW1lX2NoYW5nZSI7czoxOiJ5IjtzOjE4OiJhbGxvd19tdWx0aV9sb2dpbnMiO3M6MToieSI7czoxNjoicGFzc3dvcmRfbG9ja291dCI7czoxOiJ5IjtzOjI1OiJwYXNzd29yZF9sb2Nrb3V0X2ludGVydmFsIjtzOjE6IjEiO3M6MjA6InJlcXVpcmVfaXBfZm9yX2xvZ2luIjtzOjE6InkiO3M6MjI6InJlcXVpcmVfaXBfZm9yX3Bvc3RpbmciO3M6MToieSI7czoyNDoicmVxdWlyZV9zZWN1cmVfcGFzc3dvcmRzIjtzOjE6Im4iO3M6MTk6ImFsbG93X2RpY3Rpb25hcnlfcHciO3M6MToieSI7czoyMzoibmFtZV9vZl9kaWN0aW9uYXJ5X2ZpbGUiO3M6MDoiIjtzOjE3OiJ4c3NfY2xlYW5fdXBsb2FkcyI7czoxOiJ5IjtzOjE1OiJyZWRpcmVjdF9tZXRob2QiO3M6ODoicmVkaXJlY3QiO3M6OToiZGVmdF9sYW5nIjtzOjc6ImVuZ2xpc2giO3M6ODoieG1sX2xhbmciO3M6MjoiZW4iO3M6MTI6InNlbmRfaGVhZGVycyI7czoxOiJ5IjtzOjExOiJnemlwX291dHB1dCI7czoxOiJuIjtzOjEzOiJsb2dfcmVmZXJyZXJzIjtzOjE6Im4iO3M6MTM6Im1heF9yZWZlcnJlcnMiO3M6MzoiNTAwIjtzOjExOiJ0aW1lX2Zvcm1hdCI7czoyOiJ1cyI7czoxNToic2VydmVyX3RpbWV6b25lIjtzOjM6IlVQOCI7czoxMzoic2VydmVyX29mZnNldCI7czowOiIiO3M6MTY6ImRheWxpZ2h0X3NhdmluZ3MiO3M6MToibiI7czoyMToiZGVmYXVsdF9zaXRlX3RpbWV6b25lIjtzOjM6IlVUQyI7czoxNjoiZGVmYXVsdF9zaXRlX2RzdCI7czoxOiJuIjtzOjE1OiJob25vcl9lbnRyeV9kc3QiO3M6MToieSI7czoxMzoibWFpbF9wcm90b2NvbCI7czo0OiJtYWlsIjtzOjExOiJzbXRwX3NlcnZlciI7czowOiIiO3M6MTM6InNtdHBfdXNlcm5hbWUiO3M6MDoiIjtzOjEzOiJzbXRwX3Bhc3N3b3JkIjtzOjA6IiI7czoxMToiZW1haWxfZGVidWciO3M6MToibiI7czoxMzoiZW1haWxfY2hhcnNldCI7czo1OiJ1dGYtOCI7czoxNToiZW1haWxfYmF0Y2htb2RlIjtzOjE6Im4iO3M6MTY6ImVtYWlsX2JhdGNoX3NpemUiO3M6MDoiIjtzOjExOiJtYWlsX2Zvcm1hdCI7czo1OiJwbGFpbiI7czo5OiJ3b3JkX3dyYXAiO3M6MToieSI7czoyMjoiZW1haWxfY29uc29sZV90aW1lbG9jayI7czoxOiI1IjtzOjIyOiJsb2dfZW1haWxfY29uc29sZV9tc2dzIjtzOjE6InkiO3M6ODoiY3BfdGhlbWUiO3M6NzoiZGVmYXVsdCI7czoyMToiZW1haWxfbW9kdWxlX2NhcHRjaGFzIjtzOjE6Im4iO3M6MTY6ImxvZ19zZWFyY2hfdGVybXMiO3M6MToieSI7czoxMjoic2VjdXJlX2Zvcm1zIjtzOjE6InkiO3M6MTk6ImRlbnlfZHVwbGljYXRlX2RhdGEiO3M6MToieSI7czoyNDoicmVkaXJlY3Rfc3VibWl0dGVkX2xpbmtzIjtzOjE6Im4iO3M6MTY6ImVuYWJsZV9jZW5zb3JpbmciO3M6MToibiI7czoxNDoiY2Vuc29yZWRfd29yZHMiO3M6MDoiIjtzOjE4OiJjZW5zb3JfcmVwbGFjZW1lbnQiO3M6MDoiIjtzOjEwOiJiYW5uZWRfaXBzIjtzOjA6IiI7czoxMzoiYmFubmVkX2VtYWlscyI7czowOiIiO3M6MTY6ImJhbm5lZF91c2VybmFtZXMiO3M6MDoiIjtzOjE5OiJiYW5uZWRfc2NyZWVuX25hbWVzIjtzOjA6IiI7czoxMDoiYmFuX2FjdGlvbiI7czo4OiJyZXN0cmljdCI7czoxMToiYmFuX21lc3NhZ2UiO3M6MzQ6IlRoaXMgc2l0ZSBpcyBjdXJyZW50bHkgdW5hdmFpbGFibGUiO3M6MTU6ImJhbl9kZXN0aW5hdGlvbiI7czoyMToiaHR0cDovL3d3dy55YWhvby5jb20vIjtzOjE2OiJlbmFibGVfZW1vdGljb25zIjtzOjE6InkiO3M6MTM6ImVtb3RpY29uX3BhdGgiO3M6Mzc6Imh0dHA6Ly9hZGkudmluYXk6ODg4OC9pbWFnZXMvc21pbGV5cy8iO3M6MTk6InJlY291bnRfYmF0Y2hfdG90YWwiO3M6NDoiMTAwMCI7czoxNzoibmV3X3ZlcnNpb25fY2hlY2siO3M6MToieSI7czoxNzoiZW5hYmxlX3Rocm90dGxpbmciO3M6MToibiI7czoxNzoiYmFuaXNoX21hc2tlZF9pcHMiO3M6MToieSI7czoxNDoibWF4X3BhZ2VfbG9hZHMiO3M6MjoiMTAiO3M6MTM6InRpbWVfaW50ZXJ2YWwiO3M6MToiOCI7czoxMjoibG9ja291dF90aW1lIjtzOjI6IjMwIjtzOjE1OiJiYW5pc2htZW50X3R5cGUiO3M6NzoibWVzc2FnZSI7czoxNDoiYmFuaXNobWVudF91cmwiO3M6MDoiIjtzOjE4OiJiYW5pc2htZW50X21lc3NhZ2UiO3M6NTA6IllvdSBoYXZlIGV4Y2VlZGVkIHRoZSBhbGxvd2VkIHBhZ2UgbG9hZCBmcmVxdWVuY3kuIjtzOjE3OiJlbmFibGVfc2VhcmNoX2xvZyI7czoxOiJ5IjtzOjE5OiJtYXhfbG9nZ2VkX3NlYXJjaGVzIjtzOjM6IjUwMCI7czoxNzoidGhlbWVfZm9sZGVyX3BhdGgiO3M6Mzc6Ii9BcHBsaWNhdGlvbnMvTUFNUC9odGRvY3MvYWRpL3RoZW1lcy8iO3M6MTA6ImlzX3NpdGVfb24iO3M6MToieSI7fQ==','YTozOntzOjE5OiJtYWlsaW5nbGlzdF9lbmFibGVkIjtzOjE6InkiO3M6MTg6Im1haWxpbmdsaXN0X25vdGlmeSI7czoxOiJuIjtzOjI1OiJtYWlsaW5nbGlzdF9ub3RpZnlfZW1haWxzIjtzOjA6IiI7fQ==','YTo0NDp7czoxMDoidW5fbWluX2xlbiI7czoxOiI0IjtzOjEwOiJwd19taW5fbGVuIjtzOjE6IjUiO3M6MjU6ImFsbG93X21lbWJlcl9yZWdpc3RyYXRpb24iO3M6MToibiI7czoyNToiYWxsb3dfbWVtYmVyX2xvY2FsaXphdGlvbiI7czoxOiJ5IjtzOjE4OiJyZXFfbWJyX2FjdGl2YXRpb24iO3M6NToiZW1haWwiO3M6MjM6Im5ld19tZW1iZXJfbm90aWZpY2F0aW9uIjtzOjE6Im4iO3M6MjM6Im1icl9ub3RpZmljYXRpb25fZW1haWxzIjtzOjA6IiI7czoyNDoicmVxdWlyZV90ZXJtc19vZl9zZXJ2aWNlIjtzOjE6InkiO3M6MjI6InVzZV9tZW1iZXJzaGlwX2NhcHRjaGEiO3M6MToibiI7czoyMDoiZGVmYXVsdF9tZW1iZXJfZ3JvdXAiO3M6MToiNSI7czoxNToicHJvZmlsZV90cmlnZ2VyIjtzOjY6Im1lbWJlciI7czoxMjoibWVtYmVyX3RoZW1lIjtzOjc6ImRlZmF1bHQiO3M6MTQ6ImVuYWJsZV9hdmF0YXJzIjtzOjE6InkiO3M6MjA6ImFsbG93X2F2YXRhcl91cGxvYWRzIjtzOjE6Im4iO3M6MTA6ImF2YXRhcl91cmwiO3M6Mzc6Imh0dHA6Ly9hZGkudmluYXk6ODg4OC9pbWFnZXMvYXZhdGFycy8iO3M6MTE6ImF2YXRhcl9wYXRoIjtzOjQ1OiIvQXBwbGljYXRpb25zL01BTVAvaHRkb2NzL2FkaS9pbWFnZXMvYXZhdGFycy8iO3M6MTY6ImF2YXRhcl9tYXhfd2lkdGgiO3M6MzoiMTAwIjtzOjE3OiJhdmF0YXJfbWF4X2hlaWdodCI7czozOiIxMDAiO3M6MTM6ImF2YXRhcl9tYXhfa2IiO3M6MjoiNTAiO3M6MTM6ImVuYWJsZV9waG90b3MiO3M6MToibiI7czo5OiJwaG90b191cmwiO3M6NDM6Imh0dHA6Ly9hZGkudmluYXk6ODg4OC9pbWFnZXMvbWVtYmVyX3Bob3Rvcy8iO3M6MTA6InBob3RvX3BhdGgiO3M6NTE6Ii9BcHBsaWNhdGlvbnMvTUFNUC9odGRvY3MvYWRpL2ltYWdlcy9tZW1iZXJfcGhvdG9zLyI7czoxNToicGhvdG9fbWF4X3dpZHRoIjtzOjM6IjEwMCI7czoxNjoicGhvdG9fbWF4X2hlaWdodCI7czozOiIxMDAiO3M6MTI6InBob3RvX21heF9rYiI7czoyOiI1MCI7czoxNjoiYWxsb3dfc2lnbmF0dXJlcyI7czoxOiJ5IjtzOjEzOiJzaWdfbWF4bGVuZ3RoIjtzOjM6IjUwMCI7czoyMToic2lnX2FsbG93X2ltZ19ob3RsaW5rIjtzOjE6Im4iO3M6MjA6InNpZ19hbGxvd19pbWdfdXBsb2FkIjtzOjE6Im4iO3M6MTE6InNpZ19pbWdfdXJsIjtzOjUxOiJodHRwOi8vYWRpLnZpbmF5Ojg4ODgvaW1hZ2VzL3NpZ25hdHVyZV9hdHRhY2htZW50cy8iO3M6MTI6InNpZ19pbWdfcGF0aCI7czo1OToiL0FwcGxpY2F0aW9ucy9NQU1QL2h0ZG9jcy9hZGkvaW1hZ2VzL3NpZ25hdHVyZV9hdHRhY2htZW50cy8iO3M6MTc6InNpZ19pbWdfbWF4X3dpZHRoIjtzOjM6IjQ4MCI7czoxODoic2lnX2ltZ19tYXhfaGVpZ2h0IjtzOjI6IjgwIjtzOjE0OiJzaWdfaW1nX21heF9rYiI7czoyOiIzMCI7czoxOToicHJ2X21zZ191cGxvYWRfcGF0aCI7czo1MjoiL0FwcGxpY2F0aW9ucy9NQU1QL2h0ZG9jcy9hZGkvaW1hZ2VzL3BtX2F0dGFjaG1lbnRzLyI7czoyMzoicHJ2X21zZ19tYXhfYXR0YWNobWVudHMiO3M6MToiMyI7czoyMjoicHJ2X21zZ19hdHRhY2hfbWF4c2l6ZSI7czozOiIyNTAiO3M6MjA6InBydl9tc2dfYXR0YWNoX3RvdGFsIjtzOjM6IjEwMCI7czoxOToicHJ2X21zZ19odG1sX2Zvcm1hdCI7czo0OiJzYWZlIjtzOjE4OiJwcnZfbXNnX2F1dG9fbGlua3MiO3M6MToieSI7czoxNzoicHJ2X21zZ19tYXhfY2hhcnMiO3M6NDoiNjAwMCI7czoxOToibWVtYmVybGlzdF9vcmRlcl9ieSI7czoxMToidG90YWxfcG9zdHMiO3M6MjE6Im1lbWJlcmxpc3Rfc29ydF9vcmRlciI7czo0OiJkZXNjIjtzOjIwOiJtZW1iZXJsaXN0X3Jvd19saW1pdCI7czoyOiIyMCI7fQ==','YTo2OntzOjExOiJzdHJpY3RfdXJscyI7czoxOiJ5IjtzOjg6InNpdGVfNDA0IjtzOjA6IiI7czoxOToic2F2ZV90bXBsX3JldmlzaW9ucyI7czoxOiJ5IjtzOjE4OiJtYXhfdG1wbF9yZXZpc2lvbnMiO3M6MToiNSI7czoxNToic2F2ZV90bXBsX2ZpbGVzIjtzOjE6InkiO3M6MTg6InRtcGxfZmlsZV9iYXNlcGF0aCI7czo2NDoiL0FwcGxpY2F0aW9ucy9NQU1QL2h0ZG9jcy9hZGkvc3lzdGVtL2V4cHJlc3Npb25lbmdpbmUvdGVtcGxhdGVzLyI7fQ==','YTo5OntzOjIxOiJpbWFnZV9yZXNpemVfcHJvdG9jb2wiO3M6MzoiZ2QyIjtzOjE4OiJpbWFnZV9saWJyYXJ5X3BhdGgiO3M6MDoiIjtzOjE2OiJ0aHVtYm5haWxfcHJlZml4IjtzOjU6InRodW1iIjtzOjE0OiJ3b3JkX3NlcGFyYXRvciI7czo0OiJkYXNoIjtzOjE3OiJ1c2VfY2F0ZWdvcnlfbmFtZSI7czoxOiJ5IjtzOjIyOiJyZXNlcnZlZF9jYXRlZ29yeV93b3JkIjtzOjg6ImNhdGVnb3J5IjtzOjIzOiJhdXRvX2NvbnZlcnRfaGlnaF9hc2NpaSI7czoxOiJuIjtzOjIyOiJuZXdfcG9zdHNfY2xlYXJfY2FjaGVzIjtzOjE6InkiO3M6MjM6ImF1dG9fYXNzaWduX2NhdF9wYXJlbnRzIjtzOjE6InkiO30=','YToxOntzOjM5OiIvQXBwbGljYXRpb25zL01BTVAvaHRkb2NzL2FkaS9pbmRleC5waHAiO3M6MzI6ImQ2YmY1MGUyY2Q4NDY3NmUzYjFmMGJjMGQzM2RmYTcyIjt9');

/*!40000 ALTER TABLE `exp_sites` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_snippets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_snippets`;

CREATE TABLE `exp_snippets` (
  `snippet_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) NOT NULL,
  `snippet_name` varchar(75) NOT NULL,
  `snippet_contents` text,
  PRIMARY KEY (`snippet_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_specialty_templates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_specialty_templates`;

CREATE TABLE `exp_specialty_templates` (
  `template_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `enable_template` char(1) NOT NULL DEFAULT 'y',
  `template_name` varchar(50) NOT NULL,
  `data_title` varchar(80) NOT NULL,
  `template_data` text NOT NULL,
  PRIMARY KEY (`template_id`),
  KEY `template_name` (`template_name`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_specialty_templates` WRITE;
/*!40000 ALTER TABLE `exp_specialty_templates` DISABLE KEYS */;
INSERT INTO `exp_specialty_templates` (`template_id`,`site_id`,`enable_template`,`template_name`,`data_title`,`template_data`)
VALUES
	(1,1,'y','offline_template','','<html>\n<head>\n\n<title>System Offline</title>\n\n<style type=\"text/css\">\n\nbody { \nbackground-color:	#ffffff; \nmargin:				50px; \nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-size:			11px;\ncolor:				#000;\nbackground-color:	#fff;\n}\n\na {\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-weight:		bold;\nletter-spacing:		.09em;\ntext-decoration:	none;\ncolor:			  #330099;\nbackground-color:	transparent;\n}\n  \na:visited {\ncolor:				#330099;\nbackground-color:	transparent;\n}\n\na:hover {\ncolor:				#000;\ntext-decoration:	underline;\nbackground-color:	transparent;\n}\n\n#content  {\nborder:				#999999 1px solid;\npadding:			22px 25px 14px 25px;\n}\n\nh1 {\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-weight:		bold;\nfont-size:			14px;\ncolor:				#000;\nmargin-top: 		0;\nmargin-bottom:		14px;\n}\n\np {\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-size: 			12px;\nfont-weight: 		normal;\nmargin-top: 		12px;\nmargin-bottom: 		14px;\ncolor: 				#000;\n}\n</style>\n\n</head>\n\n<body>\n\n<div id=\"content\">\n\n<h1>System Offline</h1>\n\n<p>This site is currently offline</p>\n\n</div>\n\n</body>\n\n</html>'),
	(2,1,'y','message_template','','<html>\n<head>\n\n<title>{title}</title>\n\n<meta http-equiv=\'content-type\' content=\'text/html; charset={charset}\' />\n\n{meta_refresh}\n\n<style type=\"text/css\">\n\nbody { \nbackground-color:	#ffffff; \nmargin:				50px; \nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-size:			11px;\ncolor:				#000;\nbackground-color:	#fff;\n}\n\na {\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nletter-spacing:		.09em;\ntext-decoration:	none;\ncolor:			  #330099;\nbackground-color:	transparent;\n}\n  \na:visited {\ncolor:				#330099;\nbackground-color:	transparent;\n}\n\na:active {\ncolor:				#ccc;\nbackground-color:	transparent;\n}\n\na:hover {\ncolor:				#000;\ntext-decoration:	underline;\nbackground-color:	transparent;\n}\n\n#content  {\nborder:				#000 1px solid;\nbackground-color: 	#DEDFE3;\npadding:			22px 25px 14px 25px;\n}\n\nh1 {\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-weight:		bold;\nfont-size:			14px;\ncolor:				#000;\nmargin-top: 		0;\nmargin-bottom:		14px;\n}\n\np {\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-size: 			12px;\nfont-weight: 		normal;\nmargin-top: 		12px;\nmargin-bottom: 		14px;\ncolor: 				#000;\n}\n\nul {\nmargin-bottom: 		16px;\n}\n\nli {\nlist-style:			square;\nfont-family:		Verdana, Arial, Tahoma, Trebuchet MS, Sans-serif;\nfont-size: 			12px;\nfont-weight: 		normal;\nmargin-top: 		8px;\nmargin-bottom: 		8px;\ncolor: 				#000;\n}\n\n</style>\n\n</head>\n\n<body>\n\n<div id=\"content\">\n\n<h1>{heading}</h1>\n\n{content}\n\n<p>{link}</p>\n\n</div>\n\n</body>\n\n</html>'),
	(3,1,'y','admin_notify_reg','Notification of new member registration','New member registration site: {site_name}\n\nScreen name: {name}\nUser name: {username}\nEmail: {email}\n\nYour control panel URL: {control_panel_url}'),
	(4,1,'y','admin_notify_entry','A new channel entry has been posted','A new entry has been posted in the following channel:\n{channel_name}\n\nThe title of the entry is:\n{entry_title}\n\nPosted by: {name}\nEmail: {email}\n\nTo read the entry please visit: \n{entry_url}\n'),
	(5,1,'y','admin_notify_mailinglist','Someone has subscribed to your mailing list','A new mailing list subscription has been accepted.\n\nEmail Address: {email}\nMailing List: {mailing_list}'),
	(6,1,'y','admin_notify_comment','You have just received a comment','You have just received a comment for the following channel:\n{channel_name}\n\nThe title of the entry is:\n{entry_title}\n\nLocated at: \n{comment_url}\n\nPosted by: {name}\nEmail: {email}\nURL: {url}\nLocation: {location}\n\n{comment}'),
	(7,1,'y','mbr_activation_instructions','Enclosed is your activation code','Thank you for your new member registration.\n\nTo activate your new account, please visit the following URL:\n\n{unwrap}{activation_url}{/unwrap}\n\nThank You!\n\n{site_name}\n\n{site_url}'),
	(8,1,'y','forgot_password_instructions','Login information','{name},\n\nTo reset your password, please go to the following page:\n\n{reset_url}\n\nYour password will be automatically reset, and a new password will be emailed to you.\n\nIf you do not wish to reset your password, ignore this message. It will expire in 24 hours.\n\n{site_name}\n{site_url}'),
	(9,1,'y','reset_password_notification','New Login Information','{name},\n\nHere is your new login information:\n\nUsername: {username}\nPassword: {password}\n\n{site_name}\n{site_url}'),
	(10,1,'y','validated_member_notify','Your membership account has been activated','{name},\n\nYour membership account has been activated and is ready for use.\n\nThank You!\n\n{site_name}\n{site_url}'),
	(11,1,'y','decline_member_validation','Your membership account has been declined','{name},\n\nWe\'re sorry but our staff has decided not to validate your membership.\n\n{site_name}\n{site_url}'),
	(12,1,'y','mailinglist_activation_instructions','Email Confirmation','Thank you for joining the \"{mailing_list}\" mailing list!\n\nPlease click the link below to confirm your email.\n\nIf you do not want to be added to our list, ignore this email.\n\n{unwrap}{activation_url}{/unwrap}\n\nThank You!\n\n{site_name}'),
	(13,1,'y','comment_notification','Someone just responded to your comment','{name_of_commenter} just responded to the entry you subscribed to at:\n{channel_name}\n\nThe title of the entry is:\n{entry_title}\n\nYou can see the comment at the following URL:\n{comment_url}\n\n{comment}\n\nTo stop receiving notifications for this comment, click here:\n{notification_removal_url}'),
	(14,1,'y','comments_opened_notification','New comments have been added','Responses have been added to the entry you subscribed to at:\n{channel_name}\n\nThe title of the entry is:\n{entry_title}\n\nYou can see the comments at the following URL:\n{comment_url}\n\n{comments}\n{comment} \n{/comments}\n\nTo stop receiving notifications for this entry, click here:\n{notification_removal_url}'),
	(15,1,'y','private_message_notification','Someone has sent you a Private Message','\n{recipient_name},\n\n{sender_name} has just sent you a Private Message titled ‘{message_subject}’.\n\nYou can see the Private Message by logging in and viewing your inbox at:\n{site_url}\n\nContent:\n\n{message_content}\n\nTo stop receiving notifications of Private Messages, turn the option off in your Email Settings.\n\n{site_name}\n{site_url}'),
	(16,1,'y','pm_inbox_full','Your private message mailbox is full','{recipient_name},\n\n{sender_name} has just attempted to send you a Private Message,\nbut your inbox is full, exceeding the maximum of {pm_storage_limit}.\n\nPlease log in and remove unwanted messages from your inbox at:\n{site_url}');

/*!40000 ALTER TABLE `exp_specialty_templates` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_stats
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_stats`;

CREATE TABLE `exp_stats` (
  `stat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `total_members` mediumint(7) NOT NULL DEFAULT '0',
  `recent_member_id` int(10) NOT NULL DEFAULT '0',
  `recent_member` varchar(50) NOT NULL,
  `total_entries` mediumint(8) NOT NULL DEFAULT '0',
  `total_forum_topics` mediumint(8) NOT NULL DEFAULT '0',
  `total_forum_posts` mediumint(8) NOT NULL DEFAULT '0',
  `total_comments` mediumint(8) NOT NULL DEFAULT '0',
  `last_entry_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_forum_post_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_comment_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_visitor_date` int(10) unsigned NOT NULL DEFAULT '0',
  `most_visitors` mediumint(7) NOT NULL DEFAULT '0',
  `most_visitor_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_cache_clear` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`stat_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_stats` WRITE;
/*!40000 ALTER TABLE `exp_stats` DISABLE KEYS */;
INSERT INTO `exp_stats` (`stat_id`,`site_id`,`total_members`,`recent_member_id`,`recent_member`,`total_entries`,`total_forum_topics`,`total_forum_posts`,`total_comments`,`last_entry_date`,`last_forum_post_date`,`last_comment_date`,`last_visitor_date`,`most_visitors`,`most_visitor_date`,`last_cache_clear`)
VALUES
	(1,1,2,2,'Nils',4,0,0,0,1304420281,0,0,1304502430,9,1304500887,1304839534);

/*!40000 ALTER TABLE `exp_stats` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_status_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_status_groups`;

CREATE TABLE `exp_status_groups` (
  `group_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_name` varchar(50) NOT NULL,
  PRIMARY KEY (`group_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_status_groups` WRITE;
/*!40000 ALTER TABLE `exp_status_groups` DISABLE KEYS */;
INSERT INTO `exp_status_groups` (`group_id`,`site_id`,`group_name`)
VALUES
	(1,1,'Statuses');

/*!40000 ALTER TABLE `exp_status_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_status_no_access
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_status_no_access`;

CREATE TABLE `exp_status_no_access` (
  `status_id` int(6) unsigned NOT NULL,
  `member_group` smallint(4) unsigned NOT NULL,
  PRIMARY KEY (`status_id`,`member_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_statuses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_statuses`;

CREATE TABLE `exp_statuses` (
  `status_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_id` int(4) unsigned NOT NULL,
  `status` varchar(50) NOT NULL,
  `status_order` int(3) unsigned NOT NULL,
  `highlight` varchar(30) NOT NULL,
  PRIMARY KEY (`status_id`),
  KEY `group_id` (`group_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_statuses` WRITE;
/*!40000 ALTER TABLE `exp_statuses` DISABLE KEYS */;
INSERT INTO `exp_statuses` (`status_id`,`site_id`,`group_id`,`status`,`status_order`,`highlight`)
VALUES
	(1,1,1,'open',1,'009933'),
	(2,1,1,'closed',2,'990000');

/*!40000 ALTER TABLE `exp_statuses` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_template_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_template_groups`;

CREATE TABLE `exp_template_groups` (
  `group_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_name` varchar(50) NOT NULL,
  `group_order` int(3) unsigned NOT NULL,
  `is_site_default` char(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`group_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_template_groups` WRITE;
/*!40000 ALTER TABLE `exp_template_groups` DISABLE KEYS */;
INSERT INTO `exp_template_groups` (`group_id`,`site_id`,`group_name`,`group_order`,`is_site_default`)
VALUES
	(1,1,'home',1,'y'),
	(4,1,'ajax',4,'n'),
	(17,1,'profile',9,'n'),
	(7,1,'error',7,'n'),
	(8,1,'inc',8,'n'),
	(20,1,'meetups',12,'n'),
	(19,1,'project',11,'n'),
	(12,1,'pdf',12,'n'),
	(13,1,'search',13,'n'),
	(14,1,'sitemap',14,'n'),
	(15,1,'static',15,'n'),
	(18,1,'user',10,'n');

/*!40000 ALTER TABLE `exp_template_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_template_member_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_template_member_groups`;

CREATE TABLE `exp_template_member_groups` (
  `group_id` smallint(4) unsigned NOT NULL,
  `template_group_id` mediumint(5) unsigned NOT NULL,
  PRIMARY KEY (`group_id`,`template_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_template_no_access
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_template_no_access`;

CREATE TABLE `exp_template_no_access` (
  `template_id` int(6) unsigned NOT NULL,
  `member_group` smallint(4) unsigned NOT NULL,
  PRIMARY KEY (`template_id`,`member_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_templates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_templates`;

CREATE TABLE `exp_templates` (
  `template_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `group_id` int(6) unsigned NOT NULL,
  `template_name` varchar(50) NOT NULL,
  `save_template_file` char(1) NOT NULL DEFAULT 'n',
  `template_type` varchar(16) NOT NULL DEFAULT 'webpage',
  `template_data` mediumtext,
  `template_notes` text,
  `edit_date` int(10) NOT NULL DEFAULT '0',
  `last_author_id` int(10) unsigned NOT NULL DEFAULT '0',
  `cache` char(1) NOT NULL DEFAULT 'n',
  `refresh` int(6) unsigned NOT NULL DEFAULT '0',
  `no_auth_bounce` varchar(50) NOT NULL DEFAULT '',
  `enable_http_auth` char(1) NOT NULL DEFAULT 'n',
  `allow_php` char(1) NOT NULL DEFAULT 'n',
  `php_parse_location` char(1) NOT NULL DEFAULT 'o',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`template_id`),
  KEY `group_id` (`group_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_templates` WRITE;
/*!40000 ALTER TABLE `exp_templates` DISABLE KEYS */;
INSERT INTO `exp_templates` (`template_id`,`site_id`,`group_id`,`template_name`,`save_template_file`,`template_type`,`template_data`,`template_notes`,`edit_date`,`last_author_id`,`cache`,`refresh`,`no_auth_bounce`,`enable_http_auth`,`allow_php`,`php_parse_location`,`hits`)
VALUES
	(1,1,1,'index','y','webpage','','',1301996384,1,'n',0,'','n','n','o',111),
	(41,1,18,'login','y','webpage','{embed=\"inc/header\" body_id=\"home\"}\n\n\n\n{embed=\"inc/footer\"}',NULL,1304418893,1,'n',0,'','n','n','o',0),
	(42,1,18,'register','y','webpage','{embed=\"inc/header\" body_id=\"home\"}\n\n\n\n{embed=\"inc/footer\"}',NULL,1304418893,1,'n',0,'','n','n','o',0),
	(43,1,18,'index','y','webpage','',NULL,1304418893,1,'n',0,'','n','n','o',0),
	(44,1,19,'index','y','webpage','asdasd',NULL,1304419149,1,'n',0,'','n','n','o',9),
	(45,1,20,'index','y','webpage','{embed=\"inc/header\" title=\'{title}\'}\n\n<div id=\"main\" role=\"main\">\n\n<h1>Meetups</h1>\n\n<ul>\n{exp:channel:entries channel=\"meetups\" disable=\"{global-disable}\" dynamic=\"off\"}\n  <li>\n    <h2><a href=\"{title_permalink=meetups}\">{title}</a></h2>\n    {summary}\n  </li>\n  \n{/exp:channel:entries}\n</ul>\n\n</div>\n\n\n\n\n{embed=\"inc/footer\"}',NULL,1304420664,1,'n',0,'','n','n','o',38),
	(7,1,4,'course-preview','y','webpage','<script type=\"text/javascript\" src=\"{site_url}js/swfobject.js\"></script>\n<script type=\"text/javascript\">\n$(document).ready(function() {				\n	var so = new SWFObject(\"{get_post:file}\", \"sotester\", \"{get_post:w}\", \"{get_post:h}\", \"9\", \"#FFFFFF\");\n	so.addParam (\"wmode\", \"transparent\");\n	so.addParam (\"allowScriptAccess\", \"always\");\n	so.write(\"PreviewVideo\");\n});\n</script>\n<div id=\"PreviewVideo\">\n	\n</div>',NULL,1301995963,1,'n',0,'','n','n','o',0),
	(8,1,4,'getcourses-course','y','webpage','{exp:channel:entries channel=\"course\" disable=\"{global-disable}\" entry_id=\"{embed:entries}\" dynamic=\"off\" status=\"not Closed\" dynamic=\"off\" fixed_order=\"{embed:entries}\"}\n<li>						\n	<h3><a href=\"{title_permalink={get_post:urlcat}/view}\">{title}</a></h3>\n	<ol class=\"tags\">\n		{categories show_group=\"2\"}\n			{if parent_id!=0}<li>{category_name}</li>{/if}\n		{/categories}\n		{if course-duration}<li>{course-duration}</li>{/if}\n		{categories show_group=\"3\"}\n			<li style=\"background-color:#{category_color}\">Format: {category_name}</li>\n		{/categories}\n		{if course-fees}<li>{course-fees}</li>{/if}\n		{if course-preview}\n			{course-preview}\n			<li class=\"coursePreview\"><a href=\"{path=ajax/course-preview}/?&file={flash}&w={if width}{width}{if:else}800{/if}&h={if height}{height}{if:else}542{/if}\" rel=\"facebox\">Course Preview</a></li>\n			{/course-preview}\n		{/if}\n	</ol>\n	{course-summary}\n	<p><a href=\"{title_permalink={get_post:urlcat}/view}\">Read more</a></p>\n</li>\n{if no_results}\n<li>\n	<p>We could not find any courses that match your search. Try our <a href=\"{path=search}\">advanced search</a> option.</p>\n</li>\n{/if}\n{/exp:channel:entries}',NULL,1301995963,1,'n',0,'','n','n','o',0),
	(9,1,4,'getcourses','y','webpage','{exp:category_sorted_entries channel=\"course\" style=\"linear\" group_id=\"3\" show_empty=\"no\" category=\"{get_post:level}\" status=\"not Closed\"}\n{entry_titles}\n<?php\n$entries[] = \"{entry_id}\"; //loop through articles and load entries in array\n?>\n{/entry_titles}\n{/exp:category_sorted_entries}\n\n<?php\nif(isset($entries)){\n	$output = array_unique($entries);\n	$outputim = implode(\'|\',$output);\n} \n\n?>\n\n{embed=\"ajax/getcourses-course\" entries=\"<?php echo $outputim; ?>\"}',NULL,1301995963,1,'n',0,'','n','n','o',0),
	(10,1,4,'index','y','webpage','',NULL,1301995963,1,'n',0,'','n','n','o',0),
	(14,1,7,'404','y','webpage','{embed=\"inc/header\" title=\"404 Page not found\"}\n\n<div id=\"LeftContent\">\n\n	<h1>Page not found</h1>\n	<p>It appears that the page that you requested is not on our site. It may have moved, been deleted, or may just be a bad link. Please use the navigation items on this page to find what you are looking for. If you believe that you\'ve reached this page in error, please feel free to <a href=\"{path=contact}\">contact us</a>.</p>\n	\n</div>\n\n\n\n{embed=\"inc/footer\"}',NULL,1301995963,1,'n',0,'','n','n','o',0),
	(15,1,7,'index','y','webpage','{embed=\"inc/header\" title=\"{title}\" body_class=\"error\"}\n{meta_refresh}\n<div id=\"LeftContent\">\n\n	\n	<h1>{heading}</h1>\n\n	{content}\n\n	<p>{link}</p>\n	\n</div>\n\n\n\n{embed=\"inc/footer\"}',NULL,1301995963,1,'n',0,'','n','n','o',0),
	(16,1,8,'footer','y','webpage','<footer>\n	<p>&copy; 2011 – A Different Index is an experimental project by Vinay M <span class=\"amp\">&amp;</span> Nils Hendriks</p>\n</footer>\n\n	</div> <!--! end of #container -->\n</div>\n\n<!-- JavaScript at the bottom for fast page loading -->\n\n<!-- Grab Google CDN\'s jQuery, with a protocol relative URL; fall back to local if necessary -->\n<script src=\"//ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.js\"></script>\n<script>window.jQuery || document.write(\"<script src=\'{site_url}js/libs/jquery-1.5.1.min.js\'>\\x3C/script>\")</script>\n\n<script src=\"{site_url}js/mylibs/jquery.ui.core.js\"></script>	\n<script src=\"{site_url}js/mylibs/jquery.ui.widget.js\"></script>\n<script src=\"{site_url}js/mylibs/jquery.ui.mouse.js\"></script>		\n<script src=\"{site_url}js/mylibs/jquery.ui.slider.js\"></script>		\n	\n\n<!-- scripts concatenated and minified via ant build script-->\n<script src=\"{site_url}js/plugins.js\"></script>\n<script src=\"{site_url}js/script.js\"></script>\n<!-- end scripts-->\n\n\n<!--[if lt IE 7 ]>\n  <script src=\"js/libs/dd_belatedpng.js\"></script>\n  <script>DD_belatedPNG.fix(\"img, .png_bg\"); // Fix any <img> or .png_bg bg-images. Also, please read goo.gl/mZiyb </script>\n<![endif]-->\n\n\n<!-- mathiasbynens.be/notes/async-analytics-snippet Change UA-XXXXX-X to be your site\'s ID -->\n<!--<script>\n  var _gaq=[[\"_setAccount\",\"UA-XXXXX-X\"],[\"_trackPageview\"]];\n  (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;\n  g.src=(\"https:\"==location.protocol?\"//ssl\":\"//www\")+\".google-analytics.com/ga.js\";\n  s.parentNode.insertBefore(g,s)}(document,\"script\"));\n</script>-->\n\n</body>\n</html>',NULL,1301996349,1,'n',0,'','n','n','o',0),
	(17,1,8,'header','y','webpage','<!doctype html>\n<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->\n<!--[if lt IE 7 ]> <html class=\"no-js ie6\" lang=\"en\"> <![endif]-->\n<!--[if IE 7 ]>    <html class=\"no-js ie7\" lang=\"en\"> <![endif]-->\n<!--[if IE 8 ]>    <html class=\"no-js ie8\" lang=\"en\"> <![endif]-->\n<!--[if (gte IE 9)|!(IE)]><!--> <html class=\"no-js\" lang=\"en\"> <!--<![endif]-->\n<head>\n  <meta charset=\"utf-8\">\n\n  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame\n       Remove this if you use the .htaccess -->\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">\n\n  <title>Site template</title>\n  <meta name=\"description\" content=\"\">\n  <meta name=\"author\" content=\"\">\n\n  <!-- Mobile viewport optimized: j.mp/bplateviewport -->\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n\n  <!-- Place favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->\n  <link rel=\"shortcut icon\" href=\"/favicon.ico\">\n  <link rel=\"apple-touch-icon\" href=\"/apple-touch-icon.png\">\n\n\n  <!-- CSS: implied media=\"all\" -->\n  <link rel=\"stylesheet\" href=\"{site_url}css/style.css?v=2\">\n	<link rel=\"stylesheet/less\" href=\"{site_url}css/adifferentindex.css\">\n	<link rel=\"stylesheet\" href=\"{site_url}css/jquery-ui.css\">	\n\n  <!-- Uncomment if you are specifically targeting less enabled mobile browsers\n  <link rel=\"stylesheet\" media=\"handheld\" href=\"css/handheld.css?v=2\">  -->\n\n  <!-- All JavaScript at the bottom, except for Modernizr which enables HTML5 elements & feature detects -->\n  <script src=\"{site_url}js/libs/modernizr-1.7.min.js\"></script>\n		<script src=\"{site_url}js/libs/less-1.0.41.min.js\"></script>\n</head>\n\n<body id=\"home\">\n<div id=\"outer\">\n  <div id=\"container\">',NULL,1301996349,1,'n',0,'','n','n','o',0),
	(40,1,17,'index','y','webpage','{embed=\"inc/header\"}\n\n<div id=\"main\" role=\"main\">\n	<h1>Nils Hendriks</h1>\n	<img src=\"img/nilshendriks.jpg\" alt=\"photo of Nils Hendriks\" />\n	<p class=\"caption\">Photograph by Nils Hendriks</p>\n	<h2>Data</h2>\n	<table summary=\"This table shows data about this company\">\n		<caption>table caption</caption>\n		<tbody>\n			<tr>\n				<th>Website</th>\n				<td><a class=\"external\" href=\"http://nilshendriks.com\" title=\"\">nilshendriks.com</a></td>\n			</tr>\n			<tr>\n				<th>Jobtitle</th>\n				<td>Front End Web Developer</td>\n			</tr>\n			<tr>\n				<th>Skillset</th>\n				<td>\n					<ul>\n						<li><abbr title=\"HyperText Markup Language\">HTML</abbr></li>\n						<li><abbr title=\"Cascading Style Sheets\">CSS</abbr></li>\n						<li>unobtrusive Javascript (jQuery)</li>\n						<li>Web Standards Consulting</li>\n					</ul>\n				</td>\n			</tr>\n			<tr>\n				<th>Projects</th>\n				<td>\n					<ul>\n						<li><a class=\"external\" href=\"https://www.icscards.nl/\" title=\"\">ICS</a> – <a href=\"page.php\" title=\"to this company\'s profile page\">Edenspiekermann</a></li>\n						<li><a class=\"external\" href=\"http://www.noord-holland.nl/\" title=\"\">PNH</a> – Edenspiekermann</li>\n						<li>ABNAMRO – Edenspiekermann</li>								\n						<li><a class=\"external\" href=\"http://www.contactsingapore.sg/\" title=\"\">Contact SG</a> – Convertium</li>\n						<li>Vodafone India Mobile – Qais Consulting</li>\n						<li><abbr title=\"Civil Aviation Authority of Singapore\"><a class=\"external\" href=\"http://www.caas.gov.sg/\">CAAS</a></abbr> - Qais Consulting</li>\n					</ul>\n				</td>\n			</tr>\n			<tr>\n				<th>Location</th>\n				<td>Singapore</td>\n			</tr>\n								\n			\n<!--\n			Other sites				Stencils - stencil.posterous.com\n								Blog - blog.nilshendriks.com\n								Photography - \n								photography.nilshendriks.com\n								Playground - playground.nilshendriks.com-->\n			\n\n		</tbody>\n	</table>\n	<h2>Background</h2>\n	<p>Born and raised in The Netherlands and schooled as a Master of Arts in Film Studies. During these studies he was sidetracked by something called the World Wide Web in 1995 and it has been both his work- and playfield ever since.</p>\n	<p>Years of self teaching and working for several companies eventually lead to landing a position at Eden, now Edenspiekermann before moving to Singapore in 2007.</p>\n</div>\n<aside role=\"complementary\">\n	<div id=\"hcard-Nils-Hendriks\" class=\"vcard\">\n	 <span class=\"fn\">Nils Hendriks</span>\n	 <div class=\"org\">nilshendriks.com</div>\n	 <a class=\"email\" href=\"mailto:nils@nilshendriks.com\">nils@nilshendriks.com</a>\n	 <div class=\"adr\">\n	  <div class=\"street-address\">205 River Valley Road</div>\n	  <span class=\"locality\">Singapore</span>\n	, \n	  <span class=\"postal-code\">238274</span>\n	\n	  <span class=\"country-name\">Singapore</span>\n	\n	 </div>\n	 <div class=\"tel\">+65 8113 4709</div>\n	 <a class=\"url\" href=\"aim:goim?screenname=aimsomename\">AIM</a>\n	 <a class=\"url\" href=\"ymsgr:sendIM?yim\">YIM</a>\n	 <a class=\"url\" href=\"xmpp:jabber\">Jabber</a>\n	<div class=\"tags\"><a href=\"http://kitchen.technorati.com/contacts/tag/developer\">developer</a> </div>\n	</div>\n	<a class=\"download\" href=\"#\">Download vCard</a>\n</aside>\n\n\n{embed=\"inc/footer\"}',NULL,1301999494,1,'n',0,'','n','n','o',257),
	(18,1,8,'index','y','webpage','',NULL,1301995963,1,'n',0,'','n','n','o',0),
	(19,1,8,'print-icon','y','webpage','<a href=\"#\" id=\"PrintPage\"><img src=\"{site_url}images/site/icon-print.jpg\" alt=\"Print this page\">Print</a>',NULL,1301995963,1,'n',0,'','n','n','o',0),
	(20,1,8,'share','y','webpage','<aside class=\"module\">\n	<div class=\"addthis_toolbox addthis_pill_combo\">	    \n	    <a class=\"addthis_button_facebook_like\"></a>\n			<a class=\"addthis_button_tweet\" tw:count=\"horizontal\"></a>\n	    <a class=\"addthis_counter addthis_pill_style\"></a>\n	</div>	\n</aside>',NULL,1301995963,1,'n',0,'','n','n','o',0),
	(21,1,8,'sidebar','y','webpage','{embed=\"inc/share\"}\n\n{exp:channel:entries channel=\"announcements\" limit=\"8\" disable=\"{global-disable}\" dynamic=\"off\"}\n{if count==1}<aside><h3>Key Annoucements</h3>\n<ul class=\"links\">{/if}\n	<li><a href=\"{title_permalink=announcements}\">{title}</a></li>\n{if count==total_results}</ul></aside>{/if}\n{/exp:channel:entries}\n\n<aside class=\"module\">\n	{exp:query sql=\"SELECT field_id_1 as why_choose FROM exp_category_field_data where cat_id=\'{segment_1_category_id}\'\"}\n	<p><a href=\"{path={segment_1}/why-choose-nlba}\"><img src=\"{why_choose}\" alt=\"Why choose {segment_1_category_name}\"></a></p>\n	{/exp:query}\n</aside>\n\n<aside class=\"testimonials module\">\n{embed=\"inc/testimonial\"}\n</aside>\n',NULL,1301995963,1,'n',0,'','n','n','o',0),
	(22,1,8,'testimonial','y','webpage','<!-- Testimonials -->\n	<header><h4>Testimonials</h4></header>\n	<div class=\"content clearfix\">\n		<ul>\n			{exp:channel:entries channel=\"testimonials\" disable=\"{global-disable}\" dynamic=\"off\" limit=\"{if segment_2}5{if:else}12{/if}\"}\n			<li>\n				<p>{exp:eehive_hacksaw words=\"40\" append=\"...\" allow=\"<br />\"}{testimonial}{/exp:eehive_hacksaw}</p>\n				<small>&ndash;&ndash;&ndash; {title}</small>\n			</li>\n			{/exp:channel:entries}\n		</ul>\n		<ul class=\"pager\">\n			<li><a href=\"#\">1</a></li>\n		</ul>\n	</div>\n<!-- / Testimonials -->',NULL,1301995963,1,'n',0,'','n','n','o',0),
	(30,1,12,'index','y','webpage','<!doctype html>  \n<!--[if lt IE 7 ]> <html class=\"no-js ie6\"> <![endif]-->\n<!--[if IE 7 ]>    <html class=\"no-js ie7\"> <![endif]-->\n<!--[if IE 8 ]>    <html class=\"no-js ie8\"> <![endif]-->\n<!--[if (gte IE 9)|!(IE)]><!--> <html class=\"no-js\"> <!--<![endif]-->\n<head>\n  <meta charset=\"utf-8\">\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">\n  {exp:seo_lite url_title=\"{embed:url_title}\" default_title=\"{embed:title}\" default_keywords=\"{embed:keywords}\" default_description=\"{embed:description}\" category_title=\"{embed:category_title}\"}  \n  <meta name=\"viewport\" content=\"width=960\">\n  <link rel=\"shortcut icon\" href=\"/favicon.ico\">\n  <link rel=\"apple-touch-icon\" href=\"/apple-touch-icon.png\">\n  <link rel=\"stylesheet\" href=\"{site_url}css/style.css?v=2\">\n	<link rel=\"stylesheet/less\" href=\"{site_url}css/nlba.less\" media=\"all\">\n	<script src=\"{site_url}js/less-1.0.41.min.js\"></script>\n  <script src=\"{site_url}js/libs/modernizr-1.6.min.js\"></script>\n</head>\n<body class=\"print\">\n{exp:channel:entries channel=\"news|course|pages\" limit=\"1\" entry_id=\"{segment_3}\"}\n	<h1>{title}</h1>\n	{{segment_2}-body}\n	\n	{exp:eei_tcpdf:params cache=\"y\"	language=\"eng\"	title=\"{title}\"	author=\"National Library Board Academy\" font-family=\"arialunicid0\" keywords=\"nlba\"}\n	\n{/exp:channel:entries}\n</body>\n</html>',NULL,1301996008,1,'n',0,'','n','n','o',0),
	(31,1,13,'index','y','webpage','{embed=\"inc/header\" title=\"Search\"}\n\n<div id=\"LeftContent\">\n	\n	{embed=\"inc/print-icon\"}\n	\n	<ul id=\"Crumbs\">\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{site_url}\" itemprop=\"url\"><span itemprop=\"title\">Home</span></a></li>\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{path={segment_1}}\" itemprop=\"url\"><span itemprop=\"title\">Search</span></a></li>\n	</ul>\n\n	<h1>Search</h1>\n		\n	\n	{exp:search:advanced_form channel=\"news|course|trainers|about\" no_result_page=\"search/noresults\" results_page=\"search/results\" results=\"10\" where=\"all\" status=\"not Closed\" search_in=\"everywhere\" show_expired=\"yes\" show_future_entries=\"yes\" form_id=\"SearchForm\"} \n		<fieldset>\n			<label for=\"Advkeywords\">Search by Keyword</label>\n			<input type=\"text\" class=\"input\" maxlength=\"100\" size=\"40\" name=\"keywords\" id=\"Advkeywords\" /> \n			<select name=\"where\" id=\"Where\" size=\"4\"> \n				<option value=\"exact\" selected=\"selected\">{lang:exact_phrase_match}</option> \n				<option value=\"any\">{lang:search_any_words}</option> \n				<option value=\"all\" >{lang:search_all_words}</option> \n				<option value=\"word\" >{lang:search_exact_word}</option> \n				</select>\n		</fieldset>\n		<fieldset>\n			<select id=\"channel_id\" name=\'channel_id[]\' class=\'multiselect\' onchange=\'changemenu(this.selectedIndex);\' size=\"4\">\n				<option selected=\"selected\" value=\"null\">Everywhere</option>\n				<option value=\"4\">Courses</option>\n				<option value=\"1\">News</option>\n				<option value=\"3\">Trainer Profile</option>\n			</select> \n			<select name=\'cat_id[]\' class=\'multiselect\' size=\"6\"> <option value=\'all\' selected=\"selected\">{lang:any_category}</option> </select> \n		</fieldset>\n		<fieldset class=\"forms\">\n			<input type=\"submit\" value=\"Search\" class=\"submit\">\n		</fieldset>\n	{/exp:search:advanced_form}\n\n\n</div>\n\n<aside id=\"Sidebar\">\n</aside>\n\n{embed=\"inc/footer\"}',NULL,1301996008,1,'n',0,'','n','n','o',0),
	(32,1,13,'noresults','y','webpage','{embed=\"inc/header\" title=\"Search results for {exp:search:keywords}\"}\n\n<div id=\"LeftContent\">\n	\n	{embed=\"inc/print-icon\"}\n	\n	<!-- Breadcrumbs -->\n	<ul id=\"Crumbs\">\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{site_url}\" itemprop=\"url\"><span itemprop=\"title\">Home</span></a></li>\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\" class=\"active\"><a href=\"{path=search}\" itemprop=\"url\"><span itemprop=\"title\">Search</span></a></li>\n	</ul>\n	<!-- / Breadcrumbs -->\n\n\n	<h1>Search results for {exp:search:keywords}</h1>\n\n	<p>Your search did not return any results. Try <a href=\"{path=search}\">Advanced Search</a></p>\n\n</div>\n\n<aside id=\"Sidebar\">\n</aside>\n\n\n{embed=\"inc/footer\"}',NULL,1301996008,1,'n',0,'','n','n','o',0),
	(33,1,13,'results','y','webpage','{embed=\"inc/header\" title=\"Search results for {exp:search:keywords}\"}\n\n<div id=\"LeftContent\">\n	\n	{embed=\"inc/print-icon\"}\n	\n	<!-- Breadcrumbs -->\n	<ul id=\"Crumbs\">\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{site_url}\" itemprop=\"url\"><span itemprop=\"title\">Home</span></a></li>\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\" class=\"active\"><a href=\"{path=search}\" itemprop=\"url\"><span itemprop=\"title\">Search</span></a></li>\n	</ul>\n	<!-- / Breadcrumbs -->\n\n\n	<h1>Search results for {exp:search:keywords}</h1>\n\n	<ol class=\"searchresults\">		\n	\n	{exp:search:search_results}\n	\n	<li>\n		{if channel_short_name==\"course\"}\n			<h2><a href=\"{path=/}{exp:nlbaformat:getcaturl entry_id=\"{entry_id}\"} {/exp:nlbaformat:getcaturl}view/{url_title}\">{title}</a></h2>\n			<p>{exp:eehive_hacksaw words=\"40\" append=\"...\" allow=\"<br />\"}{course-summary}{/exp:eehive_hacksaw}</p>\n		{/if}\n		{if channel_short_name==\"trainers\"}\n			<h2><a href=\"{title_permalink=about/trainers}\">{title}</a></h2>\n			<p>{exp:eehive_hacksaw words=\"40\" append=\"...\" allow=\"<br />\"}{trainer-bio}{/exp:eehive_hacksaw}</p>\n		{/if}\n		\n		{if channel_short_name==\"about\"}\n			<h2><a href=\"{title_permalink=about}\">{title}</a></h2>\n			<p>{exp:eehive_hacksaw words=\"40\" append=\"...\" allow=\"<br />\"}{pages-body}{/exp:eehive_hacksaw}</p>			\n		{/if}\n		\n	</li>\n\n	{/exp:search:search_results}\n	</ol>\n\n	</table>\n\n	\n	{if paginate}\n\n	<div class=\'paginate\'>\n	<span class=\'pagecount\'>{page_count}</span>&nbsp; {paginate}\n	</div>\n\n	{/if}\n\n</div>\n\n<aside id=\"Sidebar\">\n</aside>\n\n\n{embed=\"inc/footer\"}',NULL,1301996008,1,'n',0,'','n','n','o',0),
	(34,1,14,'index','y','webpage','{preload_replace:channel=\"about\"}\n{embed=\"inc/header\" title=\"Sitemap\"}\n\n<div id=\"LeftContent\">\n	\n	{embed=\"inc/print-icon\"}\n	\n	<!-- Breadcrumbs -->\n	<ul id=\"Crumbs\">\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{site_url}\" itemprop=\"url\"><span itemprop=\"title\">Home</span></a></li>\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{path=sitemap}\" itemprop=\"url\"><span itemprop=\"title\">Sitemap</span></a></li>\n	</ul>\n	<!-- / Breadcrumbs -->\n\n\n	<h1>Sitemap</h1>\n	\n	<ol class=\"sitemap\">\n		<li><h2><a href=\"{path=library-professionals}\">Library Professionals</a></h2>\n			<ol>\n				{exp:query sql=\"SELECT DISTINCT A.title, A.url_title, A.entry_id as target_entry_id FROM exp_channel_titles A, exp_category_posts B where channel_id=4 and A.entry_id=B.entry_id and B.cat_id=20 and A.entry_id NOT IN (SELECT C.entry_id from exp_category_posts C WHERE cat_id IN (29)) ORDER BY A.title ASC\"}\n					<li><a href=\"{path=library-professionals/view}/{url_title}\">{title}</a></li>\n				{/exp:query}\n				<li><ol>\n				{exp:query sql=\"SELECT cat_id, cat_name, cat_url_title, cat_description, cat_image FROM exp_categories WHERE parent_id =20\"}\n					<li><h3><a href=\"{path=library-professionals/{cat_url_title}}\">{cat_name}</a></h3>\n						\n				{/exp:query}\n					<ol>\n					{exp:channel:entries channel=\"course\" disable=\"{global-disable}\" dynamic=\"off\" category=\'29\' status=\"not Closed\"}\n						<li><a href=\"{title_permalink=library-professionals/seminars/view}\">{title}</a></li>\n					{/exp:channel:entries}\n					</ol>\n					</li>	\n				</ol></li>\n			</ol>\n		</li>\n		\n		<li class=\"cat\"><h2><a href=\"{path=students}\">Students</a></h2>\n				<ol>\n					{exp:query sql=\"SELECT cat_id, cat_name from exp_categories where parent_id=21\"}	\n					<li><h3>{cat_name}</h3>\n						{embed=\"sitemap/sitemap-course\" cat_id=\"{cat_id}\"}\n					</li>\n					{/exp:query}\n				</ol>\n		</li>\n		\n		<li class=\"cat\"><h2><a href=\"{path=adults-educators}\">Adults &amp; Educators</a></h2>\n				<ol>\n					{exp:query sql=\"SELECT cat_id, cat_name from exp_categories where parent_id=25\"}	\n					<li><h3>{cat_name}</h3>\n						{embed=\"sitemap/sitemap-course\" cat_id=\"{cat_id}\"}\n					</li>\n					{/exp:query}\n				</ol>\n		</li>\n		<li><h2><a href=\"{path=about}\">About NLBA</a></h2>\n			<ol>\n				{exp:query sql=\"SELECT title, url_title, entry_id FROM exp_channel_titles WHERE channel_id=9 and entry_id!=41 ORDER BY title ASC\"}\n					<li {if segment_2==url_title}class=\"active\"{/if}><a href=\"{path=about}/{url_title}\">{title}</a></li>\n				{/exp:query}\n				<li><a href=\"{path=about/clients}\">Clients</a></li>\n				<li {if segment_2==\"trainers\"}class=\"active\"{/if}><a href=\"{path=about/trainers}\">Trainers</a></li>\n			</ol>\n		</li>\n	</ol>\n	\n	\n\n</div>\n\n<aside id=\"Sidebar\">\n	\n	\n	\n	{testimonial-module}\n	\n</aside>\n\n\n\n{embed=\"inc/footer\"}',NULL,1301996008,1,'n',0,'','n','n','o',0),
	(35,1,14,'sitemap-course','y','webpage','{exp:channel:entries channel=\"course\" disable=\"{global-disable}\" category=\"{embed:cat_id}\" dynamic=\"off\" status=\"not Closed\" orderby=\"title\" sort=\"asc\"}\n{if count==1}<ol>{/if}\n<li><a href=\"{title_permalink=students/view}\">{title}</a></li>\n{if count==total_results}</ol>{/if}\n{/exp:channel:entries}',NULL,1301996008,1,'n',0,'','n','n','o',0),
	(36,1,15,'index','y','webpage','{exp:channel:entries channel=\"pages\" disable=\"{global-disable}\" limit=\"1\"}\n{embed=\"inc/header\" title=\"{title}\"}\n\n<div id=\"LeftContent\">\n	\n	{embed=\"inc/print-icon\"}\n	\n	<!-- Breadcrumbs -->\n	<ul id=\"Crumbs\">\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{site_url}\" itemprop=\"url\"><span itemprop=\"title\">Home</span></a></li>\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\" class=\"active\"><a href=\"{path={segment_1}}\" itemprop=\"url\"><span itemprop=\"title\">{title}</span></a></li>\n	</ul>\n	<!-- / Breadcrumbs -->\n\n\n	<h1>{title}</h1>\n\n	{pages-body}		\n	\n	{if entry_id==\"25\"}\n	\n	{exp:email:contact_form user_recipients=\"false\" recipients=\"admin@example.com\" charset=\"utf-8\" form_class=\"forms\"} \n	<fieldset>\n	<p><label for=\"from\">Your Email:</label>\n			<input type=\"text\" id=\"from\" name=\"from\" size=\"40\" maxlength=\"35\" value=\"{member_email}\" />\n	</p> \n	<p>\n		<label for=\"subject\">Subject:</label> \n		<input type=\"text\" id=\"subject\" name=\"subject\" size=\"40\" value=\"Contact Form\" />\n	</p> \n	<p>\n		<label for=\"message\">Message:</label>\n		<textarea id=\"message\" name=\"message\" rows=\"12\" cols=\"40\"></textarea>\n	</p> \n	<p>\n		<input name=\"submit\" type=\'submit\' class=\"submit orange\" value=\'Send\' />\n	</p> \n	</fieldset>\n	{/exp:email:contact_form}\n\n		\n	{/if}\n\n</div>\n\n<aside id=\"Sidebar\">\n	{if entry_id==\"25\"}\n	<div id=\"GMap\">\n		\n	</div>\n		<script type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=false\"></script>			\n		<script type=\"text/javascript\">\n			var myLatlng = new google.maps.LatLng(1.333674,103.850445),\n	    myOptions = {\n	      zoom: 16,\n	      center: myLatlng,\n	      mapTypeId: google.maps.MapTypeId.ROADMAP\n	    },\n	    map = new google.maps.Map(document.getElementById(\"GMap\"), myOptions),\n			contentString = \'<div id=\"content\">\'+\n			        \'<p><strong>National Library Board Academy</strong></p>\'+\n							\'<p>Toa Payoh Public Library, Level 3<br>6 Toa Payoh Central<br>Singapore 319191</p>\'+\n							\'<p>Tel: +65 6354 5131 / 5123 / 5060 <br>Email: <a href=\"mailto:nlbacademy@nlb.gov.sg\">nlbacademy@nlb.gov.sg</a></p>\'+\n			        \'</div>\',\n\n		 infowindow = new google.maps.InfoWindow({\n		        content: contentString\n		    }),\n\n	   marker = new google.maps.Marker({\n	       position: myLatlng, \n	       map: map,\n	       title:\"National Library Board Academy\",\n				icon:\'/images/site/marker-library.png\'\n	   });\n\n		google.maps.event.addListener(marker, \'click\', function() {\n		      infowindow.open(map,marker);\n		});\n\n\n		</script>\n		\n	{/if}\n</aside>\n\n{/exp:channel:entries}\n\n{embed=\"inc/footer\"}',NULL,1301996008,1,'n',0,'','n','n','o',0),
	(37,1,15,'why-choose-nlba','y','webpage','{exp:channel:entries channel=\"pages\" disable=\"{global-disable}\" limit=\"1\"}\n{embed=\"inc/header\" title=\"{title}\"}\n\n<div id=\"LeftContent\">\n	\n	{embed=\"inc/print-icon\"}\n	\n	<!-- Breadcrumbs -->\n	<ul id=\"Crumbs\">\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\"><a href=\"{site_url}\" itemprop=\"url\"><span itemprop=\"title\">Home</span></a></li>\n		<li><a href=\"{path={segment_1}}\" itemtype=\"http://data-vocabulary.org/Breadcrumb\" itemprop=\"url\">{segment_1_category_name}</a></li>		\n		<li itemtype=\"http://data-vocabulary.org/Breadcrumb\" class=\"active\"><a href=\"{page_url}\" itemprop=\"url\"><span itemprop=\"title\">Why Choose NLBA</span></a></li>\n	</ul>\n	<!-- / Breadcrumbs -->\n\n\n	<h1>Why Choose NLBA</h1>\n\n	{pages-body}			\n\n</div>\n\n<aside id=\"Sidebar\">\n	{embed=\"inc/sidebar\"}\n</aside>\n\n{/exp:channel:entries}\n\n{embed=\"inc/footer\"}',NULL,1301996349,1,'n',0,'','n','n','o',0);

/*!40000 ALTER TABLE `exp_templates` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exp_throttle
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_throttle`;

CREATE TABLE `exp_throttle` (
  `throttle_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL,
  `locked_out` char(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`throttle_id`),
  KEY `ip_address` (`ip_address`),
  KEY `last_activity` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_upload_no_access
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_upload_no_access`;

CREATE TABLE `exp_upload_no_access` (
  `upload_id` int(6) unsigned NOT NULL,
  `upload_loc` varchar(3) NOT NULL,
  `member_group` smallint(4) unsigned NOT NULL,
  PRIMARY KEY (`upload_id`,`member_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table exp_upload_prefs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exp_upload_prefs`;

CREATE TABLE `exp_upload_prefs` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(4) unsigned NOT NULL DEFAULT '1',
  `name` varchar(50) NOT NULL,
  `server_path` varchar(150) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL,
  `allowed_types` varchar(3) NOT NULL DEFAULT 'img',
  `max_size` varchar(16) DEFAULT NULL,
  `max_height` varchar(6) DEFAULT NULL,
  `max_width` varchar(6) DEFAULT NULL,
  `properties` varchar(120) DEFAULT NULL,
  `pre_format` varchar(120) DEFAULT NULL,
  `post_format` varchar(120) DEFAULT NULL,
  `file_properties` varchar(120) DEFAULT NULL,
  `file_pre_format` varchar(120) DEFAULT NULL,
  `file_post_format` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `exp_upload_prefs` WRITE;
/*!40000 ALTER TABLE `exp_upload_prefs` DISABLE KEYS */;
INSERT INTO `exp_upload_prefs` (`id`,`site_id`,`name`,`server_path`,`url`,`allowed_types`,`max_size`,`max_height`,`max_width`,`properties`,`pre_format`,`post_format`,`file_properties`,`file_pre_format`,`file_post_format`)
VALUES
	(1,1,'Projects','/Applications/MAMP/htdocs/adi/uploads/projects/','http://adi.vinay:8888/uploads/projects/','img','','','','style=\"border: 0;\" alt=\"image\"','','','','',''),
	(2,1,'Profile','/Applications/MAMP/htdocs/adi/uploads/profile/','http://adi.vinay:8888/uploads/profile/','img','','','','style=\"border: 0;\" alt=\"image\"','','','','','');

/*!40000 ALTER TABLE `exp_upload_prefs` ENABLE KEYS */;
UNLOCK TABLES;





/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
