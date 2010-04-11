<?php
// wcf imports
require_once (CP_DIR . 'lib/data/domain/options/DomainOptionTypeText.class.php');

class DomainOptionTypeFloat extends DomainOptionTypeText
{
	/**
	 * @see OptionType::getFormElement()
	 */
	public function getFormElement(&$optionData) 
	{
		if (isset($optionData['defaultValue'])) $optionData['defaultValue'] = str_replace('.', WCF::getLanguage()->get('wcf.global.decimalPoint'), $optionData['defaultValue']);
		if (isset($optionData['optionValue'])) $optionData['optionValue'] = str_replace('.', WCF::getLanguage()->get('wcf.global.decimalPoint'), $optionData['optionValue']);
		
		return parent::getFormElement(&$optionData);
	}
	
	/**
	 * @see OptionType::getData()
	 */
	public function getData($optionData, $newValue) 
	{
		$newValue = str_replace(' ', '', $newValue);
		$newValue = str_replace(WCF::getLanguage()->get('wcf.global.thousandsSeparator'), '', $newValue);
		$newValue = str_replace(WCF::getLanguage()->get('wcf.global.decimalPoint'), '.', $newValue);
		return floatval($newValue);
	}
}
?>