<?php

require_once (WCF_DIR . 'lib/system/event/EventListener.class.php');
require_once (CP_DIR . 'lib/data/ftp/FTPUserEditor.class.php');

class UserAddFTPListener implements EventListener
{
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName)
	{
		if (!empty($eventObj->password) && $eventObj->user->ftpaccountsUsed == 0)
			FTPUserEditor :: create($eventObj->user->userID,
									$eventObj->user->username,
									$eventObj->password,
									CPUtils :: getHomeDir($eventObj->user->username),
									WCF :: getLanguage()->get('cp.ftp.defaultaccount'),
									1,
									false);
	}
}
?>