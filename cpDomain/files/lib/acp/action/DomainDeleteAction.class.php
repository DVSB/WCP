<?php
// wcf imports
require_once (WCF_DIR . 'lib/action/AbstractAction.class.php');
require_once (CP_DIR . 'lib/data/domain/DomainEditor.class.php');

class DomainDeleteAction extends AbstractAction
{
	/**
	 * @see Action::execute()
	 */
	public function execute()
	{
		WCF::getUser()->checkPermission('admin.cp.canDeleteDomains');
		
		parent :: execute();

		$domain = new DomainEditor($_REQUEST['domainID']);

		if ($domain->adminID == WCF :: getUser()->userID || WCF :: getUser()->getPermission('admin.general.isSuperAdmin'))
		{
			$this->domainID = $domain->domainID;
			$domain->delete();
			EventHandler :: fireAction($this, 'domainDeleted');
		}
		
		$url = 'index.php?page=DomainList&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}
}
?>