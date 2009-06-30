<?php
/*
 * Copyright (c) 2009 Tobias Friebel
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

require_once (CP_DIR . 'lib/data/domains/Domain.class.php');
require_once (CP_DIR . 'lib/data/user/CPUser.class.php');

/**
 * Creates/manipulates one ftp account
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	data.domains
 * @category 	Control Panel
 */
class DomainEditor extends Domain
{
	/**
	 * create a new domain
	 *
	 * @param int $userID
	 * @param string $username
	 * @param string $password
	 * @param string $homedir
	 * @param string $description
	 * @param int $undeletable
	 * @param boolean $addPostFix
	 *
	 * @return object
	 */
	public static function create($userID, $domainname, $documentroot, $description = '')
	{
		$documentroot = FileUtil :: addTrailingSlash(FileUtil :: getRealPath($documentroot));

		$sql = "INSERT INTO	cp" . CP_N . "_domains
						(userID, username, uid, gid,
						 password, homedir, undeleteable,
						 description, loginEnabled)
				VALUES
						(" . $userID . ", '" . escapeString($username) . "', " . intval($user->guid) . ", " . intval($user->guid) . ",
						 ENCRYPT('" . escapeString($password) . "'), '" . escapeString($homedir) . "', " . intval($undeletable) . ",
						 '" . escapeString($description) . "', 'Y')";
		WCF :: getDB()->sendQuery($sql);

		$domainID = WCF :: getDB()->getInsertID('cp' . CP_N . '_domains', 'domainID');
		
		$user->getEditor()->updateOptions(array('subdomainsUsed' => ++$user->subdomainsUsed));

		return new DomainEditor($domainID);
	}

	/**
	 * update domain
	 *
	 * @param string $password
	 */
	public function update($password, $homedir, $description)
	{
		// Update
		$sql = "UPDATE	cp" . CP_N . "_domains
				SET		password = ENCRYPT('" . escapeString($password) . "'),
						homedir = '" . escapeString($homedir) . "',
						description = '" . escapeString($description) . "'
				WHERE 	domainID = " . $this->domainID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * delete domain
	 */
	public function delete()
	{
		$sql = "DELETE FROM	cp" . CP_N . "_domains
				WHERE		domainID = " . $this->domainID;
		WCF :: getDB()->sendQuery($sql);

		require_once (WCF_DIR . '/lib/data/user/UserEditor.class.php');
		$user = new UserEditor($this->userID);
		$user->updateOptions(array('subdomainsUsed' => --$user->subdomainsUsed));
	}

	/**
	 * delete all domains for this user
	 */
	public static function deleteAll($userID)
	{
		$sql = "DELETE FROM	cp" . CP_N . "_domains
				WHERE		userID = " . $userID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * enable domain
	 */
	public function enable()
	{
		$sql = "UPDATE	cp" . CP_N . "_domains
				SET		deactivated = 0
				WHERE	domainID = " . $this->domainID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * disable domain
	 */
	public function disable()
	{
		$sql = "UPDATE	cp" . CP_N . "_domains
				SET		deactivated = 1
				WHERE	domainID = " . $this->domainID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * enable ftp account
	 */
	public static function enableAll($userID)
	{
		$sql = "UPDATE	cp" . CP_N . "_domains
				SET		deactivated = 0
				WHERE	userID = " . $userID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * disable ftp account
	 */
	public static function disableAll($userID)
	{
		$sql = "UPDATE	cp" . CP_N . "_domains
				SET		deactivated = 1
				WHERE	userID = " . $userID;
		WCF :: getDB()->sendQuery($sql);
	}
}

?>