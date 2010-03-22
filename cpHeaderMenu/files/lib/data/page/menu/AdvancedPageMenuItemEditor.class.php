<?php
require_once(WCF_DIR.'lib/data/page/menu/PageMenuItemEditor.class.php');

class AdvancedPageMenuItemEditor extends PageMenuItemEditor {
	/**
	 * Creates a new page menu item.
	 * 
	 * @param 	string		$name
	 * @param 	string		$link
	 * @param 	string		$iconS
	 * @param 	string		$iconM
	 * @param	string		$parent
	 * @param	string		$permissions
	 * @param	string		$groupIDs
	 * @param 	integer		$showOrder
	 * @param	string		$position
	 * @param	integer		$languageID
	 * @param	integer		$native
	 * @param	integer		$packageID	 
	 * @return	AdvancedPageMenuItemEditor
	 */	
	public static function create($name, $link, $iconS = '', $iconM = '', $parent = '', $permissions = '', $groupIDs = '', $showOrder = 0, $position = 'header', $languageID = 0, $native = 1, $packageID = PACKAGE_ID) {				
		$affectableItemIDs = self::getAffectableItemIDs($packageID);
		// get show order
		if ($showOrder == 0) {
			// get next number in row
			$sql = "SELECT	MAX(showOrder) AS showOrder
				FROM	wcf".WCF_N."_page_menu_item
				WHERE	menuPosition = '".escapeString($position)."'
						AND parentMenuItem = '".escapeString($parent)."'
						AND menuItemID IN(".$affectableItemIDs.")";
			$row = WCF::getDB()->getFirstRow($sql);
			if (!empty($row)) $showOrder = intval($row['showOrder']) + 1;
			else $showOrder = 1;
		}
		else {
			$sql = "UPDATE	wcf".WCF_N."_page_menu_item
				SET 	showOrder = showOrder + 1
				WHERE 	showOrder >= ".$showOrder."
					AND menuPosition = '".escapeString($position)."'
					AND	parentMenuItem = '".escapeString($parent)."'
					AND menuItemID IN(".$affectableItemIDs.")";
			WCF::getDB()->sendQuery($sql);
		}
		// get menu item name
		$menuItem = '';
		if ($languageID == 0) $menuItem = $name;
		
		// save
		$sql = "INSERT INTO	wcf".WCF_N."_page_menu_item
					(packageID, menuItem, menuItemLink, menuItemIconS, menuItemIconM, parentMenuItem, permissions, groupIDs, menuPosition, showOrder, native)
			VALUES		(".$packageID.", '".escapeString($menuItem)."', '".escapeString($link)."', '".escapeString($iconS)."', '".escapeString($iconM)."', '".escapeString($parent)."', '".escapeString($permissions)."', '".escapeString($groupIDs)."', '".escapeString($position)."', ".$showOrder.", ".$native.")";
		WCF::getDB()->sendQuery($sql);
		
		// get item id
		$menuItemID = WCF::getDB()->getInsertID("wcf".WCF_N."_page_menu_item", 'menuItemID');
		
		if ($languageID != 0) {
			// set name
			$menuItem = "wcf.header.menu.pageMenuItem".$menuItemID;
			$sql = "UPDATE	wcf".WCF_N."_page_menu_item
				SET	menuItem = '".escapeString($menuItem)."'
				WHERE 	menuItemID = ".$menuItemID;
			WCF::getDB()->sendQuery($sql);
			
			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array($menuItem => $name));
		}
		
		$item = new AdvancedPageMenuItemEditor($menuItemID);
		
