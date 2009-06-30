<?php

require_once (WCF_DIR . 'lib/system/event/EventListener.class.php');
require_once (CP_DIR . 'lib/data/domains/DomainEditor.class.php');

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