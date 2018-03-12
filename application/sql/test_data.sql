USE < dbname >;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

--
-- Dumping data for table `resource`
--

-- --------------------------------------------------------
/*!40000 ALTER TABLE `resource` DISABLE KEYS */;
INSERT INTO `resource` (`id`, `title`, `custom_config`, `type`, `use_custom`, `restricted`, `note`) VALUES
  (555, 'Some Institution of Washington- Yearbook', '', '1', 'F', 'T', 'Reviewed 2016'),
  (556, 'International Atomic Soda Agency', '', '1', 'F', 'F', 'negotiated 2018 with a hope for peace'),
  (557, 'Dusty Volume of Chemistry and Physics', '', '2', 'F', 'F', 'found horcrux, will follow up with destruction'),
  (558, 'Table of Contents Index', '', '2', 'F', 'F', 'surpised this is listed'),
  (559, 'ASDFGHJKL Project', 'SOME CUSTOM CONFIG', '2', 'T', 'F', 'spoke with faculty finance');
/*!40000 ALTER TABLE `resource` ENABLE KEYS */;
--
-- Dumping data for table `config`
--

-- --------------------------------------------------------
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` (`id`, `resource`, `type`, `config_value`) VALUES
  (111, 555, '1', 'someinstitution.org'),
  (112, 555, '1', 'someinstitution2.org'),
  (113, 556, '1', 'pepsi.com'),
  (114, 557, '4', 'hogwartsMuggleDept.com'),
  (115, 558, '4', 'tableindex.org'),
  (116, 558, '4', 'kickbacks.com');
/*!40000 ALTER TABLE `config` ENABLE KEYS */;

-- --------------------------------------------------------

--
-- Dumping data for table `auth`
--
/*!40000 ALTER TABLE `auth` DISABLE KEYS */;
INSERT INTO `auth` (`user`) VALUES
  ('jthurtea'),
  ('ejlynema'),
  ('jcraitz'),
  ('jpsample');
/*!40000 ALTER TABLE `auth` ENABLE KEYS */;

--
-- Dump Data for table config_type
--
/*!40000 ALTER TABLE `config_type` DISABLE KEYS */;
INSERT INTO `config_type` (`id`, `name`) VALUES
  (3, 'D'),
  (4, 'DJ'),
  (1, 'H'),
  (2, 'HJ');
/*!40000 ALTER TABLE `config_type` ENABLE KEYS */;

--
-- Dump Data for table resource_type
--
/*!40000 ALTER TABLE `resource_type` DISABLE KEYS */;
INSERT INTO `resource_type` (`id`, `name`) VALUES
  (4, 'Aggregator'),
  (2, 'Database'),
  (5, 'Ebook'),
  (1, 'Journal'),
  (3, 'Platform');
/*!40000 ALTER TABLE `resource_type` ENABLE KEYS */;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;