<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/package/plugin/AbstractXMLPackageInstallationPlugin.class.php');

/**
 * This PIP installs, updates or deletes page page menu items.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2009 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf.data.page.headerMenu
 * @subpackage	acp.package.plugin
 * @category 	Community Framework
 */
class AdvancedPageMenuPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
	public $tagName = 'advancedpagemenu';
	public $tableName = 'page_menu_item';
	
	/**
	 * @see PackageInstallationPlugin::install()
	 */
	public function install() {
		parent::install();
		
		if (!$xml = $this->getXML()) {
			return;
		}
		
		// Create an array with the data blocks (import or delete) from the xml file.
		$headerMenuXML = $xml->getElementTree('data');
		
		// Loop through the array and install or uninstall items.
		foreach ($headerMenuXML['children'] as $key => $block) {
			if (count($block['children'])) {
				// Handle the import instructions
				if ($block['name'] == 'import') {
					// Loop through items and create or update them.
					foreach ($block['children'] as $pageMenuItem) {
						// Extract item properties.
						foreach ($pageMenuItem['children'] as $child) {
							if (!isset($child['cdata'])) continue;
							$pageMenuItem[$child['name']] = $child['cdata'];
						}
					
						// check required attributes
						if (!isset($pageMenuItem['attrs']['name'])) {
							throw new SystemException("Required 'name' attribute for page menu item tag is missing.", 13023);
						}
						
						// default values
						$menuItemLink = $menuItemIconS = $menuItemIconM = $parentMenuItem = $groupIDs = $permissions = $options = '';
						$menuPosition = 'header';
						$showOrder = null;
						
						// get values
						$menuItem = $pageMenuItem['attrs']['name'];
						if (isset($pageMenuItem['link'])) $menuItemLink = $pageMenuItem['link'];
						if (isset($pageMenuItem['icon'])) $menuItemIconM = $pageMenuItem['icon']; // wcf1.0 fallback
						if (isset($pageMenuItem['iconmedium'])) $menuItemIconM = $pageMenuItem['iconmedium'];
						if (isset($pageMenuItem['iconsmall'])) $menuItemIconS = $pageMenuItem['iconsmall'];
						if (isset($pageMenuItem['showorder'])) $showOrder = intval($pageMenuItem['showorder']);
						$showOrder = $this->getShowOrder($showOrder);
						if (isset($pageMenuItem['parentMenuItem'])) $parentMenuItem = $pageMenuItem['parentMenuItem'];
						if (isset($pageMenuItem['permissions'])) $permissions = $pageMenuItem['permissions'];
						if (isset($pageMenuItem['options'])) $options = $pageMenuItem['options'];
						if (isset($pageMenuItem['position'])) $menuPosition = $pageMenuItem['position'];
						
						// Insert or update items. 
						// Update through the mysql "ON DUPLICATE KEY"-syntax. 
						$sql = "INSERT INTO			wcf".WCF_N."_page_menu_item
											(packageID, menuItem, menuItemLink, menuItemIconS, menuItemIconM, showOrder, permissions, parentMenuItem, groupIDs, options, menuPosition)
							VALUES				(".$this->installation->getPackageID().",
											'".escapeString($menuItem)."',
											'".escapeString($menuItemLink)."',
											'".escapeString($menuItemIconS)."',
											'".escapeString($menuItemIconM)."',
											".$showOrder.",
											'".escapeString($permissions)."',
											'".escapeString($parentMenuItem)."',
											'".escapeString($groupIDs)."',
											'".escapeString($options)."',
											'".escapeString($menuPosition)."')
							ON DUPLICATE KEY UPDATE 	menuItemLink = VALUES(menuItemLink),
											menuItemIconS = VALUES(menuItemIconS),
											menuItemIconM = VALUES(menuItemIconM),
											showOrder = VALUES(showOrder),
											permissions = VALUES(permissions),
											parentMenuItem = VALUES(parentMenuItem),
											groupIDs = VALUES(groupIDs),
											options = VALUES(options),
											menuPosition = VALUES(menuPosition)";
						WCF::getDB()->sendQuery($sql);
					}
				}
				// Handle the delete instructions.
				else if ($block['name'] == 'delete') {
					if ($this->installation->getAction() == 'update') {
						// Loop through items and delete them.
						$itemNames = '';
						foreach ($block['children'] as $menuItem) {
							// check required attributes
							if (!isset($menuItem['attrs']['name'])) {
								throw new SystemException("Required 'name' attribute for page menu item tag is missing.", 13023);
							}
							// Create a string with all item names which should be deleted (comma seperated).
							if (!empty($itemNames)) $itemNames .= ',';
							$itemNames .= "'".escapeString($menuItem['attrs']['name'])."'";
						}
						// Delete items.
						if (!empty($itemNames)) {
							$sql = "DELETE FROM	wcf".WCF_N."_page_menu_item
								WHERE		packageID = ".$this->installation->getPackageID()."
										AND menuItem IN (".$itemNames.")";
							WCF::getDB()->sendQuery($sql);
						}
					}
				}
			}
		}
	}
}
?>