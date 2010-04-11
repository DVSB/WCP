<?php
// wcf imports
require_once (WCF_DIR . 'lib/acp/option/OptionTypeBoolean.class.php');

class DomainOptionTypeBoolean extends OptionTypeBoolean
{
	/**
	 * @see SearchableUserOption::getCondition()
	 */
	public function getCondition($optionData, $value, $matchesExactly = true)
	{
		$value = intval($value);
		if (!$value)
			return false;
		
		return "option_value.domainOption" . $optionData['optionID'] . " = 1";
	}
}
?>