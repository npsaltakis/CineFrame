CREATE TABLE IF NOT EXISTS `#__cineframe_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `ordering` INT NOT NULL DEFAULT 0,
  `published` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cineframe_videos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `catid` INT UNSIGNED NOT NULL DEFAULT 0,
  `type` VARCHAR(20) NOT NULL DEFAULT 'youtube',
  `source` MEDIUMTEXT NOT NULL,
  `thumb` VARCHAR(500) NOT NULL DEFAULT '',
  `description` TEXT NULL DEFAULT NULL,
  `width` INT NOT NULL DEFAULT 0,
  `published` TINYINT NOT NULL DEFAULT 1,
  `ordering` INT NOT NULL DEFAULT 0,
  `created` DATETIME NULL DEFAULT NULL,
  `created_by` INT UNSIGNED NOT NULL DEFAULT 0,
  `modified` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_published` (`published`),
  KEY `idx_catid` (`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
