<?php

require_once (WCF_DIR . 'lib/system/event/EventListener.class.php');
require_once (CP_DIR . 'lib/data/ftp/FTPUserEditor.class.php');

class UserACPEmailsListener implements EventListener
{
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName)
	{
		switch ($className)
		{
			case 'UserBanForm':
				foreach ($eventObj->userIDArray as $userID)
					EmailEditor :: disableAll($userID);
			break;

			case 'UserBanAction':
				foreach ($eventObj->userIDs as $userID)
					EmailEditor :: disableAll($userID);
			break;

			case 'UserUnbanAction':
				foreach ($eventObj->userIDs as $userID)
					EmailEditor :: enableAll($userID);
			break;

			case 'UserDeleteAction':
				foreach ($eventObj->userIDs as $userID)
					EmailEditor :: deleteAll($userID);
			break;
		}
	}
}
?>