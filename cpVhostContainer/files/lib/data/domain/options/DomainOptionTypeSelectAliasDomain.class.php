<?php
// wcf imports
require_once (CP_DIR . 'lib/data/domain/options/DomainOptionTypeSelect.class.php');
require_once (CP_DIR . 'lib/data/domain/Domain.class.php');

class DomainOptionTypeSelectAliasDomain extends DomainOptionTypeSelect
{
	/**
	 * @see OptionType::getFormElement()
	 */
	public function getFormElement(&$optionData)
	{
		if (!isset($optionData['optionValue']))
			$optionData['optionValue'] = false;

		if (isset($this->form->domainID))
			$myDomainID = $this->form->domainID;
		else
			$myDomainID = 0;

		$sql = "SELECT 	domain.domainID, domain.domainname
				FROM	cp" . CP_N . "_domain domain
				JOIN	cp" . CP_N . "_domain_option_value domain_value
				ON		(domain.domainID = domain_value.domainID)
				WHERE 	domain_value.domainOption" . Domain :: getDomainOptionID('isAliasDomain') . " <> 'alias'
					AND	domain_value.domainOption" . Domain :: getDomainOptionID('noWebDomain') . " <> 1
					AND	domain.domainID <> " . intval($myDomainID);

		$result = WCF :: getDB()->sendQuery($sql);

		$options = array (
			'' => '---'
		);
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$options[$row['domainID']] = $row['domainname'];
		}

		WCF :: getTPL()->assign(array (
			'optionData' => $optionData,
			'options' => $options
		));
		return WCF :: getTPL()->fetch('optionTypeSelect');
	}

	/**
	 * @see OptionType::getData()
	 */
	public function getData($optionData, $newValue)
	{
		return $newValue;
	}

	/**
	 * @see OptionType::validate()
	 */
	public function validate($optionData, $newValue)
	{
		if (!empty($newValue))
		{
			$sql = "SELECT 	domain.domainID
					FROM	cp" . CP_N . "_domain domain
					JOIN	cp" . CP_N . "_domain_option_value domain_value
					ON		(domain.domainID = domain_value.domainID)
					WHERE 	domain_value.domainOption" . Domain :: getDomainOptionID('isAliasDomain') . " <> 'alias'
						AND	domain_value.domainOption" . Domain :: getDomainOptionID('noWebDomain') . " <> 1
						AND	domain.domainID = " . intval($newValue);

			$result = WCF :: getDB()->getFirstRow($sql);

			if (empty($result) || $result['domainID'] != $newValue)
			{
				throw new UserInputException($optionData['optionName'], 'validationFailed');
			}
		}
	}
}
?>
