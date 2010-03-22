<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/action/PageMenuItemSortAction.class.php');
require_once(WCF_DIR.'lib/data/page/menu/AdvancedPageMenuItemEditor.class.php');

class AdvancedPageMenuItemSortAction extends PageMenuItemSortAction {	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		AbstractAction::execute();
		
		// check permissions
		WCF::getUser()->checkPermission('admin.pageMenu.canEditPageMenuItem');
		
		// update positions
		foreach ($this->headerPositions as $menuItemID => $data) {
			foreach ($data as $parentMenuItem => $showOrder) {								
				AdvancedPageMenuItemEditor::updateShowOrder(intval($menuItemID), intval($parentMenuItem), 'header', $showOrder);
			}
		}
		foreach ($this->footerPositions as $menuItemID => $showOrder) {				
			AdvancedPageMenuItemEditor::updateShowOrder(intval($menuItemID), 0, 'footer', $showOrder);			
		}
		
		// delete cache
		AdvancedPageMenuItemEditor::clearCache();
		$this->executed();
		
		// forward to list page
		header('Location: index.php?page=AdvancedPageMenuItemList&successfullSorting=1&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>