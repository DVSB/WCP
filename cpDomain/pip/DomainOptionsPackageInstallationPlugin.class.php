<?php
// wcf imports
require_once (WCF_DIR . 'lib/acp/package/plugin/AbstractXMLPackageInstallationPlugin.class.php');
require_once (CP_DIR . 'lib/data/domain/DomainOptionEditor.class.php');

/**
 * Adds Options to Domaintable
 *
 * @author			Tobias Friebel
 * @copyright		2009 Tobias Friebel
 * @license			GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package			com.toby.cp.domain
 * @subpackage		pip
 * @category		ControlPanel
 */
class DomainOptionsPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin
{
	public $tagName = 'domainoptions';
	public $tableName = 'domain_option';
	
	public static $reservedTags = array (
		'name', 
		'optiontype', 
		'defaultvalue', 
		'validationpattern', 
		'required', 
		'editable', 
		'visible',  
		'showorder', 
		'outputclass', 
		'selectoptions', 
		'enableoptions', 
		'disabled', 
		'categoryname', 
		'permissions', 
		'options', 
		'attrs', 
		'cdata'
	);

	/**
	 * Installs domain option categories.
	 * 
	 * @param 	array		$category
	 * @param	array		$categoryXML
	 */
	protected function saveCategory($category, $categoryXML = null)
	{		
		$sql = "INSERT INTO		cp" . CP_N . "_" . $this->tableName . "_category
								(packageID, categoryName, parentCategoryName" . ($category['showOrder'] !== null ? ", showOrder" : "") . ", permissions, options)
				VALUES			(" . $this->installation->getPackageID() . ", 
								'" . escapeString($category['categoryName']) . "', 
								'" . $category['parentCategoryName'] . "', 
								" . ($category['showOrder'] !== null ? $category['showOrder'] . "," : "") . " 
								'" . escapeString($category['permissions']) . "',
								'" . escapeString($category['options']) . "')
				ON DUPLICATE KEY UPDATE 	
							parentCategoryName = VALUES(parentCategoryName),
							" . ($category['showOrder'] !== null ? "showOrder = VALUES(showOrder)," : "") . "
							permissions = VALUES(permissions),
							options = VALUES(options)";
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * @see	 AbstractOptionPackageInstallationPlugin::saveOption()
	 */
	protected function saveOption($option, $categoryName, $existingOptionID = 0)
	{
		// default values
		$optionName = $optionType = $defaultValue = $validationPattern = $selectOptions = $enableOptions = $permissions = $options = '';
		$required = $editable = $visible = $disabled = 0;
		$showOrder = null;
		
		// make xml tags-names (keys in array) to lower case
		$this->keysToLowerCase($option);
		
		// get values
		if (isset($option['name']))
			$optionName = $option['name'];
		if (isset($option['optiontype']))
			$optionType = $option['optiontype'];
		if (isset($option['defaultvalue']))
			$defaultValue = $option['defaultvalue'];
		if (isset($option['validationpattern']))
			$validationPattern = $option['validationpattern'];
		if (isset($option['required']))
			$required = intval($option['required']);
		if (isset($option['editable']))
			$editable = intval($option['editable']);
		if (isset($option['visible']))
			$visible = intval($option['visible']);
		if (isset($option['showorder']))
			$showOrder = intval($option['showorder']);
		if (isset($option['selectoptions']))
			$selectOptions = $option['selectoptions'];
		if (isset($option['enableoptions']))
			$enableOptions = $option['enableoptions'];
		if (isset($option['disabled']))
			$disabled = intval($option['disabled']);
		
		$showOrder = $this->getShowOrder($showOrder, $categoryName, 'categoryName');
		
		if (isset($option['permissions']))
			$permissions = $option['permissions'];
		if (isset($option['options']))
			$options = $option['options'];
			
		// check if optionType exists
		$classFile = CP_DIR . 'lib/domain/options/OptionType' . ucfirst($optionType) . '.class.php';
		if (!@file_exists($classFile))
		{
			throw new SystemException('Unable to find file ' . $classFile, 11002);
		}
		
		// collect additional tags and their values
		$additionalData = array ();
		foreach ($option as $tag => $value)
		{
			if (!in_array($tag, self :: $reservedTags))
				$additionalData[$tag] = $value;
		}
		
		// get optionID if it was installed by this package already
		$sql = "SELECT	*
				FROM 	cp" . CP_N . "_" . $this->tableName . "
				WHERE 	optionName = '" . escapeString($optionName) . "'
					AND	packageID = " . $this->installation->getPackageID();
		$result = WCF :: getDB()->getFirstRow($sql);
		
		// update option
		if (!empty($result['optionID']) && $this->installation->getAction() == 'update')
		{
			$userOption = new DomainOptionEditor(null, $result);
			$userOption->update($optionName, $categoryName, $optionType, $defaultValue, $validationPattern, $selectOptions, $enableOptions, $required, $editable, $visible, $showOrder, $disabled, $permissions, $options, $additionalData);
		}
		// insert new option
		else
		{
			DomainOptionEditor :: create($optionName, $categoryName, $optionType, $defaultValue, $validationPattern, $selectOptions, $enableOptions, $required, $editable, $visible, $showOrder, $disabled, $this->installation->getPackageID(), $permissions, $options, $additionalData);
		}
	}

	/**
	 * Returns the show order value.
	 * 
	 * @param	integer		$showOrder
	 * @param	string		$parentName
	 * @param	string		$columnName
	 * @param	string		$tableNameExtension
	 * @return	integer 	new show order
	 */
	protected function getShowOrder($showOrder, $parentName = null, $columnName = null, $tableNameExtension = '')
	{
		if ($showOrder === null)
		{
			// get greatest showOrder value
			$sql = "SELECT	MAX(showOrder) AS showOrder
			  		FROM	cp" . CP_N . "_" . $this->tableName . $tableNameExtension . " 
					" . ($columnName !== null ? "WHERE " . $columnName . " = '" . escapeString($parentName) . "'" : "");
			$maxShowOrder = WCF :: getDB()->getFirstRow($sql);
			if (is_array($maxShowOrder) && isset($maxShowOrder['showOrder']))
			{
				return $maxShowOrder['showOrder'] + 1;
			}
			else
			{
				return 1;
			}
		}
		else
		{
			// increase all showOrder values which are >= $showOrder
			$sql = "UPDATE	cp" . CP_N . "_" . $this->tableName . $tableNameExtension . "
					SET	showOrder = showOrder+1
					WHERE	showOrder >= " . $showOrder . " 
					" . ($columnName !== null ? "AND " . $columnName . " = '" . escapeString($parentName) . "'" : "");
			WCF :: getDB()->sendQuery($sql);
			// return the wanted showOrder level
			return $showOrder;
		}
	}

	/** 
	 * Drops the columns from user option value table from options
	 * installed by this package.
	 */
	public function uninstall()
	{
		// get optionsIDs from package
		$sql = "SELECT	optionID
				FROM 	cp" . CP_N . "_domain_option
				WHERE	packageID = " . $this->installation->getPackageID();
		$result = WCF :: getDB()->sendQuery($sql);
		$optionIDs = array ();
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$optionIDs[] = $row['optionID'];
		}
		$this->dropColumns($optionIDs);
		
		// uninstall options and categories
		// call uninstall event
		EventHandler :: fireAction($this, 'uninstall');
		
		$sql = "DELETE FROM	cp" . CP_N . "_".$this->tableName."
				WHERE		packageID = ".$this->installation->getPackageID();
		WCF::getDB()->sendQuery($sql);
	}

