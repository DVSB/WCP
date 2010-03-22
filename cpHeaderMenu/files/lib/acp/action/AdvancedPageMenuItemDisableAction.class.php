<?php
require_once(WCF_DIR.'lib/acp/action/AbstractAdvancedPageMenuItemAction.class.php');

class AdvancedPageMenuItemDisableAction extends AbstractAdvancedPageMenuItemAction {
/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
				
		// check permission
		WCF::getUser()->checkPermission('admin.pageMenu.canEditPageMenuItem');
		
		// disable item
		$this->item->enable(0);
		$this->executed();
	}
}
?>