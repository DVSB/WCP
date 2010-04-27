<?php

require_once (WCF_DIR . 'lib/page/SortablePage.class.php');
require_once (CP_DIR . 'lib/data/vhost/VhostContainerList.class.php');

/**
 * List all VhostContainer
 * 
 * @author		Tobias Friebel
 * @copyright	2010 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.vhost
 * @subpackage	acp.page
 * @category 	Control Panel
 * @id			$Id$
 */
class VhostContainerListPage extends SortablePage
{
	// system
	public $itemsPerPage = 50;
	public $templateName = 'vhostContainerList';
	
	public $defaultSortField = 'vhostName';
	public $defaultSortOrder = 'ASC';
	
	/**
	 * vhostContainerList object
	 *
	 * @var	vhostList
	 */
	public $vhostList = null;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters()
	{
		parent :: readParameters();
		
		$this->vhostList = new VhostContainerList();
	}

	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		parent :: readData();
		
		// read objects
		$this->vhostList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->vhostList->sqlLimit = $this->itemsPerPage;
		$this->vhostList->sqlOrderBy = 'vhostContainer.' . $this->sortField . ' ' . $this->sortOrder;
		$this->vhostList->readObjects();
	}
	
	/**
	 * @see SortablePage::validateSortField()
	 */
	public function validateSortField()
	{
		parent :: validateSortField();
		
		switch ($this->sortField)
		{
			case 'vhostName':
			case 'ipAddress':
			case 'vhostType':
			break;
			default:
				if (!isset($this->options[$this->sortField]))
				{
					$this->sortField = $this->defaultSortField;
				}
		}
	}
	
	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems()
	{
		parent :: countItems();
		
		return $this->vhostList->countObjects();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();
		
		WCF :: getTPL()->assign(array (
			'vhosts' => $this->vhostList->getObjects(), 
			'deletedVhosts' => '',
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		// set active menu item
		WCFACP :: getMenu()->setActiveMenuItem('cp.acp.menu.link.vhostcontainer.list');
		
		// check permission
		WCF :: getUser()->checkPermission('admin.cp.canSeeVhostContainer');
		
		parent :: show();
	}
}
?>
?>