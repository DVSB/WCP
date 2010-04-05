<?php

require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

class IndexEmailsListener implements EventListener
{
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName)
	{
		$eventObj->addDisplay('wcf.user.option.emailAddresses',  WCF :: getUser()->emailAddressesUsed.' / '.WCF :: getUser()->emailAddresses);
		$eventObj->addDisplay('wcf.user.option.emailAccounts',  WCF :: getUser()->emailAccountsUsed.' / '.WCF :: getUser()->emailAccounts);
		$eventObj->addDisplay('wcf.user.option.emailForwards',  WCF :: getUser()->emailForwardsUsed.' / '.WCF :: getUser()->emailForwards);
	}
}
?>