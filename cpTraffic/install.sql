CREATE TABLE cp1_1_traffic (
  id int(11) unsigned NOT NULL auto_increment,
  userID int(11) NOT NULL default '0',
  year int(4) unsigned zerofill NOT NULL default '0000',
  month int(2) unsigned zerofill NOT NULL default '00',
  day int(2) unsigned zerofill NOT NULL default '00',
  stamp int(11) unsigned NOT NULL default '0',
  http bigint(30) unsigned NOT NULL default '0',
  ftp_up bigint(30) unsigned NOT NULL default '0',
  ftp_down bigint(30) unsigned NOT NULL default '0',
  mail bigint(30) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY userID (userID)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE cp1_1_mail_traffic (
  userID int(11) NOT NULL default '0',
  domain varchar(80) NOT NULL default '',
  bytes int(10) unsigned NOT NULL default '0',
  source varchar(4) default NULL,
  KEY userID (userID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;