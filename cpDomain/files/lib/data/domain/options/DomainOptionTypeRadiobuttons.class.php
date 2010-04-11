<?php
// wcf imports
require_once (WCF_DIR . 'lib/acp/option/OptionTypeRadiobuttons.class.php');

class DomainOptionTypeRadiobuttons extends OptionTypeRadiobuttons
{
	/**
	 * @see SearchableUserOption::getCondition()
	 */
	public function getCondition($optionData, $value, $matchesExactly = true)
	{
		$value = StringUtil :: trim($value);
		if (!$value)
			return false;
		
		return "option_value.domainOption" . $optionData['optionID'] . " = '" . escapeString($value) . "'";
	}
}
?>