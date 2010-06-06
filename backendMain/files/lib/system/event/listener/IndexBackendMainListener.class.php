<?php

require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

class IndexBackendMainListener implements EventListener
{
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName)
	{
		$eventObj->addDisplay('wcf.user.option.diskspace',  WCF :: getLanguage()->getDynamicVariable('wcf.user.option.diskspace.values', array('used' => WCF :: getUser()->diskspaceUsed, 
			'avail' => WCF :: getUser()->diskspace)));
	}
}
?>