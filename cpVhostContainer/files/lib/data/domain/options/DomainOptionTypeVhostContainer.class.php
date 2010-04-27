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
				FROM	cp" . CP_N . "_vhostContainer";
		
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
}
?>