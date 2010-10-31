<?php
require_once (CP_DIR . 'lib/data/email/EmailEditor.class.php');
require_once (WCF_DIR . 'lib/form/AbstractSecureForm.class.php');

class EmailAddForm extends AbstractSecureForm
{
	/**
	 * @see AbstractPage::$templateName
	 */
	public $templateName = 'emailAdd';

	public $neededPermissions = 'cp.email.canAddAddress';

	public $emailaddress = '';

	public $isCatchall = 0;

	public $domains = array();

	public $domainID = 0;

	public $email;

	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		$this->domains = EmailUtil :: getDomainsForUser(CPCore :: getUser()->userID);

		parent :: readData();
	}

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters()
	{
		parent :: readFormParameters();

		if (isset($_POST['emailaddress']))
			$this->emailaddress = StringUtil :: trim($_POST['emailaddress']);

		if (isset($_POST['domainID']))
			$this->domainID = intval($_POST['domainID']);

		if (isset($_POST['isCatchall']))
			$this->isCatchall = intval($_POST['isCatchall']);
	}

	/**
	 * @see Form::validate()
	 */
	public function validate()
	{
		parent :: validate();

		if (empty($this->emailaddress))
			throw new UserInputException('emailaddress', 'empty');

		if ($this->domainID == 0)
			throw new UserInputException('domainID', 'empty');

		if (!array_key_exists($this->domainID, $this->domains))
			throw new UserInputException('domainID', 'notValid');

		if ($this->isCatchall && !EmailUtil :: isAvailableCatchall($this->domainID))
			throw new UserInputException('isCatchall', 'notValid');

		if (!EmailUtil :: isValidEmailaddress($this->emailaddress . '@' . $this->domains[$this->domainID]))
			throw new UserInputException('emailaddress', 'notValid');
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();

		WCF :: getTPL()->assign(array (
			'emailaddress' => $this->emailaddress,
			'domains' => $this->domains,
			'domainID' => $this->domainID,
			'isCatchall' => $this->isCatchall,
			'action' => 'add'
		));
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		parent :: save();

		// create
		$this->email = EmailEditor :: create(WCF :: getUser()->userID, $this->emailaddress, $this->domains[$this->domainID], $this->domainID, $this->isCatchall);
		$this->saved();

		$url = 'index.php?page=EmailList' . SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil :: redirect($url);
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		require_once (WCF_DIR . 'lib/page/util/menu/PageMenu.class.php');
		PageMenu :: setActiveMenuItem('cp.header.menu.email');

		if (WCF :: getUser()->emailAddresses <= WCF :: getUser()->emailAddressesUsed)
		{
			require_once (WCF_DIR . 'lib/system/exception/PermissionDeniedException.class.php');
			throw new PermissionDeniedException();
		}

		parent :: show();
	}
}
?>