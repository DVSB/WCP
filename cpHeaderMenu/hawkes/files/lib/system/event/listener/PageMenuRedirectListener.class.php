<?php
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

class PageMenuRedirectListener implements EventListener {		
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if ($className == 'PageMenuItemListPage') {
			HeaderUtil::redirect('index.php?page=AdvancedPageMenuItemList&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		}
		else if ($className == 'PageMenuItemAddForm') {
			HeaderUtil::redirect('index.php?form=AdvancedPageMenuItemAdd&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		}
	}
}
?>