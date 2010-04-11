<?php
// wcf imports
require_once (CP_DIR . 'lib/data/domain/options/DomainOptionTypeText.class.php');

class DomainOptionTypePath extends DomainOptionTypeText
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
		
		WCF :: getTPL()->assign(array (
			'optionData' => $optionData, 
			'inputType' => $this->inputType
		));
		return WCF :: getTPL()->fetch('optionTypePath');
	}
}
?>