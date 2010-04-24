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
	public $menuItemName = 'cp.acp.menu.link.vhostcontainer.add';
	public $permission = 'admin.cp.canAddVhostContainer';
	
	public $additionalFields = array();
	public $vhostContainer;
	
	public $vhostName = '';
	public $vhostContainerID = 0;

	public $ipAddress = '';
	public $port = '80';
	
	public $vhostType = '';
	
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
			
		if (empty($this->port))
			throw new UserInputException('port', 'empty');
			
		if (empty($this->vhostType))
			throw new UserInputException('vhostType', 'empty');
		
		// validate dynamic options
		parent :: validate();
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		parent :: save();
		
		// create
		require_once (CP_DIR . 'lib/data/vhost/VhostContainerEditor.class.php');
		
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
		$this->vhostContainer = VhostContainerEditor :: create($this->vhostName, $this->ipAddress, $this->port, $this->vhostType, $this->additionalFields);
		$this->saved();
		
		// show empty add form
		WCF :: getTPL()->assign(array (
			'success' => true, 
			'newVhostContainer' => $this->vhostContainer,
		));
		
		// reset values
		$this->vhostName = '';
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();
		
		InlineCalendar :: assignVariables();
		
		WCF :: getTPL()->assign(array (
			'vhostName' => $this->vhostName, 
			
			'action' => 'add',
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		// set active menu item
		WCFACP :: getMenu()->setActiveMenuItem($this->menuItemName);
		
		// check permission
		WCF :: getUser()->checkPermission($this->permission);
		
		// show form
		parent :: show();
	}
}
?>