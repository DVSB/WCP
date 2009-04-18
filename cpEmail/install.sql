CREATE TABLE `cp1_1_mail_traffic` (
  `userID` int(11) NOT NULL default '0',
  `domain` varchar(80) NOT NULL default '',
  `bytes` int(10) unsigned NOT NULL default '0',
  `source` varchar(4) default NULL,
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `cp1_1_mail_users` (
  `id` int(11) NOT NULL auto_increment,
  `userID` int(11) NOT NULL default '0',
  `email` varchar(255) NOT NULL default '',
  `username` varchar(255) NOT NULL default '',
  `password` varchar(128) NOT NULL default '',
  `password_enc` varchar(128) NOT NULL default '',
  `uid` int(11) NOT NULL default '0',
  `gid` int(11) NOT NULL default '0',
  `homedir` varchar(255) NOT NULL default '',
  `maildir` varchar(255) NOT NULL default '',
  `postfix` enum('Y','N') NOT NULL default 'Y',
  `domainid` int(11) NOT NULL default '0',
  `quota` bigint(13) NOT NULL default '0',
  `pop3` tinyint(1) NOT NULL default '1',
  `imap` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `cp1_1_mail_virtual` (
  `id` int(11) NOT NULL auto_increment,
  `userID` int(11) NOT NULL default '0',
  `email` varchar(255) NOT NULL default '',
  `email_full` varchar(255) NOT NULL default '',
  `destination` text NOT NULL,
  `domainid` int(11) NOT NULL default '0',
  `popaccountid` int(11) NOT NULL default '0',
  `iscatchall` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `cp1_1_modules_maildrop` (
  `spam` enum('Y','N') NOT NULL default 'N',
  `greylisting` enum('1','0') NOT NULL default '1',
  `email` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `cp1_1_modules_mailman` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `userID` int(11) NOT NULL default '0',
  `domainid` int(11) unsigned NOT NULL default '0',
  `listname` varchar(20) NOT NULL default '',
  `domain` varchar(36) NOT NULL default '',
  `owner` varchar(80) NOT NULL default '',
  `list_address` varchar(255) NOT NULL default '',
  `description` varchar(150) NOT NULL default '',
  `password` varchar(20) NOT NULL default '',
  `flag` tinyint(1) NOT NULL default '0',
  `created` datetime NOT NULL,
  `transport` varchar(30) NOT NULL default 'mailman:',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;