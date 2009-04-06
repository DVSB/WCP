<?php
require_once (WCF_DIR . 'lib/data/DatabaseObjectList.class.php');
require_once (CP_DIR . 'lib/data/ftp/FTPUser.class.php');

class FTPUserList extends DatabaseObjectList
{
	public $ftpUsers = array ();

	/**
	 * @see DatabaseObjectList::countObjects()
	 */
	public function countObjects()
	{
		$sql = "SELECT	COUNT(*) AS count
				FROM	cp" . CP_N . "_ftp_users
			" . (!empty($this->sqlConditions) ? "WHERE " . $this->sqlConditions : '');
		$row = WCF :: getDB()->getFirstRow($sql);
		return $row['count'];
	}

	/**
	 * @see DatabaseObjectList::readObjects()
	 */
	public function readObjects()
	{
		$sql = "SELECT		" . (!empty($this->sqlSelects) ? $this->sqlSelects . ',' : '') . "
							ftp_users.*, user_table.username,
				FROM		cp" . CP_N . "_ftp_users ftp_users
				LEFT JOIN	cp" . CP_N . "_user user_table
					ON		(user_table.userID = ftp_users.userID)
				" . $this->sqlJoins . "
				" . (!empty($this->sqlConditions) ? "WHERE " . $this->sqlConditions : '') . "
				" . (!empty($this->sqlOrderBy) ? "ORDER BY " . $this->sqlOrderBy : '');
		$result = WCF :: getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF :: getDB()->fetchArray())
		{
			$this->ftpUsers[] = new FTPUsers(null, $row);
		}
	}

	/**
	 * @see DatabaseObjectList::getObjects()
	 */
	public function getObjects()
	{
		return $this->ftpUsers;
	}
}
?>