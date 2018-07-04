-- ----------------------------------------------------
--
-- Author: Michael Mifsud <info@tropotek.com>
--
-- ----------------------------------------------------



-- DROP TABLE IF EXISTS `mail_log`;
CREATE TABLE IF NOT EXISTS `mail_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `to` text,
  `from` text,
  `subject` text,
  `body` text,
  `hash` varchar(64) DEFAULT NULL,
  `notes` text,
  `del` TINYINT(1) NOT NULL DEFAULT 0,
  `created` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`hash`)
) ENGINE=InnoDB;







