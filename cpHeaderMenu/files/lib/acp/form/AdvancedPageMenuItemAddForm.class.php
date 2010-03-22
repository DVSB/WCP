<?php
require_once(WCF_DIR.'lib/acp/form/PageMenuItemAddForm.class.php');
require_once(WCF_DIR.'lib/page/util/menu/AdvancedPageMenu.class.php');
require_once(WCF_DIR.'lib/data/page/menu/AdvancedPageMenuItemEditor.class.php');

class AdvancedPageMenuItemAddForm extends PageMenuItemAddForm {
	// system
	public $templateName = 'advancedPageMenuItemAdd';
	public $activeTabMenuItem = 'data';

	// properties	
	public $parentMenuItem = '';
	public $permissions = array();
	public $groupIDs = array();

	// form options	
	public $menuItemSelect = array();
	public $groupSelect = array();

	// cache data
	public $cacheName = 'group-option-';
	public $cacheClass = 'CacheBuilderOption';

	// cache content
	public $cachedCategories = array();
	public $cachedOptions = array();
	public $cachedCategoryStructure = array();
	public $cachedOptionToCategories = array();

	// form parameters
	public $values = array();

	/**
	 * Name of the active option category.
	 *
	 * @var string
	 */
	public $activeCategory = '';

	/**
	 * Options of the active category.
	 *
	 * @var array
	 */
	public $activeOptions = array();

	/**
	 * Type object cache.
	 *
	 * @var array
	 */
	public $typeObjects = array();

	/**
	 * @see Page::readParameters()	 
	 */
	public function readParameters() {
		parent::readParameters();
		
		// read preselect parameters
		if (isset($_GET['parentMenuItem'])) $this->parentMenuItem = StringUtil::trim($_GET['parentMenuItem']);
	}
	
