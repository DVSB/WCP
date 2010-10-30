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
			case 'SubDomainAddForm':
			case 'DomainEnableAction':
				if (!$eventObj->domain->noWebDomain)
					JobhandlerUtils :: addJob('createVhost', $eventObj->domain->userID, array('domainID' => $eventObj->domain->domainID));
			break;

			case 'DomainEditForm':
			case 'SubDomainEditForm':
				if (!$eventObj->domain->noWebDomain)
					JobhandlerUtils :: addJob('updateVhost', $eventObj->domain->userID, array('domainID' => $eventObj->domainID));
				else
					JobhandlerUtils :: addJob('deleteVhost', WCF :: getUser()->userID, array('domainID' => $eventObj->domainID), 'asap', 99);
			break;

			case 'DomainDeleteAction':
			case 'SubDomainDeleteAction':
			case 'DomainDisableAction':
				JobhandlerUtils :: addJob('deleteVhost', WCF :: getUser()->userID, array('domainID' => $eventObj->domainID), 'asap', 99);
			break;
		}
	}
}
?>