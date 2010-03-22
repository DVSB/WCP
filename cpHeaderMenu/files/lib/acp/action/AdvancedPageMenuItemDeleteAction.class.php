<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/action/PageMenuItemDeleteAction.class.php');
require_once(WCF_DIR.'lib/data/page/menu/AdvancedPageMenuItemEditor.class.php');

class AdvancedPageMenuItemDeleteAction extends PageMenuItemDeleteAction {		
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['pageMenuItemID'])) $this->pageMenuItemID = intval($_REQUEST['pageMenuItemID']);
		$this->pageMenuItem = new AdvancedPageMenuItemEditor($this->pageMenuItemID);
		if (!$this->pageMenuItem->menuItemID) {
			require_once(WCF_DIR.'lib/system/exception/IllegalLinkException.class.php');
			throw new IllegalLinkException();
		}
	}
		
	/**
	 * @see Action::executed()
	 */
	public function executed() {
		parent::executed();
		AdvancedPageMenuItemEditor::clearCache();		
		// forward to list page
		header('Location: index.php?page=AdvancedPageMenuItemList&deletedPageMenuItemID='.$this->pageMenuItemID.'&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);	
		exit;
	}
}
?>