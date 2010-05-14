<?php
require_once (CP_DIR . 'lib/data/email/EmailEditor.class.php');
require_once (WCF_DIR . 'lib/form/AbstractSecureForm.class.php');

class EmailSetAccountPWForm extends AbstractSecureForm
{
	/**
	 * @see AbstractPage::$templateName
	 */
	public $templateName = 'emailSetAccountPW';
	
	public $neededPermissions = array('cp.email.canAddAddress', 'cp.email.canAddAccount');

	public $password = '';
	
	public $email;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters()
	{
		if (isset($_REQUEST['mailID']))
			$this->email = new EmailEditor($_REQUEST['mailID']);
		
		if (!$this->email->mailID)
		{
			throw new IllegalLinkException();
		}
			
		if ($this->email->userID != WCF :: getUser()->userID)
		{
			throw new PermissionDeniedException();
		}

		parent :: readParameters();
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters()
	{
		parent :: readFormParameters();

		if (isset($_POST['password']))
			$this->password = StringUtil :: trim($_POST['password']);
	}

	/**
	 * @see Form::validate()
	 */
	public function validate()
	{
		parent :: validate();

		if (empty($this->password))
			throw new UserInputException('password', 'notempty');
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();

		WCF :: getTPL()->assign(array (
			'mailID' => $this->email->mailID,
			'emailaddress_full' => $this->email->emailaddress_full,
			'isUpdate' => (bool) $this->email->accountID,
		));
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		parent :: save();

		if (!$this->email->accountID)
			$this->email->addAccount($this->password);
		else
			$this->email->updateAccount($this->password);
		
		$this->saved();

		$url = 'index.php?page=EmailDetail&mailID=' . $this->email->mailID . SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
		PageMenu::setActiveMenuItem('cp.header.menu.email');

		//nur sperren wenn man ein neues Mailkonto anlegt und eigentlich keine mehr zur Verfügung hat
		if (WCF :: getUser()->emailAccounts <= WCF :: getUser()->emailAccountsUsed && !$this->email->accountID)
		{
			require_once(WCF_DIR.'lib/system/exception/PermissionDeniedException.class.php');
			throw new PermissionDeniedException();
		}

		parent :: show();
	}
}
?>