CREATE TABLE cp1_1_ftp_groups (
  ftpGroupID int(20) NOT NULL auto_increment PRIMARY KEY,
  userID int(11) NOT NULL default '0',
  groupname varchar(60) NOT NULL default '',
  gid int(5) NOT NULL default '0',
  members longtext NOT NULL,
  UNIQUE KEY groupname (groupname),
  KEY userID (userID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE cp1_1_ftp_users (
  ftpUserID int(20) NOT NULL auto_increment PRIMARY KEY,
  userID int(11) NOT NULL default '0',
  username varchar(255) NOT NULL default '',
  uid int(5) NOT NULL default '0',
  gid int(5) NOT NULL default '0',
  password varchar(20) NOT NULL default '',
  homedir varchar(255) NOT NULL default '',
  shell varchar(255) NOT NULL default '/bin/false',
  loginEnabled enum('N','Y') NOT NULL default 'N',
  loginCount int(15) NOT NULL default '0',
  lastLogin datetime NOT NULL default '0000-00-00 00:00:00',
  upcount int(15) NOT NULL default '0',
  upbytes bigint(30) NOT NULL default '0',
  downcount int(15) NOT NULL default '0',
  downbytes bigint(30) NOT NULL default '0',
  undeleteable tinyint(1) NOT NULL default '0',
  description text NOT NULL default '',
  UNIQUE KEY username (username),
  KEY userID (userID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;