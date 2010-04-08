<?php

/**
 * Contains domain-related functions.
 * 
 * @author 		Tobias Friebel
 * @copyright	2009 Tobias Friebel	
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.email
 * @subpackage	util
 * @category 	ControlPanel
 * @id			$Id$
 */
class EmailUtil
{
	/**
	 * Returns true, if the given emailaddress is a valid emailaddress.
	 * 
	 * @param	string		$emailaddress		emailaddress
	 * 
	 * @return 	boolean
	 */
	public static function isValidEmailaddress($emailaddress)
	{
		$emailaddress = strtolower($emailaddress);
		return filter_var($emailaddress, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * Returns true, if the given emailaddress is available.
	 * 
	 * @param	string		$emailaddress		emailaddress
	 * @return 	boolean
	 */
	public static function isAvailableEmailaddress($emailaddress, $mailID = 0)
	{
		$sql = "SELECT 	COUNT(emailaddress) AS count
				FROM 	cp" . CP_N . "_mail_virtual
				WHERE 	emailaddress = '" . escapeString($emailaddress) . "'";
		
		if ($mailID != 0)
			$sql .= " AND mailID <> " . intval($mailID);
		
		$existCount = WCF :: getDB()->getFirstRow($sql);
		return $existCount['count'] == 0;
	}
	
	/**
	 * Returns true, if the given domain has no catchall
	 * 
	 * @param	int		$domainID		domainID
	 * @param	int		$mainID			mailID
	 * @return 	boolean
	 */
	public static function isAvailableCatchall($domainID, $mailID = 0)
	{
		$sql = "SELECT 	COUNT(*) AS count
				FROM 	cp" . CP_N . "_mail_virtual
				WHERE 	isCatchall = 1 
					AND domainID = " . intval($domainID);
		
		if ($mailID != 0)
			$sql .= " AND mailID <> " . intval($mailID);
		
		$existCount = WCF :: getDB()->getFirstRow($sql);
		return $existCount['count'] == 0;
	}

	/**
	 * Returns emaildomains for given userID
	 * 
	 * @param	int			$userID
	 * @return 	array
	 */
	public static function getDomainsForUser($userID)
	{
		require_once (CP_DIR . 'lib/data/domain/Domain.class.php');
		
		$optionID = Domain :: getDomainOptionID('isEmailDomain');
		
		$sql = "SELECT 	domainID, domainname
				FROM 	cp" . CP_N . "_domain
				JOIN	cp" . CP_N . "_domain_option_value
				WHERE 	userID = '" . intval($userID) . "'
					AND	domainOption" . $optionID . " = 1
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