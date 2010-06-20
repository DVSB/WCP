<?php
// wcf imports
require_once (WCF_DIR . 'lib/action/AbstractAction.class.php');
require_once (WCF_DIR . 'lib/data/cronjobs/CronjobEditor.class.php');

/*
 * Copyright (c) 2009 Tobias Friebel  
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

class JobhandlerLogDeleteAction extends AbstractAction
{
	/**
	 * @see Action::execute()
	 */
	public function execute()
	{
		parent :: execute();
		
		// check permission
		WCF :: getUser()->checkPermission('admin.cp.canClearJobhandlerLog');
		
		$sql = "DELETE FROM	cp" . CP_N . "_jobhandler_task_log";
		WCF :: getDB()->sendQuery($sql);
		
		$this->executed();
		
		// forward
		HeaderUtil :: redirect('index.php?page=JobhandlerTaskLogList' . SID_ARG_2ND_NOT_ENCODED);
		exit();
	}
}
?>