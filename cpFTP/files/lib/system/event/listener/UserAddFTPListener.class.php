<?php

require_once(WCF_DIR.'lib/system/event/EventListener.class.php');
require_once(CP_DIR.'lib/data/ftp/FTPUserEditor.class.php');

class UserAddFTPListener implements EventListener
{
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName)
	{
		if (!empty($eventObj->password))
			FTPUserEditor :: create($eventObj->userID, $eventObj->username, $eventObj->password, CPUtils :: getHomeDir($eventObj->username), 1);
	}
}
?>