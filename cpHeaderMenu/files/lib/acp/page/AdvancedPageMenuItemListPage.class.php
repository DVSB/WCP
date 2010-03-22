<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/page/PageMenuItemListPage.class.php');

class AdvancedPageMenuItemListPage extends PageMenuItemListPage {
	// system
	public $templateName = 'advancedPageMenuItemList';					
	
	/**
	 * list of header menu items unorganized
	 * 
	 * @var array<PageMenuItem>
	 */
	public $headerMenuItems = array();
	
	/**
	 * list of footer menu items unorganized
	 * 
	 * @var array<PageMenuItem>
	 */
	public $footerMenuItems = array();
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
	
		$this->makeMenuItemList();
		$this->makeMenuItemList('footer');				
	}
	
	/**
	 * Gets page menu items.
	 */
	protected function readPageMenuItems() {
		$sql = "SELECT		menu_item.*,
					IFNULL((SELECT menuItemID FROM wcf".WCF_N."_page_menu_item
					WHERE menuItem = menu_item.parentMenuItem LIMIT 1), 0) AS parentMenuItemID
			FROM		wcf".WCF_N."_package_dependency package_dependency,
						wcf".WCF_N."_page_menu_item menu_item
			WHERE menu_item.packageID = package_dependency.dependency
			AND   package_dependency.packageID = ".PACKAGE_ID."			
			ORDER BY	parentMenuItemID, showOrder";			
		$headerPosition = $footerPosition = 1;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {			
			if ($row['menuPosition'] == 'header') {				
				if (!isset($this->headerMenuItems[$row['parentMenuItemID']])) {
					$headerPosition = 1;
					$this->headerMenuItems[$row['parentMenuItemID']] = array();
				}
				$row['showOrder'] = $headerPosition;
				$this->headerMenuItems[$row['parentMenuItemID']][] = new PageMenuItem(null, $row);
				$headerPosition++;
			}
			else  {
				if (!isset($this->footerMenuItems[$row['parentMenuItemID']])) {
					$footerPosition = 1;
					$this->footerMenuItems[$row['parentMenuItemID']] = array();
				}
				$row['showOrder'] = $footerPosition;
				$this->footerMenuItems[$row['parentMenuItemID']][] = new PageMenuItem(null, $row);
				$footerPosition++;
			}
		}		
	}
	
	/**
	 * Renders one level of the page item structure.
	 *
	 * @param	string		$position
	 * @param	integer		$parentMenuItemID
	 * @param	integer		$depth
	 * @param	integer		$openParents
	 */
	protected function makeMenuItemList($position = 'header', $parentMenuItemID = 0, $depth = 1, $openParents = 0) {
		$varname = $position.'MenuItems';
		$listname = $position.'MenuItemList';
		
		if (!isset($this->{$varname}[$parentMenuItemID])) return;
		
		$i = 0; $children = count($this->{$varname}[$parentMenuItemID]);		
		foreach ($this->{$varname}[$parentMenuItemID] as $menuItem) {
			$childrenOpenParents = $openParents + 1;			
			$hasChildren = isset($this->{$varname}[$menuItem->menuItemID]);
			$last = $i == count($this->{$varname}[$parentMenuItemID]) - 1;			
			if ($hasChildren && !$last) $childrenOpenParents = 1;
			$this->{$listname}[] = array('depth' => $depth, 'hasChildren' => $hasChildren, 'openParents' => ((!$hasChildren && $last) ? ($openParents) : (0)), 'menuItem' => $menuItem, 'position' => $i + 1, 'maxPosition' => $children, 'parentMenuItemID' => $parentMenuItemID);			
			// make next level of the list
			$this->makeMenuItemList($position, $menuItem->menuItemID, $depth + 1, $childrenOpenParents);
			$i++;
		}
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'headerMenuItemList' => $this->headerMenuItemList,
			'footerMenuItemList' => $this->footerMenuItemList,
			'deletedPageMenuItemID' => $this->deletedPageMenuItemID,
			'successfullSorting' => $this->successfullSorting
		));
	}
	
}
?>