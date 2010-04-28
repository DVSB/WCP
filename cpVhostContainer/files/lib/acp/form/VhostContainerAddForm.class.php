<?php
require_once (WCF_DIR . 'lib/acp/form/ACPForm.class.php');

/**
 * Shows the vhostContainer add form.
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.vhostcontainer
 * @subpackage	acp.form
 * @category 	Control Panel
 * @id			$Id$
 */
class VhostContainerAddForm extends ACPForm
{
	public $templateName = 'vhostContainerAdd';
	public $activeMenuItem = 'cp.acp.menu.link.vhostcontainer.add';
	public $permission = 'admin.cp.canAddVhostContainer';
	
	public $additionalFields = array();
	public $vhostContainer;
	
	public $vhostName = '';
	public $vhostContainerID = 0;

	public $ipAddress = '';
	public $port = '80';
	
	public $vhostType = '';
	public $vhostTypes = array();
	
	public $isContainer = 1;
	public $isIPv6 = 0;
	public $isSSL = 0;

	public $addListenStatement = 0;
	public $addNameStatement = 0;
	public $addServerName = 1;
	
	public $overwriteTemplate = 0;
	public $vhostTemplate;
	
	public $vhostComments;
	
	public $sslCertFile;
	public $sslCertKeyFile;
	public $sslCertChainFile;

	/**
	 * @see Page:readData()
	 */
	public function readData()
	{
		$sql = "SELECT	categoryName
				FROM	wcf" . WCF_N . "_option_category
				WHERE	categoryName LIKE 'cpvhostcontainer.Container%'
						AND parentCategoryName = 'cpvhostcontainer'";
		
		$result = WCF :: getDB()->sendQuery($sql);
		
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$type = explode('.', $row['categoryName']);
			
			if (count($type) != 2)
				continue;
			
			$this->vhostTypes[$type[1]] = WCF :: getLanguage()->get('wcf.acp.option.category.' . $row['categoryName']);
		}
		
		parent :: readData();
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters()
	{
		parent :: readFormParameters();
		
		if (isset($_POST['vhostName']))
			$this->vhostName = StringUtil :: trim($_POST['vhostName']);
			
		if (isset($_POST['ipAddress']))
			$this->ipAddress = StringUtil :: trim($_POST['ipAddress']);
			
		if (isset($_POST['port']))
			$this->port = intval($_POST['port']);
			
		if (isset($_POST['vhostType']))
			$this->vhostType = StringUtil :: trim($_POST['vhostType']);
			
		if (isset($_POST['isContainer']))
			$this->isContainer = intval($_POST['isContainer']);
			
		if (isset($_POST['isIPv6']))
			$this->isIPv6 = intval($_POST['isIPv6']);
			
		if (isset($_POST['isSSL']))
			$this->isSSL = intval($_POST['isSSL']);
			
		if (isset($_POST['addListenStatement']))
			$this->addListenStatement = intval($_POST['addListenStatement']);
			
		if (isset($_POST['addNameStatement']))
			$this->addNameStatement = intval($_POST['addNameStatement']);
			
		if (isset($_POST['addServerName']))
			$this->addServerName = intval($_POST['addServerName']);
			
		if (isset($_POST['overwriteTemplate']))
			$this->overwriteTemplate = intval($_POST['overwriteTemplate']);
			
		if (isset($_POST['vhostTemplate']))
			$this->vhostTemplate = StringUtil :: trim($_POST['vhostTemplate']);
			
		if (isset($_POST['vhostComments']))
			$this->vhostComments = StringUtil :: trim($_POST['vhostComments']);
			
		if (isset($_POST['sslCertFile']))
			$this->sslCertFile = StringUtil :: trim($_POST['sslCertFile']);
			
		if (isset($_POST['sslCertKeyFile']))
			$this->sslCertKeyFile = StringUtil :: trim($_POST['sslCertKeyFile']);
			
		if (isset($_POST['sslCertChainFile']))
			$this->sslCertChainFile = StringUtil :: trim($_POST['sslCertChainFile']);
	}

