<?php
require_once (WCF_DIR . 'lib/data/DatabaseObjectList.class.php');
require_once (CP_DIR . 'lib/data/domain/Domain.class.php');

/**
 * Handels a list of domains
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.ftp
 * @subpackage	data.ftp
 * @category 	Control Panel
 * @id			$Id$
 */
class DomainList extends DatabaseObjectList
{
	public $domains = array ();

	/**
	 * @see DatabaseObjectList::countObjects()
	 */
	public function countObjects()
	{
		$sql = "SELECT	COUNT(*) AS count
				FROM	cp" . CP_N . "_domain
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
							domain.*, domain_option.*, user.username AS username, admin.username AS adminname
				FROM		cp" . CP_N . "_domain domain
				LEFT JOIN 	cp" . CP_N . "_domain_option_value domain_option 
							ON (domain_option.domainID = domain.domainID)
				JOIN		wcf" . WCF_N . "_user user
							ON (domain.userID = user.userID)
				JOIN		wcf" . WCF_N . "_user admin
							ON (domain.adminID = admin.userID)
				" . $this->sqlJoins . "
				" . (!empty($this->sqlConditions) ? "WHERE " . $this->sqlConditions : '') . "
				" . (!empty($this->sqlOrderBy) ? "ORDER BY " . $this->sqlOrderBy : '');
		
		$result = WCF :: getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$this->domains[] = new Domain(null, $row);
		}
	}

	/**
	 * @see DatabaseObjectList::getObjects()
	 */
	public function getObjects()
	{
		return $this->domains;
	}
}
?>