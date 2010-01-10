<?php
// wcf imports
require_once (WCF_DIR . 'lib/action/AbstractAction.class.php');
require_once (CP_DIR . 'lib/data/mysql/MySQLEditor.class.php');

class MySQLDeleteAction extends AbstractAction
{
	/**
	 * @see Action::execute()
	 */
	public function execute()
	{
		WCF::getUser()->checkPermission('cp.mysql.canDeleteMySQL');
		
		parent :: execute();

		$mysql = new MySQLEditor($_REQUEST['mysqlID']);

		if ($mysql->userID == WCF :: getUser()->userID)
			$mysql->delete();

		$url = 'index.php?page=MySQLList'. SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}
}
?>