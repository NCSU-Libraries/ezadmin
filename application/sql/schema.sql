SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

USE < dbname >;

--
-- Database: `ezproxy`
--

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `id`           INT(11)                     NOT NULL AUTO_INCREMENT
    PRIMARY KEY,
  `resource`     INT(11)                     NOT NULL,
  `type`         INT(11)                     NOT NULL,
  `config_value` TEXT                        NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

CREATE INDEX resource
  ON config (resource);

CREATE INDEX config_type_fk
  ON config (type);

--
-- Table structure for table `config_type`
--

CREATE TABLE IF NOT EXISTS `config_type`
(
  id   INT AUTO_INCREMENT
    PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  CONSTRAINT name
  UNIQUE (name)
)
  ENGINE = InnoDB
  CHARSET = latin1;

ALTER TABLE config
  ADD CONSTRAINT config_type_fk
FOREIGN KEY (type) REFERENCES config_type (id);

--
-- Table structure for table `resource`
--

CREATE TABLE IF NOT EXISTS `resource` (
  `id`                  INT(11)         NOT NULL AUTO_INCREMENT
    PRIMARY KEY,
  `title`               VARCHAR(255)    NOT NULL,
  `custom_config`       TEXT            NULL,
  `type`                INT             NOT NULL,
  `use_custom`          ENUM ('T', 'F') NOT NULL DEFAULT 'F',
  `restricted`          ENUM ('T', 'F') NOT NULL DEFAULT 'F',
  `note`                TEXT            NULL,
  `last_edit_date`      TIMESTAMP       NULL ON UPDATE CURRENT_TIMESTAMP,
  `last_edited_by_user` VARCHAR(255)    NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

CREATE INDEX resource_type_fk
  ON resource (type);

ALTER TABLE `config`
  ADD CONSTRAINT `config_ibfk_1` FOREIGN KEY (`resource`) REFERENCES `resource` (`id`);

--
-- Table structure for table `resource_type`
--
CREATE TABLE IF NOT EXISTS `resource_type`
(
  id   INT AUTO_INCREMENT
    PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  CONSTRAINT name
  UNIQUE (name)
)
  ENGINE = InnoDB
  CHARSET = latin1;

ALTER TABLE resource
  ADD CONSTRAINT resource_type_fk
FOREIGN KEY (type) REFERENCES resource_type (id);

--
-- Table structure for table `auth`
--  
CREATE TABLE IF NOT EXISTS `auth` (
  `user` CHAR(8) NOT NULL,
  PRIMARY KEY (`user`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1;