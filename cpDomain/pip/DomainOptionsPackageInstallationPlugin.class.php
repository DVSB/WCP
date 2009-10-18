<?php
// wcf imports
require_once (WCF_DIR . 'lib/acp/package/plugin/AbstractOptionPackageInstallationPlugin.class.php');
require_once (WCF_DIR . 'lib/data/user/option/UserOptionEditor.class.php');

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
		'searchable', 
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
	 * @see	 AbstractOptionPackageInstallationPlugin::saveOption()
	 */
	protected function saveOption($option, $categoryName, $existingOptionID = 0)
	{
		// default values
		$optionName = $optionType = $defaultValue = $validationPattern = $outputClass = $selectOptions = $enableOptions = $permissions = $options = '';
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
			
		if (isset($option['outputclass']))
			$outputClass = $option['outputclass'];
			
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
		$classFile = WCF_DIR . 'lib/acp/option/OptionType' . ucfirst($optionType) . '.class.php';
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
				AND		packageID = " . $this->installation->getPackageID();
		$result = WCF :: getDB()->getFirstRow($sql);
		
		// update option
		if (!empty($result['optionID']) && $this->installation->getAction() == 'update')
		{
			$domainOption = new DomainOptionEditor(null, $result);
			$domainOption->update($optionName, $categoryName, $optionType, $defaultValue, $validationPattern, $selectOptions, $enableOptions, $required, $editable, $visible, $outputClass, $showOrder, $disabled, $permissions, $options, $additionalData);
		}
		// insert new option
		else
		{
			DomainOptionEditor :: create($optionName, $optionType, $defaultValue, $validationPattern, $selectOptions, $enableOptions, $required, $editable, $visible, $showOrder, $this->installation->getPackageID());
		}
	}
	
	/**
	 * @see	 PackageInstallationPlugin::hasUninstall()
	 */
	public function hasUninstall() 
	{
		// call hasUninstall event
		EventHandler::fireAction($this, 'hasUninstall');

		$sql = "SELECT	COUNT(*) AS count
				FROM	cp" . CP_N . "_".$this->tableName."
				WHERE	packageID = ".$this->installation->getPackageID();
		$installationCount = WCF::getDB()->getFirstRow($sql);
		return $installationCount['count'];
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
		parent :: uninstall();
	}

	/** 
	 * Drops the columns from user option value table from options
	 * deleted by this package update.
	 * 
	 * @param 	string 		$optionNames 
	 */
	protected function deleteOptions($optionNames)
	{
		$sql = "SELECT	optionName
				FROM 	cp" . CP_N . "_domain_option
				WHERE	optionName IN (" . $optionNames . ")
				AND 	packageID = " . $this->installation->getPackageID();
		$result = WCF :: getDB()->sendQuery($sql);
		$optionNames = array ();
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$optionNames[] = $row['optionName'];
		}
		$this->dropColumns($optionNames);
		parent :: deleteOptions($optionNames);
	}

	/** 
	 * Drops columns from domain-value table.
	 * 
	 * @param 	array 		$optionNames  
	 */
	protected function dropColumns($optionNames)
	{
		foreach ($optionNames as $optionName)
		{
			$sql = "ALTER TABLE 	cp" . CP_N . "_domain_option_value
					DROP COLUMN		" . $optionName;
			WCF :: getDB()->sendQuery($sql);
		}
	}
}
?>