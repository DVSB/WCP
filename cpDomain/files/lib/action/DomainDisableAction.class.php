<?php
// wcf imports
require_once (WCF_DIR . 'lib/action/AbstractSecureAction.class.php');
require_once (CP_DIR . 'lib/data/domain/DomainEditor.class.php');

class DomainDisableAction extends AbstractSecureAction
{
	/**
	 * @see Action::execute()
	 */
	public function execute()
	{
		parent :: execute();

		$this->domain = new DomainEditor($_REQUEST['domainID']);

		if ($this->domain->userID == WCF :: getUser()->userID)
		{
			$this->domain->disable();
			EventHandler :: fireAction($this, 'domainDisabled');
		}

		$url = 'index.php?page=DomainList'. SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}
}
?>