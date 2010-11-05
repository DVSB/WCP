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
		// try php-filter, should be conform to http://www.faqs.org/rfcs/rfc2396.html
		if(filter_var('http://' . $domainname, FILTER_VALIDATE_URL) === false)
			return false;

		// see http://tools.ietf.org/html/rfc1035
		// the domainname must be shorter than 255
		if (strlen($domainname) > 255)
			return false;

		// stolen from http://www.shauninman.com/archive/2006/05/08/validating_domain_names
		// the domainname only consists of chars a-z, numbers (but not starting with one!) and - (but not starting or ending with one!)
		// also, we check the toplevel-domain, this part can change over time as there are more tlds.
		$regexp = '/^([a-z0-9]([-a-z0-9]*[a-z0-9])?\\.)+((a[cdefgilmnoqrstuwxz]|aero|arpa)|(b[abdefghijmnorstvwyz]|biz)|(c[acdfghiklmnorsuvxyz]|cat|com|coop)|d[ejkmoz]|(e[ceghrstu]|edu)|f[ijkmor]|(g[abdefghilmnpqrstuwy]|gov)|h[kmnrtu]|(i[delmnoqrst]|info|int)|(j[emop]|jobs)|k[eghimnprwyz]|l[abcikrstuvy]|(m[acdghklmnopqrstuvwxyz]|mil|mobi|museum)|(n[acefgilopruz]|name|net)|(om|org)|(p[aefghklmnrstwy]|pro)|qa|r[eouw]|s[abcdeghijklmnortvyz]|(t[cdfghjklmnoprtvwz]|travel)|u[agkmsyz]|v[aceginu]|w[fs]|y[etu]|z[amw])$/i';
		if (preg_match($regexp, $domainname) !== 1)
			return false;

		// see http://tools.ietf.org/html/rfc1035
		// no part of domainname is allowed to be longer than 63 characters
		$tmp = explode('.', $domainname);
		foreach ($tmp as $t)
		{
			if (strlen($t) > 63)
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
	 * @param	string		$additionalWhere	get only what you need
	 * @return 	array
	 */
	public static function getDomains($addSubDomains = false, $addDeactivated = false, $additionalWhere = '')
	{
		$sql = "SELECT 		domain.domainID, domain.domainname, parentdomain.domainname AS parentDomainName
				FROM 		cp" . CP_N . "_domain domain
				LEFT JOIN	cp" . CP_N . "_domain parentdomain
							ON (domain.parentDomainID = parentdomain.domainID)
				LEFT JOIN	cp" . CP_N . "_domain_option_value
							ON (domain.domainID = cp" . CP_N . "_domain_option_value.domainID)
				WHERE 		1 = 1 " . (!$addSubDomains ? ' AND (domain.parentDomainID IS NULL OR domain.parentDomainID = 0) ' : '');

		if (!$addDeactivated)
			$sql .= " AND domain.deactivated = 0";

		if ($additionalWhere)
			$sql .= " AND "	. $additionalWhere;

		//check if used in ACP
		if (class_exists('CPACP') && !WCF :: getUser()->getPermission('admin.general.isSuperAdmin'))
		{
			$sql .= " AND domain.adminID = " . intval(WCF :: getUser()->userID);
		}

		$result = WCF :: getDB()->sendQuery($sql);

		$domains = array(0 => '---');
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$domains[$row['domainID']] = $row['domainname'];
		}

		return $domains;
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
		return self :: getDomains($addSubDomains, "domain.userID = " . intval($userID));
	}
}
?>
