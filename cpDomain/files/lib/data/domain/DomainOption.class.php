<?php
// wcf imports
require_once (WCF_DIR . 'lib/data/DatabaseObject.class.php');

/**
 * Represents a DomainOption
 *
 * @author			Tobias Friebel
 * @copyright		2009 Tobias Friebel
 * @license			GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package			com.toby.cp.domain
 * @subpackage		data.domains
 * @category		ControlPanel
 */
class DomainOption extends DatabaseObject
{

	/**
	 * Creates a new DomainOption object.
	 * 
	 * @param	integer		$optionID
	 * @param	array		$row
	 */
	public function __construct($optionID, $row = null)
	{
		if ($optionID !== null)
		{
			$sql = "SELECT	*
					FROM	cp" . CP_N . "_domain_option
					WHERE	optionID = " . $optionID;
			$row = WCF :: getDB()->getFirstRow($sql);
		}
		
		parent :: __construct($row);
	}
}
?>