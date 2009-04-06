<?php
/*
 * Copyright (c) 2009 Tobias Friebel
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

require_once (WCF_DIR . 'lib/data/DatabaseObject.class.php');

class FTPUser extends DatabaseObject
{
	public function __construct($ftpUserID, $row = null)
	{
		if ($ftpUserID !== null)
		{
			$sql = "SELECT	*
					FROM	cp" . CP_N . "_ftp_users ftp_users
					JOIN	cp" . CP_N . "_ftp_groups ftp_groups
						ON	(ftp_users.userID = ftp_groups.userID)
					WHERE	ftpUserID = " . $ftpUserID;
			$row = WCF :: getDB()->getFirstRow($sql);
		}

		parent :: __construct($row);
	}
}
?>