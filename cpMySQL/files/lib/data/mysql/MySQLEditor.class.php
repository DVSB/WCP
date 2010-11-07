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
		parent :: __construct($mysqlID, $row);

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

		try
		{
			self :: $rootDB->sendQuery("CREATE DATABASE IF NOT EXISTS `" . escapeString($dbname) . "`");

			self :: $rootDB->sendQuery("GRANT ALL PRIVILEGES ON `" . str_replace('_', '\_', escapeString($dbname)) . "`.* TO `" . escapeString($dbname) . "`@`" . MYSQL_ACCESS_HOST . "` IDENTIFIED BY '" . escapeString($password) . "'");

			$sql = "INSERT INTO	cp" . CP_N . "_mysql
							(userID, mysqlname, description)
					VALUES
							(" . $userID . ", '" . escapeString($dbname) . "', '" . escapeString($description) . "')";
			WCF :: getDB()->sendQuery($sql);

			$mysqlID = WCF :: getDB()->getInsertID('cp' . CP_N . '_mysql', 'mysqlID');

			$user->getEditor()->updateOptions(array (
				'mysqlsUsed' => ++$user->mysqlsUsed
			));

			return new MySQLEditor($mysqlID);
		}
		catch (Exception $e)
		{
			throw new SystemException('Databasecreation failed: ', 0, $e->getMessage());
		}
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

		self :: $rootDB->sendQuery("SET PASSWORD FOR `" . $this->mysqlname . "`@`" . MYSQL_ACCESS_HOST . "` = PASSWORD('" . escapeString($password) . "')");
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
		require (CP_DIR . 'mysqlrootconfig.inc.php');
		require (WCF_DIR . 'config.inc.php');

		// create database connection
		require_once (WCF_DIR . 'lib/system/database/MySQLDatabase.class.php');
		self :: $rootDB = new MySQLDatabase(MYSQL_ACCESS_HOST, $root_user, $root_password, $dbName, $dbCharset);
	}

	/**
	 * delete an mysql db
	 */
	public function delete()
	{
		try
		{
			self :: $rootDB->sendQuery("DROP USER `" . $this->mysqlname . "`@`" . MYSQL_ACCESS_HOST . "`");
			self :: $rootDB->sendQuery("DROP DATABASE IF EXISTS `" . $this->mysqlname . "`");


			$sql = "DELETE FROM	cp" . CP_N . "_mysql
					WHERE		mysqlID = " . $this->mysqlID;
			WCF :: getDB()->sendQuery($sql);

			require_once (WCF_DIR . '/lib/data/user/UserEditor.class.php');
			$user = new UserEditor($this->userID);
			$user->updateOptions(array (
				'mysqlsUsed' => --$user->mysqlsUsed
			));
		}
		catch (Exception $e)
		{
			throw new SystemException('Databasedeletion failed: ', 0, $e->getMessage());
		}
	}

	/**
	 * delete all mysql dbs for this user
	 */
	public static function deleteAll($userID)
	{
		$sql = "SELECT 	mysqlID
				FROM 	cp" . CP_N . "_mysql
				WHERE 	userID = " . intval($userID);
		$result = WCF :: getDB()->getResultList($sql);

		foreach ($result as $id)
		{
			$obj = new MySQLEditor($id);
			$obj->delete();
		}
	}
}

?>