<?php
require_once(WCF_DIR.'lib/page/util/menu/TreeMenu.class.php');
require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/event/EventHandler.class.php');

/**
 * Builds the page menu.
 *
 * @author		Marcel Werk
 * @copyright	2001-2009 WoltLab GmbH
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.woltlab.wcf.data.page.headerMenu
 * @subpackage	page.util.menu
 * @category 	Community Framework
 */
class PageMenu extends TreeMenu
{
	protected static $activeMenuItem = '';
	public $menuItems = null;
	
	/**
	 * Loads cached menu items.
	 */
	protected function loadCache() {
		// call loadCache event
		EventHandler::fireAction($this, 'loadCache');
		
		WCF::getCache()->addResource('pageMenu-'.PACKAGE_ID, WCF_DIR.'cache/cache.pageMenu-'.PACKAGE_ID.'.php', WCF_DIR.'lib/system/cache/CacheBuilderPageMenu.class.php');
		$this->menuItems = WCF::getCache()->get('pageMenu-'.PACKAGE_ID);
	}
	
	/**
	 * Builds the menu.
	 */
	protected function buildMenu() {
		// get menu items from cache
		$this->loadCache();
		
		// check item permissions
		$this->checkPermissions();
		
		// check item options
		$this->checkOptions();
		
		// check group allocations
		$this->checkGroups();

		// remove items without children
		$this->removeEmptyItems();
		
		// parse menu items
		$this->parseMenuItems();
		
		// call buildMenu event
		EventHandler::fireAction($this, 'buildMenu');
	}
	
/**
	 * @see TreeMenu::parseMenuItems()
	 */
	protected function parseMenuItems() {
		foreach ($this->menuItems as $parentMenuItem => $items) {
			foreach ($items as $key => $item) {
				// get relative path
				$path = $applicationPath = '';
				if (empty($item['packageDir'])) {
					$path = RELATIVE_WCF_DIR;
				}
				else if ($item['packageID'] != PACKAGE_ID) {
					$path = $applicationPath = FileUtil::getRealPath(RELATIVE_WCF_DIR.$item['packageDir']);
				}
					
				// add path and session id to link
				if (!empty($applicationPath) && !preg_match('~^(?:https?://|/)~', $item['menuItemLink'])) {
					$item['menuItemLink'] = $applicationPath.$item['menuItemLink'];
				}
					
				// append session id
				if (!preg_match('~^https?://~', $item['menuItemLink'])) {
					if (strpos($item['menuItemLink'], '?') !== false) {
						$item['menuItemLink'] .= SID_ARG_2ND_NOT_ENCODED;
					}
					else {
						$item['menuItemLink'] .= SID_ARG_1ST;
					}
				}

				if (!class_exists('WCFACP')) {
					// add path to image link
					if (!empty($item['menuItemIconS'])) {
						$item['menuItemIconS'] = StyleManager::getStyle()->getIconPath($item['menuItemIconS']);
					}
					if (!empty($item['menuItemIconM'])) {
						$item['menuItemIconM'] = StyleManager::getStyle()->getIconPath($item['menuItemIconM']);
					}
				}
					
				// check active menu item
				$item['activeMenuItem'] = ($item['menuItem'] == self::$activeMenuItem);

				$this->menuItems[$parentMenuItem][$key] = $item;
				$this->menuItemList[$item['menuItem']] =& $this->menuItems[$parentMenuItem][$key];
			}
		}
	}
	
	/**	 
	 * @see	TreeMenu::removeEmptyItems()
	 */
	protected function removeEmptyItems($parentMenuItem = '') {
		if (!isset($this->menuItems[$parentMenuItem])) return;
		
		foreach ($this->menuItems[$parentMenuItem] as $key => $item) {
			$this->removeEmptyItems($item['menuItem']);
			if ((empty($item['menuItemLink']) && (!isset($this->menuItems[$item['menuItem']]) || !count($this->menuItems[$item['menuItem']]))) || $item['isDisabled'] == 1) {
				// remove this item
				unset($this->menuItems[$parentMenuItem][$key]);
			}
		}
	}
	
