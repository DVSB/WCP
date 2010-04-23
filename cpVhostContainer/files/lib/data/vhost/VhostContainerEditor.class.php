<?php
// wcf imports
if (!defined('NO_IMPORTS'))
{
	require_once (CP_DIR . 'lib/data/vhost/VhostContainer.class.php');
}

/**
 * VhostContainerEditor creates, edits or deletes VhostContainers.
 *
 * @author		Tobias Friebel
 * @copyright	2010 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.vhost
 * @subpackage	data.vhost
 * @category 	Control Panel
 * @id			$Id$
 */
class VhostContainerEditor extends VhostContainer
{
	/**
	 * Creates a new vhost with all required and filled out additional fields.
	 *
	 * @param 	string 		$vhostName
	 * @param	array		$additionalFields
	 * 
	 * @return 	VhostContainerEditor
	 */
	public static function create($vhostName, $additionalFields = array())
	{
		// insert main data
		$vhostContainerID = self :: insert($vhostName, $additionalFields);
		
		$vhostContainer = new VhostContainerEditor($vhostContainerID);
		
		return $vhostContainer;
	}

	/**
	 * Inserts the main vhostContainer data into the vhostContainer table. 
	 *
	 * @param 	string 		$vhostName
	 * @param	array		$additionalFields
	 * 
	 * @return 	integer		new domainID
	 */
	public static function insert($vhostName, $additionalFields = array())
	{
		$additionalColumnNames = $additionalColumnValues = '';
			
		foreach ($additionalFields as $key => $value)
		{
			$additionalColumnNames .= ', ' . $key;
			$additionalColumnValues .= ', ' . ((is_int($value)) ? $value : "'" . escapeString($value) . "'");
		}
		
		$sql = "INSERT INTO	cp" . CP_N . "_vhostContainer
						(vhostName
						" . $additionalColumnNames . ")
				VALUES	('" . escapeString($vhostName) . "'
						" . $additionalColumnValues . ")";
		WCF :: getDB()->sendQuery($sql);
		return WCF :: getDB()->getInsertID();
	}

	/**
	 * Updates this vhostContainer. 
	 * 
	 * @param	string		$vhostName
	 * @param	array 		$additionalFields
	 */
	public function update($vhostName, $additionalFields = array())
	{
		$this->updateVhostContainer($vhostName, $additionalFields);
	}

	/**
	 * Updates additional vhostContainer fields.
	 * 
	 * @param	array 	$additionalFields
	 */
	public function updateFields($additionalFields)
	{
		$this->updateVhostContainer('', $additionalFields);
	}

	/**
	 * Updates the static data of this vhostContainer.
	 *
 	 * @param 	string 		$vhostName
	 * @param	array		$additionalFields
	 */
	protected function updateVhostContainer($vhostName = '', $additionalFields = array())
	{
		$updateSQL = '';
		if (!empty($domainname))
		{
			$updateSQL = "vhostName = '" . escapeString($vhostName) . "'";
			$this->vhostName = $vhostName;
		}
		
		foreach ($additionalFields as $key => $value)
		{
			if (!empty($updateSQL))
				$updateSQL .= ',';
			$updateSQL .= $key . '=' . ((is_int($value)) ? $value : "'" . escapeString($value) . "'");
		}
		
		if (!empty($updateSQL))
		{
			// save user
			$sql = "UPDATE	cp" . CP_N . "_vhostContainer
					SET	" . $updateSQL . "
					WHERE 	vhostContainerID = " . $this->vhostContainerID;
			WCF :: getDB()->sendQuery($sql);
		}
	}

	/**
	 * Deletes vhostContainer.
	 * Returns the number of deleted Containers.
	 *
	 * @param	array		$vhostContainerIDs
	 * @return	integer
	 */
	public static function deleteVhostContainer($vhostContainerIDs)
	{
		if (count($vhostContainerID) == 0)
			return 0;
		
		$vhostContainerIDsStr = implode(',', $vhostContainerIDs);
		
		// delete options for this domain
		$sql = "DELETE 	FROM cp" . CP_N . "_vhostContainer
				WHERE 	vhostContainerID IN (" . $vhostContainerIDsStr . ")";
		WCF :: getDB()->sendQuery($sql);
		

		return count($vhostContainerIDs);
	}
	
	/**
	 * Deletes this domain
	 */
	public function delete()
	{
		// delete domain from domain table
		$sql = "DELETE 	FROM cp" . CP_N . "_vhostContainer
				WHERE 	vhostContainerID = " . $this->vhostContainerID;
		WCF :: getDB()->sendQuery($sql);
	}
}
?>