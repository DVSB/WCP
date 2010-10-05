<?php
/*
 * Copyright (c) 2009 Tobias Friebel  
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

require_once (WCF_DIR . 'lib/page/SortablePage.class.php');

class JobhandlerTaskLogListPage extends SortablePage
{
	// system
	public $templateName = 'jobhandlerTaskLogList';
	public $defaultSortField = 'jobhandlerTaskLogID';
	public $neededPermissions = 'admin.cp.canSeeJobhandlerLog';
	public $defaultSortOrder = 'DESC';
	
	/**
	 * list of jobhandlers
	 * 
	 * @var	array
	 */
	public $logs = array ();

	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		parent :: readData();
		
		$this->readJobhandlerLog();
	}

	/**
	 * Gets the list of cronjobs.
	 */
	protected function readJobhandlerLog()
	{
		$sql = "SELECT		jobhandler_log.*
				FROM		cp" . CP_N . "_jobhandler_task_log jobhandler_log
				ORDER BY	jobhandler_log.".$this->sortField." ".$this->sortOrder;
		
		$result = WCF :: getDB()->sendQuery($sql, $this->itemsPerPage, ($this->pageNo - 1) * $this->itemsPerPage);
		
		while ($row = WCF :: getDB()->fetchArray($result))
		{			
			$this->logs[] = $row;
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();
		
		WCF :: getTPL()->assign(array (
			'logs' => $this->logs
		));
	}

	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems()
	{
		parent :: countItems();
		
		// count cronjobs
		$sql = "SELECT	COUNT(*) AS count
				FROM	cp" . CP_N . "_jobhandler_task_log";
		
		$row = WCF :: getDB()->getFirstRow($sql);
		
		return $row['count'];
	}

	/**
	 * @see SortablePage::validateSortField()
	 */
	public function validateSortField()
	{
		parent :: validateSortField();
		
		switch ($this->sortField)
		{
			case 'jobhandlerTaskLogID':
			case 'execTimeStart':
			case 'execTimeEnd':
			case 'execJobhandler':
			break;
			default:
				$this->sortField = $this->defaultSortField;
		}
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		// set active menu item.
		CPACP :: getMenu()->setActiveMenuItem('cp.acp.menu.link.jobhandler.log');
		
		parent :: show();
	}
}
?>