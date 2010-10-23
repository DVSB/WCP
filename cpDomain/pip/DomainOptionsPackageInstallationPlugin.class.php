<?php
// wcf imports
require_once (WCF_DIR . 'lib/acp/package/plugin/AbstractXMLPackageInstallationPlugin.class.php');

/**
 * Adds Options to Domaintable
 *
 * @author			Tobias Friebel
 * @copyright		2009 Tobias Friebel
 * @license			GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package			com.toby.cp.domain
 * @subpackage		pip
 * @category		ControlPanel
 * @id				$Id$
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
		'showorder', 
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
	 * If our package is about to be installed, use variables instead of constants
	 * 
	 * This is a ugly workaround for a CP-Installation, this will surely break if
	 * you install another CP from an existing CP
	 */
	public function setParamsForInstall()
	{
		$standalonePackage = $this->installation->getPackage()->getParentPackage();
			
		//ugly workaround for CP-Install
		if (!defined('CP_N'))
			define('CP_N', WCF_N . '_' . $standalonePackage->getInstanceNo());
	
		if (!defined('CP_DIR'))
			define('CP_DIR', FileUtil::addTrailingSlash(FileUtil::getRealPath(WCF_DIR.$standalonePackage->getDir())));
		
		require_once (CP_DIR . 'lib/data/domain/DomainOptionEditor.class.php');
	}
	
	/**
	 * Installs option categories and options.
	 */
	public function install()
	{
		parent :: install();
		
		if (!$xml = $this->getXML())
		{
			return;
		}
		
		// create an array with the import and delete instructions from the xml file
		$optionsXML = $xml->getElementTree('data');
		
		// install or uninstall categories and options.
		foreach ($optionsXML['children'] as $key => $block)
		{
			if (count($block['children']))
			{
				// handle the import instructions
				if ($block['name'] == 'import')
				{
					//only possible for installs
					$this->setParamsForInstall();
					
					// loop through categories and options
					foreach ($block['children'] as $child)
					{
						// handle categories					
						if ($child['name'] == 'categories')
						{
							// loop through all categories
							foreach ($child['children'] as $category)
							{
								// check required category name
								if (!isset($category['attrs']['name']))
								{
									throw new SystemException("Required 'name' attribute for option category is missing", 13023);
								}
								
								// default values
								$categoryName = $parentCategoryName = $permissions = $options = '';
								$showOrder = null;
								
								// make xml tags-names (keys in array) to lower case
								$this->keysToLowerCase($category);
								
								// get category data from children (parent, showorder, icon and menuicon)
								foreach ($category['children'] as $data)
								{
									if (!isset($data['cdata']))
										continue;
									$category[$data['name']] = $data['cdata'];
								}
								
								// get and secure values
								$categoryName = escapeString($category['attrs']['name']);
								
								if (isset($category['permissions']))
									$permissions = $category['permissions'];
									
								if (isset($category['options']))
									$options = $category['options'];
									
								if (isset($category['parent']))
									$parentCategoryName = escapeString($category['parent']);
									
								if (!empty($category['showorder']))
									$showOrder = intval($category['showorder']);
									
								if ($showOrder !== null || $this->installation->getAction() != 'update')
								{
									$showOrder = $this->getShowOrder($showOrder, $parentCategoryName, 'parentCategoryName', '_category');
								}
								
								// if a parent category was set and this parent is not in database 
								// or it is a category from a package from other package environment: don't install further.
								if ($parentCategoryName != '')
								{
									$sql = "SELECT	COUNT(categoryID) AS count
											FROM	cp" . CP_N . "_" . $this->tableName . "_category
											WHERE	categoryName = '" . escapeString($parentCategoryName) . "'";
									$parentCategoryCount = WCF :: getDB()->getFirstRow($sql);
									
									// unable to find parent category in dependency-packages: abort installation
									if ($parentCategoryCount['count'] == 0)
									{
										throw new SystemException("Unable to find parent 'option category' with name '" . $parentCategoryName . "' for category with name '" . $categoryName . "'.", 13011);
									}
								}
								
								// save category
								$categoryData = array (
									'categoryName' => $categoryName, 
									'parentCategoryName' => $parentCategoryName, 
									'showOrder' => $showOrder, 
									'permissions' => $permissions, 
									'options' => $options
								);
								$this->saveCategory($categoryData, $category);
							}
						}
						// handle options
						elseif ($child['name'] == 'options')
						{
							// <option> 
							foreach ($child['children'] as $option)
							{
								// extract <category> <optiontype> <optionvalue> <visible> etc
								foreach ($option['children'] as $_child)
								{
									$option[$_child['name']] = $_child['cdata'];
								}
								
								// convert character encoding
								if (CHARSET != 'UTF-8')
								{
									if (isset($option['defaultvalue']))
									{
										$option['defaultvalue'] = StringUtil :: convertEncoding('UTF-8', CHARSET, $option['defaultvalue']);
									}
									if (isset($option['selectoptions']))
									{
										$option['selectoptions'] = StringUtil :: convertEncoding('UTF-8', CHARSET, $option['selectoptions']);
									}
								}
								
								// check required category name
								if (!isset($option['categoryname']))
								{
									throw new SystemException("Required category for option is missing", 13023);
								}
								$categoryName = escapeString($option['categoryname']);
								
								// store option name
								$option['name'] = $option['attrs']['name'];
								
								// children info already stored with name => cdata
								// shrink array 
								unset($option['children']);
								
								if (!preg_match("/^[\w-\.]+$/", $option['name']))
								{
									$matches = array ();
									preg_match_all("/(\W)/", $option['name'], $matches);
									throw new SystemException("The user option '" . $option['name'] . "' has at least one non-alphanumeric character (underscore is permitted): (" . implode("), ( ", $matches[1]) . ").", 13024);
								}
								$this->saveOption($option, $categoryName);
							}
						}
					}
				}
				// handle the delete instructions
				else if ($block['name'] == 'delete' && $this->installation->getAction() == 'update')
				{
					$optionNames = '';
					$categoryNames = '';
					foreach ($block['children'] as $deleteTag)
					{
						// check required attributes
						if (!isset($deleteTag['attrs']['name']))
						{
							throw new SystemException("Required 'name' attribute for '" . $deleteTag['name'] . "'-tag is missing", 13023);
						}
						
						if ($deleteTag['name'] == 'option')
						{
							// build optionnames string
							if (!empty($optionNames))
								$optionNames .= ',';
							$optionNames .= "'" . escapeString($deleteTag['attrs']['name']) . "'";
						}
						elseif ($deleteTag['name'] == 'optioncategory')
						{
							// build categorynames string
							if (!empty($categoryNames))
								$categoryNames .= ',';
							$categoryNames .= "'" . escapeString($deleteTag['attrs']['name']) . "'";
						}
					}
					// delete options
					if (!empty($optionNames))
					{
						$this->deleteOptions($optionNames);
					}
					// elete categories
					if (!empty($categoryNames))
					{
						$this->deleteCategories($categoryNames);
					}
				}
			}
		}
	}

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
		$required = $editable = $disabled = 0;
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
		$classFile = CP_DIR . 'lib/data/domain/options/DomainOptionType' . ucfirst($optionType) . '.class.php';
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
			$userOption->update($optionName, $categoryName, $optionType, $defaultValue, $validationPattern, $selectOptions, $enableOptions, $required, $editable, $showOrder, $disabled, $permissions, $options, $additionalData);
		}
		// insert new option
		else
		{
			DomainOptionEditor :: create($optionName, $categoryName, $optionType, $defaultValue, $validationPattern, $selectOptions, $enableOptions, $required, $editable, $showOrder, $disabled, $permissions, $options, $additionalData, $this->installation->getPackageID());
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
	 * @see	PackageInstallationPlugin::hasUninstall()
	 */
	public function hasUninstall()
	{
		EventHandler :: fireAction($this, 'hasUninstall');
		
		$sql = "SELECT	COUNT(*) AS count
				FROM	cp" . CP_N . "_" . $this->tableName . "
				WHERE	packageID = " . $this->installation->getPackageID();
		$optionCount = WCF :: getDB()->getFirstRow($sql);
		
		$sql = "SELECT 	COUNT(categoryID) AS count
				FROM 	cp" . CP_N . "_" . $this->tableName . "_category
				WHERE	packageID = " . $this->installation->getPackageID();
		$categoryCount = WCF :: getDB()->getFirstRow($sql);
		
		return ($optionCount['count'] > 0 || $categoryCount['count'] > 0);
	}

	/** 
	 * Drops the columns from user option value table from options
	 * installed by this package.
	 */
	public function uninstall()
	{
		// get optionsIDs from package
		$sql = "SELECT	optionID
				FROM 	cp" . CP_N . "_" . $this->tableName . "
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
		
		$sql = "DELETE FROM	cp" . CP_N . "_" . $this->tableName . "
				WHERE		packageID = " . $this->installation->getPackageID();
		WCF :: getDB()->sendQuery($sql);
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
				FROM 	cp" . CP_N . "_" . $this->tableName . "
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
		$sql = "DELETE FROM	cp" . CP_N . "_" . $this->tableName . "
				WHERE		optionName IN (" . $optionNames . ")
				AND 		packageID = " . $this->installation->getPackageID();
		WCF :: getDB()->sendQuery($sql);
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
				FROM 	cp" . CP_N . "_" . $this->tableName . "
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
		$sql = "DELETE FROM	cp" . CP_N . "_" . $this->tableName . "
				WHERE		categoryName IN (" . $categoryNames . ")";
		WCF :: getDB()->sendQuery($sql);
		
		// delete categories
		$sql = "DELETE FROM	cp" . CP_N . "_" . $this->tableName . "_category
				WHERE		categoryName IN (" . $categoryNames . ")
				AND 		packageID = " . $this->installation->getPackageID();
		WCF :: getDB()->sendQuery($sql);
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
			$sql = "ALTER TABLE 	cp" . CP_N . "_" . $this->tableName . "_value
					DROP COLUMN	domainOption" . $optionID;
			WCF :: getDB()->sendQuery($sql);
		}
	}
}
?>
