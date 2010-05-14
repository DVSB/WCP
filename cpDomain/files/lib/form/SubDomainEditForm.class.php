<?php

require_once (CP_DIR . 'lib/form/SubDomainAddForm.class.php');

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
class SubDomainEditForm extends SubDomainAddForm
{
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

			if ($this->domain->userID != WCF :: getUser()->userID || !$this->domain->parentDomainID)
			{
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		if (!count($_POST))
		{
			// default values
			$this->readDefaultValues();
		}
		
		parent :: readData();
	}
	
	/**
	 * Gets the default values.
	 */
	protected function readDefaultValues()
	{
		$this->domainname = $this->domain->domainname;
		
		$this->parentDomainName = $this->domain->parentDomainName;
		$this->parentDomainID = $this->domain->parentDomainID;
		
		if ($this->parentDomainName)
			$this->domainname = str_ireplace('.'.$this->parentDomainName, '', $this->domainname);
			
		foreach ($this->activeOptions as $key => $option)
		{
			$value = $this->domain->{'domainOption' . $option['optionID']};
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
			'action' => 'edit',
		));
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		AbstractForm :: save();
		
		// save domain
		$this->domain->update($this->domainname, 0, 0, $this->parentDomainID, $this->activeOptions, $this->additionalFields);
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
		PageMenu :: setActiveMenuItem('cp.header.menu.domain');
		
		if (!WCF::getUser()->userID) 
		{
			throw new PermissionDeniedException();
		}
		
		// check permission
		if (!empty($this->neededPermissions)) WCF :: getUser()->checkPermission($this->neededPermissions);
		
		// get domain options and categories from cache
		$this->readCache();
		
		// show form
		DynamicOptionListForm :: show();
	}
}
?>