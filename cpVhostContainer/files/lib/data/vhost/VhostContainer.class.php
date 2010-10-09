<?php
// wcf imports
if (!defined('NO_IMPORTS'))
{
	require_once (WCF_DIR . 'lib/data/DatabaseObject.class.php');
}

/**
 * VhostContainer class defines all functions to "get" the information (data) of a vhost. It is a reading class only.
 *
 * This class provides all necessary functions to "read" all possible vhostdata. 
 * This includes required data and optional data. To set this vhostdata read 
 * the documentation of VhostContainerEditor.class.php which extends VhostContainer
 * 
 * @author		Tobias Friebel
 * @copyright	2010 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.vhost
 * @subpackage	data.vhost
 * @category 	Control Panel
 * @id			$Id$
 */
class VhostContainer extends DatabaseObject
{
	/**
	 * Gets VHostContainer for given ID
	 *
	 * @param 	string 		$vhostContainerID
	 * @param 	array 		$row
	 */
	public function __construct($vhostContainerID, $row = null)
	{
		if (!is_null($vhostContainerID))
		{
			$sql = "SELECT 		*
					FROM 		cp" . CP_N . "_vhostContainer
					WHERE 		vhostContainerID = " . intval($vhostContainerID);
			$row = WCF :: getDB()->getFirstRow($sql);
		}
		
		// handle result set
		parent :: __construct($row);
	}

	/**
	 * Returns a VhostContainerEditor object to edit this vhostContainer.
	 * 
	 * @return	VhostContainerEditor
	 */
	public function getEditor()
	{
		require_once (CP_DIR . 'lib/data/domain/VhostContainerEditor.class.php');
		return new VhostContainerEditor($this->vhostContainerID);
	}
}
?>