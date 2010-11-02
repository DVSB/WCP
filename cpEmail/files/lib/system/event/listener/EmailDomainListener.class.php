<?php

require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

class EmailDomainListener implements EventListener
{
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName)
	{
		switch ($eventName)
		{
			case 'assignVariables':
				if (isset($eventObj->domain) && isset($eventObj->domain->isEmailDomain))
					WCF :: getTPL()->assign('isEmailDomain', $eventObj->domain->isEmailDomain);
				else
					WCF :: getTPL()->assign('isEmailDomain', true);
				WCF :: getTPL()->append('additionalSettings', WCF :: getTPL()->fetch('emailDomain'));
			break;

			case 'save':
				if (isset($_POST['isEmailDomain']))
					$eventObj->additionalFields['isEmailID'] = intval($_POST['isEmailDomain']);
				else
					$eventObj->additionalFields['isEmailID'] = 0;
			break;
		}
	}
}
?>