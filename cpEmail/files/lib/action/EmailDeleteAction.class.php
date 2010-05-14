<?php
// wcf imports
require_once (WCF_DIR . 'lib/action/AbstractSecureAction.class.php');
require_once (CP_DIR . 'lib/data/email/EmailEditor.class.php');

class EmailDeleteAction extends AbstractSecureAction
{
	/**
	 * @see Action::execute()
	 */
	public function execute()
	{
		WCF::getUser()->checkPermission('cp.email.canDeleteAddresses');
		
		parent :: execute();

		$email = new EmailEditor($_REQUEST['mailID']);

		if ($email->userID == WCF :: getUser()->userID)
			$email->delete();

		$url = 'index.php?page=EmailList'. SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}
}
?>