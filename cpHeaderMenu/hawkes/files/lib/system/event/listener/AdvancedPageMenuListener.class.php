<?php
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');
require_once(WCF_DIR.'lib/page/util/menu/AdvancedPageMenu.class.php');

class AdvancedPageMenuListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		// check if the current application uses the page menu
		if (interface_exists('PageMenuContainer')) {
			$pageMenu = AdvancedPageMenu::getInstance();					
			WCF::getTPL()->assign(array('pageMenu' => $pageMenu));			
		}
	}
}
?>