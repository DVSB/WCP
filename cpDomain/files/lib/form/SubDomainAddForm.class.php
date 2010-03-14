<?php
require_once (CP_DIR . 'lib/acp/form/DomainAddForm.class.php');
require_once (CP_DIR . 'lib/data/user/CPUser.class.php');

/**
 * Shows the subdomain add form.
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	acp.form
 * @category 	Control Panel
 * @id			$Id$
 */
class SubDomainAddForm extends DomainAddForm
{
	public $templateName = 'subdomainAdd';
	public $menuItemName = 'cp.acp.menu.link.domains.add';
	//public $permission = 'admin.cp.canAddDomain';

	/**
	 * @see Form::validate()
	 */
	public function validate()
	{
		// validate static user options 
		try
		{
			$this->validateDomainname($this->domainname);
		}
		catch (UserInputException $e)
		{
			$this->errorType[$e->getField()] = $e->getType();
		}
		
		// validate dynamic options
		parent :: validate();
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		AbstractForm :: save();
		
		// create
		require_once (CP_DIR . 'lib/data/domain/DomainEditor.class.php');
		$this->domain = DomainEditor :: create($this->domainname, CPCore :: getUser()->userID, $this->adminID, $this->parentDomainID, $this->activeOptions, $this->additionalFields);
		$this->saved();
		
		// show empty add form
		WCF :: getTPL()->assign(array (
			'success' => true, 
			'newDomain' => $this->domain,
		));
		
		// reset values
		$this->domainname = '';
	
		foreach ($this->activeOptions as $key => $option)
		{
			unset($this->activeOptions[$key]['optionValue']);
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();
		
		WCF :: getTPL()->assign(array (
			'domainname' => $this->domainname, 
			'options' => $this->options, 
			'action' => 'add',
			'activeTabMenuItem' 	=> $this->activeTabMenuItem,
			'activeSubTabMenuItem' 	=> $this->activeSubTabMenuItem
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		// set active menu item
		WCF :: getMenu()->setActiveMenuItem($this->menuItemName);
		
		// check permission
		WCF :: getUser()->checkPermission($this->permission);
		
		// get domain options and categories from cache
		$this->readCache();
		
		// show form
		parent :: show();
	}
}
?>