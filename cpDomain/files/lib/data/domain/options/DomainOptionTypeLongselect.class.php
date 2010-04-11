<?php
// wcf imports
require_once (CP_DIR . 'lib/data/domain/options/DomainOptionTypeSelect.class.php');

class DomainOptionTypeLongselect extends DomainOptionTypeSelect
{
	/**
	 * @see OptionType::getFormElement()
	 */
	public function getFormElement(&$optionData)
	{
		$optionData['divClass'] = 'longSelect';
		return parent :: getFormElement($optionData);
	}
}
?>