	/**	 
	 * @see Form::readParameters
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['parentMenuItem'])) $this->parentMenuItem = StringUtil::trim($_POST['parentMenuItem']);
		$pageMenu = AdvancedPageMenu::getInstance();
		$pageMenu->getMenuItems();
		if (($this->parentMenuItem != 'header' || $this->parentMenuItem != 'footer') && isset($pageMenu->menuItemList[$this->parentMenuItem])) {
			$this->position = $pageMenu->menuItemList[$this->parentMenuItem]['menuPosition'];
		}
		else {
			$this->position = $this->parentMenuItem;
			$this->parentMenuItem = '';
		}
		if (isset($_POST['groupIDs']) && is_array($_POST['groupIDs'])) $this->groupIDs = ArrayUtil::toIntegerArray($_POST['groupIDs']);
		if (isset($_POST['permissions']) && is_array($_POST['permissions'])) $this->permissions = ArrayUtil::trim($_POST['permissions']);	
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		$this->groupSelect = Group::getAccessibleGroups();
		$this->makeMenuItemSelect('header');
		$this->menuItemSelect['footer'] = WCF::getLanguage()->get('wcf.acp.pageMenuItem.position.footer');
	}

	/**
	 * Creates the select array for the parent menu items
	 * 
	 * @param 	string	$position
	 * @param 	string	$parentMenuItem
	 * @param 	integer	$depth
	 * @return 	void
	 */
	public function makeMenuItemSelect($position = 'header', $parentMenuItem = '', $depth = -1) {
		$pageMenu = AdvancedPageMenu::getInstance();
		if ($depth == -1) {
			$this->menuItemSelect[$position] = WCF::getLanguage()->get('wcf.acp.pageMenuItem.position.'.$position);
			$this->makeMenuItemSelect($position, $parentMenuItem, $depth + 1);
		}
		else {
			foreach($pageMenu->getMenuItems($parentMenuItem, $position) as $item) {
				$title = WCF::getLanguage()->get(StringUtil::encodeHTML($item['menuItem']));
				if ($depth >= 0) $title = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth + 1). ' ' . $title;
				$this->menuItemSelect[$item['menuItem']] = $title;
				if(count($pageMenu->getMenuItems($item['menuItem'], $position))) {
					$this->makeMenuItemSelect($position, $item['menuItem'], $depth + 1);
				}
			}
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'options' => $this->getOptionTree(),
			'permissions' => $this->permissions,
			'menuItemSelect' => $this->menuItemSelect,
			'parentMenuItem' => $this->parentMenuItem,
			'groupSelect' => $this->groupSelect,
			'groupIDs' => $this->groupIDs,
			'activeTabMenuItem' => $this->activeTabMenuItem
		));
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		ACPForm::save();
		
		$this->pageMenuItem = AdvancedPageMenuItemEditor::create($this->name, $this->link, $this->iconS, $this->iconM, $this->parentMenuItem, implode(',', $this->permissions), implode(',', $this->groupIDs), $this->showOrder, $this->position, WCF::getLanguage()->getLanguageID());
		
		// reset values
		$this->name = $this->link = $this->iconS = $this->iconM = $this->parentMenuItem = '';
		$this->position = 'header';
		$this->showOrder = 0;
		$this->permissions = $this->groupIDs = array();
		
		// delete cache
		AdvancedPageMenuItemEditor::clearCache();
		$this->saved();
		
		// show success message
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		$this->readCache();

		parent::show();
	}

	/**
	 * Gets all options and option categories from cache.
	 */
	protected function readCache() {
		// get cache contents
		$cacheName = $this->cacheName.PACKAGE_ID;
		WCF::getCache()->addResource($cacheName, WCF_DIR.'cache/cache.'.$cacheName.'.php', WCF_DIR.'lib/system/cache/'.$this->cacheClass.'.class.php');
		$this->cachedCategories = WCF::getCache()->get($cacheName, 'categories');
		$this->cachedOptions = WCF::getCache()->get($cacheName, 'options');
		$this->cachedCategoryStructure = WCF::getCache()->get($cacheName, 'categoryStructure');
		$this->cachedOptionToCategories = WCF::getCache()->get($cacheName, 'optionToCategories');

		// get active options
		$this->loadActiveOptions($this->activeCategory);
	}

	/**
	 * Creates a list of all active options.
	 *
	 * @param	string		$parentCategoryName
	 */
	protected function loadActiveOptions($parentCategoryName) {
		if (!isset($this->cachedCategories[$parentCategoryName]) || $this->checkCategory($this->cachedCategories[$parentCategoryName])) {
			if (isset($this->cachedOptionToCategories[$parentCategoryName])) {
				foreach ($this->cachedOptionToCategories[$parentCategoryName] as $optionName) {
					if (!$this->checkOption($optionName)) continue;
					$this->activeOptions[$optionName] =& $this->cachedOptions[$optionName];
				}
			}
			if (isset($this->cachedCategoryStructure[$parentCategoryName])) {
				foreach ($this->cachedCategoryStructure[$parentCategoryName] as $categoryName) {
					$this->loadActiveOptions($categoryName);
				}
			}
		}
	}

	/**
	 * Returns an object of the requested option type.
	 *
	 * @param	string			$type
	 * @return	OptionType
	 */
	protected function getTypeObject($type) {
		if (!isset($this->typeObjects[$type])) {
			$className = 'OptionType'.ucfirst(strtolower($type));
			$classPath = WCF_DIR.'lib/acp/option/'.$className.'.class.php';
				
			// include class file
			if (!file_exists($classPath)) {
				throw new SystemException("unable to find class file '".$classPath."'", 11000);
			}
			require_once($classPath);
				
			// create instance
			if (!class_exists($className)) {
				throw new SystemException("unable to find class '".$className."'", 11001);
			}
			$this->typeObjects[$type] = new $className();
		}

		return $this->typeObjects[$type];
	}

	/**
	 * Returns the tree of options.
	 *
	 * @param	string		$parentCategoryName
	 * @return	array
	 */
	protected function getOptionTree($parentCategoryName = '') {
		$options = array();

		if (isset($this->cachedCategoryStructure[$parentCategoryName])) {
			// get super categories
			foreach ($this->cachedCategoryStructure[$parentCategoryName] as $superCategoryName) {
				$superCategory = $this->cachedCategories[$superCategoryName];
				if ($this->checkCategory($superCategory)) {
					$superCategory['options'] = $this->getCategoryOptions($superCategoryName);
						
					if (count($superCategory['options']) > 0) {
						$options[] = $superCategory;
					}
				}
			}
		}

		return $options;
	}

	/**
	 * Checks the required permissions and options of a category.
	 *
	 * @param	array		$category
	 * @return	boolean
	 */
	protected function checkCategory($category) {
		if (!empty($category['permissions'])) {
			$hasPermission = false;
			$permissions = explode(',', $category['permissions']);
			foreach ($permissions as $permission) {
				if (WCF::getUser()->getPermission($permission)) {
					$hasPermission = true;
					break;
				}
			}
				
			if (!$hasPermission) return false;
				
		}
		if (!empty($category['options'])) {
			$hasEnabledOption = false;
			$options = explode(',', strtoupper($category['options']));
			foreach ($options as $option) {
				if (defined($option) && constant($option)) {
					$hasEnabledOption = true;
					break;
				}
			}
				
			if (!$hasEnabledOption) return false;
		}

		return true;
	}

	/**
	 * Returns a list with the options of a specific option category.
	 *
	 * @param	string		$categoryName
	 * @param	boolean		$inherit
	 * @return	array
	 */
	protected function getCategoryOptions($categoryName = '', $inherit = true) {
		$children = array();

		// get sub categories
		if ($inherit && isset($this->cachedCategoryStructure[$categoryName])) {
			foreach ($this->cachedCategoryStructure[$categoryName] as $subCategoryName) {
				$children = array_merge($children, $this->getCategoryOptions($subCategoryName));
			}
		}

		// get options
		if (isset($this->cachedOptionToCategories[$categoryName])) {
			$i = 0;
			$last = count($this->cachedOptionToCategories[$categoryName]) - 1;
			foreach ($this->cachedOptionToCategories[$categoryName] as $optionName) {
				if (!$this->checkOption($optionName) || !isset($this->activeOptions[$optionName])) continue;

				// get option data
				$option = $this->activeOptions[$optionName];

				// add option to list
				if($option['optionType'] == 'boolean') {
					$children[] = $option;
				}

				$i++;
			}
		}

		return $children;
	}

	/**
	 * @see OptionType::getFormElement()
	 */
	protected function getFormElement($type, &$optionData) {
		return $this->getTypeObject($type)->getFormElement($optionData);
	}

	/**
	 * Filters displayed options by specific parameters.
	 *
	 * @param	string		$optionName
	 * @return	boolean
	 */
	protected function checkOption($optionName) {
		$optionData = $this->cachedOptions[$optionName];

		if (!empty($optionData['permissions'])) {
			$hasPermission = false;
			$permissions = explode(',', $optionData['permissions']);
			foreach ($permissions as $permission) {
				if (WCF::getUser()->getPermission($permission)) {
					$hasPermission = true;
					break;
				}
			}
				
			if (!$hasPermission) return false;
				
		}
		if (!empty($optionData['options'])) {
			$hasEnabledOption = false;
			$options = explode(',', strtoupper($optionData['options']));
			foreach ($options as $option) {
				if (defined($option) && constant($option)) {
					$hasEnabledOption = true;
					break;
				}
			}
				
			if (!$hasEnabledOption) return false;
		}

		return true;
	}
}
?>