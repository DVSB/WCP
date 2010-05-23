<?php
// wcf imports
require_once (CP_DIR . 'lib/acp/form/DomainAddForm.class.php');

/**
 * Shows the domain edit form.
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	acp.form
 * @category 	Control Panel
 * @id			$Id$
 */
class DomainEditForm extends DomainAddForm
{
	public $permission = 'admin.cp.canEditDomains';
	
	public $domainID = 0;
	public $url = '';

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters()
	{
		parent :: readParameters();
		
		if (isset($_REQUEST['domainID']))
		{
			$this->domainID = intval($_REQUEST['domainID']);
			
			require_once (CP_DIR . 'lib/data/domain/DomainEditor.class.php');
			
			$this->domain = new DomainEditor($this->domainID);
			
			if (!$this->domain->domainID)
			{
				throw new IllegalLinkException();
			}
			
			if ($this->domain->adminID != WCF :: getUser()->userID && !WCF :: getUser()->getPermission('admin.general.isSuperAdmin'))
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
		
		$this->username = $this->domain->username;
		$this->userID = $this->domain->userID;
		
		$this->adminname = $this->domain->adminname;
		$this->adminID = $this->domain->adminID;
		
		$this->parentDomainName = $this->domain->parentDomainName;
		$this->parentDomainID = $this->domain->parentDomainID;
		
		$this->registrationDateDay = gmdate('d', $this->domain->registrationDate);
		$this->registrationDateMonth = gmdate('m', $this->domain->registrationDate);
		$this->registrationDateYear = gmdate('Y', $this->domain->registrationDate);
			
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
		
		// save user
		$this->additionalFields['registrationDate'] = $this->registrationDate;
		$this->domain->update($this->domainname, $this->userID, $this->adminID, 0, $this->activeOptions, $this->additionalFields);
		$this->saved();
		
		// show success message
		WCF :: getTPL()->assign('success', true);
	}
}
?>