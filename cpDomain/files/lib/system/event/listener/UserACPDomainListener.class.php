<?php

require_once (WCF_DIR . 'lib/system/event/EventListener.class.php');
require_once (CP_DIR . 'lib/data/domain/DomainEditor.class.php');

/**
 * Adds Actions for Domain-User-Interaction
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	system.event.listener
 * @category 	Control Panel
 * @id			$Id$
 */
class UserACPDomainListener implements EventListener
{
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName)
	{
		switch ($className)
		{
			case 'UserAddForm':
			case 'UserEditForm':
				if ($eventObj->user->addDefaultSubdomain == true)
					DomainEditor :: addDefaultSubDomain($eventObj->user->userID);
				else
					DomainEditor :: removeDefaultSubDomain($eventObj->user->userID); 
			break;

			case 'UserBanForm':
				foreach ($eventObj->userIDArray as $userID)
					DomainEditor :: disableAll($userID);
			break;

			case 'UserBanAction':
				foreach ($eventObj->userIDs as $userID)
					DomainEditor :: disableAll($userID);
			break;

			case 'UserUnbanAction':
				foreach ($eventObj->userIDs as $userID)
					DomainEditor :: enableAll($userID);
			break;

			case 'UserDeleteAction':
				foreach ($eventObj->userIDs as $userID)
					DomainEditor :: deleteAll($userID);
			break;
		}
	}
}
?>