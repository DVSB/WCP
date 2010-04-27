<?php
// wcf imports
require_once (WCF_DIR . 'lib/action/AbstractAction.class.php');
require_once (CP_DIR . 'lib/data/vhost/VhostContainerEditor.class.php');

class VhostContainerDeleteAction extends AbstractAction
{
	/**
	 * @see Action::execute()
	 */
	public function execute()
	{
		parent :: execute();

		$vhost = new VhostContainerEditor($_REQUEST['vhostContainerID']);
		
		$optionID = Domain :: getDomainOptionID('vhostContainerID');
		
		$sql = "SELECT COUNT(*) AS count
				FROM cp" . CP_N . "domain_option_value 
				WHERE domainOption" . $optionID . " = " . $vhost->vhostContainerID;
		
		$existCount = WCF :: getDB()->getFirstRow($sql);

		if (WCF :: getUser()->getPermission('admin.cp.canDeleteVhostContainer') && $existCount['count'] == 0)
		{
			$vhost->delete();
		}
		
		$url = 'index.php?page=VhostContainerList&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}
}
?>