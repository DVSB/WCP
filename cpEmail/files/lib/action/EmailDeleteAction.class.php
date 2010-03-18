<?php
// wcf imports
require_once (WCF_DIR . 'lib/action/AbstractAction.class.php');
require_once (CP_DIR . 'lib/data/ftp/FTPUserEditor.class.php');

class EmailDeleteAction extends AbstractAction
{
	/**
	 * @see Action::execute()
	 */
	public function execute()
	{
		parent :: execute();

		$email = new EmailEditor($_REQUEST['emailID']);

		if ($email->userID == WCF :: getUser()->userID)
			$email->delete();

		$url = 'index.php?page=EmailList'. SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}
}
?>