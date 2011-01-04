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
	 * @param	string		$ipAddress
	 * @param	integer		$port
	 * @param	string		$vhostType
	 * @param	array		$additionalFields
	 *
	 * @return 	VhostContainerEditor
	 */
	public static function create($vhostName, $ipAddress, $port, $vhostType, $additionalFields = array())
	{
		// insert main data
		$vhostContainerID = self :: insert($vhostName, $ipAddress, $port, $vhostType, $additionalFields);

		$vhostContainer = new VhostContainerEditor($vhostContainerID);

		return $vhostContainer;
	}

	/**
	 * Inserts the main vhostContainer data into the vhostContainer table.
	 *
	 * @param 	string 		$vhostName
	 * @param	string		$ipAddress
	 * @param	int			$port
	 * @param	string		$vhostType
	 * @param	array		$additionalFields
	 *
	 * @return 	integer		new domainID
	 */
	public static function insert($vhostName, $ipAddress, $port, $vhostType, $additionalFields = array())
	{
		$additionalColumnNames = $additionalColumnValues = '';

		foreach ($additionalFields as $key => $value)
		{
			$additionalColumnNames .= ', ' . $key;
			$additionalColumnValues .= ', ' . ((is_int($value)) ? $value : "'" . escapeString($value) . "'");
		}

		$sql = "INSERT INTO	cp" . CP_N . "_vhostContainer
						(vhostName, ipAddress, port, vhostType
						" . $additionalColumnNames . ")
				VALUES	('" . escapeString($vhostName) . "',
						 '" . escapeString($ipAddress) . "',
						 " . intval($port) . ",
						 '" . escapeString($vhostType) . "'
						" . $additionalColumnValues . ")";
		WCF :: getDB()->sendQuery($sql);
		return WCF :: getDB()->getInsertID();
	}

	/**
	 * Updates this vhostContainer.
	 *
	 * @param	string		$vhostName
	 * @param	string		$ipAddress
	 * @param	int			$port
	 * @param	string		$vhostType
	 * @param	array 		$additionalFields
	 */
	public function update($vhostName, $ipAddress, $port, $vhostType, $additionalFields = array())
	{
		$this->updateVhostContainer($vhostName, $ipAddress, $port, $vhostType, $additionalFields);
	}

	/**
	 * Updates additional vhostContainer fields.
	 *
	 * @param	array 	$additionalFields
	 */
	public function updateFields($additionalFields)
	{
		$this->updateVhostContainer('', '', 0, '', $additionalFields);
	}

	/**
	 * Updates the static data of this vhostContainer.
	 *
 	 * @param 	string 		$vhostName
 	 * @param	string		$ipAddress
	 * @param	int			$port
	 * @param	string		$vhostType
	 * @param	array		$additionalFields
	 */
	protected function updateVhostContainer($vhostName = '', $ipAddress = '', $port = 0, $vhostType = '', $additionalFields = array())
	{
		$updateSQL = '';
		if (!empty($vhostName))
		{
			if (!empty($updateSQL))
				$updateSQL .= ',';
			$updateSQL .= "vhostName = '" . escapeString($vhostName) . "'";
			$this->vhostName = $vhostName;
		}

		if (!empty($ipAddress))
		{
			if (!empty($updateSQL))
				$updateSQL .= ',';
			$updateSQL .= "ipAddress = '" . escapeString($ipAddress) . "'";
			$this->ipAddress = $ipAddress;
		}

		if ($port > 0)
		{
			if (!empty($updateSQL))
				$updateSQL .= ',';
			$updateSQL .= "port = " . intval($port);
			$this->port = $port;
		}

		if (!empty($vhostType))
		{
			if (!empty($updateSQL))
				$updateSQL .= ',';
			$updateSQL .= "vhostType = '" . escapeString($vhostType) . "'";
			$this->vhostType = $vhostType;
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
		if (count($vhostContainerIDs) == 0)
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