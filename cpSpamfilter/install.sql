CREATE TABLE cp1_1_modules_maildrop (
  spam enum('Y','N') NOT NULL default 'N',
  greylisting enum('1','0') NOT NULL default '1',
  email varchar(128) NOT NULL default '',
  PRIMARY KEY  (email)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;