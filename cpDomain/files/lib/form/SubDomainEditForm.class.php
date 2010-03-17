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
	public $templateName = 'subDomainEdit';
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
	 * @see Form::save()
	 */
	public function save()
	{
		AbstractForm :: save();
		
		// save domain
		$this->domain->update($this->domainname, CPCore :: getUser()->userID, CPCore :: getUser()->adminID, $this->parentDomainID, $this->activeOptions, $this->additionalFields);
		$this->saved();
		
		// show success message
		WCF :: getTPL()->assign('success', true);
	}

}
?>