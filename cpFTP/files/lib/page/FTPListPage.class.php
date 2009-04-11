<?php
/*
 * Copyright (c) 2009 Tobias Friebel
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

// wcf imports
require_once (WCF_DIR . 'lib/page/SortablePage.class.php');
require_once (CP_DIR . 'lib/data/ftp/FTPUserList.class.php');

/**
 * Shows a list available ftpaccounts for this user
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.ftp
 * @subpackage	page
 * @category 	Control Panel
 */
class FTPListPage extends SortablePage
{
	// system
	public $templateName = 'ftpList';
	public $itemsPerPage = 20;
	public $defaultSortField = 'username';
	public $defaultSortOrder = 'ASC';
	//public $neededPermissions = 'name.der.berechtigung';

	/**
	 * ftp ist object
	 *
	 * @var	FTPUserList
	 */
	public $ftpList = null;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters()
	{
		parent :: readParameters();

		$this->ftpList = new FTPUserList();
	}

	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		parent :: readData();

		// read objects
		$this->ftpList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->ftpList->sqlLimit = $this->itemsPerPage;
		$this->ftpList->sqlOrderBy = $this->sortField . ' ' . $this->sortOrder;
		$this->ftpList->sqlConditions = 'ftp_users.userID = ' . WCF :: getUser()->userID;
		$this->ftpList->readObjects();
	}

	/**
	 * @see SortablePage::validateSortField()
	 */
	public function validateSortField()
	{
		parent :: validateSortField();

		switch ($this->sortField)
		{
			case 'username':
			case 'lastLogin':
			case 'homedir':
			case 'loginCount':
			break;
			default:
				$this->sortField = $this->defaultSortField;
		}
	}

	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems()
	{
		parent :: countItems();

		return $this->ftpList->countObjects();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();

		WCF :: getTPL()->assign(array (
			'ftpAccounts' => $this->ftpList->getObjects()
		));
	}
}
?>