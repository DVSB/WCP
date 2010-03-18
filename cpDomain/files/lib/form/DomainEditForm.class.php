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
 * @id			$Id: SubDomainEditForm.class.php 58 2010-03-17 20:20:51Z toby $
 */
class DomainEditForm extends SubDomainAddForm
{
	public $templateName = 'domainEdit';
	public $neededPermissions = ''; //admin.domain.canEditDomain';

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
			
			$this->readDefaultValues();
		}
	}

	/**
	 * Gets the default values.
	 */
	protected function readDefaultValues()
	{
		$this->domainname = $this->domain->domainname;
			
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
			'domainID' => $this->domainID,
			'action' => 'edit',
		));
	}

	/**
	 * @see Form::validate()
	 */
	public function validate()
	{
		// validate dynamic options
		DynamicOptionListForm :: validate();
	}
	
	/**
	 * @see Form::save()
	 */
	public function save()
	{
		AbstractForm :: save();
		
		// save domain
		$this->domain->update('', 0, 0, 0, $this->activeOptions, $this->additionalFields);
		$this->saved();
		
		// show success message
		WCF :: getTPL()->assign('success', true);
	}

}
?>