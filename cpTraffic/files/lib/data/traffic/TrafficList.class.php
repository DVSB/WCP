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
class TrafficList extends DatabaseObjectList
{
	public $traffic = array ();

	/**
	 * @see DatabaseObjectList::countObjects()
	 */
	public function countObjects()
	{
		$sql = "SELECT	COUNT(*) AS count
				FROM	cp" . CP_N . "_traffic
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
							.*
				FROM		cp" . CP_N . "_traffic traffic
				" . $this->sqlJoins . "
				" . (!empty($this->sqlConditions) ? "WHERE " . $this->sqlConditions : '') . "
				" . (!empty($this->sqlOrderBy) ? "ORDER BY " . $this->sqlOrderBy : '');
		$result = WCF :: getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$this->traffic[] = new FTPUser(null, $row);
		}
	}

	/**
	 * @see DatabaseObjectList::getObjects()
	 */
	public function getObjects()
	{
		return $this->traffic;
	}
}
?>