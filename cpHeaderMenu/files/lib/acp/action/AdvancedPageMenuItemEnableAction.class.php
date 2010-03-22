<?php
require_once(WCF_DIR.'lib/acp/action/AbstractAdvancedPageMenuItemAction.class.php');

class AdvancedPageMenuItemEnableAction extends AbstractAdvancedPageMenuItemAction {
/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
				
		// check permission
		WCF::getUser()->checkPermission('admin.pageMenu.canEditPageMenuItem');
		
		// enable item
		$this->item->enable();
		$this->executed();
	}
}
?>