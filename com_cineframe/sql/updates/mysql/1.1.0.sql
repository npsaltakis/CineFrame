CREATE TABLE IF NOT EXISTS `#__cineframe_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `ordering` INT NOT NULL DEFAULT 0,
  `published` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `#__cineframe_videos`
  ADD COLUMN `catid` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `title`,
  ADD COLUMN `thumb` VARCHAR(500) NOT NULL DEFAULT '' AFTER `source`,
  ADD COLUMN `description` TEXT NULL DEFAULT NULL AFTER `thumb`;
