CREATE TABLE `cp1_1_databases` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `userID` int(11) NOT NULL default '0',
  `databasename` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;