		return $item;
	}	

	/**
	 * Updates this page menu item.
	 * 
	 * @param 	string		$name
	 * @param 	string		$link
	 * @param 	string		$iconS
	 * @param 	string		$iconM
	 * @param	string		$parent
	 * @param	string		$permissions
	 * @param	string		$groupIDs
	 * @param 	integer		$showOrder
	 * @param	string		$position
	 * @param	integer		$languageID
	 */	
	public function update($name, $link, $iconS = '', $iconM = '', $parent = '', $permissions = '', $groupIDs = '', $showOrder = 0, $position = 'header', $languageID = 0) {
		$affectableItemIDs = self::getAffectableItemIDs();
		if ($position == $this->menuPosition) {
			if ($this->showOrder != $showOrder) {
				if ($showOrder < $this->showOrder) {
					$sql = "UPDATE	wcf".WCF_N."_page_menu_item
						SET 	showOrder = showOrder + 1
						WHERE 	showOrder >= ".$showOrder."
							AND showOrder < ".$this->showOrder."
							AND menuPosition = '".escapeString($position)."'
							AND parentMenuItem = '".escapeString($parent)."'
							AND menuItemID IN(".$affectableItemIDs.")";
					WCF::getDB()->sendQuery($sql);
				}
				else if ($showOrder > $this->showOrder) {
					$sql = "UPDATE	wcf".WCF_N."_page_menu_item
						SET	showOrder = showOrder - 1
						WHERE	showOrder <= ".$showOrder."
							AND showOrder > ".$this->showOrder."
							AND menuPosition = '".escapeString($position)."'
							AND parentMenuItem = '".escapeString($parent)."'
							AND menuItemID IN(".$affectableItemIDs.")";
					WCF::getDB()->sendQuery($sql);
				}
			}
		}
		else {
			$sql = "UPDATE	wcf".WCF_N."_page_menu_item
				SET 	showOrder = showOrder - 1
				WHERE 	showOrder >= ".$this->showOrder."
					AND menuPosition = '".escapeString($this->menuPosition)."'					
					AND parentMenuItem = '".escapeString($parent)."'
					AND menuItemID IN(".$affectableItemIDs.")";
			WCF::getDB()->sendQuery($sql);
				
			$sql = "UPDATE 	wcf".WCF_N."_page_menu_item
				SET 	showOrder = showOrder + 1
				WHERE 	showOrder >= ".$showOrder."
					AND menuPosition = '".escapeString($position)."'
					AND parentMenuItem = '".escapeString($parent)."'
					AND menuItemID IN(".$affectableItemIDs.")";
			WCF::getDB()->sendQuery($sql);
		}
		
		// If the new parent menu item has this item as a parent link it to the next upper node
		$sql = "SELECT menuItemID, parentMenuItem FROM wcf".WCF_N."_page_menu_item
					WHERE menuItem = '".escapeString($parent)."'
					AND menuItemID IN(".$affectableItemIDs.")";
		$row = WCF::getDB()->getFirstRow($sql);
		if ($row && (($languageID == 0 && $name == $row['parentMenuItem']) || ($languageID != 0 && $this->menuItem == $row['parentMenuItem']))) {
			$sql = "UPDATE wcf".WCF_N."_page_menu_item
						SET parentMenuItem = '".escapeString($this->parentMenuItem)."'
						WHERE menuItemID = ".$row['menuItemID'];
			WCF::getDB()->sendQuery($sql);
		} 
		
		// Update
		$sql = "UPDATE	wcf".WCF_N."_page_menu_item
			SET	".($languageID == 0 ? "menuItem = '".escapeString($name)."'," : '')."
				menuItemlink	= '".escapeString($link)."',
				menuItemIconS 	= '".escapeString($iconS)."',
				menuItemIconM 	= '".escapeString($iconM)."',
				parentMenuItem	= '".escapeString($parent)."',
				permissions		= '".escapeString($permissions)."',
				groupIDs		= '".escapeString($groupIDs)."',
				menuPosition	= '".escapeString($position)."',
				showOrder 	= ".$showOrder."
			WHERE 	menuItemID 	= ".$this->menuItemID;
		WCF::getDB()->sendQuery($sql);
		
		if ($languageID != 0) {
			// save language variables
			$language = new LanguageEditor($languageID);
			$language->updateItems(array($this->menuItem => $name), 0, PACKAGE_ID, array($this->menuItem => 1));
		}
		
		// relink children if the element name changed
		if ($this->menuItem != $name && $languageID == 0) {
			$sql = "UPDATE	wcf".WCF_N."_page_menu_item
				SET	parentMenuItem = '".escapeString($name)."'
				WHERE parentMenuItem = '".escapeString($this->menuItem)."'
				AND menuItemID IN(".$affectableItemIDs.")";
			WCF::getDB()->sendQuery($sql);
		}
	}

	/**
	 * Deletes this page menu item.
	 */
	public function delete() {
		$affectableItemIDs = self::getAffectableItemIDs();
		// update show order
		$sql = "UPDATE	wcf".WCF_N."_page_menu_item
			SET	showOrder = showOrder - 1
			WHERE	showOrder >= ".$this->showOrder."
				AND menuPosition = '".escapeString($this->menuPosition)."'
				AND	parentMenuItem = '".$this->parentMenuItem."'
				AND menuItemID IN (".$affectableItemIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		// delete
		$sql = "DELETE FROM	wcf".WCF_N."_page_menu_item
			WHERE		menuItemID = ".$this->menuItemID;
		WCF::getDB()->sendQuery($sql);
		
		// 	relink children to the next upper node
		$sql = "UPDATE	wcf".WCF_N."_page_menu_item
			SET	parentMenuItem = '".escapeString($this->parentMenuItem)."'
			WHERE parentMenuItem = '".escapeString($this->menuItem)."'
			AND menuItemID IN (".$affectableItemIDs.")";
		WCF::getDB()->sendQuery($sql);		
			
		// delete language variables
		LanguageEditor::deleteVariable($this->menuItem);
	}		
	
	/**
	 * Updates the positions of a page menu item directly.
	 *
	 * @param	integer		$menuItemID
	 * @param	integer		$parentMenuItemID
	 * @param	string		$position
	 * @param	integer		$showOrder
	 */
	public static function updateShowOrder($menuItemID, $parentMenuItemID = '', $position = 'header', $showOrder = 1) {
		// Update
		$sql = "UPDATE	wcf".WCF_N."_page_menu_item menu_item
			SET	menu_item.showOrder = ".$showOrder.",
				menu_item.menuPosition = '".escapeString($position)."',
				menu_item.parentMenuItem = IFNULL((SELECT menuItem FROM (
									SELECT * FROM wcf".WCF_N."_page_menu_item) AS items
									WHERE menuItemID = ".escapeString($parentMenuItemID)."), '')
			WHERE 	menu_item.menuItemID = ".$menuItemID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Returns all IDs as concatenated string which may be affected by UPDATE queries based on the dependency to
	 * the packageID ($packageID)
	 *  
	 * @param $packageID
	 * @return string
	 */
	public static function getAffectableItemIDs($packageID = PACKAGE_ID) {
		$sql = "SELECT		menuItem, menuItemID 
			FROM		wcf".WCF_N."_page_menu_item menu_item,
					wcf".WCF_N."_package_dependency package_dependency
			WHERE 		menu_item.packageID = package_dependency.dependency
					AND package_dependency.packageID = ".$packageID."
			ORDER BY	package_dependency.priority";
		$result = WCF::getDB()->sendQuery($sql);
		$itemIDs = array();
		while ($row = WCF::getDB()->fetchArray($result)) {
			$itemIDs[$row['menuItem']] = $row['menuItemID'];
		}
		
		return count($itemIDs) ? implode(',', $itemIDs) : '0';
	}
	
	/**
	 * @see PageMenuItemEditor::clearCache()
	 */
	public static function clearCache() {
		parent::clearCache();
		
		WCF::getCache()->clear(WCF_DIR.'cache', 'cache.advancedPageMenu-*.php');
	}
}
?>