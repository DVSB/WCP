<?php
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

class StyleFormAdvancedPageMenuListener implements EventListener {
	protected $pageMenuVariables = array(
								'menu.advancedPageMenu.padding' => 1,
								'menu.advancedPageMenu.padding.unit' => 'px',
								'menu.advancedPageMenu.item.padding' => 3,
								'menu.advancedPageMenu.item.padding.unit' => 'px',
								'menu.advancedPageMenu.shadow.color' => '#888888',
								'menu.advancedPageMenu.shadow.size' => 2,
								'menu.advancedPageMenu.shadow.size.unit' => 'px',
								'menu.advancedPageMenu.shadow.offset' => 3,
								'menu.advancedPageMenu.shadow.offset.unit' => 'px'
								
			);
	
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if ($eventName == 'assignVariables') {
			
			WCF::getTPL()->assign(array('variables' => array_merge($this->pageMenuVariables, $eventObj->variables),
										'units' => $eventObj->units));
			WCF::getTPL()->append('additionalMenuTabSubTabs', '<li id="menuTab-advancedPageMenu"><a onclick="tabMenu.showTabMenuContent(\'menuTab-advancedPageMenu\');"><span>'.WCF::getLanguage()->get('wcf.acp.style.editor.menu.advancedPageMenu').'</span></a></li>');
			WCF::getTPL()->append('additionalTabContents', WCF::getTPL()->fetch('advancedPageMenuStyleTab'));
		}
		else if ($eventName == 'validate') {						
			foreach ($this->pageMenuVariables as $name => &$value) {				
				if (isset($eventObj->variables[$name])) $value = $eventObj->variables[$name]; 
			}
		}
		else if ($eventName == 'save') {						
			$eventObj->variables = array_merge($eventObj->variables, $this->pageMenuVariables);			
		}
	}
}
?>