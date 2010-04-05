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
require_once (CP_DIR . 'lib/data/email/EmailList.class.php');

/**
 * Shows a list available ftpaccounts for this user
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.email
 * @subpackage	page
 * @category 	Control Panel
 */
class EmailListPage extends SortablePage
{
	// system
	public $templateName = 'emailList';
	public $itemsPerPage = 20;
	public $defaultSortField = 'emailaddress';
	public $defaultSortOrder = 'ASC';
	//public $neededPermissions = 'name.der.berechtigung';

	/**
	 * ftp ist object
	 *
	 * @var	FTPUserList
	 */
	public $emailList = null;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters()
	{
		parent :: readParameters();

		$this->emailList = new EmailList();
	}

	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		parent :: readData();

		// read objects
		$this->emailList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->emailList->sqlLimit = $this->itemsPerPage;
		$this->emailList->sqlOrderBy = 'virtual.' . $this->sortField . ' ' . $this->sortOrder;
		$this->emailList->sqlConditions = 'virtual.userID = ' . WCF :: getUser()->userID;
		$this->emailList->readObjects();
	}

	/**
	 * @see SortablePage::validateSortField()
	 */
	public function validateSortField()
	{
		parent :: validateSortField();

		switch ($this->sortField)
		{
			case 'emailaddress':
			case 'destination':
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

		return $this->emailList->countObjects();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();

		WCF :: getTPL()->assign(array (
			'emails' => $this->emailList->getObjects()
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
		PageMenu :: setActiveMenuItem('cp.header.menu.email');

		parent :: show();
	}
}
?>