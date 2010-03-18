<?php
/*
 * Copyright (c) 2009 Tobias Friebel
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

require_once (WCF_DIR . 'lib/data/DatabaseObject.class.php');
require_once (CP_DIR . 'lib/data/user/CPUser.class.php');

/**
 * Represents one email
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.email
 * @subpackage	data.email
 * @category 	Control Panel
 */
class Email extends DatabaseObject
{
	public function __construct($emailID, $row = null)
	{
		if ($emailID !== null)
		{
			$sql = "SELECT		*
					FROM		cp" . CP_N . "_mail_virtual
					LEFT JOIN	cp" . CP_N . "_mail_accounts USING (accountID)
					WHERE		mailID = " . intval($emailID);
			$row = WCF :: getDB()->getFirstRow($sql);
		}
		
		parent :: __construct($row);
	}
}
?>