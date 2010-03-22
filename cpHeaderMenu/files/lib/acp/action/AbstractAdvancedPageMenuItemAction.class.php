<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/page/menu/AdvancedPageMenuItemEditor.class.php');

class AbstractAdvancedPageMenuItemAction extends AbstractAction {
	/**
	 * item id
	 * 
	 * @var	integer
	 */
	public $itemID = 0;
	
	/**
	 * item editor object
	 * 
	 * @var	AdvancedPageMenuItemEditor
	 */
	public $item = null;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['pageMenuItemID'])) $this->itemID = intval($_REQUEST['pageMenuItemID']);
		$this->item = new AdvancedPageMenuItemEditor($this->itemID);	
		if (!$this->item->menuItemID) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see AbstractAction::executed()
	 */
	protected function executed() {
		parent::executed();
		
		// forward to list page
		HeaderUtil::redirect('index.php?page=AdvancedPageMenuItemList&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>