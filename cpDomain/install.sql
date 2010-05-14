DROP TABLE IF EXISTS cp1_1_domain;
CREATE TABLE cp1_1_domain (	
  	domainID int(10) unsigned NOT NULL auto_increment,
  	userID int(10) NOT NULL default '0',
  	domainname varchar(255) NOT NULL default '',
	adminID int(10) unsigned NOT NULL default '0',
	addDate int(11) NOT NULL,
	registrationDate INT(11) NOT NULL,
	parentDomainID int(10) unsigned NOT NULL default '0',
	deactivated tinyint(1) NOT NULL default '0',
	PRIMARY KEY  (domainID),
	KEY userID (userID),
	KEY parentdomain (parentDomainID),
	KEY domain (domainname)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS cp1_1_domain_option_category;
CREATE TABLE IF NOT EXISTS cp1_1_domain_option_category (
	categoryID int(10) unsigned NOT NULL AUTO_INCREMENT,
	packageID int(10) unsigned NOT NULL DEFAULT '0',
	categoryName varchar(255) NOT NULL DEFAULT '',
	parentCategoryName varchar(255) NOT NULL DEFAULT '',
	showOrder int(10) unsigned NOT NULL DEFAULT '0',
	permissions text,
	options text,
	PRIMARY KEY (categoryID),
	UNIQUE KEY categoryName (categoryName,packageID)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS cp1_1_domain_option;
CREATE TABLE IF NOT EXISTS cp1_1_domain_option (
	optionID int(10) unsigned NOT NULL auto_increment,
	packageID int(10) unsigned NOT NULL default 0,
	optionName varchar(255) NOT NULL default '',
	categoryName varchar(255) NOT NULL default '',
	optionType varchar(255) NOT NULL default '',
	defaultValue mediumtext NULL,
	validationPattern text NULL,
	selectOptions mediumtext NULL,
	enableOptions mediumtext NULL,
	required tinyint(1) unsigned NOT NULL default 0,
	editable tinyint(1) NOT NULL default 0,
	hidden tinyint(1) unsigned NOT NULL default 0,
	disabled tinyint(1) unsigned NOT NULL default 0,
	showOrder int(10) unsigned NOT NULL DEFAULT '0',
	permissions TEXT NULL,
	options TEXT NULL,
	additionalData MEDIUMTEXT NULL,
	PRIMARY KEY (optionID),
	UNIQUE KEY (optionName, packageID),
	KEY (categoryName)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS cp1_1_domain_option_value;
CREATE TABLE cp1_1_domain_option_value (
	domainID int(10) unsigned NOT NULL default 0,
	PRIMARY KEY (domainID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;