<?php
require_once(WCF_DIR.'lib/system/exception/SystemException.class.php');
require_once(WCF_DIR.'lib/system/template/TemplatePluginFunction.class.php');
require_once(WCF_DIR.'lib/system/template/Template.class.php');
require_once(WCF_DIR.'lib/page/util/menu/AdvancedPageMenu.class.php');

/**
 * Builds the javascript menu items
 *
 * @package	net.hawkes.advancedheadermenu
 * @author	Oliver Kliebisch
 * @copyright	2008 Oliver Kliebisch
 * @license	Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 Unported <http://creativecommons.org/licenses/by-nc-nd/3.0/>
 */



class TemplatePluginFunctionAdvancedmenu implements TemplatePluginFunction {
	protected $output = '';
	protected $position = 'header';
	protected $menuSet = 'ms';

	/**
	 * @see TemplatePluginFunction::execute()
	 */
	public function execute($tagArgs, Template $tplObj) {
		$pageMenu = AdvancedPageMenu::getInstance();
		if (isset($tagArgs['position'])) {
			$this->position = $tagArgs['position'];
		}
		if (isset($tagArgs['menuset'])) {
			$this->menuSet = $tagArgs['menuset'];
		}
		$mainMenus = $pageMenu->getMenuItems('', $this->position);
		foreach($mainMenus as $menu) {
			if(count($pageMenu->getMenuItems($menu['menuItem'], $this->position)) > 0) {
				$this->output.="var menu".$menu['menuItemID']." = ".$this->menuSet.".addMenu(document.getElementById('mainMenuItem".$menu['menuItemID']."'));\r\n";
				$count=0;
				foreach($pageMenu->getMenuItems($menu['menuItem'], $this->position) as $subItem) {
					$this->output.="menu".$menu['menuItemID'].".addItem(\"".$this->getIcon($subItem)."&nbsp;".$this->encodeJS(WCF::getLanguage()->getDynamicVariable(StringUtil::encodeHTML($subItem['menuItem'])))."\", \"".$subItem['menuItemLink']."\");\r\n";
					if(count($pageMenu->getMenuItems($subItem['menuItem'], $this->position))) {
						$this->addSubMenu($menu, $subItem, $count);
					}
					$count++;
				}
			}
		}
		return $this->output;
	}

	protected function addSubMenu($menu, $subMenu, $index, $inRecursion = false) {
		$pageMenu = AdvancedPageMenu::getInstance();
		$this->output.="var submenu".$subMenu['menuItemID']." = ".($inRecursion ? "sub" : "")."menu".$menu['menuItemID'].".addMenu(".($inRecursion ? "sub" : "")."menu".$menu['menuItemID'].".items[".$index."]);\r\n";
		$count=0;
		foreach($pageMenu->getMenuItems($subMenu['menuItem'], $this->position) as $subItem) {
			$this->output.="submenu".$subMenu['menuItemID'].".addItem(\"".$this->getIcon($subItem)."&nbsp;".$this->encodeJS(WCF::getLanguage()->getDynamicVariable(StringUtil::encodeHTML($subItem['menuItem'])))."\", \"".$subItem['menuItemLink']."\");\r\n";
			if(count($pageMenu->getMenuItems($subItem['menuItem'], $this->position))) {
				$this->addSubMenu($subMenu, $subItem, $count, true);
			}
			$count++;
		}
	}

	protected function getIcon($item) {
		if(!empty($item['menuItemIconM'])) {
			$output = "<img src='".$item['menuItemIconM']."' alt=''/>";
			return $output;
		}
		else {
			$output = "<img src='".StyleManager::getStyle()->getIconPath('menuSpacer.png')." alt=''/>";
			return $output;
		}
	}

	protected function encodeJS($text) {
		$text = StringUtil::replace("\\", "\\\\", $text);
		$text = StringUtil::replace('"', "'", $text);
		$text = StringUtil::replace("\n", '\n', $text);

		return $text;
	}
}
?>