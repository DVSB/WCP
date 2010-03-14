<?php
// wcf imports
require_once (WCF_DIR . 'lib/page/AbstractPage.class.php');
require_once (CP_DIR . 'lib/data/domain/DomainEditor.class.php');

/**
 * Actions on Domains
 *
 * @author			Tobias Friebel
 * @copyright		2009 Tobias Friebel
 * @license			GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package			com.toby.cp.domain
 * @subpackage		acp.page
 * @category 		ControlPanel
 * @id				$Id$
 */
class DomainActionPage extends AbstractPage
{
	public $domainID = 0;
	public static $validFunctions = array (
		'mark', 
		'unmark', 
		'unmarkAll', 
		'deleteMarked'
	);

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters()
	{
		parent :: readParameters();
		
		if (isset($_REQUEST['domainID']))
			$this->domainID = ArrayUtil :: toIntegerArray($_REQUEST['domainID']);
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		parent :: show();
		
		if (in_array($this->action, self :: $validFunctions))
		{
			$this->{$this->action}();
		}
	}

	/**
	 * Marks a user.
	 */
	public function mark()
	{
		if (!is_array($this->domainID))
			$this->domainID = array (
				$this->domainID
			);
		foreach ($this->domainID as $domainID)
		{
			$markedDomains = WCF :: getSession()->getVar('markedDomains');
			if ($markedDomains == null || !is_array($markedDomains))
			{
				$markedDomains = array (
					$domainID
				);
				WCF :: getSession()->register('markedDomains', $markedDomains);
			}
			else
			{
				if (!in_array($domainID, $markedDomains))
				{
					array_push($markedDomains, $domainID);
					WCF :: getSession()->register('markedDomains', $markedDomains);
				}
			}
		}
	}

	/**
	 * Unmarks a user.
	 */
	public function unmark()
	{
		if (!is_array($this->domainID))
			$this->domainID = array (
				$this->domainID
			);
		foreach ($this->domainID as $domainID)
		{
			$markedDomains = WCF :: getSession()->getVar('markedDomains');
			if (is_array($markedDomains) && in_array($domainID, $markedDomains))
			{
				$key = array_search($domainID, $markedDomains);
				
				unset($markedDomains[$key]);
				if (count($markedDomains) == 0)
				{
					self :: unmarkAll();
				}
				else
				{
					WCF :: getSession()->register('markedDomains', $markedDomains);
				}
			}
		}
	}

	/**
	 * Unmarks all marked users.
	 */
	public static function unmarkAll()
	{
		DomainEditor :: unmarkAll();
	}

	/**
	 * Deletes marked users.
	 */
	public function deleteMarked()
	{
		WCF :: getUser()->checkPermission('admin.user.canDeleteUser');
		
		$domainIDs = WCF :: getSession()->getVar('markedDomains');
		if (!is_array($domainIDs))
			$domainIDs = array ();
		$deletedDomains = 0;

		// check permission
		if (count($domainIDs) > 0)
		{
			$deletedDomains = DomainEditor :: deleteDomains($domainIDs);
		}
		
		self :: unmarkAll();
		HeaderUtil :: redirect('index.php?page=DomainList&deletedDomains=' . $deletedDomains . SID_ARG_2ND_NOT_ENCODED);
		exit();
	}
}
?>