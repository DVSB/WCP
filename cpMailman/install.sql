CREATE TABLE cp1_1_modules_mailman (
  id int(11) unsigned NOT NULL auto_increment,
  userID int(11) NOT NULL default '0',
  domainid int(11) unsigned NOT NULL default '0',
  listname varchar(20) NOT NULL default '',
  domain varchar(36) NOT NULL default '',
  owner varchar(80) NOT NULL default '',
  list_address varchar(255) NOT NULL default '',
  description varchar(150) NOT NULL default '',
  password varchar(20) NOT NULL default '',
  flag tinyint(1) NOT NULL default '0',
  created datetime NOT NULL,
  transport varchar(30) NOT NULL default 'mailman:',
  PRIMARY KEY  (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;