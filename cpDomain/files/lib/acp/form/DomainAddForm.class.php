<?php
require_once (WCF_DIR . 'lib/acp/form/DynamicOptionListForm.class.php');
require_once (WCF_DIR . 'lib/page/util/InlineCalendar.class.php');
require_once (WCF_DIR . 'lib/data/user/group/Group.class.php');
require_once (CP_DIR . 'lib/data/user/CPUser.class.php');

/**
 * Shows the domain add form.
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	acp.form
 * @category 	Control Panel
 * @id			$Id$
 */
class DomainAddForm extends DynamicOptionListForm
{
	public $templateName = 'domainAdd';
	public $menuItemName = 'cp.acp.menu.link.domains.add';
	public $permission = 'admin.cp.canAddDomain';
	
	public $cacheClass = 'CacheBuilderDomainOption';

	public $cacheName = 'domain-option-';
	public $additionalFields = array();
	public $domain;
	public $options;
	
	public $domainname = '';
	public $domainID = 0;
	
	public $username;
	public $userID = 0;
	
	public $adminname;
	public $adminID = 0;
	
	public $registrationDateDay = 0;
	public $registrationDateMonth = 0;
	public $registrationDateYear = '';
	public $registrationDate = 0;
	
	public $activeTabMenuItem = '';
	public $activeSubTabMenuItem = '';

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters()
	{
		parent :: readFormParameters();
		
		if (isset($_POST['domainname']))
			$this->domainname = StringUtil :: trim($_POST['domainname']);
			
		if (isset($_POST['username']))
			$this->username = StringUtil :: trim($_POST['username']);
			
		if (isset($_POST['adminname']))
			$this->adminname = StringUtil :: trim($_POST['adminname']);
			
		if (isset($_POST['parentDomainID']))
			$this->parentDomainID = StringUtil :: trim($_POST['parentDomainID']);
			
		if (isset($_POST['registrationDateDay'])) 
			$this->registrationDateDay = intval($_POST['registrationDateDay']);
		if (isset($_POST['registrationDateMonth'])) 
			$this->registrationDateMonth = intval($_POST['registrationDateMonth']);
		if (!empty($_POST['registrationDateYear'])) 
			$this->registrationDateYear = intval($_POST['registrationDateYear']);
			
		if ($this->registrationDateDay && $this->registrationDateMonth && $this->registrationDateYear) 
		{
			$this->registrationDate = @gmmktime(0, 0, 0, $this->registrationDateMonth, $this->registrationDateDay, $this->registrationDateYear);
		}
			
		if (isset($_POST['activeTabMenuItem'])) 
			$this->activeTabMenuItem = $_POST['activeTabMenuItem'];
			
		if (isset($_POST['activeSubTabMenuItem'])) 
			$this->activeSubTabMenuItem = $_POST['activeSubTabMenuItem'];
	}

	/**
	 * @see Form::validate()
	 */
	public function validate()
	{
		// validate static user options 
		try
		{
			$this->validateDomainname($this->domainname);
		}
		catch (UserInputException $e)
		{
			$this->errorType[$e->getField()] = $e->getType();
		}
		
		try 
		{
			// get user
			$user = new CPUser(null, null, $this->username);
			if (!$user->userID) 
			{
				throw new UserInputException('username', 'notFound');
			}
			
			if (!Group :: isAccessibleGroup($user->getGroupIDs())) 
			{
				throw new UserInputException('username', 'invalidUser');
			}
			
			if ($user->isCustomer == 0)
			{
				throw new UserInputException('username', 'noCustomer');
			}
				
			$this->userID = $user->userID;
		}
		catch (UserInputException $e) 
		{
			$this->errorType[$e->getType()] = $e->getType();
		}
		
		try 
		{
			// get user
			$user = new UserSession(null, null, $this->adminname);
			if (!$user->userID) 
			{
				throw new UserInputException('adminname', 'notFound');
			}

			if (!$user->getPermission('admin.general.canUseAcp')) 
			{
				throw new UserInputException('adminname', 'noAdmin');
			}
				
			$this->adminID = $user->userID;
		}
		catch (UserInputException $e) 
		{
			$this->errorType[$e->getType()] = $e->getType();
		}

		if ($this->registrationDate <= 0)
			$this->errorType['registrationDate'] = 'invalid';
		
		// validate dynamic options
		parent :: validate();
	}
	
