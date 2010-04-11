<?php
// wcf imports
require_once (CP_DIR . 'lib/data/domain/options/DomainOptionTypeText.class.php');

class DomainOptionTypeInteger extends DomainOptionTypeText
{

	/**
	 * @see OptionType::getData()
	 */
	public function getData($optionData, $newValue)
	{
		return intval($newValue);
	}
}
?>