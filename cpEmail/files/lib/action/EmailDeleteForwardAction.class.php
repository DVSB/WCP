<?php
// wcf imports
require_once (WCF_DIR . 'lib/action/AbstractSecureAction.class.php');
require_once (CP_DIR . 'lib/data/email/EmailEditor.class.php');

class EmailDeleteForwardAction extends AbstractSecureAction
{
	/**
	 * @see Action::execute()
	 */
	public function execute()
	{
		WCF::getUser()->checkPermission('cp.email.canDeleteForwards');

		parent :: execute();

		$email = new EmailEditor($_REQUEST['mailID']);

		if ($email->userID == WCF :: getUser()->userID && $email->emailaddress != $_REQUEST['forward'])
			$email->removeForward($_REQUEST['forward']);

		$url = 'index.php?page=EmailDetail&mailID=' . $email->mailID . SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}
}
?>