CREATE TABLE IF NOT EXISTS `data_sync` (
  `data_sync_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `source_id` INT(10) UNSIGNED NOT NULL,
  `id` VARCHAR(32) NOT NULL,
  `status` VARCHAR(32) NOT NULL DEFAULT 'success',
  `last_sync` timestamp NULL DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`data_sync_id`),
  UNIQUE KEY `source_id_composite_key` (`source_id`, `id`),
  KEY `source_id` (`source_id`),
  KEY `id` (`id`),
  KEY `status` (`status`),
  KEY `last_sync` (`last_sync`),
  KEY `created` (`created`),
  CONSTRAINT `source_id_fk`
    FOREIGN KEY (`source_id`)
    REFERENCES `data_sources` (`data_source_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
