ALTER TABLE cp1_1_user 
ADD guid int(5) NOT NULL default '0',
ADD homeDir varchar(255) NOT NULL;

CREATE TABLE cp1_1_jobhandler_task (
	jobhandlerTaskID int(10) unsigned NOT NULL auto_increment,
	jobhandler varchar(20) NOT NULL,
	nextExec varchar(20) NOT NULL default 'asap',
	lastExec int(10) unsigned NOT NULL default 0,
	priority tinyint(1) unsigned NOT NULL default 0,
	volatile tinyint(1) unsigned NOT NULL default 1,
	userID int(10) unsigned NOT NULL default 0,
	data TEXT NOT NULL,
	packageID int(10) unsigned NOT NULL default 0,
	PRIMARY KEY (jobhandlerTaskID),
	KEY (jobhandler),
	KEY (packageID),
	UNIQUE (jobhandler, nextExec, userID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE cp1_1_jobhandler_task_log (
	jobhandlerTaskLogID int(10) unsigned NOT NULL auto_increment,
	execTimeStart int(10) unsigned NOT NULL default 0,
	execTimeEnd int(10) unsigned NOT NULL default 0,
	execJobhandler TEXT NOT NULL DEFAULT '',
	success tinyint(1) unsigned NOT NULL default 0,
	data TEXT NOT NULL,
	PRIMARY KEY (jobhandlerTaskLogID),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;