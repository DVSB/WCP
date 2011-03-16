<?php
/*
 * Copyright (c) 2009 Tobias Friebel
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

class UserAddBackendMainListener implements EventListener
{
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName)
	{
		if (empty($eventObj->cpUser->homedir) && $eventObj->cpUser->isCustomer)
		{
			// create cp user record
			$sql = "UPDATE	cp" . CP_N . "_user
					SET 	homedir = '" . CPUtils :: getHomeDir($eventObj->user->username) . "',
							guid = " . CPUtils :: getNewGUID() . "
					WHERE	userID = " . $eventObj->cpUser->userID;
			WCF :: getDB()->sendQuery($sql);

			if ($eventObj->cpUser->isCustomer)
				JobhandlerUtils :: addJob('createhome', $eventObj->user->userID, array(), 'asap', 100);
		}
	}
}
?>
