<?php

require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

class IndexEmailsListener implements EventListener
{
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName)
	{
		$eventObj->addDisplay('wcf.user.option.ftpaccounts',  WCF :: getUser()->ftpaccountsUsed.' / '.WCF :: getUser()->ftpaccounts);
	}
}
?>