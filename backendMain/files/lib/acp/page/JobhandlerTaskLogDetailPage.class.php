<?php
/*
 * Copyright (c) 2009 Tobias Friebel  
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

require_once (WCF_DIR . 'lib/page/AbstractPage.class.php');

class JobhandlerTaskLogDetailPage extends AbstractPage
{
	// system
	public $templateName = 'jobhandlerTaskLogDetail';
	public $neededPermissions = 'admin.cp.canSeeJobhandlerLog';
	
	public $logID = 0;
	public $log = array();

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters()
	{
		parent :: readParameters();
		
		if (isset($_REQUEST['logID']))
			$this->logID = intval($_REQUEST['logID']);
	}

	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		parent :: readData();
		
		// get package data
		$sql = "SELECT		jobhandler_log.*
				FROM		cp" . CP_N . "_jobhandler_task_log jobhandler_log
				WHERE		jobhandlerTaskLogID = " . $this->logID;
		
		$result = WCF :: getDB()->sendQuery($sql);
		
		$this->log = WCF :: getDB()->fetchArray($result);
		
		if (!$this->log)
			throw new IllegalLinkException();
			
		$this->log['execJobhandler'] = str_replace(', ', "\n", $this->log['execJobhandler']);
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();
		
		WCF :: getTPL()->assign(array (
			'log' => $this->log
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		// enable menu item
		CPACP :: getMenu()->setActiveMenuItem('cp.acp.menu.link.jobhandler.log');
		
		parent :: show();
	}
}
?>