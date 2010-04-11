<?php
// wcf imports
require_once (CP_DIR . 'lib/data/domain/options/DomainOptionTypeText.class.php');

class DomainOptionTypeTextarea extends DomainOptionTypeText
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
				$optionData['optionValue'] = '';
		}
		
		WCF :: getTPL()->assign('optionData', $optionData);
		return WCF :: getTPL()->fetch('optionTypeTextarea');
	}

	/**
	 * @see OptionType::validate()
	 */
	public function validate($optionData, $newValue)
	{
	}

	/**
	 * @see OptionType::getData()
	 */
	public function getData($optionData, $newValue)
	{
		return $newValue;
	}
}
?>