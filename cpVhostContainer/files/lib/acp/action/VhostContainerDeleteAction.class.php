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

		if (WCF :: getUser()->getPermission('admin.cp.canDeleteVhostContainer'))
		{
			$vhost->delete();
			JobhandlerUtils :: addJob('deleteVhosts', 0, array('vhostContainerID' => $vhost->vhostContainerID), 'asap');
			EventHandler :: fireAction($this, 'vhostContainerDeleted');
		}
		
		$url = 'index.php?page=VhostContainerList&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}
}
?>