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
	
	public $useTemplate = 0;
	public $vhostTemplate = '';
	
	public $vhostComments = '';
	
	public $sslCertFile = '';
	public $sslCertKeyFile = '';
	public $sslCertChainFile = '';

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
			
		
	}

	/**
	 * @see Form::validate()
	 */
	public function validate()
	{
		if (empty($this->vhostName))
			throw new UserInputException('vhostName', 'empty');
		
		
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
		
		$this->vhostContainer = VhostContainerEditor :: create($this->vhostName, $this->additionalFields);
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