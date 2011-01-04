<?php
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');

/**
 * Does the importer progress.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2009 WoltLab GmbH
 * @license	WoltLab Burning Board License <http://www.woltlab.com/products/burning_board/license.php>
 * @package	com.woltlab.wbb.importer
 * @subpackage	acp.action
 * @category 	Burning Board
 */
class ImporterAction extends AbstractAction {
	public $step = '';
	public $offset = 0;
	public $data = array();
	public $settings = array();
	public $sourceName = '';
	public $limit = 1;
	public $sourceData = array();
	public $steps = array('groups', 'avatars', 'userOptions', 'users', 'boards', 'boardPermissions', 'moderators', 'threads',
			'threadRatings', 'posts', 'privateMessageFolders', 'privateMessages',
			'privateMessageRecipients', 'attachments', 'boardSubscriptions', 'threadSubscriptions',
			'polls', 'pollOptions', 'pollOptionVotes', 'pollVotes', 'smilies', 'calendars', 'calendarEvents');
	
	/**
	 * Creates a new ImporterAction object.
	 */
	public function __construct() {
		try {
			parent::__construct();
		}
		catch (SystemException $e) {
			WCF::getTPL()->assign('e', $e);
			WCF::getTPL()->display('workerException');
		}
	}
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// parameters
		if (isset($_REQUEST['step'])) $this->step = $_REQUEST['step'];
		if (isset($_REQUEST['offset'])) $this->offset = intval($_REQUEST['offset']);
		
		// importer options
		$this->data = WCF::getSession()->getVar('importData');
		// default data
		$this->data['posts'] = $this->data['threads'];
		$this->data['privateMessageRecipients'] = $this->data['privateMessages'];
		$this->data['pollOptions'] = $this->data['pollOptionVotes'] = $this->data['pollVotes'] = $this->data['polls'];
		
		$this->settings = WCF::getSession()->getVar('importSettings');
		
		// get source info
		$this->sourceName = WCF::getSession()->getVar('sourceName');
		$sql = "SELECT	*
			FROM	wbb".WBB_N."_import_source
			WHERE	sourceName = '".escapeString($this->sourceName)."'";
		$this->sourceData = WCF::getDB()->getFirstRow($sql);
		
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
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.maintenance.canImportData');
		
		$this->limit = $this->exporter->limits[$this->step];
		if (in_array($this->step, $this->steps)) {
			if ($this->data[$this->step]) {
				$countFunctionName = 'count'.ucfirst($this->step);
				$exportFunctionName = 'export'.ucfirst($this->step);
				$count = $this->exporter->$countFunctionName();
				if ($count > $this->offset) {
					$this->exporter->$exportFunctionName($this->offset, $this->limit);
					$this->calcProgress($this->offset, $count);
					$this->nextLoop($this->offset, $count);
					return;
				}
			}
		}
		else {
			throw new SystemException("unknown step '".$this->step."'");
		}
		
		$this->executed();
		$this->calcProgress();
		$this->nextStep();
	}
	
	/**
	 * Shows the workerNext tpl.
	 * 
	 * @param	string		$step
	 * @param	string		$nextStep
	 * @param	string		$stepTitle
	 * @param	string		$url
	 */
	protected function showNextPage($step, $nextStep, $stepTitle, $url) {
		WCF::getTPL()->assign(array(
			'step' => $step,
			'nextStep' => $nextStep,
			'stepTitle' => $stepTitle,
			'url' => $url
		));
		WCF::getTPL()->display('workerNext');
		exit;
	}
	
	/**
	 * Calculates the progress bar.
	 * 
	 * @param	integer		$offset
	 * @param	integer		$count
	 */
	protected function calcProgress($offset = 1, $count = 1) {
		// steps
		$steps = $i = 0;
		foreach ($this->steps as $step) {
			if ($step == $this->step) $i = $steps; 
			if ($this->data[$step]) $steps++;
		}
		
		$part = 100 / $steps;
		$progress = $part * $i;
		
		// current step progress
		$progress += $offset / $count * $part;
		
		// format
		$progress = round($progress, 0);
		WCF::getTPL()->assign('progress', $progress);
	}
	
	/**
	 * Forwards to next loop of the current step.
	 * 
	 * @param	integer		$count
	 */
	protected function nextLoop($offset, $count) {
		$this->showNextPage($this->step, $this->step, WCF::getLanguage()->get('wbb.acp.importer.progress.'.$this->step).' ('.round($offset / $count * 100, 0).'%)', 'index.php?action=Importer&step='.$this->step.'&offset='.($offset + $this->limit).'&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
	}
	
	protected function getNextStep() {
		$nextStep = '';
		$key = array_search($this->step, $this->steps);
		for ($i = $key + 1, $j = count($this->steps); $i < $j; $i++) {
			if ($this->data[$this->steps[$i]]) {
				$key = $i;
				$nextStep = $this->steps[$i];
				break;
			}
		}
		
		return $nextStep;
	}
	
	/**
	 * Determines the next step.
	 */
	protected function nextStep() {
		// get next step
		$nextStep = $this->getNextStep();
		
		if (empty($nextStep)) {
			$this->cleanup();
			
			// show finish
			WCF::getTPL()->assign('stepTitle', WCF::getLanguage()->get('wbb.acp.importer.progress.finish'));
			WCF::getTPL()->display('workerFinish');
			exit;
		}
		else {
			$this->showNextPage($this->step, $nextStep, WCF::getLanguage()->get('wbb.acp.importer.progress.'.$this->step).' (100%)', 'index.php?action=Importer&step='.$nextStep.'&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		}
	}
	
	/**
	 * cleans up database and caches.
	 */
	protected function cleanup() {
		// clean db
		$sql = "TRUNCATE TABLE wbb".WBB_N."_import_mapping";
		WCF::getDB()->sendQuery($sql);
		
		// clear cache
		WCF::getCache()->clear(WCF_DIR.'cache', '*.php', true);
		WCF::getCache()->clear(WBB_DIR.'cache', '*.php', true);
		
		// reset all sessions
		Session::resetSessions();
	}
}
?>