<?php
// wcf imports
require_once (CP_DIR . 'lib/data/domain/options/DomainOptionTypeSelect.class.php');

class DomainOptionTypeVhostContainer extends DomainOptionTypeSelect
{
	/**
	 * @see OptionType::getFormElement()
	 */
	public function getFormElement(&$optionData)
	{
		if (!isset($optionData['optionValue']))
			$optionData['optionValue'] = false;
			
		$sql = "SELECT 	vhostContainerID, vhostName 
				FROM	cp" . CP_N . "_vhostContainer
				WHERE 	isContainer = 1";
		
		$result = WCF :: getDB()->sendQuery($sql);
		
		$options = array (
			'' => '---'
		);
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$options[$row['vhostContainerID']] = $row['vhostName'];
		}
		
		WCF :: getTPL()->assign(array (
			'optionData' => $optionData, 
			'options' => $options
		));
		return WCF :: getTPL()->fetch('optionTypeSelect');
	}
	
	/**
	 * @see OptionType::validate()
	 */
	public function validate($optionData, $newValue) 
	{
		if (!empty($newValue)) 
		{
			$sql = "SELECT 	count(*) AS c 
					FROM	cp" . CP_N . "_vhostContainer
					WHERE 	isContainer = 1 AND vhostContainerID = " . intval($newValue);
		
			$result = WCF :: getDB()->getFirstRow($sql);
			
			if ($result['c'] != 1) 
			{
				throw new UserInputException($optionData['optionName'], 'validationFailed');
			}
		}
	}
}
?>