	/** 
	 * Drops the columns from domain option value table from options
	 * deleted by this package update.
	 * 
	 * @param 	string 		$optionNames 
	 */
	protected function deleteOptions($optionNames)
	{
		$sql = "SELECT	optionID
				FROM 	cp" . CP_N . "_domain_option
				WHERE	optionName IN (" . $optionNames . ")
				AND 	packageID = " . $this->installation->getPackageID();
		$result = WCF :: getDB()->sendQuery($sql);
		$optionIDs = array ();
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$optionIDs[] = $row['optionID'];
		}
		$this->dropColumns($optionIDs);
		
		// delete options
		$sql = "DELETE FROM	cp" . CP_N . "_".$this->tableName."
				WHERE		optionName IN (".$optionNames.")
				AND 		packageID = ".$this->installation->getPackageID();
		WCF::getDB()->sendQuery($sql);
	}

	/** 
	 * Drops the columns from domain option value table from option categories
	 * deleted by this package update.
	 * 
	 * @param 	string 		$categoryNames  
	 */
	protected function deleteCategories($categoryNames)
	{
		$sql = "SELECT	optionID
				FROM 	cp" . CP_N . "_domain_option
				WHERE	categoryName IN (" . $categoryNames . ")
				AND 	packageID = " . $this->installation->getPackageID();
		$result = WCF :: getDB()->sendQuery($sql);
		$optionIDs = array ();
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$optionIDs[] = $row['optionID'];
		}
		$this->dropColumns($optionIDs);
		
		// delete options from the categories
		$sql = "DELETE FROM	cp" . CP_N . "_".$this->tableName."
				WHERE		categoryName IN (".$categoryNames.")";
		WCF::getDB()->sendQuery($sql);
						
		// delete categories
		$sql = "DELETE FROM	cp" . CP_N . "_".$this->tableName."_category
				WHERE		categoryName IN (".$categoryNames.")
				AND 		packageID = ".$this->installation->getPackageID();
		WCF::getDB()->sendQuery($sql);
	}

	/** 
	 * Drops columns from domain-option-value table.
	 * 
	 * @param 	array 		$optionIDs  
	 */
	protected function dropColumns($optionIDs)
	{
		foreach ($optionIDs as $optionID)
		{
			$sql = "ALTER TABLE 	cp" . CP_N . "_domain_option_value
					DROP COLUMN	domainOption" . $optionID;
			WCF :: getDB()->sendQuery($sql);
		}
	}
}
?>