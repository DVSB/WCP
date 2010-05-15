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
			case 'UserAddForm':
			case 'UserEditForm':
				if (!empty($eventObj->password) && $eventObj->user->ftpaccountsUsed == 0)
					FTPUserEditor :: create($eventObj->user->userID,
											$eventObj->user->username,
											$eventObj->password,
											CPUtils :: getHomeDir($eventObj->user->username),
											WCF :: getLanguage()->get('cp.ftp.defaultaccount'),
											1,
											false);
			break;

			case 'UserBanForm':
				foreach ($eventObj->userIDArray as $userID)
					FTPUserEditor :: disableAll($userID);
			break;

			case 'UserBanAction':
				foreach ($eventObj->userIDs as $userID)
					FTPUserEditor :: disableAll($userID);
			break;

			case 'UserUnbanAction':
				foreach ($eventObj->userIDs as $userID)
					FTPUserEditor :: enableAll($userID);
			break;

			case 'UserDeleteAction':
				foreach ($eventObj->userIDs as $userID)
					FTPUserEditor :: deleteAll($userID);
			break;
		}
	}
}
?>