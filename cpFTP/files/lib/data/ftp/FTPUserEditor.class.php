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
require_once (CP_DIR . 'lib/data/user/CPUser.class.php');

/**
 * Creates/manipulates one ftp account
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.ftp
 * @subpackage	data.ftp
 * @category 	Control Panel
 */
class FTPUserEditor extends FTPUser
{
	/**
	 * create a new ftp account
	 *
	 * @param int $userID
	 * @param string $username
	 * @param string $password
	 * @param string $homedir
	 * @param int $undeletable
	 *
	 * @return object
	 */
	public static function create($userID, $username, $password, $homedir, $description = '', $undeletable = 0, $addPostFix = true)
	{
		$user = new CPUser($userID);

		$groupname = $username;

		if ($addPostFix)
		{
			$sql = "SELECT username AS name
					FROM cp" . CP_N . "_ftp_users
					WHERE userID = " . intval($userID) . "
					ORDER BY CONCAT(
						IF( ASCII( LEFT( username, 1 ) ) > 57, LEFT( username, 1 ), '0' ),
						IF( ASCII( RIGHT( username, 1 ) ) > 57, LPAD( username, 255, '0' ), LPAD( CONCAT( username, '-' ), 255, '0' ) )
						) DESC
					LIMIT 1";
			$postFix = WCF :: getDB()->getFirstRow($sql);

			if (empty($postFix))
			{
				$username .= FTP_POSTFIX . '1';
			}
			else
			{
				$postFix = intval(str_replace($username . FTP_POSTFIX, '', $postFix['name']));
				$username .= FTP_POSTFIX . ++$postFix;
			}
		}

		$homedir = FileUtil :: addTrailingSlash(FileUtil :: getRealPath($homedir));

		$sql = "INSERT INTO	cp" . CP_N . "_ftp_users
						(userID, username, uid, gid,
						 password, homedir, undeleteable,
						 description, loginEnabled)
				VALUES
						(" . $userID . ", '" . escapeString($username) . "', " . intval($user->guid) . ", " . intval($user->guid) . ",
						 ENCRYPT('" . escapeString($password) . "'), '" . escapeString($homedir) . "', " . intval($undeletable) . ",
						 '" . escapeString($description) . "', 'Y')";
		WCF :: getDB()->sendQuery($sql);

		$ftpUserID = WCF :: getDB()->getInsertID('cp' . CP_N . '_ftp_users', 'ftpUserID');

		$sql = "INSERT INTO cp" . CP_N . "_ftp_groups
						(userID, groupname, gid, members)
				VALUES
						(" . $userID . ", '" . escapeString($groupname) . "', " . intval($user->guid) . ", '" . escapeString($username) . "')
				ON DUPLICATE KEY UPDATE
						members = CONCAT_WS(',', members, '" . escapeString($username) . "')";
		WCF :: getDB()->sendQuery($sql);

		$user->getEditor()->updateOptions(array('ftpaccountsUsed' => ++$user->ftpaccountsUsed));

		return new FTPUserEditor($ftpUserID);
	}

	/**
	 * change password of ftp-account
	 *
	 * @param string $password
	 */
	public function update($password, $path, $description)
	{
		// Update
		$sql = "UPDATE	cp" . CP_N . "_ftp_users
				SET		password = ENCRYPT('" . escapeString($password) . "'),
						path = '" . escapeString($path) . "',
						description = '" . escapeString($description) . "'
				WHERE 	ftpUserID = " . $this->ftpUserID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * delete an ftp account
	 */
	public function delete()
	{
		$sql = "UPDATE 	cp" . CP_N . "_ftp_groups
				SET 	members = REPLACE(members, '," . $this->username . "', '')
				WHERE 	userID = " . $this->userID;
		WCF :: getDB()->sendQuery($sql);

		$sql = "DELETE FROM	cp" . CP_N . "_ftp_users
				WHERE		ftpUserID = " . $this->ftpUserID;
		WCF :: getDB()->sendQuery($sql);

		require_once (WCF_DIR . '/lib/data/user/UserEditor.class.php');
		$user = new UserEditor($this->userID);
		$user->updateOptions(array('ftpaccountsUsed' => --$user->ftpaccountsUsed));
	}

	/**
	 * delete all ftp accounts for this user
	 */
	public static function deleteAll($userID)
	{
		$sql = "DELETE 	FROM cp" . CP_N . "_ftp_groups
				WHERE 		 userID = " . $userID;
		WCF :: getDB()->sendQuery($sql);

		$sql = "DELETE FROM	cp" . CP_N . "_ftp_users
				WHERE		userID = " . $userID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * enable ftp account
	 */
	public function enable()
	{
		$sql = "UPDATE	cp" . CP_N . "_ftp_users
				SET		loginEnabled = 'Y'
				WHERE	ftpUserID = " . $this->ftpUserID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * disable ftp account
	 */
	public function disable()
	{
		$sql = "UPDATE	cp" . CP_N . "_ftp_users
				SET		loginEnabled = 'N'
				WHERE	ftpUserID = " . $this->ftpUserID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * enable ftp account
	 */
	public static function enableAll($userID)
	{
		$sql = "UPDATE	cp" . CP_N . "_ftp_users
				SET		loginEnabled = 'Y'
				WHERE	userID = " . $userID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * disable ftp account
	 */
	public static function disableAll($userID)
	{
		$sql = "UPDATE	cp" . CP_N . "_ftp_users
				SET		loginEnabled = 'N'
				WHERE	userID = " . $userID;
		WCF :: getDB()->sendQuery($sql);
	}
}

?>