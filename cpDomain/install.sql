CREATE TABLE cp1_1_domains (
	--- defaultfelder
  	domainID int(10) unsigned NOT NULL auto_increment,
  	userID int(10) NOT NULL default '0',
  	domainname varchar(255) NOT NULL default '',
	adminID int(10) unsigned NOT NULL default '0',
	addDate int(11) NOT NULL default '0',
	registrationDate date NOT NULL,
	canEditDomain tinyint(1) NOT NULL default '1',
	parentDomainID int(10) unsigned NOT NULL default '0',
	deactivated tinyint(1) NOT NULL default '0',

	--- webfelder
	ipAndPort int(10) unsigned NOT NULL default '1',
	isWildcardDomain tinyint(1) NOT NULL default '0',
	wwwServerAlias tinyint(1) NOT NULL default '1',	
	specialSettings text NOT NULL,
	aliasDomain int(10) unsigned default NULL,
	redirectDomain int(10) unsigned default NULL,
	documentroot varchar(255) NOT NULL default '',
	noWebDomain tinyint(1) NOT NULL default '0',

	--- emailfelder
	isEmailDomain tinyint(1) NOT NULL default '0',
	subCanEmailDomain tinyint(1) NOT NULL default '0',
	
	--- phpfelder
	openbasedir tinyint(1) NOT NULL default '0',
	openbasedirPath tinyint(1) NOT NULL default '0',
	
	PRIMARY KEY  (domainID),
	KEY userID (userID),
	KEY parentdomain (parentdomainID),
	KEY domain (domainname)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;