<?php
// wcf imports
require_once (CP_DIR . 'lib/data/domain/options/DomainOptionTypeSelect.class.php');

class DomainOptionTypeNoAliases extends DomainOptionTypeSelect
{
	/**
	 * @see OptionType::getFormElement()
	 */
	public function getFormElement(&$optionData)
	{
		if (!isset($optionData['optionValue']))
			$optionData['optionValue'] = false;
			
		$sql = "SELECT 	domain.domainID, domain.domainname 
				FROM	cp" . CP_N . "_domain domain
				JOIN	cp" . CP_N . "_domain_option_value domain_value
				ON		(domain.domainID = domain_value.domainID)
				WHERE 	domain_value.domainOption" . Domain :: getDomainOptionID('isAliasDomain') . " <> 'alias'";
		
		$result = WCF :: getDB()->sendQuery($sql);
		
		$options = array (
			'' => '---'
		);
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$options[$row['domainID']] = $row['domainname'];
		}
		
		WCF :: getTPL()->assign(array (
			'optionData' => $optionData, 
			'options' => $options
		));
		return WCF :: getTPL()->fetch('optionTypeSelect');
	}
}
?>