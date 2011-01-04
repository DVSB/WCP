<?php
// wbb imports
require_once(WBB_DIR.'lib/system/importer/Importer.class.php');

// wcf imports
require_once(WCF_DIR.'lib/acp/form/ACPForm.class.php');
require_once(WCF_DIR.'lib/acp/package/Package.class.php');

/**
 * Shows the importer form.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2009 WoltLab GmbH
 * @license	WoltLab Burning Board License <http://www.woltlab.com/products/burning_board/license.php>
 * @package	com.woltlab.wbb.importer
 * @subpackage	acp.form
 * @category 	Burning Board
 */
class ImporterForm extends ACPForm {
	public $activeMenuItem = 'wbb.acp.menu.link.system.importer';
	public $neededPermissions = 'admin.maintenance.canImportData';
		
	public $sourceName = '';
	public $sourceData = array();
	public $exporter = null;
	public $useCLI = 0;
	
	/**
	 * @see Page::readParameters
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['sourceName'])) {
			$this->sourceName = $_REQUEST['sourceName'];
			// validate name
			$sql = "SELECT	*
				FROM	wbb".WBB_N."_import_source
				WHERE	sourceName = '".escapeString($this->sourceName)."'";
			$this->sourceData = WCF::getDB()->getFirstRow($sql);
			if (empty($this->sourceData['sourceName'])) {
				require_once(WCF_DIR.'lib/system/exception/IllegalLinkException.class.php');
				throw new IllegalLinkException();
			}
		}
		else {
			// select first source
			$sql = "SELECT	*
				FROM	wbb".WBB_N."_import_source";
			$result = WCF::getDB()->sendQuery($sql);
			if (WCF::getDB()->countRows($result) == 1) {
				$this->sourceData = WCF::getDB()->fetchArray($result);
				$this->sourceName = $this->sourceData['sourceName'];
			}
		}
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['data']) && is_array($_POST['data'])) {
			$tmpData = $_POST['data'];
			foreach ($this->exporter->data as $key => $value) {
				if (!isset($tmpData[$key])) $tmpData[$key] = $value;
			}
			$this->exporter->data = $tmpData;
		}
		if (isset($_POST['settings']) && is_array($_POST['settings'])) {
			$tmpSettings = $_POST['settings'];
			foreach ($this->exporter->settings as $key => $value) {
				if (!isset($tmpSettings[$key])) $tmpSettings[$key] = $value;
			}
			$this->exporter->settings = $tmpSettings;
		}
		if (isset($_POST['useCLI'])) $this->useCLI = intval($_POST['useCLI']);
	}
	
	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		$this->exporter->validate();
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();

		// save settings in session
		WCF::getSession()->register('importSettings', $this->exporter->settings);
		WCF::getSession()->register('importData', $this->exporter->data);
		WCF::getSession()->register('sourceName', $this->sourceName);
		
		// clean db
		$sql = "TRUNCATE TABLE wbb".WBB_N."_import_mapping";
		WCF::getDB()->sendQuery($sql);
		$this->saved();
		
		if ($this->useCLI == 1) {
			WCF::getTPL()->assign(array(
				'sourceName' => $this->sourceName
			));
			WCF::getTPL()->display('importerCLI');
		}
		else {
			// start import
			WCF::getTPL()->assign(array(
				'pageTitle' => WCF::getLanguage()->get('wbb.acp.importer'),
				'url' => 'index.php?action=Importer&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED,
				'progress' => 0
			));
			WCF::getTPL()->display('worker');
		}
		exit;
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		if (empty($this->sourceName)) {
			WCF::getTPL()->assign('availableSources', $this->getAvailableSources());
		}
		else {
			require_once(WBB_DIR.'lib/data/board/Board.class.php');
			WCF::getTPL()->assign(array(
				'sourceName' => $this->sourceName,
				'sourceData' => $this->sourceData,
				'boards' => Board::getBoardSelect(array(), true, true),
				'data' => $this->exporter->data,
				'supportedData' => $this->exporter->supportedData,
				'settings' => $this->exporter->settings,
				'encodings' => Exporter::getAvailableEncodings(),
				'needsPasswordConversion' => $this->exporter->needsPasswordConversion,
				'supportedDatabaseClasses' => $this->exporter->getSupportedDatabaseClasses(),
				'useCLI' => $this->useCLI
			));
		}
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		if (empty($this->sourceName)) $this->templateName = 'importerSelectSource';
		else {
			$this->templateName = 'importerConfig';
			// include class file
			if (!file_exists(WBB_DIR.$this->sourceData['classPath'])) {
				throw new SystemException("unable to find class file '".WBB_DIR.$this->sourceData['classPath']."'", 11000);
			}
			require_once(WBB_DIR.$this->sourceData['classPath']);
			
			// create instance
			$className = StringUtil::getClassName($this->sourceData['classPath']);
			if (!class_exists($className)) {
				throw new SystemException("unable to find class '".$className."'", 11001);
			}
			$this->exporter = new $className();
			
			// disable unsupported data
			if (Package::compareVersion(Importer::getBurningBoardVersion(), '3.0.0 Beta 1', '<')) { // lite 2.0.x
				$this->exporter->supportedData['moderators'] = $this->exporter->supportedData['boardSubscriptions'] = $this->exporter->supportedData['threadRatings'] = $this->exporter->supportedData['threadSubscriptions'] = $this->exporter->supportedData['privateMessages'] = $this->exporter->supportedData['privateMessageFolders'] = 0;
			}
			if (Importer::getDependencyVersion('com.woltlab.wcal.wbb') === false) {
				$this->exporter->supportedData['calendars'] = 0;
				$this->exporter->supportedData['calendarEvents'] = 0;
			}
		}
		
		parent::show();
	}
	
	/**
	 * Returns a list of available sources.
	 * 
	 * @return	array
	 */
	protected function getAvailableSources() {
		$sources = array();
		$sql = "SELECT		sourceName
			FROM		wbb".WBB_N."_import_source
			ORDER BY	sourceName";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$sources[] = $row['sourceName'];
		}
		
		return $sources;
	}
}
?>