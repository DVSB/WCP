<?php

/**
 * Contains domain-related functions.
 * 
 * @author 		Tobias Friebel
 * @copyright	2009 Tobias Friebel	
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	acp.form
 * @category 	Control Panel
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
	public static function isAvailableDomainname($name)
	{
		$sql = "SELECT 	COUNT(domainname) AS count
				FROM 	cp" . CP_N . "_domains	
				WHERE 	domainname = '" . escapeString($name) . "'";
		$existCount = WCF :: getDB()->getFirstRow($sql);
		return $existCount['count'] == 0;
	}
}
?>