	/**
	 * @see Form::validate()
	 */
	public function validate()
	{
		if (empty($this->vhostName))
			throw new UserInputException('vhostName', 'empty');
		
		if (empty($this->ipAddress))
			throw new UserInputException('ipAddress', 'empty');
			
		if (!$this->validateIP($this->ipAddress, $this->isIPv6))
			throw new UserInputException('ipAddress', 'notValid');
			
		if (empty($this->port))
			throw new UserInputException('port', 'empty');
			
		if ($this->port < 0 || $this->port > 65535)
			throw new UserInputException('port', 'notValid');
			
		if (empty($this->vhostType))
			throw new UserInputException('vhostType', 'empty');
			
		if (!array_key_exists($this->vhostType, $this->vhostTypes))
			throw new UserInputException('vhostType', 'notValid');
		
		// validate dynamic options
		parent :: validate();
	}
	
	/**
	 * Checks whether it is a valid ip
	 *
	 * @param string $ipAddress
	 * @param bool	$isIPv6
	 *
	 * @return bool
	 */
	private function validateIP($ipAddress, $isIPv6)
	{
		if ($isIPv6 && filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== FALSE)
			return true;
		elseif (!$isIPv6 && filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_RES_RANGE) !== FALSE)
			return true;
		else 
			return false;
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		parent :: save();
		
		// create
		require_once (CP_DIR . 'lib/data/vhost/VhostContainerEditor.class.php');
		$this->additionalFields['isContainer'] = $this->isContainer;
		$this->additionalFields['isIPv6'] = $this->isIPv6;
		$this->additionalFields['isSSL'] = $this->isSSL;
		$this->additionalFields['addListenStatement'] = $this->addListenStatement;
		$this->additionalFields['addNameStatement'] = $this->addNameStatement;
		$this->additionalFields['addServerName'] = $this->addServerName;
		$this->additionalFields['overwriteTemplate'] = $this->overwriteTemplate;
		$this->additionalFields['vhostTemplate'] = $this->vhostTemplate;
		$this->additionalFields['vhostComments'] = $this->vhostComments;
		$this->additionalFields['sslCertFile'] = $this->sslCertFile;
		$this->additionalFields['sslCertKeyFile'] = $this->sslCertKeyFile;
		$this->additionalFields['sslCertChainFile'] = $this->sslCertChainFile;
		$this->vhostContainer = VhostContainerEditor :: create($this->vhostName, $this->ipAddress, $this->port, $this->vhostType, $this->additionalFields);
		$this->saved();
		
		// show empty add form
		WCF :: getTPL()->assign(array (
			'success' => true, 
		));
		
		// reset values
		$this->vhostName = $this->ipAddress = $this->vhostType = $this->vhostTemplate = $this->vhostComments = '';
		$this->port = 80;
		$this->isContainer = $this->addServerName = 1;
		$this->isIPv6 = $this->isSSL = $this->addListenStatement = $this->addNameStatement = $this->overwriteTemplate = 0;
		$this->sslCertFile = $this->sslCertKeyFile = $this->sslCertChainFile = null;
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();
		
		WCF :: getTPL()->assign(array (
			'vhostName' => $this->vhostName, 
			'ipAddress' => $this->ipAddress, 
			'port' => $this->port, 
			'vhostType' => $this->vhostType,
			'vhostTypes' => $this->vhostTypes,
			'isContainer' => $this->isContainer,
			'isIPv6' => $this->isIPv6,
			'isSSL' => $this->isSSL,
			'addListenStatement' => $this->addListenStatement,
			'addNameStatement' => $this->addNameStatement,
			'addServerName' => $this->addServerName,
			'overwriteTemplate' => $this->overwriteTemplate,
			'vhostTemplate' => $this->vhostTemplate,
			'vhostComments' => $this->vhostComments,
			'sslCertFile' => $this->sslCertFile,
			'sslCertKeyFile' => $this->sslCertKeyFile,
			'sslCertChainFile' => $this->sslCertChainFile,
			'action' => 'add',
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		// check permission
		WCF :: getUser()->checkPermission($this->permission);
		
		// show form
		parent :: show();
	}
}
?>