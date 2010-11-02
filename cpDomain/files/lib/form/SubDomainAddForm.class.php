<?php
require_once (CP_DIR . 'lib/acp/form/DynamicDomainOptionListForm.class.php');
require_once (CP_DIR . 'lib/data/user/CPUser.class.php');

/**
 * Shows the subdomain add form.
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	form
 * @category 	Control Panel
 * @id			$Id$
 */
class SubDomainAddForm extends DynamicDomainOptionListForm
{
	public $templateName = 'subDomainAdd';
	public $neededPermissions = 'cp.domain.canAddSubDomain';

	public $additionalFields = array();
	public $domain;

	public $domainname = '';
	public $domainID = 0;

	public $parentDomainID = 0;

	public $deactivated = 0;

	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		$this->parentDomains = DomainUtil :: getDomainsForUser(CPCore :: getUser()->userID);

		parent :: readData();

		$this->options = $this->getOptionTree();
	}

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters()
	{
		parent :: readFormParameters();

		if (isset($_POST['domainname']))
			$this->domainname = StringUtil :: trim($_POST['domainname']);

		if (isset($_POST['parentDomainID']))
			$this->parentDomainID = StringUtil :: trim($_POST['parentDomainID']);

		if (isset($_POST['deactivated']))
			$this->deactivated = intval($_POST['deactivated']);

		if (isset($_POST['activeTabMenuItem']))
			$this->activeTabMenuItem = $_POST['activeTabMenuItem'];

		if (isset($_POST['activeSubTabMenuItem']))
			$this->activeSubTabMenuItem = $_POST['activeSubTabMenuItem'];

		// check security token
		$this->checkSecurityToken();
	}

	/**
	 * Validates the security token.
	 */
	protected function checkSecurityToken()
	{
		if (!isset($_POST['t']) || !WCF::getSession()->checkSecurityToken($_POST['t']))
		{
			throw new IllegalLinkException();
		}
	}

	/**
	 * @see Form::validate()
	 */
	public function validate()
	{
		if ($this->parentDomainID == 0)
			throw new UserInputException('parentDomain', 'empty');

		if (!array_key_exists($this->parentDomainID, $this->parentDomains))
			throw new UserInputException('parentDomain', 'notValid');

		try
		{
			$this->validateDomainname($this->domainname . '.' . $this->parentDomains[$this->parentDomainID]);
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
		$this->additionalFields['registrationDate'] = TIME_NOW;
		$this->additionalFields['deactivated'] = $this->deactivated;
		$this->domain = DomainEditor :: create($this->domainname . '.' . $this->parentDomains[$this->parentDomainID], CPCore :: getUser()->userID, CPCore :: getUser()->adminID, $this->parentDomainID, $this->activeOptions, $this->additionalFields);
		$this->saved();

		$url = 'index.php?page=DomainList' . SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil :: redirect($url);
	}

	/**
	 * Throws a InputException if the domainname is not unique or not valid.
	 *
	 * @param	string		$domainname
	 */
	protected function validateDomainname($domainname)
	{
		if (empty($domainname))
		{
			throw new UserInputException('domainname');
		}

		if (!DomainUtil :: isValidDomainname($domainname))
		{
			throw new UserInputException('domainname', 'notValid');
		}

		if (!DomainUtil :: isAvailableDomainname($domainname, $this->domainID))
		{
			throw new UserInputException('domainname', 'notUnique');
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
			'domainID' => $this->domainID,
			'parentDomains' => $this->parentDomains,
			'parentDomainID' => $this->parentDomainID,
			'deactivated' => $this->deactivated,
			'action' => 'add'
		));
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

		if (WCF :: getUser()->subdomains <= WCF :: getUser()->subdomainsUsed)
		{
			require_once(WCF_DIR.'lib/system/exception/PermissionDeniedException.class.php');
			throw new PermissionDeniedException();
		}

		// check permission
		if (!empty($this->neededPermissions)) WCF :: getUser()->checkPermission($this->neededPermissions);

		// get domain options and categories from cache
		$this->readCache();

		// show form
		parent :: show();
	}

	/**
	 * @see DynamicOptionListForm::checkOption()
	 */
	protected function checkOption($optionName)
	{
		if (!parent ::  checkOption($optionName))
			return false;

		$option = $this->cachedOptions[$optionName];
		// show options visible for and editable by user
		return ($option['editable'] == 1 && !$option['disabled']);
	}
}
?>
