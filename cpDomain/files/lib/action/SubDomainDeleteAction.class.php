<?php
// wcf imports
require_once (WCF_DIR . 'lib/action/AbstractAction.class.php');
require_once (CP_DIR . 'lib/data/domain/DomainEditor.class.php');

class SubDomainDeleteAction extends AbstractAction
{
	/**
	 * @see Action::execute()
	 */
	public function execute()
	{
		parent :: execute();

		$domain = new DomainEditor($_REQUEST['domainID']);

		if ($domain->userID == WCF :: getUser()->userID && $domain->parentDomainID != 0)
		{
			$domain->delete();
			EventHandler :: fireAction($this, 'domainDeleted');
		}
		
		$url = 'index.php?page=DomainList'. SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}
}
?>