<?php
// wcf imports
require_once (WCF_DIR . 'lib/page/SortablePage.class.php');
require_once (WCF_DIR . 'lib/system/event/EventHandler.class.php');
require_once (CP_DIR . 'lib/data/domains/DomainList.class.php');

/**
 * List all Domains
 *
 * @author			Tobias Friebel
 * @copyright		2009 Tobias Friebel
 * @license			GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package			com.toby.cp.domain
 * @subpackage		acp.page
 * @category 		ControlPanel
 */
class DomainListPage extends SortablePage
{
	// system
	public $itemsPerPage = 50;
	public $defaultSortField = 'domainname';
	public $templateName = 'domainList';
	
	// data
	public $domainIDs = array ();
	public $domains = array ();
	public $url = '';
	public $columns = array (
		'email', 
		'registrationDate'
	);
	public $outputObjects = array ();
	public $options = array ();
	public $columnValues = array ();
	public $columnHeads = array ();
	public $sqlConditions = '';

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters()
	{
		parent :: readParameters();
		
		// get user options
		$this->readUserOptions();
	}

	/**
	 * @see SortablePage::validateSortField()
	 */
	public function validateSortField()
	{
		parent :: validateSortField();
		
		switch ($this->sortField)
		{
			case 'email':
			case 'userID':
			case 'registrationDate':
			case 'username':
			break;
			default:
				if (!isset($this->options[$this->sortField]))
				{
					$this->sortField = $this->defaultSortField;
				}
		}
	}

	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		parent :: readData();
		
		// get marked users
		$this->markedDomains = WCF :: getSession()->getVar('markedDomains');
		if ($this->markedDomains == null || !is_array($this->markedDomains))
			$this->markedDomains = array ();

		
		// get users
		$this->readDomains();
		
		// build page url
		$this->url = 'index.php?page=DomainList&searchID=' . $this->searchID . '&action=' . rawurlencode($this->action) . '&pageNo=' . $this->pageNo . '&sortField=' . $this->sortField . '&sortOrder=' . $this->sortOrder . '&packageID=' . PACKAGE_ID . SID_ARG_2ND_NOT_ENCODED;
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();
		
