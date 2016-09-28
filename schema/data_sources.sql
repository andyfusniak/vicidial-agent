CREATE TABLE IF NOT EXISTS `data_sources` (
  `data_source_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) CHARACTER SET utf8 NOT NULL,
  `source_count_total` INT(10) UNSIGNED NULL DEFAULT NULL,
  `source_last_record` INT(10) UNSIGNED NULL DEFAULT NULL,
  `cursor` INT(10) UNSIGNED NULL DEFAULT NULL,
  `sync_success_total` INT(10) UNSIGNED NULL DEFAULT NULL,
  `sync_error_total` INT(10) UNSIGNED NULL DEFAULT NULL,
  `last_sync` TIMESTAMP NULL DEFAULT NULL,
  `created` TIMESTAMP NULL DEFAULT NULL,
  `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`data_source_id`),
  UNIQUE KEY `name` (`name`),
  KEY `created` (`created`),
  KEY `last_sync` (`last_sync`),
  KEY `modified` (`modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
