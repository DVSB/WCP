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
	public $permission = 'admin.cp.canEditVhostContainer';

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
			$this->vhostName = $this->vhostContainer->vhostName;
			$this->ipAddress = $this->vhostContainer->ipAddress;
			$this->port = $this->vhostContainer->port;
			$this->vhostType = $this->vhostContainer->vhostType;
			$this->isContainer = $this->vhostContainer->isContainer;
			$this->isIPv6 = $this->vhostContainer->isIPv6;
			$this->isSSL = $this->vhostContainer->isSSL;
			$this->addListenStatement = $this->vhostContainer->addListenStatement;
			$this->addNameStatement = $this->vhostContainer->addNameStatement;
			$this->addServerName = $this->vhostContainer->addServerName;
			$this->overwriteTemplate = $this->vhostContainer->overwriteTemplate;
			$this->vhostTemplate = $this->vhostContainer->vhostTemplate;
			$this->vhostComments = $this->vhostContainer->vhostComments;
			$this->sslCertFile = $this->vhostContainer->sslCertFile;
			$this->sslCertKeyFile = $this->vhostContainer->sslCertKeyFile;
			$this->sslCertChainFile = $this->vhostContainer->sslCertChainFile;
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
		));
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		ACPForm :: save();

		// save vhostContainer
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
		$this->vhostContainer->update($this->vhostName, $this->ipAddress, $this->port, $this->vhostType, $this->additionalFields);

		JobhandlerUtils :: addJob('createVhost', 0, array('vhostContainerID' => $this->vhostContainerID), 'asap');

		$this->saved();

		// show success message
		WCF :: getTPL()->assign('success', true);
	}
}
?>