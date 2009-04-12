<?php
// wcf imports
require_once (WCF_DIR . 'lib/action/AbstractAction.class.php');
require_once (CP_DIR . 'lib/data/ftp/FTPUserEditor.class.php');

class FTPDisableAction extends AbstractAction
{
	/**
	 * @see Action::execute()
	 */
	public function execute()
	{
		parent :: execute();

		$ftpAccount = new FTPUserEditor($_REQUEST['ftpUserID']);

		if ($ftpAccount->userID == WCF :: getUser()->userID)
			$ftpAccount->disable();

		$url = 'index.php?page=FTPList'. SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}
}
?>