<?php
require_once (WCF_DIR . 'lib/data/DatabaseObject.class.php');
require_once (CP_DIR . 'lib/data/user/CPUser.class.php');

/**
 * Represents one ftp account
 *
 * @author		Tobias Friebel
 * @copyright	2010 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.traffic
 * @subpackage	data.traffic
 * @category 	Control Panel
 */
class Traffic extends DatabaseObject
{
	public function __construct($trafficID, $row = null)
	{
		if ($trafficID !== null)
		{
			$sql = "SELECT	traffic.*
					FROM	cp" . CP_N . "_traffic traffic
					WHERE	trafficID = " . intval($trafficID);
			$row = WCF :: getDB()->getFirstRow($sql);
		}

		parent :: __construct($row);
	}
}
?>