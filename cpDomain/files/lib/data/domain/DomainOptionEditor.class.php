<?php
require_once (CP_DIR . 'lib/data/domain/DomainOption.class.php');

/**
 * Add/modifies Domainoptions
 *
 * @author			Tobias Friebel
 * @copyright		2009 Tobias Friebel
 * @license			GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package			com.toby.cp.domain
 * @subpackage		data.domains
 * @category		ControlPanel
 * @id				$Id$
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
				DROP 		domainOption" . $this->optionID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * Creates a new domain option.
	 *
	 * @param 	string 		$optionName
	 * @param 	string 		$optionType
	 * @param 	mixed 		$defaultValue
	 * @param 	string 		$validationPattern
	 * @param 	string 		$selectOptions
	 * @param 	string 		$enableOptions
	 * @param 	boolean 	$required
	 * @param 	integer 	$editable
	 * @param 	boolean 	$searchable
	 * @param 	integer 	$showOrder
	 * @param 	integer 	$packageID
	 * @return 	string
	 */
	public static function create($optionName, $categoryName, $optionType, $defaultValue, $validationPattern,
									$selectOptions, $enableOptions, $required, $editable, $showOrder,
									$disabled, $permissions, $options, $additionalData, $packageID = PACKAGE_ID)
	{
		// insert new option
		$sql = "INSERT INTO	cp" . CP_N . "_domain_option
				(packageID, optionName, categoryName, optionType, defaultValue,
				 validationPattern, selectOptions, enableOptions, required, editable,
				 showOrder, disabled, permissions, options, additionalData)
				VALUES
				(" . $packageID . ", '" . escapeString($optionName) . "', '" . escapeString($categoryName) . "', '" . escapeString($optionType) . "', '" . escapeString($defaultValue) . "',
					'" . escapeString($validationPattern) . "', '" . escapeString($selectOptions) . "', '" . escapeString($enableOptions) . "', " . $required . ", " . $editable . ",
					" . $showOrder . ", " . $disabled . ", '" . escapeString($permissions) . "', '" . escapeString($options) . "', '" . escapeString(serialize($additionalData)) . "')";
		WCF :: getDB()->sendQuery($sql);

		$optionID = WCF::getDB()->getInsertID();

		// add new option to table "cp".CP_N."_domain_option_value"
		$sql = "ALTER TABLE 	cp" . CP_N . "_domain_option_value
				ADD COLUMN	domainOption" . $optionID . " " . self :: getColumnType($optionType);
		WCF :: getDB()->sendQuery($sql);

		// add the default value to this column
		if ($defaultValue)
		{
			$sql = "UPDATE	cp" . CP_N . "_domain_option_value
					SET 	domainOption" . $optionID . " = '" . escapeString($defaultValue) . "'";
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
		//small trick for optionTypes which are IDs, but need own name
		//asume everything with ID at the end is an integer
		if (stripos($optionType, 'ID') == (strlen($optionType) - 2))
			$optionType = 'integer';

		switch ($optionType)
		{
			case 'boolean':
				return 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0';
			case 'integer':
				return 'INT(10) UNSIGNED NOT NULL DEFAULT 0';
			case 'unsignedinteger':
				return 'INT(10) NOT NULL DEFAULT 0';
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
	 * Updates the data of an existing domain option.
	 *
	 * @param 	string 		$optionName
	 * @param 	string 		$categoryName
	 * @param 	string 		$optionType
	 * @param 	mixed 		$defaultValue
	 * @param 	string 		$validationPattern
	 * @param 	string 		$selectOptions
	 * @param 	string 		$enableOptions
	 * @param 	boolean 	$required
	 * @param 	integer 	$editable
	 * @param 	integer 	$showOrder
	 * @param 	boolean 	$disabled
	 * @param	string		$permissions
	 * @param	string		$options
	 * @param	array		$addionalData
	 */
	public function update($optionName, $categoryName, $optionType, $defaultValue, $validationPattern,
							$selectOptions, $enableOptions, $required, $editable, $showOrder, $disabled = 0,
							$permissions = '', $options = '', $additionalData = null)
	{
		$sql = "UPDATE 	cp" . CP_N . "_domain_option
				SET		optionName = '".escapeString($optionName)."',
						categoryName = '".escapeString($categoryName)."',
						optionType = '".escapeString($optionType)."',
						defaultValue = '".escapeString($defaultValue)."',
						validationPattern = '".escapeString($validationPattern)."',
						selectOptions = '".escapeString($selectOptions)."',
						required = ".$required.",
						editable = ".$editable.",
						showOrder = ".$showOrder.",
						enableOptions = '".escapeString($enableOptions)."',
						disabled = ".$disabled.",
						permissions = '".escapeString($permissions)."',
						options = '".escapeString($options)."'
						".($additionalData !== null ? ", additionalData = '".escapeString(serialize($additionalData))."'" : "")."
				WHERE 	optionID = " . $this->optionID;
		WCF :: getDB()->sendQuery($sql);

		// alter the table "wcf".WCF_N."_user_option_value" with this new option
		$sql = "ALTER TABLE cp" . CP_N . "_domain_option_value
				CHANGE		domainOption" . $this->optionID . "
							domainOption" . $this->optionID . " " . self :: getColumnType($optionType);
		WCF :: getDB()->sendQuery($sql);
	}
}
?>