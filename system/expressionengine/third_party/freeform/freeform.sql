CREATE TABLE IF NOT EXISTS `exp_freeform_attachments` (
	`attachment_id`		int(10) unsigned 	NOT NULL auto_increment,
	`entry_id` 			int(10) unsigned 	NOT NULL,
	`pref_id` 			int(10) unsigned 	NOT NULL,
	`entry_date`		int(10) 			NOT NULL,
	`server_path` 		varchar(150) 		NOT NULL,
	`filename` 			varchar(150) 		NOT NULL,
	`extension`			varchar(7) 			NOT NULL,
	`filesize` 			int(10) 			NOT NULL,
	`emailed` 			char(1) 			NOT NULL default 'n',
	PRIMARY KEY 		(`attachment_id`),
	KEY 				(`entry_id`),
	KEY 				(`pref_id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci ;;


CREATE TABLE IF NOT EXISTS `exp_freeform_entries` (
	`entry_id`    		int(10) unsigned 	NOT NULL auto_increment,
	`group_id`    		int(10) unsigned 	NOT NULL default '0',
	`weblog_id` 		int(4) unsigned 	NOT NULL,
	`author_id` 		int(10) unsigned 	NOT NULL default '0',
	`ip_address`		varchar(16) 		NOT NULL default '0',
	`form_name`			varchar(50) 		NOT NULL,
	`template`    		varchar(150) 		NOT NULL,
	`entry_date`  		int(10) 			NOT NULL,
	`edit_date` 		int(10) 			NOT NULL,
	`status`	  		char(10) 			NOT NULL default 'open',
	`name`      		varchar(50) 		NOT NULL,
	`email`      		varchar(50) 		NOT NULL,
	`website`     		varchar(50) 		NOT NULL,
	`street1`     		varchar(50) 		NOT NULL,
	`street2`     		varchar(50) 		NOT NULL,
	`street3`     		varchar(50) 		NOT NULL,
	`city`      		varchar(50) 		NOT NULL,
	`state`      		varchar(50) 		NOT NULL,
	`country`     		varchar(50) 		NOT NULL,
	`postalcode`		varchar(50) 		NOT NULL,
	`phone1`	  		varchar(50) 		NOT NULL,
	`phone2`	  		varchar(50) 		NOT NULL,
	`fax`	      		varchar(50) 		NOT NULL,
	PRIMARY KEY 		(`entry_id`),
	KEY 				(`author_id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci ;;


CREATE TABLE IF NOT EXISTS `exp_freeform_fields`(
	`field_id`    		int(10) unsigned 	NOT NULL auto_increment,
	`field_order`   	int(10) 			NOT NULL default '0',
	`field_type`		varchar(50) 		NOT NULL default 'text',
	`field_length`    	int(3) 				NOT NULL default '150',
	`form_name`    		varchar(50) 		NOT NULL,
	`name`    			varchar(50) 		NOT NULL,
	`name_old`    		varchar(50) 		NOT NULL,
	`label`    			varchar(100) 		NOT NULL,
	`weblog_id`    		int(4) unsigned 	NOT NULL,
	`author_id`    		int(10) unsigned 	NOT NULL default '0',
	`entry_date`		int(10) 			NOT NULL,
	`edit_date`    		int(10) 			NOT NULL,
	`editable`    		char(1) 			NOT NULL default 'y',
	`status`			char(10) 			NOT NULL default 'open',
	PRIMARY KEY 		(`field_id`),
	KEY 				(`name`),
	KEY 				(`author_id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci ;;

CREATE TABLE IF NOT EXISTS `exp_freeform_params` (
	`params_id` 		int(10) unsigned 	NOT NULL auto_increment,
	`entry_date` 		int(10) 			NOT NULL,
	`data` 				text 				NOT NULL,
	PRIMARY KEY 		(`params_id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci ;;

CREATE TABLE IF NOT EXISTS `exp_freeform_preferences` (
	`preference_id` 	int(10) unsigned 	NOT NULL AUTO_INCREMENT,
	`preference_name` 	varchar(80) 		NOT NULL,
	`preference_value` 	text 				NOT NULL,
	PRIMARY KEY 		(`preference_id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;;

CREATE TABLE IF NOT EXISTS `exp_freeform_user_email` (
	`email_id`			int(10) unsigned 	NOT NULL AUTO_INCREMENT,
	`entry_id`			int(10) unsigned 	NOT NULL,
	`email_count`		int(10) unsigned 	NOT NULL default '0',
	PRIMARY KEY 		(`email_id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;;

CREATE TABLE IF NOT EXISTS `exp_freeform_templates` (
	`template_id` 		int(6) unsigned 	NOT NULL auto_increment,
	`enable_template` 	char(1) 			NOT NULL default 'y',
	`wordwrap` 			char(1) 			NOT NULL default 'y',
	`html` 				char(1) 			NOT NULL default 'n',
	`template_name` 	varchar(150) 		NOT NULL,
	`template_label` 	varchar(150) 		NOT NULL,
	`data_from_name` 	varchar(150) 		NOT NULL,
	`data_from_email` 	varchar(200) 		NOT NULL,
	`data_title` 		varchar(80) 		NOT NULL,
	`template_data` 	text 				NOT NULL,
	PRIMARY KEY 		(`template_id`),
	KEY 				(`template_name`)
) CHARACTER SET utf8 COLLATE utf8_general_ci ;;
