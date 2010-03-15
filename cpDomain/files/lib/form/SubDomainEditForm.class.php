<?php
// wcf imports
require_once (CP_DIR . 'lib/acp/form/DomainEditForm.class.php');

/**
 * Shows the subdomain edit form.
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	form
 * @category 	Control Panel
 * @id			$Id$
 */
class SubDomainEditForm extends DomainEditForm
{
	//public $permission = 'admin.domain.canEditDomain';
	
	public $domainID = 0;
	public $url = '';

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters()
	{
		AbstractPage :: readParameters();
		
		if (isset($_REQUEST['domainID']))
		{
			$this->domainID = intval($_REQUEST['domainID']);
			
			require_once (CP_DIR . 'lib/data/domain/DomainEditor.class.php');
			
			$this->domain = new DomainEditor($this->domainID);
			
			if (!$this->domain->domainID)
			{
				throw new IllegalLinkException();
			}
			
			if ($this->domain->userID != WCF :: getUser()->userID)
			{
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * Gets the default values.
	 */
	protected function readDefaultValues()
	{
		$this->domainname = $this->domain->domainname;
		
		$this->parentDomainName = $this->domain->parentDomainName;
		$this->parentDomainID = $this->domain->parentDomainID;
			
		foreach ($this->activeOptions as $key => $option)
		{
			$value = $this->user->{'userOption' . $option['optionID']};
			if ($value !== null)
			{
				$this->activeOptions[$key]['optionValue'] = $value;
			}
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();
		
		WCF :: getTPL()->assign(array (
			'domainID' => $this->domain->domainID, 
			'action' => 'edit', 
			'url' => $this->url, 
			'markedUsers' => 0, 
			'domain' => $this->domain
		));
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		AbstractForm :: save();
		
		// save domain
		$this->domain->update($this->domainname, CPCore :: getUser()->userID, $this->adminID, $this->parentDomainID, $this->activeOptions, $this->additionalFields);
		$this->saved();
		
		// show success message
		WCF :: getTPL()->assign('success', true);
	}
	
	/**
	 * @see Page::show()
	 */
	public function show()
	{
		require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
		PageMenu::setActiveMenuItem('cp.header.menu.domain');
		
		// check permission
		WCF :: getUser()->checkPermission($this->permission);
		
		// get domain options and categories from cache
		$this->readCache();
		
		// show form
		AbstractPage :: show();
	}
	
}
?>