<?php
// wcf imports
if (!defined('NO_IMPORTS'))
{
	require_once (WCF_DIR . 'lib/data/DatabaseObject.class.php');
}

/**
 * Domain class defines all functions to "get" the information (data) of a domain. It is a reading class only.
 *
 * This class provides all necessary functions to "read" all possible domaindata. 
 * This includes required data and optional data. To set this domaindata read 
 * the documentation of DomainEditor.class.php which extends Domain.class.php
 * 
 * @author		Tobias Friebel
 * @copyright	2010 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	data.domain
 * @category 	Control Panel
 */
class Domain extends DatabaseObject
{
	protected static $domainOptions = null;

	/**
	 * Gets the main data of the passed user (id, name or whole datablock) 
	 * and pass it over to the "protected function initUser()".
	 * You can also create an emtpy user object e.g. to search for users.
	 *
	 * @param 	string 		$userID
	 * @param 	array 		$row
	 * @param 	string 		$username
	 * @param 	string 		$email
	 */
	public function __construct($domainID, $row = null)
	{
		// execute sql statement
		$sqlCondition = '';
		if ($domainID !== null)
		{
			$sqlCondition = "domain.domainID = " . $domainID;
		}
		
		if (!empty($sqlCondition))
		{
			$sql = "SELECT 		domain.*, domain_option.*,
					FROM 		cp" . CP_N . "_domains domain
					LEFT JOIN 	cp" . CP_N . "_domain_option_value domain_option 
							ON (domain_option.domainID = domain.domainID)
					WHERE 	" . $sqlCondition;
			$row = WCF :: getDB()->getFirstRow($sql);
		}
		
		// handle result set
		parent :: __construct($row);
	}

	/**
	 * Returns a DomainEditor object to edit this user.
	 * 
	 * @return	DomainEditor
	 */
	public function getEditor()
	{
		require_once (CP_DIR . 'lib/data/domain/DomainEditor.class.php');
		return new DomainEditor($this->domainID);
	}

	/**
	 * Returns the value of the domain option with the given name.
	 * 
	 * @param	string		$name		domain option name
	 * @return	mixed					domain option value
	 */
	public function getDomainOption($name)
	{
		$optionID = self :: getDomainOptionID($name);
		
		if ($optionID === null)
		{
			return null;
		}
		
		if (!isset($this->data['domainOption' . $optionID]))
			return null;
			
		return $this->data['domainOption' . $optionID];
	}

	/**
	 * @see DatabaseObject::__get()
	 */
	public function __get($name)
	{
		$value = parent :: __get($name);
		
		if ($value === null)
			$value = $this->getDomainOption($name);
		
		return $value;
	}

	/**
	 * Gets all domain options from cache.
	 */
	protected static function getDomainOptionCache()
	{
		$cacheName = 'domain-option';
		CPCore :: getCache()->addResource($cacheName, CP_DIR . 'cache/cache.' . $cacheName . '.php', CP_DIR . 'lib/system/cache/CacheBuilderOption.class.php');
		self :: $domainOptions = CPCore :: getCache()->get($cacheName, 'options');
	}

	/**
	 * Returns the id of a domain option.
	 * 
	 * @param	string		$name
	 * @return	integer		id
	 */
	public static function getDomainOptionID($name)
	{
		// get user option cache if necessary
		if (self :: $domainOptions === null)
		{
			self :: getDomainOptionCache();
		}
		
		if (!isset(self :: $domainOptions[$name]))
		{
			return null;
		}
		
		return self :: $domainOptions[$name]['optionID'];
	}
}
?>