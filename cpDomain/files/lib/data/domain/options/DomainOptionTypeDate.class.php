<?php
// wcf imports
require_once (WCF_DIR . 'lib/acp/option/OptionTypeDate.class.php');

class DomainOptionTypeDate extends OptionTypeDate
{
	/**
	 * @see SearchableUserOption::getCondition()
	 */
	public function getCondition($optionData, $value, $matchesExactly = true)
	{
		$value = $this->getData($optionData, $value);
		if ($value == '')
			return false;
		
		return "option_value.domainOption" . $optionData['optionID'] . " = '" . escapeString($value) . "'";
	}
}
?>