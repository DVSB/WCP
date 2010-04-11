<?php
// wcf imports
require_once (WCF_DIR . 'lib/acp/option/OptionTypeText.class.php');

class DomainOptionTypeText extends OptionTypeText
{
	/**
	 * @see SearchableUserOption::getCondition()
	 */
	public function getCondition($optionData, $value, $matchesExactly = true)
	{
		$value = StringUtil :: trim($value);
		if (empty($value))
			return false;
		
		if ($matchesExactly)
		{
			return "option_value.domainOption" . $optionData['optionID'] . " = '" . escapeString($value) . "'";
		}
		else
		{
			return "option_value.domainOption" . $optionData['optionID'] . " LIKE '%" . addcslashes(escapeString($value), '_%') . "%'";
		}
	}
}
?>