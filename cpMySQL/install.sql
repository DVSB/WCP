CREATE TABLE cp1_1_mysql (
  mysqlID int(11) unsigned NOT NULL auto_increment,
  userID int(11) NOT NULL default '0',
  mysqlname varchar(255) NOT NULL default '',
  description varchar(255) NOT NULL default '',
  PRIMARY KEY  (databaseID),
  KEY userID (userID)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;