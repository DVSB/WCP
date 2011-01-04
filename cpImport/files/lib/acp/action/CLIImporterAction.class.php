<?php
require_once(WBB_DIR.'lib/acp/action/ImporterAction.class.php');
require_once(WCF_DIR.'lib/system/exception/NamedUserException.class.php');

/**
 * Does the importer progress (cli).
 * 
 * @author	Marcel Werk
 * @copyright	2001-2009 WoltLab GmbH
 * @license	WoltLab Burning Board License <http://www.woltlab.com/products/burning_board/license.php>
 * @package	com.woltlab.wbb.importer
 * @subpackage	acp.action
 * @category 	Burning Board
 */
class CLIImporterAction extends ImporterAction {
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		AbstractAction::readParameters();
		
		// get session id
		$sessionID = '';
		if (isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
			$sessionID = end($_SERVER['argv']);
		}
		
		// validate session id
		if (empty($sessionID)) {
			throw new NamedUserException('session id is missing');
		}
		
		// get session
		$session = new Session($sessionID);
		if ($session->isCorrupt() || !$session->userID) {
			throw new NamedUserException('session id '.$sessionID.' is not valid');
		}
		
		// check permissions
		if (!$session->getUser()->getPermission('admin.maintenance.canImportData')) {
			throw new NamedUserException('user does not have the permission to import data');
		}
		
		// get import settings
		// importer options
		$this->data = $session->getVar('importData');
		$this->settings = $session->getVar('importSettings');
		$this->sourceName = $session->getVar('sourceName');
		if ($this->data === null || $this->settings === null || $this->sourceName === null) {
			throw new NamedUserException('could not find import settings');
		}
		
		// default data
		$this->data['posts'] = $this->data['threads'];
		$this->data['privateMessageRecipients'] = $this->data['privateMessages'];
		$this->data['pollOptions'] = $this->data['pollOptionVotes'] = $this->data['pollVotes'] = $this->data['polls'];
		
		// get source info
		$sql = "SELECT	*
			FROM	wbb".WBB_N."_import_source
			WHERE	sourceName = '".escapeString($this->sourceName)."'";
		$this->sourceData = WCF::getDB()->getFirstRow($sql);
	
		// include class file
		if (!file_exists(WBB_DIR.$this->sourceData['classPath'])) {
			throw new SystemException("unable to find class file '".WBB_DIR.$this->sourceData['classPath']."'", 11000);
		}
		require_once(WBB_DIR.$this->sourceData['classPath']);
	
		// create exporter instance
		$className = StringUtil::getClassName($this->sourceData['classPath']);
		if (!class_exists($className)) {
			throw new SystemException("unable to find class '".$className."'", 11001);
		}
		$this->exporter = new $className();
		$this->exporter->settings = $this->settings;
		$this->exporter->data = $this->data;
		$this->exporter->init();
		
		// get first step
		if (empty($this->step)) {
			foreach ($this->steps as $step) {
				if ($this->data[$step]) {
					$this->step = $step;
					break;
				}
			}
		}
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		AbstractAction::execute();
		
		// import
		while (!empty($this->data[$this->step])) {
			$this->limit = $this->exporter->limits[$this->step] * 10;
			
			$countFunctionName = 'count'.ucfirst($this->step);
			$exportFunctionName = 'export'.ucfirst($this->step);
			$count = $this->exporter->$countFunctionName();
			if ($count > 0) {
				for ($i = 0; $i < $count; $i += $this->limit) {
					$this->exporter->$exportFunctionName($i, $this->limit);
					$done = $i + $this->limit;
					if ($done > $count) $done = $count;
					echo "\rimporting ".$this->step." (".$done."/".$count.")";
				}
				echo "\n";
			}
			
			$this->step = $this->getNextStep();
		}
		
		// clean up
		$this->cleanup();
		
		// show success message
		echo "import done\n";
		exit;
	}
}
?>