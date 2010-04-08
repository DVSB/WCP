<?php
// wcf imports
require_once (WCF_DIR . 'lib/action/AbstractSecureAction.class.php');
require_once (CP_DIR . 'lib/data/email/EmailEditor.class.php');

class EmailToggleCatchallAction extends AbstractSecureAction
{
	/**
	 * @see Action::execute()
	 */
	public function execute()
	{
		parent :: execute();

		$email = new EmailEditor($_REQUEST['mailID']);

		if ($email->userID == WCF :: getUser()->userID && EmailUtil :: isAvailableCatchall($email->domainID, $email->mailID))
			$email->toggleCatchall();

		$url = 'index.php?page=EmailDetail&mailID=' . $email->mailID . SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}
}
?>