<?php
require_once (CP_DIR . 'lib/data/domains/DomainOption.class.php');

/**
 * Add/modifies Domainoptions
 *
 * @author			Tobias Friebel
 * @copyright		2009 Tobias Friebel
 * @license			GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package			com.toby.cp.domain
 * @subpackage		data.domains
 * @category		ControlPanel
 */
class DomainOptionEditor extends DomainOption
{
	/**
	 * Disables this option.
	 */
	public function disable()
	{
		$this->enable(false);
	}

	/**
	 * Deletes this user option.
	 */
	public function delete()
	{
		$sql = "DELETE FROM	cp" . CP_N . "_domain_option
				WHERE		optionID = " . $this->optionID;
		WCF :: getDB()->sendQuery($sql);
		
		$sql = "ALTER TABLE	cp" . CP_N . "_domain_option_value
				DROP 		userOption" . $this->optionID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * Creates a new user option.
	 *
	 * @param 	string 		$optionName
	 * @param 	string 		$optionType
	 * @param 	mixed 		$defaultValue
	 * @param 	string 		$validationPattern
	 * @param 	string 		$selectOptions
	 * @param 	string 		$enableOptions
	 * @param 	boolean 	$required
	 * @param 	integer 	$editable
	 * @param 	integer 	$visible
	 * @param 	boolean 	$searchable
	 * @param 	integer 	$showOrder
	 * @param 	integer 	$packageID
	 * @return 	string
	 */
	public static function create($optionName, $optionType, $defaultValue, $validationPattern, $selectOptions, $enableOptions, $required, $editable, $visible, $showOrder, $packageID = PACKAGE_ID)
	{
		// insert new option
		$sql = "INSERT INTO	cp" . CP_N . "_domain_option
					(packageID, optionName, optionType, defaultValue,
					validationPattern, selectOptions, enableOptions, required, editable,
					visible, searchable, showOrder)
			VALUES		(" . $packageID . ", '" . escapeString($optionName) . "', '" . escapeString($optionType) . "', '" . escapeString($defaultValue) . "',
					'" . escapeString($validationPattern) . "', '" . escapeString($selectOptions) . "', '" . escapeString($enableOptions) . "', " . $required . ", " . $editable . ",
					" . $visible . ", " . $showOrder . ")";
		WCF :: getDB()->sendQuery($sql);
		
		// add new option to table "cp".CP_N."_domain_option_value" 
		$sql = "ALTER TABLE 	cp" . CP_N . "_domain_option_value
				ADD COLUMN	" . $optionName . " " . self :: getColumnType($optionType);
		WCF :: getDB()->sendQuery($sql);
		
		// add the default value to this column
		if ($defaultValue)
		{
			$sql = "UPDATE	cp" . CP_N . "_domain_option_value
					SET 	" . $optionName . " = '" . escapeString($defaultValue) . "'";
			WCF :: getDB()->sendQuery($sql);
		}
		
		return $optionName;
	}

	/**
	 * Determines the needed sql column type for a user option.
	 * 
	 * @param	string		$optionType
	 * @return	string		column type
	 */
	public static function getColumnType($optionType)
	{
		switch ($optionType)
		{
			case 'boolean':
				return 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0';
			case 'integer':
				return 'INT(10) UNSIGNED NOT NULL DEFAULT 0';
			case 'float':
				return 'FLOAT NOT NULL DEFAULT 0.0';
			case 'textarea':
				return 'MEDIUMTEXT';
			case 'date':
				return "CHAR(10) NOT NULL DEFAULT '0000-00-00'";
			default:
				return 'TEXT';
		}
	}

	/**
	 * Updates the data of an existing user option.
	 *
	 * @param 	string 		$optionType
	 * @param 	mixed 		$defaultValue
	 * @param 	string 		$validationPattern
	 * @param 	string 		$selectOptions
	 * @param 	string 		$enableOptions
	 * @param 	boolean 	$required
	 * @param 	integer 	$editable
	 * @param 	integer 	$visible
	 * @param 	integer 	$showOrder
	 */
	public function update($optionType, $defaultValue, $validationPattern, $selectOptions, $enableOptions, $required, $editable, $visible, $showOrder)
	{
		$sql = "UPDATE 	cp" . CP_N . "_domain_option
				SET		optionType = '" . escapeString($optionType) . "',
						defaultValue = '" . escapeString($defaultValue) . "',
						validationPattern = '" . escapeString($validationPattern) . "',
						selectOptions = '" . escapeString($selectOptions) . "',
						required = " . $required . ",
						editable = " . $editable . ",
						visible = " . $visible . ",
						showOrder = " . $showOrder . ",
				WHERE 	optionID = " . $this->optionID;
		WCF :: getDB()->sendQuery($sql);
		
		// alter the table "wcf".WCF_N."_user_option_value" with this new option
		$sql = "ALTER TABLE 	cp" . CP_N . "_domain_option_value
				CHANGE		" . $this->optionName . " 
					" . $this->optionName . " " . $this->getColumnType($optionType);
		WCF :: getDB()->sendQuery($sql);
	}
}
?>