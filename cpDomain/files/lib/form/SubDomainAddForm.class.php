<?php
require_once (WCF_DIR . 'lib/acp/form/DynamicOptionListForm.class.php');
require_once (CP_DIR . 'lib/data/user/CPUser.class.php');

/**
 * Shows the subdomain add form.
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	form
 * @category 	Control Panel
 * @id			$Id$
 */
class SubDomainAddForm extends DynamicOptionListForm
{
	public $templateName = 'subDomainAdd';
	public $neededPermissions = ''; //admin.cp.canAddDomain';
	
	public $cacheClass = 'CacheBuilderDomainOption';

	public $cacheName = 'domain-option-';
	public $additionalFields = array();
	public $domain;
	public $options;
	
	public $domainname = '';
	public $domainID = 0;
	
	public $parentDomainID = 0;
	
	public $activeTabMenuItem = '';
	public $activeSubTabMenuItem = '';
	
	public function __construct()
	{
		$this->parentDomains = DomainUtil :: getDomainsForUser(CPCore :: getUser()->userID);
		
		parent :: __construct();
	}

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters()
	{
		parent :: readFormParameters();
		
		if (isset($_POST['domainname']))
			$this->domainname = StringUtil :: trim($_POST['domainname']);
			
		if (isset($_POST['parentDomainID']))
			$this->parentDomainID = StringUtil :: trim($_POST['parentDomainID']);

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
		//concat subdomainpart with parentdomain
		$this->domainname .= '.' . $this->parentDomains[$this->parentDomainID];
		
		try
		{
			$this->validateDomainname($this->domainname);
		}
		catch (UserInputException $e)
		{
			$this->errorType[$e->getField()] = $e->getType();
		}
		
		if ($this->parentDomainID == 0)
			throw new UserInputException('parentDomain', 'empty');
			
		if (!array_key_exists($this->parentDomainID, $this->parentDomains))
			throw new UserInputException('parentDomain', 'notValid');
		
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
		$this->domain = DomainEditor :: create($this->domainname, CPCore :: getUser()->userID, CPCore :: getUser()->adminID, $this->parentDomainID, $this->activeOptions, $this->additionalFields);
		$this->saved();
		
		// show empty add form
		WCF :: getTPL()->assign(array (
			'success' => true, 
			'newDomain' => $this->domain
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
	protected function readCache() 
	{
		// get cache contents
		$cacheName = $this->cacheName;
		WCF::getCache()->addResource($cacheName, CP_DIR . 'cache/cache.'.$cacheName.'.php', CP_DIR . 'lib/system/cache/'.$this->cacheClass.'.class.php');
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
		
		WCF :: getTPL()->assign(array (
			'domainname' => $this->domainname, 
			'options' => $this->options, 
			'domainID' => $this->domainID,
			'parentDomains' => $this->parentDomains,
			'parentDomainID' => $this->parentDomainID,
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
		require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
		PageMenu :: setActiveMenuItem('cp.header.menu.domain');
		
		if (!WCF::getUser()->userID) 
		{
			throw new PermissionDeniedException();
		}
		
		if (WCF :: getUser()->subdomains <= WCF :: getUser()->subdomainsUsed)
		{
			require_once(WCF_DIR.'lib/system/exception/PermissionDeniedException.class.php');
			throw new PermissionDeniedException();
		}
		
		// check permission
		if (!empty($this->neededPermissions)) WCF :: getUser()->checkPermission($this->neededPermissions);
		
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