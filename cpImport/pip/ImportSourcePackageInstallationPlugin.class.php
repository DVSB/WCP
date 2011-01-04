<?php
require_once (WCF_DIR . 'lib/acp/package/plugin/AbstractXMLPackageInstallationPlugin.class.php');

/**
 * This PIP installs, updates or deletes import sources.
 *
 * @author	Marcel Werk
 * @copyright	2001-2009 WoltLab GmbH
 * @license	WoltLab Burning Board License <http://www.woltlab.com/products/burning_board/license.php>
 * @package	com.woltlab.wbb.importer
 * @subpackage	acp.package.plugin
 * @category 	Burning Board
 */
class ImportSourcePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin
{
	public $tagName = 'importsource';

	/**
	 * Installs import sources
	 */
	public function install()
	{
		parent :: install();

		if (!$xml = $this->getXML())
		{
			return;
		}

		// Create an array with the data blocks (import or delete) from the xml file.
		$importSourceXML = $xml->getElementTree('data');

		// Loop through the array and install import sources
		foreach ($importSourceXML['children'] as $key => $block)
		{
			if (count($block['children']))
			{
				// Handle the import instructions
				if ($block['name'] == 'import')
				{
					// Loop through import sources and create or update them.
					foreach ($block['children'] as $importSourceData)
					{
						// Extract item properties.
						foreach ($importSourceData['children'] as $child)
						{
							if (!isset($child['cdata']))
								continue;
							$importSourceData[$child['name']] = $child['cdata'];
						}

						// check required attributes
						if (!isset($importSourceData['attrs']['name']))
						{
							throw new SystemException("Required 'name' attribute for import source tag is missing.", 13023);
						}

						if (!isset($importSourceData['classpath']))
						{
							throw new SystemException("Required 'classpath' attribute for import source tag is missing.", 13023);
						}

						// get values
						$sourceName = $importSourceData['attrs']['name'];
						$classPath = $importSourceData['classpath'];
						$template = '';
						if (isset($importSourceData['template']))
							$template = $importSourceData['template'];

						// Insert or update items.
						// Update through the mysql "ON DUPLICATE KEY"-syntax.
						$sql = "INSERT INTO		cp" . CP_N . "_import_source
												(packageID, sourceName, classPath, templateName)
								VALUES			(" . $this->installation->getPackageID() . ",
												'" . escapeString($sourceName) . "',
												'" . escapeString($classPath) . "',
												'" . escapeString($template) . "')
								ON DUPLICATE KEY UPDATE 	classPath = VALUES(classPath),
														templateName = VALUES(templateName)";
						WCF :: getDB()->sendQuery($sql);
					}
				}
			}
		}
	}

	/**
	 * @see	 PackageInstallationPlugin::hasUninstall()
	 */
	public function hasUninstall()
	{
		try
		{
			$sql = "SELECT	COUNT(*) AS count
					FROM	cp" . CP_N . "_import_source
					WHERE	packageID = " . $this->installation->getPackageID();
			$installationCount = WCF :: getDB()->getFirstRow($sql);
			return $installationCount['count'];
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	/**
	 * @see	 PackageInstallationPlugin::uninstall()
	 */
	public function uninstall()
	{
		$instanceNo = WCF_N . '_' . $parentPackage->getInstanceNo();
		$sql = "DELETE FROM	cp" . CP_N . "_import_source
				WHERE		packageID = " . $this->installation->getPackageID();
		WCF :: getDB()->sendQuery($sql);
	}
}
?>