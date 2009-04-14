<?php
/*
 * Copyright (c) 2009 Tobias Friebel
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

require_once (CP_DIR . 'lib/data/mysql/MySQL.class.php');
require_once (CP_DIR . 'lib/data/user/CPUser.class.php');

/**
 * Creates/manipulates one ftp account
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.mysql
 * @subpackage	data.mysql
 * @category 	Control Panel
 */
class MySQLEditor extends MySQL
{
	/**
	 * holds an db-instance with root-access
	 *
	 * @var db-object
	 */
	protected static $rootDB;

	/**
	 * constructor
	 *
	 * @param integer $mysqlID
	 * @param array $row
	 */
	public function __construct($mysqlID, $row = null)
	{
		parent :: __construct($row);

		self :: getRootDB();
	}

	/**
	 * create a new ftp account
	 *
	 * @param int $userID
	 * @param string $dbname
	 * @param string $password
	 * @param string $description
	 * @param boolean $addPostFix
	 *
	 * @return object
	 */
	public static function create($userID, $dbname, $password, $description = '', $addPostFix = true)
	{
		self :: getRootDB();

		$user = new CPUser($userID);

		if ($addPostFix)
		{
			$sql = "SELECT mysqlname AS name
					FROM cp" . CP_N . "_mysql
					WHERE userID = " . intval($userID) . "
					ORDER BY SUBSTRING_INDEX(mysqlname, '" . MYSQL_POSTFIX . "', -1) + 0 DESC
					LIMIT 1";
			$postFix = WCF :: getDB()->getFirstRow($sql);

			if (empty($postFix))
			{
				$dbname .= MYSQL_POSTFIX . '1';
			}
			else
			{
				$postFix = intval(str_replace($dbname . MYSQL_POSTFIX, '', $postFix['name']));
				$dbname .= MYSQL_POSTFIX . ++$postFix;
			}
		}

		$sql = "INSERT INTO	cp" . CP_N . "_mysql
						(userID, mysqlname, description)
				VALUES
						(" . $userID . ", '" . escapeString($dbname) . "', '" . escapeString($description) . "')";
		WCF :: getDB()->sendQuery($sql);

		$mysqlID = WCF :: getDB()->getInsertID('cp' . CP_N . '_mysql', 'mysqlID');

		$user->getEditor()->updateOptions(array (
			'mysqlsUsed' => ++$user->mysqlsUsed
		));

		self :: $rootDB->query("CREATE DATABASE `" . escapeString($dbname) . "`");

		self :: $rootDB->query("GRANT ALL PRIVILEGES ON `" . str_replace('_', '\_', escapeString($dbname)) . "`.* TO `" . escapeString($dbname) . "`@`" . MYSQL_ACCESS_HOST . "` IDENTIFIED BY 'password'");
		self :: $rootDB->query("SET PASSWORD FOR `" . escapeString($dbname) . "`@`" . MYSQL_ACCESS_HOST . "` = PASSWORD('" . escapeString($password) . "')");

		return new MySQLEditor($mysqlID);
	}

	/**
	 * change password of mysql-db
	 *
	 * @param string $password
	 * @param string $description
	 */
	public function update($password, $description)
	{
		// Update
		$sql = "UPDATE	cp" . CP_N . "_mysql
				SET		description = '" . escapeString($description) . "'
				WHERE 	mysqlID = " . $this->mysqlID;
		WCF :: getDB()->sendQuery($sql);

		self :: $rootDB->query("SET PASSWORD FOR `" . $this->mysqlname . "`@`" . MYSQL_ACCESS_HOST . "` = PASSWORD('" . escapeString($password) . "')");
	}

	/**
	 * get an db-instance with rootaccess
	 */
	public static function getRootDB()
	{
		if (self :: $rootDB)
			return;

		// get configuration
		$dbName = $dbCharset = $root_user = $root_password = '';
		require_once (CP_DIR . 'mysqlrootconfig.inc.php');
		require_once (WCF_DIR . 'config.inc.php');

		// create database connection
		require_once (WCF_DIR . 'lib/system/database/MySQLDatabase.class.php');
		self :: $rootDB = new MySQLDatabase(MYSQL_ACCESS_HOST, $root_user, $root_password, $dbName, $dbCharset);
	}

	/**
	 * delete an mysql db
	 */
	public function delete()
	{
		self :: $rootDB->query("REVOKE ALL PRIVILEGES ON * . * FROM `" . $this->mysqlname . "`@`" . MYSQL_ACCESS_HOST . "`");
		self :: $rootDB->query("REVOKE ALL PRIVILEGES ON `" . str_replace('_', '\_', $this->mysqlname) . "` . * FROM `" . $this->mysqlname . "`@`" . MYSQL_ACCESS_HOST . "`");
		self :: $rootDB->query("DELETE FROM mysql.user WHERE User = '" . $this->mysqlname . "' AND Host = '" . MYSQL_ACCESS_HOST . "'");

		self :: $rootDB->query("DROP DATABASE IF EXISTS `" . $this->mysqlname . "`");
		self :: $rootDB->query("FLUSH PRIVILEGES");

		$sql = "DELETE FROM	cp" . CP_N . "_mysql
				WHERE		mysqlID = " . $this->mysqlID;
		WCF :: getDB()->sendQuery($sql);

		require_once (WCF_DIR . '/lib/data/user/UserEditor.class.php');
		$user = new UserEditor($this->userID);
		$user->updateOptions(array (
			'mysqlsUsed' => --$user->mysqlsUsed
		));
	}

	/**
	 * delete all mysql dbs for this user
	 */
	public static function deleteAll($userID)
	{
		$sql = "SELECT 	mysqlID
				FROM 	cp" . CP_N . "_mysql
				WHERE 	userID = " . $userID;
		$result = WCF :: getDB()->getResultList($sql);

		foreach ($result as $id)
		{
			$obj = new MySQLEditor($id);
			$obj->delete();
		}
	}
}

?>