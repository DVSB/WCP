<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/form/ACPForm.class.php');
require_once(WCF_DIR.'lib/system/io/Tar.class.php');
require_once(WCF_DIR.'lib/data/page/menu/AdvancedPageMenuItemEditor.class.php');

/**
 * A form for importing page menu items
 *
 * @package		net.hawkes.advancedpagemenu
 * @author		Oliver Kliebisch
 * @copyright	2008-2009 Oliver Kliebisch 
 * @subpackage	acp.form
 * @category	WCF
 */
class AdvancedPageMenuImportForm extends ACPForm {
	public $neededPermissions = 'admin.pageMenu.canAddPageMenuItem';
	public $templateName = 'advancedPageMenuImport';	
	public $activeMenuItem = 'wcf.acp.menu.link.pageMenuItem.import';
	
	/**
	 * The uploaded import file
	 * 
	 * @var ressource
	 */
	public $file;	

	/**
	 * An array containing the data of all items which are about to be imported
	 * 
	 * @var array<mixed>
	 */
	public $itemData = array();
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_FILES['file'])) $this->file = $_FILES['file'];		
	} 		
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();

		// upload
		if ($this->file && $this->file['error'] != 4) {
			if ($this->file['error'] != 0) {
				throw new UserInputException('file', 'uploadFailed');
			}		
		
			$fileName = $_FILES['file']['tmp_name'];			
			$newName = TMP_DIR.$_FILES['file']['name'];			
			
			if (!move_uploaded_file($fileName, $newName)) {
				throw new UserInputException('file');	
			}			
			$fileName = $newName;
			$tar = new Tar($fileName);						
			
			// start handling the box data
			$xml = new XML();
			try {
				$xml->loadString($tar->extractToString('advancedHeaderMenu.xml'));
			}
			catch (Exception $e) { // bugfix to avoid file caching problems
				$xml->loadString($tar->extractToString('advancedHeaderMenu.xml'));
			}
			$tree = $xml->getElementTree('pagemenuitem');			
			foreach ($tree['children'] as $item) {
				$data = array();								
				foreach($item['children'] as $variables) {						
					foreach($variables['children'] as $variable) {							
						$data[$variables['attrs']['name']][$variable['name']] = $variable['cdata'];
					}																					
				}
				$this->itemData = $data;
			}
			
			// get languages
			foreach ($tar->getContentList() as $index => $file) {
				$languageXML = new XML();
				if ($file['filename'] == 'advancedHeaderMenu.xml' || StringUtil::indexOf($file['filename'], '.xml') == -1) {
					continue;
				}
				try {
					$languageXML->loadString($tar->extractToString($file['filename']));
				}
				catch (Exception $e) { // bugfix to avoid file caching problems
					$languageXML->loadString($tar->extractToString($file['filename']));
				}
				
				$languageCode = LanguageEditor::readLanguageCodeFromXML($languageXML);				
				
				$languageEditor = LanguageEditor::getLanguageByCode($languageCode);
				
				if ($languageEditor === null) {
					continue;
				}
				
				try {
					$languageEditor->updateFromXML($languageXML, PACKAGE_ID);
				}
				catch (Exception $e) {}
			}
		}
	}
	
	/**
	 * @see Form::save()	 
	 */
	public function save() {
		parent::save();				
		
		// find existing items
		$sql = "SELECT menu_item.menuItem, menu_item.menuItemID FROM wcf".WCF_N."_package_dependency package_dependency,
												wcf".WCF_N."_page_menu_item menu_item						
				WHERE menuItem IN('".implode("','", array_map('escapeString', array_keys($this->itemData)))."')
				AND	  menu_item.packageID = package_dependency.dependency
				AND   package_dependency.packageID = ".PACKAGE_ID."	
				ORDER BY 	package_dependency.priority ASC";
		$result = WCF::getDB()->sendQuery($sql);
		
		while ($row = WCF::getDB()->fetchArray($result)) {
			$item = $this->itemData[$row['menuItem']];
			$editor = new AdvancedPageMenuItemEditor($row['menuItemID']);
			$this->keysToLowerCase($item);
			if (!isset($item['iconsmall'])) $item['iconsmall'] = $editor->menuItemIconS;
			if (!isset($item['iconmedium'])) $item['iconmedium'] = $editor->menuItemIconM;
			if (!isset($item['parent'])) $item['parent'] = $editor->parentMenuItem;
			if (!isset($item['permissions'])) $item['permissions'] = $editor->permissions;
			if (!isset($item['groupids'])) $item['groupids'] = $editor->groupIDs;
			if (!isset($item['showorder'])) $item['showorder'] = $editor->showOrder;
			if (!isset($item['position'])) $item['position'] = $editor->position;
			$editor->update($row['menuItem'], $item['link'], $item['iconsmall'], $item['iconmedium'], $item['parent'], $item['permissions'], $item['groupids'], $item['showorder'], $item['position'], 0);
			unset($this->itemData[$row['menuItem']]);
		}
		
		foreach ($this->itemData as $menuItem => $item) {
			$this->keysToLowerCase($item);			
			if (!isset($item['iconsmall'])) $item['iconsmall'] = '';
			if (!isset($item['iconmedium'])) $item['iconmedium'] = '';
			if (!isset($item['parent'])) $item['parent'] = '';
			if (!isset($item['permissions'])) $item['permissions'] = '';
			if (!isset($item['groupids'])) $item['groupids'] = '';
			if (!isset($item['showorder'])) $item['showorder'] = 0;
			if (!isset($item['position'])) $item['position'] = '';
			AdvancedPageMenuItemEditor::create($menuItem, $item['link'], $item['iconsmall'], $item['iconmedium'], $item['parent'], $item['permissions'], $item['groupids'], $item['showorder'], $item['position'], 0, 0);
		}
		AdvancedPageMenuItemEditor::clearCache();
		
		$this->saved();						
		WCF::getTPL()->assign('success', true);
	}
		
	/**
	 * Makes the keys of an array to lower case. 
	 * 
	 * @param	array	$array	
	 */
	protected function keysToLowerCase(&$array) {
		$originalKeys = array_keys($array);
		foreach ($originalKeys as $originalKey) {
			if (!isset($array[strtolower($originalKey)])) {
				$array[strtolower($originalKey)] = $array[$originalKey];
				unset($array[$originalKey]);							
			}
		}
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// check master password
		WCFACP::checkMasterPassword();
		
		parent::show();
	}
}
?>