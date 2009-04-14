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

/**
 * Represents one mysql database
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.mysql
 * @subpackage	data.mysql
 * @category 	Control Panel
 */
class MySQL extends DatabaseObject
{
	public function __construct($mysqlID, $row = null)
	{
		if ($mysqlID !== null)
		{
			$sql = "SELECT	*
					FROM	cp" . CP_N . "_mysql
					WHERE	mysqlID = " . intval($mysqlID);
			$row = WCF :: getDB()->getFirstRow($sql);
		}

		parent :: __construct($row);
	}
}
?>