CREATE TABLE `cp1_1_ipsandports` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `ip` varchar(39) NOT NULL default '',
  `port` int(5) NOT NULL default '80',
  `listen_statement` tinyint(1) NOT NULL default '0',
  `namevirtualhost_statement` tinyint(1) NOT NULL default '0',
  `vhostcontainer` tinyint(1) NOT NULL default '0',
  `vhostcontainer_servername_statement` tinyint(1) NOT NULL default '0',
  `specialsettings` text NOT NULL,
  `ssl` tinyint(4) NOT NULL default '0',
  `ssl_cert` tinytext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;