<?php
require_once (CP_DIR . 'lib/data/email/EmailEditor.class.php');
require_once (WCF_DIR . 'lib/form/AbstractSecureForm.class.php');

class EmailAddForwardForm extends AbstractSecureForm
{
	/**
	 * @see AbstractPage::$templateName
	 */
	public $templateName = 'emailAddForward';

	public $forward = '';
	
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

		if (isset($_POST['forward']))
			$this->forward = StringUtil :: trim($_POST['forward']);
	}

	/**
	 * @see Form::validate()
	 */
	public function validate()
	{
		parent :: validate();

		if (empty($this->forward))
			throw new UserInputException('forward', 'notempty');
			
		if (!EmailUtil :: isValidEmailaddress($this->forward))
			throw new UserInputException('forward', 'notValid');
			
		if ($this->forward == $this->email->emailaddress_full)
			throw new UserInputException('forward', 'notValid');
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
		));
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		parent :: save();

		$this->email->addForward($this->forward);
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
		PageMenu :: setActiveMenuItem('cp.header.menu.email');

		if (WCF :: getUser()->emailForwards <= WCF :: getUser()->emailForwardsUsed)
		{
			require_once(WCF_DIR.'lib/system/exception/PermissionDeniedException.class.php');
			throw new PermissionDeniedException();
		}

		parent :: show();
	}
}
?>