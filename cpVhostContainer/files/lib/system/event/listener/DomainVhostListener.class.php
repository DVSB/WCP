<?php

require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Controls creation/modification of vhosts in system
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.vhostcontainer
 * @subpackage	system.event.listener
 * @category 	Control Panel
 * @id			$Id$
 */
class DomainVhostListener implements EventListener
{
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName)
	{
		switch ($className)
		{
			case 'DomainAddForm':
				JobhandlerUtils :: addJob('createVhost', $eventObj->userID, array('domainID' => $eventObj->domain->domainID));
			break;

			case 'DomainEditForm':
				JobhandlerUtils :: addJob('updateVhost', $eventObj->userID, array('domainID' => $eventObj->domainID));
			break;
			
			case 'DomainDeleteAction':
				JobhandlerUtils :: addJob('deleteVhost', 0, array('domainID' => $eventObj->domainID));
			break;
			
			case 'SubDomainAddForm':
				JobhandlerUtils :: addJob('createVhost', $eventObj->domain->userID, array('domainID' => $eventObj->domain->domainID));
			break;
			
			case 'SubDomainDeleteAction':
				JobhandlerUtils :: addJob('deleteVhost', $eventObj->domain->userID, array('domainID' => $eventObj->domainID));
			break;
			
			case 'DomainEnableAction':
				JobhandlerUtils :: addJob('createVhost', 0, array('domainID' => $eventObj->domainID));
			break;
			
			case 'DomainDisableAction':
				JobhandlerUtils :: addJob('deleteVhost', 0, array('domainID' => $eventObj->domainID));
			break;
		}
	}
}
?>