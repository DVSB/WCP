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

		switch (get_class($this->form))
		{
			case 'DomainEditForm':
			case 'SubDomainEditForm':
				$this->user = new CPUser($this->form->domain->userID);
			break;

			case 'DomainAddForm':
				$this->user = new CPUser($this->form->userID);
			break;

			case 'SubDomainAddForm':
			default:
				$this->user = WCF :: getUser();
		}

		$optionData['optionValue'] = str_replace($this->user->homeDir, '', $optionData['optionValue']);

		WCF :: getTPL()->assign(array (
			'optionData' => $optionData,
			'inputType' => $this->inputType
		));
		return WCF :: getTPL()->fetch('optionTypePath');
	}

	/**
	 * @see OptionType::getData()
	 */
	public function getData($optionData, $newValue)
	{
		if (!empty($newValue))
		{
			switch (get_class($this->form))
			{
				case 'DomainEditForm':
				case 'SubDomainEditForm':
					$this->user = new CPUser($this->form->domain->userID);
				break;

				case 'DomainAddForm':
					$this->user = new CPUser($this->form->userID);
				break;

				case 'SubDomainAddForm':
				default:
					$this->user = WCF :: getUser();
			}

			if (strpos($newValue, $this->user->homeDir) !== 0)
				$newValue = FileUtil :: getRealPath($this->user->homeDir . $newValue);
		}

		return $newValue;
	}

	/**
	 * @see OptionType::validate()
	 */
	public function validate($optionData, $newValue)
	{
		if (!empty($newValue))
		{
			$newValue = FileUtil :: getRealPath($this->user->homeDir . $newValue);

			if ($newValue == $this->user->homeDir)
				throw new UserInputException($optionData['optionName'], 'notValid');

			if (strpos($newValue, $this->user->homeDir) !== 0)
				throw new UserInputException($optionData['optionName'], 'notValid');
		}
	}
}
?>