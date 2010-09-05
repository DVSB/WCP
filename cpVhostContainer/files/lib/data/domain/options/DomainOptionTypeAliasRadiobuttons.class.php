<?php
// wcf imports
require_once (CP_DIR . 'lib/data/domain/options/DomainOptionTypeRadiobuttons.class.php');

class DomainOptionTypeAliasRadiobuttons extends DomainOptionTypeRadiobuttons
{
	/**
	 * @see OptionType::getFormElement()
	 */
	public function getFormElement(&$optionData) 
	{
		//TODO: Get Parameters and search for aliasdomains
		
		return parent :: getFormElement(&$optionData);
	}
	
	/**
	 * @see OptionType::validate()
	 */
	public function validate($optionData, $newValue) 
	{
		if ($newValue == 'alias')
			throw new UserInputException($optionData['optionName'], 'validationFailed');
		
		parent :: validate($optionData, $newValue);
	}
}
?>