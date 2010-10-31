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
			case 'DomainEditForm':
			case 'SubDomainEditForm':
				$this->createJob($eventObj->domain);
			break;

			case 'DomainDeleteAction':
			case 'SubDomainDeleteAction':
			case 'DomainDisableAction':
				JobhandlerUtils :: addJob('deleteVhost', WCF :: getUser()->userID, array('domainID' => $eventObj->domainID));
			break;
		}
	}

	public function createJob($domain)
	{
		if ($domain->isAliasDomain == 'alias' && $domain->aliasDomainID != 0)
		{
			JobhandlerUtils :: addJob('createVhost', $domain->userID, array('domainID' => $domain->aliasDomainID));
			return;
		}

		if ($domain->noWebDomain == 1)
		{
			JobhandlerUtils :: addJob('deleteVhost', $domain->userID, array('domainID' => $domain->domainID));
			return;
		}

		JobhandlerUtils :: addJob('createVhost', $domain->userID, array('domainID' => $domain->domainID));
	}
}
?>