<?php
// wcf imports
require_once (CP_DIR . 'lib/acp/form/VhostContainerAddForm.class.php');

/**
 * Shows the vhostcontainer edit form.
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.vhostcontainer
 * @subpackage	acp.form
 * @category 	Control Panel
 * @id			$Id$
 */
class VhostContainerEditForm extends VhostContainerAddForm
{
	//public $permission = 'admin.domain.canEditDomain';
	
	public $vhostContainerID = 0;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters()
	{
		parent :: readParameters();
		
		if (isset($_REQUEST['vhostContainerID']))
		{
			$this->vhostContainerID = intval($_REQUEST['vhostContainerID']);
			
			require_once (CP_DIR . 'lib/data/vhost/VhostContainerEditor.class.php');
			
			$this->vhostContainer = new VhostContainerEditor($this->vhostContainerID);
			
			if (!$this->vhostContainer->vhostContainerID)
			{
				throw new IllegalLinkException();
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
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();
		
		WCF :: getTPL()->assign(array (
			'vhostContainerID' => $this->vhostContainer->vhostContainerID, 
			'action' => 'edit', 
			'vhostContainer' => $this->vhostContainer
		));
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		ACPForm :: save();
		
		// save user
		$this->vhostContainer->update($this->vhostName, $this->additionalFields);
		$this->saved();
		
		// show success message
		WCF :: getTPL()->assign('success', true);
	}
}
?>