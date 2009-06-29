<?php

require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

class IndexMySQLListener implements EventListener
{
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName)
	{
		$eventObj->addDisplay('wcf.user.option.mysqls',  WCF :: getUser()->mysqlsUsed.' / '.WCF :: getUser()->mysqls);
	}
}
?>