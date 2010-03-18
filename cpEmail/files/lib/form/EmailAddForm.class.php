<?php
require_once (CP_DIR . 'lib/data/email/EmailEditor.class.php');
require_once (WCF_DIR . 'lib/form/AbstractSecureForm.class.php');

class EmailAddForm extends AbstractSecureForm
{
	/**
	 * @see AbstractPage::$templateName
	 */
	public $templateName = 'emailAdd';
	
	public $password = '';
	
	public $path = '';
	
	public $description = '';
	
	public $ftpAccount;

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters()
	{
		parent :: readFormParameters();
		
		if (isset($_POST['password']))
			$this->password = StringUtil :: trim($_POST['password']);
		
		if (isset($_POST['path']))
			$this->path = StringUtil :: trim($_POST['path']);
		
		if (isset($_POST['description']))
			$this->description = StringUtil :: trim($_POST['description']);
	}

	/**
	 * @see Form::validate()
	 */
	public function validate()
	{
		parent :: validate();
		
		if (empty($this->password))
			throw new UserInputException('password', 'notempty');
		
		if (empty($this->path))
			throw new UserInputException('path', 'notempty');
		
		if (!CPUtils :: validatePath(WCF :: getUser()->homeDir . $this->path, WCF :: getUser()->homeDir))
			throw new UserInputException('path', 'invalid');
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();
		
		WCF :: getTPL()->assign(array (
			'password' => $this->password, 
			'path' => $this->path, 
			'description' => $this->description, 
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
		$this->ftpAccount = FTPUserEditor :: create(WCF :: getUser()->userID, WCF :: getUser()->username, $this->password, WCF :: getUser()->homeDir . '/' . $this->path, $this->description);
		$this->saved();
		
		$url = 'index.php?page=FTPList' . SID_ARG_2ND_NOT_ENCODED;
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