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

		$url = 'index.php?page=VhostContainerList&'.SID_ARG_2ND_NOT_ENCODED;
		
		if (WCF :: getUser()->getPermission('admin.cp.canDeleteVhostContainer') && $existCount['count'] == 0)
		{
			$vhost->delete();
			$url .= '&deleted=success';
		}
		else
		{
			$url .= 'deleted=failed';
		}
		
		HeaderUtil::redirect($url);
	}
}
?>