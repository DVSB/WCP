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
	
	PRIMARY KEY  (domainID),
	KEY userID (userID),
	KEY parentdomain (parentdomainID),
	KEY domain (domainname)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE cp1_1_domain_option (
	domainOptionID int(10) unsigned NOT NULL auto_increment,
	packageID int(10) unsigned NOT NULL default 0,
	optionName varchar(255) NOT NULL default '',
	optionType varchar(255) NOT NULL default '',
	optionValue mediumtext NULL,
	validationPattern text NULL,
	selectOptions mediumtext NULL,
	enableOptions mediumtext NULL,
	showOrder int(10) unsigned NOT NULL default 0,
	hidden tinyint(1) unsigned NOT NULL default 0,
	permissions TEXT NULL,
	options TEXT NULL,
	PRIMARY KEY (optionID),
	UNIQUE KEY (optionName, packageID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE cp1_1_domain_option_value (
	domainID int(10) unsigned NOT NULL default 0,
	PRIMARY KEY (userID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;