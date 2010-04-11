<?php
// wcf imports
require_once (CP_DIR . 'lib/data/domain/options/DomainOptionTypeRadiobuttons.class.php');

class DomainOptionTypeSelect extends DomainOptionTypeRadiobuttons
{
	/**
	 * @see OptionType::getFormElement()
	 */
	public function getFormElement(&$optionData)
	{
		if (!isset($optionData['optionValue']))
		{
			if (isset($optionData['defaultValue']))
				$optionData['optionValue'] = $optionData['defaultValue'];
			else
				$optionData['optionValue'] = false;
		}
		
		// get options
		$options = OptionUtil :: parseSelectOptions($optionData['selectOptions']);
		
		WCF :: getTPL()->assign(array (
			'optionData' => $optionData, 
			'options' => $options
		));
		return WCF :: getTPL()->fetch('optionTypeSelect');
	}

	/**
	 * @see SearchableUserOption::getSearchFormElement()
	 */
	public function getSearchFormElement(&$optionData)
	{
		$optionData['selectOptions'] = ":\n" . $optionData['selectOptions'];
		return $this->getFormElement($optionData);
	}
}
?>