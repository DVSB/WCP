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
 * Represents one ftp account
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.ftp
 * @subpackage	data.ftp
 * @category 	Control Panel
 */
class FTPUser extends DatabaseObject
{
	static $users = array();

	public function __construct($ftpUserID, $row = null)
	{
		if ($ftpUserID !== null)
		{
			$sql = "SELECT	*
					FROM	cp" . CP_N . "_ftp_users ftp_users
					JOIN	cp" . CP_N . "_ftp_groups ftp_groups
						ON	(ftp_users.userID = ftp_groups.userID)
					WHERE	ftpUserID = " . intval($ftpUserID);
			$row = WCF :: getDB()->getFirstRow($sql);
		}

		parent :: __construct($row);
	}

	protected function handleData($data)
	{
		if (!array_key_exists($data['userID'], self :: $users))
			self :: $users[$data['userID']] = new CPUser($data['userID']);
		$data['relativehomedir'] = '/' . StringUtil :: replace(self :: $users[$data['userID']]->homeDir, '', $data['homedir']);
		parent :: handleData($data);
	}
}
?>