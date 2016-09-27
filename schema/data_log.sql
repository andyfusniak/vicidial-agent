CREATE TABLE IF NOT EXISTS `data_log` (
  `data_log_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `source_id` INT(10) UNSIGNED NOT NULL,
  `action` VARCHAR(32) NOT NULL,
  `status` VARCHAR(32) NOT NULL,
  `api_call` TEXT,
  `response` TEXT,
  `created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`data_log_id`),
  KEY `source_id` (`source_id`),
  KEY `action` (`action`),
  KEY `status` (`status`),
  KEY `created` (`created`),
  CONSTRAINT `source_id_fk2`
    FOREIGN KEY (`source_id`)
    REFERENCES `data_sources` (`data_source_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
