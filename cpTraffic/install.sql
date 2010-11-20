CREATE TABLE cp1_1_traffic (
	trafficID int(11) unsigned NOT NULL auto_increment,
	userID int(11) NOT NULL default 0,
	countDate int(11) NOT NULL default 0,
	countBytes bigint(30) NOT NULL default 0,
	countType enum('http', 'ftp_up', 'ftp_down', 'mail') NOT NULL default 'http',
	PRIMARY KEY (trafficID),
	KEY userID (userID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE cp1_1_mail_traffic (
	userID int(11) NOT NULL default 0,
	domain varchar(255) NOT NULL default '',
	countDate int(11) NOT NULL default 0,
	bytes bigint(30) unsigned NOT NULL default 0,
	source varchar(4) default NULL,
	KEY userID (userID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;