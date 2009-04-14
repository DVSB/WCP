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
require_once (CP_DIR . 'lib/data/mysql/MySQLList.class.php');

/**
 * Shows a list available mysqldbs for this user
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.mysql
 * @subpackage	page
 * @category 	Control Panel
 */
class MySQLListPage extends SortablePage
{
	// system
	public $templateName = 'mysqlList';
	public $itemsPerPage = 20;
	public $defaultSortField = 'mysqlname';
	public $defaultSortOrder = 'ASC';
	//public $neededPermissions = 'name.der.berechtigung';

	/**
	 * ftp ist object
	 *
	 * @var	FTPUserList
	 */
	public $mysqlList = null;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters()
	{
		parent :: readParameters();

		$this->mysqlList = new MySQLList();
	}

	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		parent :: readData();

		// read objects
		$this->mysqlList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->mysqlList->sqlLimit = $this->itemsPerPage;
		$this->mysqlList->sqlOrderBy = 'mysqls.' . $this->sortField . ' ' . $this->sortOrder;
		$this->mysqlList->sqlConditions = 'mysqls.userID = ' . WCF :: getUser()->userID;
		$this->mysqlList->readObjects();
	}

	/**
	 * @see SortablePage::validateSortField()
	 */
	public function validateSortField()
	{
		parent :: validateSortField();

		switch ($this->sortField)
		{
			case 'mysqlname':
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

		return $this->mysqlList->countObjects();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();

		WCF :: getTPL()->assign(array (
			'mysqls' => $this->mysqlList->getObjects()
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
		PageMenu::setActiveMenuItem('cp.header.menu.mysql');

		parent::show();
	}
}
?>