CREATE TABLE IF NOT EXISTS `cot_userman` (
	`user_id` INT(12),
	`start_date` DATE,
	`stop_date` DATE,
	`groups_access` VARCHAR(256) NOT NULL,
	`start_reason` VARCHAR(256) NOT NULL,
	`stop_reason` VARCHAR(256) NOT NULL,
	`groups_default` VARCHAR(256) NOT NULL,
	`active` tinyint(1) unsigned NOT NULL default '0',
	PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
