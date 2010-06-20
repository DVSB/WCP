<?php
// wcf imports
require_once (WCF_DIR . 'lib/data/cronjobs/Cronjob.class.php');

/*
 * Copyright (c) 2010 Tobias Friebel  
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

class CleanUpJobhandlerLogCronjob implements Cronjob
{
	/**
	 * @see Cronjob::execute()
	 */
	public function execute($data)
	{
		$sql = "DELETE FROM	cp" . CP_N . "_jobhandler_task_log
				WHERE		execTimeEnd < " . (TIME_NOW - (86400 * 30));
		WCF :: getDB()->sendQuery($sql);
	}
}
?>