ALTER TABLE wcf1_page_menu_item ADD parentMenuItem VARCHAR( 255 ) NOT NULL DEFAULT '';
ALTER TABLE wcf1_page_menu_item ADD groupIDs TEXT NULL;
ALTER TABLE wcf1_page_menu_item ADD native TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '1'; 
