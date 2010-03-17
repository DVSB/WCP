<?php

require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Adds Informations to Indexpage for user
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	system.event.listener
 * @category 	Control Panel
 * @id			$Id$
 */
class IndexDomainListener implements EventListener
{
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName)
	{
		$eventObj->addDisplay('wcf.user.option.domains',  DomainUtil :: countDomainsForUser(WCF :: getUser()->userID));
		$eventObj->addDisplay('wcf.user.option.subdomains',  WCF :: getUser()->subdomainsUsed.' / '.WCF :: getUser()->subdomains);
	}
}
?>