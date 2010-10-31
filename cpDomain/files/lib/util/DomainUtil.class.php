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
	 * @param	string		$domainname		domainname
	 *
	 * @return 	boolean
	 */
	public static function isValidDomainname($domainname)
	{
		$domainname_tmp = 'http://' . $domainname;

		if(filter_var($domainname_tmp, FILTER_VALIDATE_URL) !== false)
		{
			return true;
		}
		else
		{
			return false;
		}
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
	 * @param	string		$additionalWhere	get only what you need
	 * @return 	array
	 */
	public static function getDomainsForUser($userID, $addSubDomains = false, $additionalWhere = '')
	{
		$sql = "SELECT 		domain.domainID, domain.domainname, parentdomain.domainname AS parentDomainName
				FROM 		cp" . CP_N . "_domain domain
				LEFT JOIN	cp" . CP_N . "_domain parentdomain
							ON (domain.parentDomainID = parentdomain.domainID)
				LEFT JOIN	cp" . CP_N . "_domain_option_value
							ON (domain.domainID = cp" . CP_N . "_domain_option_value.domainID)
				WHERE 		domain.userID = '" . intval($userID) . "'
						" . (!$addSubDomains ? ' AND (domain.parentDomainID IS NULL OR domain.parentDomainID = 0) ' : '') . "
							AND domain.deactivated = 0";

		if ($additionalWhere)
			$sql .= ' AND '	. $additionalWhere;

		$result = WCF :: getDB()->sendQuery($sql);

		$domains = array();
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			if (!empty($row['parentDomainName']))
				$row['domainname'] .= '.' . $row['parentDomainName'];
			$domains[$row['domainID']] = $row['domainname'];
		}

		return $domains;
	}
}
?>
