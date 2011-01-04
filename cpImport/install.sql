CREATE TABLE cp1_1_import_mapping (
	idType varchar(75) NOT NULL DEFAULT '',
	oldID varchar(255) NOT NULL DEFAULT '',
	newID int(11) UNSIGNED NOT NULL DEFAULT 0,
	UNIQUE KEY (idType, oldID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE cp1_1_import_source (
	sourceName varchar(255) NOT NULL,
	packageID int(11) UNSIGNED NOT NULL DEFAULT 0,
	classPath varchar(255) NOT NULL,
	templateName varchar(255) NOT NULL DEFAULT '',
	UNIQUE KEY (sourceName)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;