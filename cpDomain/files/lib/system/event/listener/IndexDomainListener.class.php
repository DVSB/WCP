<?php

require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

class IndexDomainListener implements EventListener
{
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName)
	{
		$eventObj->addDisplay('wcf.user.option.subdomains',  WCF :: getUser()->subdomainsUsed.' / '.WCF :: getUser()->subdomains);
	}
}
?>