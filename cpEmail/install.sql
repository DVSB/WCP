CREATE TABLE cp1_1_mail_virtual (
	mailID int(10) NOT NULL auto_increment,
	userID int(10) NOT NULL default '0',
	emailaddress varchar(255) NOT NULL default '',
	emailaddress_full varchar(255) NOT NULL default '',
	destination text NOT NULL,
	domainID int(10) NOT NULL default '0',
	accountID int(10) NOT NULL default '0',
	isCatchall tinyint(1) unsigned NOT NULL default '0',
	enabled enum('N','Y') NOT NULL default 'Y',
	PRIMARY KEY  (mailID),
	KEY accountID (accountID),
	UNIQUE KEY emailaddress (emailaddress)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE cp1_1_mail_account (
	accountID int(10) NOT NULL auto_increment,
	emailaddress varchar(255) NOT NULL default '',
	username varchar(255) NOT NULL default '',
	password varchar(128) NOT NULL default '',
	password_enc varchar(128) NOT NULL default '',
	uid int(11) NOT NULL default '0',
	gid int(11) NOT NULL default '0',
	homeDir varchar(255) NOT NULL default '',
	mailDir varchar(255) NOT NULL default '',
	postfix enum('Y','N') NOT NULL default 'Y',
	domainID int(10) NOT NULL default '0',
	quota bigint(13) NOT NULL default '0',
	pop3 tinyint(1) NOT NULL default '1',
	imap tinyint(1) NOT NULL default '1',
	PRIMARY KEY  (accountID),
	UNIQUE KEY email (emailaddress)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;