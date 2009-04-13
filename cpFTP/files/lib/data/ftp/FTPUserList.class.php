<?php
require_once (WCF_DIR . 'lib/data/DatabaseObjectList.class.php');
require_once (CP_DIR . 'lib/data/ftp/FTPUser.class.php');

/**
 * Handels a list of ftp accounts
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.ftp
 * @subpackage	data.ftp
 * @category 	Control Panel
 */
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
		//naturally order by username
		if (strpos($this->sqlOrderBy, 'username') !== false)
		{
			list(, $sortOrder) = explode(' ', $this->sqlOrderBy);
			$this->sqlOrderBy = "SUBSTRING_INDEX(ftp_users.username, '" . FTP_POSTFIX . "', 1) " . $sortOrder . ", SUBSTRING_INDEX(ftp_users.username, '" . FTP_POSTFIX . "', -1) + 0 " . $sortOrder;
		}

		$sql = "SELECT		" . (!empty($this->sqlSelects) ? $this->sqlSelects . ',' : '') . "
							ftp_users.*
				FROM		cp" . CP_N . "_ftp_users ftp_users
				" . $this->sqlJoins . "
				" . (!empty($this->sqlConditions) ? "WHERE " . $this->sqlConditions : '') . "
				" . (!empty($this->sqlOrderBy) ? "ORDER BY " . $this->sqlOrderBy : '');
		$result = WCF :: getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$this->ftpUsers[] = new FTPUser(null, $row);
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