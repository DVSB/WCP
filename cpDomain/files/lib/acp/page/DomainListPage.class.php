<?php
// wcf imports
require_once (WCF_DIR . 'lib/page/SortablePage.class.php');
require_once (WCF_DIR . 'lib/system/event/EventHandler.class.php');
require_once (CP_DIR . 'lib/data/domain/DomainList.class.php');

/**
 * List all Domains
 *
 * @author			Tobias Friebel
 * @copyright		2009 Tobias Friebel
 * @license			GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package			com.toby.cp.domain
 * @subpackage		acp.page
 * @category 		ControlPanel
 * @id				$Id$
 */
class DomainListPage extends SortablePage
{
	// system
	public $itemsPerPage = 50;
	public $templateName = 'domainList';
	
	public $defaultSortField = 'domainname';
	public $defaultSortOrder = 'ASC';
	
	public $permission = 'admin.cp.canSeeDomains';
	
	/**
	 * domainlist object
	 *
	 * @var	DomainList
	 */
	public $domainList = null;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters()
	{
		parent :: readParameters();
		
		$this->domainList = new DomainList();
	}

	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		parent :: readData();
		
		// read objects
		if (!WCF::getUser()->getPermission('admin.general.isSuperAdmin'))
			$this->domainList->sqlConditions = 'domain.adminID = ' . WCF :: getUser()->userID;
			
		$this->domainList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->domainList->sqlLimit = $this->itemsPerPage;
		$this->domainList->sqlOrderBy = 'domain.' . $this->sortField . ' ' . $this->sortOrder;
		$this->domainList->readObjects();
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
			case 'adminname':
			case 'registrationDate':
			case 'domainname':
			case 'parentDomainID':
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
		
		return $this->domainList->countObjects();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();
		
		WCF :: getTPL()->assign(array (
			'domains' => $this->domainList->getObjects(), 
			'deletedDomains' => '',
			'disabledDomains' => '',
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		// set active menu item
		WCFACP :: getMenu()->setActiveMenuItem('cp.acp.menu.link.domains.list');
		
		// check permission
		WCF :: getUser()->checkPermission('admin.cp.canSeeDomains');
		
		parent :: show();
	}
}
?>