	/**
	 * Checks the group IDs if the menu item.
	 * Removes items not accessible to current user.
	 *
	 * @param	string		$parentMenuItem
	 */
	protected function checkGroups($parentMenuItem = '') {
		if (!isset($this->menuItems[$parentMenuItem]) || class_exists('WCFACP')) return;

		foreach ($this->menuItems[$parentMenuItem] as $key => $item) {
			$hasPermission = true;
			// check the groupIDs of this item for the active user
			if (!empty($item['groupIDs'])){
				$hasPermission = false;
				$groupIDs = explode(',', $item['groupIDs']);
				foreach ($groupIDs as $groupID) {
					if (in_array($groupID, WCF::getUser()->getGroupIDs())) {
						$hasPermission = true;
						break;
					}
				}
			}
				
			if ($hasPermission) {
				// check group allocations of the children
				$this->checkGroups($item['menuItem']);
			}
			else {
				// remove this item
				unset($this->menuItems[$parentMenuItem][$key]);
			}
		}
	}

	/**
	 * @see TreeMenu::parseMenuItemLink()
	 */
	protected function parseMenuItemLink($link, $path) { }
	
	/**
	 * Checks the permissions of the menu items.
	 * Removes items without permission.
	 */
	protected function checkPermissions() {
		foreach ($this->menuItems as $key => $item) {
			$hasPermission = true;
			// check the permission of this item for the active user
			if (!empty($item['permissions'])) {
				$hasPermission = false;
				$permissions = explode(',', $item['permissions']);
				foreach ($permissions as $permission) {
					if (WCF::getUser()->getPermission($permission)) {
						$hasPermission = true;
						break;
					}
				}
			}
			
			if (!$hasPermission) {
				// remove this item
				unset($this->menuItems[$key]);
			}
		}
	}
	
	/**
	 * Checks the options of the menu items.
	 * Removes items of disabled options.
	 */
	protected function checkOptions() {
		foreach ($this->menuItems as $key => $item) {
			$hasEnabledOption = true;
			// check the options of this item
			if (!empty($item['options'])) {
				$hasEnabledOption = false;
				$options = explode(',', strtoupper($item['options']));
				foreach ($options as $option) {
					if (defined($option) && constant($option)) {
						$hasEnabledOption = true;
						break;
					}
				}
			}
			
			if (!$hasEnabledOption) {
				// remove this item
				unset($this->menuItems[$key]);
			}
		}
	}
	
	/**
	 * Sets the active menu item. 
	 * This should be done before the headerMenu.tpl template calls the function getMenu().
	 * 
	 * This function should be used in each script which uses a template that includes the headerMenu.tpl.
	 * 
	 * @param	string		$menuItem	name of the active menu item
	 */
	public static function setActiveMenuItem($menuItem) {
		self::$activeMenuItem = $menuItem;
	}
	
	/**
	 * Returns the list of menu items.
	 * 
	 * @return	array
	 */
	public function getMenuItems($parentMenuItem = null, $position = 'header') {
		if ($this->menuItems === null) {
			$this->buildMenu();
		}
		if ($parentMenuItem === null && !empty($position)) {
			$returnItems = array();
			foreach ($this->menuItems as $parentMenuItem => $items) {
				foreach ($items as $key => $item) {
					if ($item['menuPosition'] == $position) {
						$returnItems[] = $item;
					}
				}
			}
				
			return $returnItems;
		}
		else if ($parentMenuItem !== null && !empty($position)) {
			if (isset($this->menuItems[$parentMenuItem])) {
				$returnItems = array();
				foreach ($this->menuItems[$parentMenuItem] as $key => $item) {
					if ($item['menuPosition'] == $position) {
						$returnItems[] = $item;
					}
				}

				return $returnItems;
			}
				
			return array();
		}

		return $this->menuItems;
	}
}
?>