	/**
	 * Validates an option.
	 * 
	 * @param	string		$key		option name
	 * @param	array		$option		option data
	 */
	protected function validateOption($key, $option)
	{
		parent :: validateOption($key, $option);
		
		if ($option['required'] && empty($this->activeOptions[$key]['optionValue']))
		{
			throw new UserInputException($option['optionName']);
		}
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		AbstractForm :: save();
		
		// create
		require_once (CP_DIR . 'lib/data/domain/DomainEditor.class.php');
		$this->additionalFields['registrationDate'] = $this->registrationDate;
		$this->domain = DomainEditor :: create($this->domainname, $this->userID, $this->adminID, 0, $this->activeOptions, $this->additionalFields);
		$this->saved();
		
		// show empty add form
		WCF :: getTPL()->assign(array (
			'success' => true, 
			'newDomain' => $this->domain,
		));
		
		// reset values
		$this->domainname = '';
	
		foreach ($this->activeOptions as $key => $option)
		{
			unset($this->activeOptions[$key]['optionValue']);
		}
	}

	/**
	 * Throws a InputException if the domainname is not unique or not valid.
	 * 
	 * @param	string		$domainname
	 */
	protected function validateDomainname($domainname)
	{
		if (empty($domainname))
		{
			throw new UserInputException('domainname');
		}

		if (!DomainUtil :: isValidDomainname($domainname))
		{
			throw new UserInputException('domainname', 'notValid');
		}
		
		if (!DomainUtil :: isAvailableDomainname($domainname, $this->domainID))
		{
			throw new UserInputException('domainname', 'notUnique');
		}
	}
	
	/**
	 * Gets all options and option categories from cache.
	 */
	protected function readCache() {
		// get cache contents
		$cacheName = $this->cacheName;
		WCF::getCache()->addResource($cacheName, CP_DIR.'cache/cache.'.$cacheName.'.php', CP_DIR.'lib/system/cache/'.$this->cacheClass.'.class.php');
		$this->cachedCategories = WCF::getCache()->get($cacheName, 'categories');
		$this->cachedOptions = WCF::getCache()->get($cacheName, 'options');
		$this->cachedCategoryStructure = WCF::getCache()->get($cacheName, 'categoryStructure');
		$this->cachedOptionToCategories = WCF::getCache()->get($cacheName, 'optionToCategories');
		
		// get active options
		$this->loadActiveOptions($this->activeCategory);
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		parent :: readData();
		
		$this->options = $this->getOptionTree();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();
		
		InlineCalendar :: assignVariables();
		
		WCF :: getTPL()->assign(array (
			'domainname' => $this->domainname, 
			'username' => $this->username,
			'adminname' => $this->adminname,
			'registrationDateDay' => $this->registrationDateDay,
			'registrationDateMonth' => $this->registrationDateMonth,
			'registrationDateYear' => $this->registrationDateYear,
			'options' => $this->options, 
			'action' => 'add',
			'activeTabMenuItem' 	=> $this->activeTabMenuItem,
			'activeSubTabMenuItem' 	=> $this->activeSubTabMenuItem
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		// set active menu item
		WCFACP :: getMenu()->setActiveMenuItem($this->menuItemName);
		
		// check permission
		WCF :: getUser()->checkPermission($this->permission);
		
		// get domain options and categories from cache
		$this->readCache();
		
		// show form
		parent :: show();
	}

	/**
	 * @see DynamicOptionListForm::getOptionTree()
	 */
	protected function getOptionTree($parentCategoryName = '', $level = 0)
	{
		$options = array ();
		
		if (isset($this->cachedCategoryStructure[$parentCategoryName]))
		{
			// get super categories
			foreach ($this->cachedCategoryStructure[$parentCategoryName] as $superCategoryName)
			{
				$superCategory = $this->cachedCategories[$superCategoryName];
				$superCategory['options'] = array ();
				
				if ($this->checkCategory($superCategory))
				{
					if ($level <= 0)
					{
						$superCategory['categories'] = $this->getOptionTree($superCategoryName, $level + 1);
					}
					if ($level > 0 || count($superCategory['categories']) == 0)
					{
						$superCategory['options'] = $this->getCategoryOptions($superCategoryName);
					}
					
					if ((isset($superCategory['categories']) && count($superCategory['categories']) > 0) || (isset($superCategory['options']) && count($superCategory['options']) > 0))
					{
						$options[] = $superCategory;
					}
				}
			}
		}
		
		return $options;
	}
}
?>