CREATE TABLE cp1_1_vhostContainer (
	vhostContainerID int(10) unsigned NOT NULL auto_increment,
	vhostName varchar(50) NOT NULL default '',
	ipAddress varchar(39) NOT NULL default '',
	port int(5) NOT NULL default '80',
	vhostType varchar(20) NOT NULL default '',
	isContainer tinyint(1) NOT NULL default 1,
	isIPv6 tinyint(1) NOT NULL default 0,
	isSSL tinyint(1) NOT NULL default 0,
	useTemplate tinyint(1) NOT NULL default 0,
	availableForUser tinyint(1) NOT NULL default 0, -- Not Yet, maybe later
	addListenStatement tinyint(1) NOT NULL default 0,
	addNameStatement tinyint(1) NOT NULL default 0,
	addServerName tinyint(1) NOT NULL default 1,
	vhostTemplate text,
	vhostDescription text,
	sslCertFile varchar(255) NULL,
	sslCertKeyFile varchar(255) NULL,
	sslCertChainFile varchar(255) NULL,
	PRIMARY KEY  (vhostContainerID)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;