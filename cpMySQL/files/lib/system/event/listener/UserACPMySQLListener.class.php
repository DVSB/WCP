<?php

require_once (WCF_DIR . 'lib/system/event/EventListener.class.php');
require_once (CP_DIR . 'lib/data/mysql/MySQLEditor.class.php');

class UserACPMySQLListener implements EventListener
{
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName)
	{
		switch ($className)
		{
			case 'UserDeleteAction':
				foreach ($eventObj->userIDs as $userID)
					MySQLEditor :: deleteAll($userID);
			break;
		}
	}
}
?>