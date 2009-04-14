<?php
require_once (WCF_DIR . 'lib/data/DatabaseObjectList.class.php');
require_once (CP_DIR . 'lib/data/mysql/MySQL.class.php');

/**
 * Handels a list of ftp accounts
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.mysql
 * @subpackage	data.mysql
 * @category 	Control Panel
 */
class MySQLList extends DatabaseObjectList
{
	public $mysqls = array ();

	/**
	 * @see DatabaseObjectList::countObjects()
	 */
	public function countObjects()
	{
		$sql = "SELECT	COUNT(*) AS count
				FROM	cp" . CP_N . "_mysql
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
		if (strpos($this->sqlOrderBy, 'mysqlname') !== false)
		{
			list(, $sortOrder) = explode(' ', $this->sqlOrderBy);
			$this->sqlOrderBy = "SUBSTRING_INDEX(mysqls.mysqlname, '" . MYSQL_POSTFIX . "', 1) " . $sortOrder . ", SUBSTRING_INDEX(mysqls.mysqlname, '" . MYSQL_POSTFIX . "', -1) + 0 " . $sortOrder;
		}

		$sql = "SELECT		" . (!empty($this->sqlSelects) ? $this->sqlSelects . ',' : '') . "
							mysqls.*
				FROM		cp" . CP_N . "_mysql mysqls
				" . $this->sqlJoins . "
				" . (!empty($this->sqlConditions) ? "WHERE " . $this->sqlConditions : '') . "
				" . (!empty($this->sqlOrderBy) ? "ORDER BY " . $this->sqlOrderBy : '');
		$result = WCF :: getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$this->mysqls[] = new MySQL(null, $row);
		}
	}

	/**
	 * @see DatabaseObjectList::getObjects()
	 */
	public function getObjects()
	{
		return $this->mysqls;
	}
}
?>