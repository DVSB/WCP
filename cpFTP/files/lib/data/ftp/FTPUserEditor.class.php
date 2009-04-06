<?php
/*
 * Copyright (c) 2009 Tobias Friebel
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

require_once (CP_DIR . 'lib/data/ftp/FTPUser.class.php');

class FTPUserEditor extends FTPUser
{
	public static function create($userID, $username, $password, $homedir, $undeletable = 0)
	{
		$user = new User($userID);

		$sql = "INSERT INTO	cp" . CP_N . "_ftp_users
						(userID, username, uid, gid, password, homedir, undeletable)
				VALUES
						(" . $userID . ", '" . escapeString($username) . "', " . intval($user->guid) . ", " . intval($user->guid) . ", ENCRYPT('" . escapeString($password) . "'), '" . escapeString($homedir) . "', " . intval($undeletable) . ")
				ON DUPLICATE KEY UPDATE
						password = ENCRYPT('" . escapeString($password) . "')";
		WCF :: getDB()->sendQuery($sql);

		$ftpUserID = WCF :: getDB()->getInsertID('cp' . CP_N . '_ftp_users', 'ftpUserID');

		$sql = "INSERT INTO cp" . CP_N . "_ftp_groups
						(userID, groupname, gid, members)
				VALUES
						(" . $userID . ", '" . escapeString($username) . "', " . $user->guid . ", '" . escapeString($username) . "')
				ON DUPLICATE KEY UPDATE
						members = CONCAT_WS(',', members, '" . escapeString($username) . "')";
		WCF :: getDB()->sendQuery($sql);

		return new FTPUserEditor($ftpUserID);
	}

	public function update($password)
	{
		// Update
		$sql = "UPDATE	cp" . CP_N . "_ftp_users
				SET		password = ENCRYPT('" . escapeString($password) . "')
				WHERE 	ftpUserID = " . $this->ftpUserID;
		WCF :: getDB()->sendQuery($sql);
	}

	public function delete()
	{
		if ($this->undeleteable != 0)
			return;

		$sql = "UPDATE 	cp" . CP_N . "_ftp_groups
				SET 	members = REPLACE(members, '," . $this->username . "', '')
				WHERE 	userID = " . $this->userID;
		WCF :: getDB()->sendQuery($sql);

		$sql = "DELETE FROM	cp" . CP_N . "_ftp_users
				WHERE		ftpUserID = " . $this->ftpUserID;
		WCF :: getDB()->sendQuery($sql);
	}
}

?>