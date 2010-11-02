<?php
require_once (CP_DIR . 'lib/acp/form/DynamicDomainOptionListForm.class.php');
require_once (WCF_DIR . 'lib/page/util/InlineCalendar.class.php');
require_once (WCF_DIR . 'lib/data/user/group/Group.class.php');
require_once (CP_DIR . 'lib/data/user/CPUser.class.php');

/**
 * Shows the domain add form.
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	acp.form
 * @category 	Control Panel
 * @id			$Id$
 */
class DomainAddForm extends DynamicDomainOptionListForm
{
	public $templateName = 'domainAdd';
	public $menuItemName = 'cp.acp.menu.link.domains.add';
	public $permission = 'admin.cp.canAddDomain';

	public $additionalFields = array();
	public $domain;

	public $domainname = '';
	public $domainID = 0;

	public $username;
	public $userID = 0;

	public $adminID = 0;

	public $parentDomainID = 0;

	public $disabled = 0;

	public $registrationDateDay = 0;
	public $registrationDateMonth = 0;
	public $registrationDateYear = '';
	public $registrationDate = 0;

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters()
	{
		parent :: readFormParameters();

		if (isset($_POST['domainname']))
			$this->domainname = StringUtil :: trim($_POST['domainname']);

		if (isset($_POST['username']))
			$this->username = StringUtil :: trim($_POST['username']);

		if (isset($_POST['parentDomainID']))
			$this->parentDomainID = StringUtil :: trim($_POST['parentDomainID']);

		if (isset($_POST['disabled']))
			$this->disabled = intval($_POST['disabled']);

		if (isset($_POST['registrationDateDay']))
			$this->registrationDateDay = intval($_POST['registrationDateDay']);
		if (isset($_POST['registrationDateMonth']))
			$this->registrationDateMonth = intval($_POST['registrationDateMonth']);
		if (!empty($_POST['registrationDateYear']))
			$this->registrationDateYear = intval($_POST['registrationDateYear']);

		if ($this->registrationDateDay && $this->registrationDateMonth && $this->registrationDateYear)
		{
			$this->registrationDate = @gmmktime(0, 0, 0, $this->registrationDateMonth, $this->registrationDateDay, $this->registrationDateYear);
		}

		if (isset($_POST['activeTabMenuItem']))
			$this->activeTabMenuItem = $_POST['activeTabMenuItem'];

		if (isset($_POST['activeSubTabMenuItem']))
			$this->activeSubTabMenuItem = $_POST['activeSubTabMenuItem'];
	}

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

		try
		{
			if (empty($this->username))
			{
				throw new UserInputException('username');
			}

			// get user
			$user = new CPUser(null, null, $this->username);
			if (!$user->userID)
			{
				throw new UserInputException('username', 'notFound');
			}

			if (!Group :: isAccessibleGroup($user->getGroupIDs()))
			{
				throw new UserInputException('username', 'invalidUser');
			}

			if ($user->isCustomer == 0)
			{
				throw new UserInputException('username', 'noCustomer');
			}

			$this->userID = $user->userID;
			$this->adminID = $user->adminID;
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
		$this->additionalFields['registrationDate'] = $this->registrationDate;
		$this->additionalFields['disabled'] = $this->disabled;
		$this->domain = DomainEditor :: create($this->domainname, $this->userID, $this->adminID, 0, $this->activeOptions, $this->additionalFields);
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

		if ($this->parentDomainID != 0 && !array_key_exists($this->parentDomainID, $this->parentDomains))
			throw new UserInputException('parentDomain', 'notValid');
	}

	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		parent :: readData();

		$this->parentDomains = DomainUtil :: getDomains(true, true);

		$this->options = $this->getOptionTree();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();

		InlineCalendar :: assignVariables();

		WCF :: getTPL()->assign(array (
			'domainname' => $this->domainname,
			'username' => $this->username,
			'registrationDateDay' => $this->registrationDateDay,
			'registrationDateMonth' => $this->registrationDateMonth,
			'registrationDateYear' => $this->registrationDateYear,
			'options' => $this->options,
			'parentDomains' => $this->parentDomains,
			'parentDomainID' => $this->parentDomainID,
			'action' => 'add',
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		// set active menu item
		WCFACP :: getMenu()->setActiveMenuItem($this->menuItemName);

		// check permission
		WCF :: getUser()->checkPermission($this->permission);

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
		if (!parent::checkOption($optionName))
			return false;

		$option = $this->cachedOptions[$optionName];
		return ($option['editable'] >= 0 && !$option['disabled']);
	}
}
?>