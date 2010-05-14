<?php
// wcf imports
require_once (WCF_DIR . 'lib/action/AbstractSecureAction.class.php');
require_once (CP_DIR . 'lib/data/domain/DomainEditor.class.php');

class SubDomainDeleteAction extends AbstractSecureAction
{
	/**
	 * @see Action::execute()
	 */
	public function execute()
	{
		WCF::getUser()->checkPermission('cp.domain.canDeleteSubDomains');
		
		parent :: execute();

		$domain = new DomainEditor($_REQUEST['domainID']);

		if ($domain->userID == WCF :: getUser()->userID && $domain->parentDomainID != 0)
		{
			$this->domainID = $domain->domainID;
			$domain->delete();
			EventHandler :: fireAction($this, 'domainDeleted');
		}
		
		$url = 'index.php?page=DomainList'. SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}
}
?>