		WCF :: getTPL()->assign(array (
			'domains' => $this->domains, 
			'markedDomains' => count($this->markedDomains), 
			'url' => $this->url, 
			'columnHeads' => $this->columnHeads, 
			'columnValues' => $this->columnValues
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

	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems()
	{
		parent :: countItems();
		
		$sql = "SELECT	COUNT(*) AS count
				FROM	cp"  .CP_N . "_domains domains
				" . (!empty($this->sqlConditions) ? 'WHERE ' . $this->sqlConditions : '');
		$row = WCF :: getDB()->getFirstRow($sql);
		return $row['count'];
	}

	/**
	 * Gets the list of results.
	 */
	protected function readDomains()
	{
		// get user ids
		$userIDs = array ();
		$sql = "SELECT		user_table.userID
			FROM		wcf" . WCF_N . "_user user_table
			" . (isset($this->options[$this->sortField]) ? "LEFT JOIN wcf" . WCF_N . "_user_option_value USING (userID)" : '') . "
			" . (!empty($this->sqlConditions) ? 'WHERE ' . $this->sqlConditions : '') . "
			ORDER BY	" . (isset($this->options[$this->sortField]) ? 'userOption' . $this->options[$this->sortField]['optionID'] : $this->sortField) . " " . $this->sortOrder;
		$result = WCF :: getDB()->sendQuery($sql, $this->itemsPerPage, ($this->pageNo - 1) * $this->itemsPerPage);
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$userIDs[] = $row['userID'];
		}
		
		// get user data
		if (count($userIDs))
		{
			$sql = "SELECT		option_value.*, user_table.*,
						GROUP_CONCAT(groupID SEPARATOR ',') AS groupIDs
				FROM		wcf" . WCF_N . "_user user_table
				LEFT JOIN	wcf" . WCF_N . "_user_option_value option_value
				ON		(option_value.userID = user_table.userID)
				LEFT JOIN	wcf" . WCF_N . "_user_to_groups groups
				ON		(groups.userID = user_table.userID)
				WHERE		user_table.userID IN (" . implode(',', $userIDs) . ")
				GROUP BY	user_table.userID
				ORDER BY	" . (isset($this->options[$this->sortField]) ? 'option_value.userOption' . $this->options[$this->sortField]['optionID'] : 'user_table.' . $this->sortField) . " " . $this->sortOrder;
			$result = WCF :: getDB()->sendQuery($sql);
			while ($row = WCF :: getDB()->fetchArray($result))
			{
				$accessible = Group :: isAccessibleGroup(explode(',', $row['groupIDs']));
				$row['accessible'] = $accessible;
				$row['deletable'] = ($accessible && WCF :: getUser()->getPermission('admin.user.canDeleteUser') && $row['userID'] != WCF :: getUser()->userID) ? 1 : 0;
				$row['editable'] = ($accessible && WCF :: getUser()->getPermission('admin.user.canEditUser')) ? 1 : 0;
				$row['isMarked'] = intval(in_array($row['userID'], $this->markedUsers));
				
				$this->users[] = new User(null, $row);
			}
			
			// get special columns
			foreach ($this->users as $key => $user)
			{
				foreach ($this->columns as $column)
				{
					if (isset($this->options[$column]))
					{
						if ($this->options[$column]['outputClass'])
						{
							$outputObj = $this->getOutputObject($this->options[$column]['outputClass']);
							$this->columnValues[$user->userID][$column] = $outputObj->getOutput($user, $this->options[$column], $user->{$column});
						}
						else
						{
							$this->columnValues[$user->userID][$column] = StringUtil :: encodeHTML($user->{$column});
						}
					}
					else
					{
						switch ($column)
						{
							case 'email':
								$this->columnValues[$user->userID][$column] = '<a href="mailto:' . StringUtil :: encodeHTML($user->email) . '">' . StringUtil :: encodeHTML($user->email) . '</a>';
							break;
							case 'registrationDate':
								$this->columnValues[$user->userID][$column] = DateUtil :: formatDate(null, $user->{$column});
							break;
						}
					}
				}
			}
		}
	}

	/**
	 * Gets the user options from cache.
	 */
	protected function readDomainOptions()
	{
		// add cache resource
		$cacheName = 'user-option-' . PACKAGE_ID;
		WCF :: getCache()->addResource($cacheName, WCF_DIR . 'cache/cache.' . $cacheName . '.php', WCF_DIR . 'lib/system/cache/CacheBuilderOption.class.php');
		
		// get options
		$this->options = WCF :: getCache()->get($cacheName, 'options');
	}

	/**
	 * Reads the column heads.
	 */
	protected function readColumnsHeads()
	{
		foreach ($this->columns as $column)
		{
			if (isset($this->options[$column]))
			{
				$this->columnHeads[$column] = 'wcf.user.option.' . $column;
			}
			else
			{
				$this->columnHeads[$column] = 'wcf.user.' . $column;
			}
		}
	}

	/**
	 * Returns an object of the requested option output type.
	 * 
	 * @param	string			$type
	 * @return	UserOptionOutput
	 */
	protected function getOutputObject($className)
	{
		if (!isset($this->outputObjects[$className]))
		{
			// include class file
			$classPath = WCF_DIR . 'lib/data/user/option/' . $className . '.class.php';
			if (!file_exists($classPath))
			{
				throw new SystemException("unable to find class file '" . $classPath . "'", 11000);
			}
			require_once ($classPath);
			
			// create instance
			if (!class_exists($className))
			{
				throw new SystemException("unable to find class '" . $className . "'", 11001);
			}
			$this->outputObjects[$className] = new $className();
		}
		
		return $this->outputObjects[$className];
	}
}
?>