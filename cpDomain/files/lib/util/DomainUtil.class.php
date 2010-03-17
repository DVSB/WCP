<?php

/**
 * Contains domain-related functions.
 * 
 * @author 		Tobias Friebel
 * @copyright	2009 Tobias Friebel	
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	util
 * @category 	ControlPanel
 * @id			$Id$
 */
class DomainUtil
{
	/**
	 * Returns true, if the given name is a valid domainname.
	 * 
	 * @param	string		$name		domainname
	 * 
	 * @return 	boolean
	 */
	public static function isValidDomainname($name)
	{
		// check illegal characters
		if (!preg_match('!^[^,\n]+$!', $name))
		{
			return false;
		}
		return true;
	}

	/**
	 * Returns true, if the given domain is available.
	 * 
	 * @param	string		$name		domainname
	 * @return 	boolean
	 */
	public static function isAvailableDomainname($name, $domainID = 0)
	{
		$sql = "SELECT 	COUNT(domainname) AS count
				FROM 	cp" . CP_N . "_domain
				WHERE 	domainname = '" . escapeString($name) . "'";
		
		if ($domainID != 0)
			$sql .= " AND domainID <> " . $domainID;
		
		$existCount = WCF :: getDB()->getFirstRow($sql);
		return $existCount['count'] == 0;
	}
	
	/**
	 * Returns number of domains for given userID, minus subdomains
	 * 
	 * @param	int			$userID
	 * @param 	bool		$addSubDomains		if true, subdomains will also be counted
	 * @return 	int
	 */
	public static function countDomainsForUser($userID, $addSubDomains = false)
	{
		$sql = "SELECT 	COUNT(*) AS count
				FROM 	cp" . CP_N . "_domain	
				WHERE 	userID = " . intval($userID) . "
					" . (!$addSubDomains ? ' AND (parentDomainID IS NULL OR parentDomainID = 0) ' : '') . " 
					AND deactivated = 0";
		$count = WCF :: getDB()->getFirstRow($sql);
		return $count['count'];
	}
	
	/**
	 * Returns domains for given userID, minus subdomains
	 * 
	 * @param	int			$userID
	 * @param 	bool		$addSubDomains		if true, subdomains will also be counted
	 * @return 	array
	 */
	public static function getDomainsForUser($userID, $addSubDomains = false)
	{
		$sql = "SELECT 	domainID, domainname
				FROM 	cp" . CP_N . "_domain	
				WHERE 	userID = '" . intval($userID) . "'
					" . (!$addSubDomains ? ' AND (parentDomainID IS NULL OR parentDomainID = 0) ' : '') . " 
					AND deactivated = 0";
		
		$result = WCF :: getDB()->sendQuery($sql);
		
		$domains = array();
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$domains[$row['domainID']] = $row['domainname'];
		}
		
		return $domains;
	}
}
?>