CREATE TABLE IF NOT EXISTS `resource_type` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO `resource_type` VALUES
(1, 'Journal'),
(2, 'Database'),
(3, 'Platform'),
(4, 'Aggregator'),
(5, 'Ebook');

ALTER TABLE `resource` ADD
	`type` INT( 11 ) AFTER `resource_type`;

UPDATE `resource` SET `type` = 1 WHERE `resource_type` = 'Journal';
UPDATE `resource` SET `type` = 2 WHERE `resource_type` = 'Database';
UPDATE `resource` SET `type` = 3 WHERE `resource_type` = 'Platform';
UPDATE `resource` SET `type` = 4 WHERE `resource_type` = 'Aggregator';
UPDATE `resource` SET `type` = 5 WHERE `resource_type` = 'Ebook';

ALTER TABLE `resource` MODIFY `type` INT(11) NOT NULL;

ALTER TABLE `resource`
  ADD CONSTRAINT `resource_type_fk` FOREIGN KEY (`type`) REFERENCES `resource_type` (`id`);

ALTER TABLE `resource` DROP `resource_type`;

CREATE TABLE IF NOT EXISTS `config_type` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO `config_type` VALUES
(1, 'H'),
(2, 'HJ'),
(3, 'D'),
(4, 'DJ');

ALTER TABLE `config` ADD
	`type` INT( 11 ) AFTER `config_type`;

UPDATE `config` SET `type` = 1 WHERE `config_type` = 'H';
UPDATE `config` SET `type` = 2 WHERE `config_type` = 'HJ';
UPDATE `config` SET `type` = 3 WHERE `config_type` = 'D';
UPDATE `config` SET `type` = 4 WHERE `config_type` = 'DJ';

ALTER TABLE `config` MODIFY `type` INT(11) NOT NULL;

ALTER TABLE `config`
  ADD CONSTRAINT `config_type_fk` FOREIGN KEY (`type`) REFERENCES `config_type` (`id`);

 ALTER TABLE `config` DROP `config_type`;