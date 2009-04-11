<?php
require_once (CP_DIR . 'lib/data/ftp/FTPUserEditor.class.php');
require_once (WCF_DIR . 'lib/form/AbstractSecureForm.class.php');

class FTPAddForm extends AbstractSecureForm
{
	/**
	 * @see AbstractPage::$templateName
	 */
	public $templateName = 'ftpAdd';

	public $password = '';

	public $path = '';

	public $description = '';

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

		if (!CPUtils :: validatePath($this->path, WCF :: getUser()->homeDir, true))
			throw new UserInputException('path', 'invalid');

		if (WCF :: getUser()->ftpaccounts >= WCF :: getUser()->ftpaccountsUsed)
			throw new UserInputException('fpt', 'tomuch');
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
		));
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		parent :: save();

		// create
		$this->ftpUser = FTPUserEditor :: create(WCF :: getUser()->userID,
												 WCF :: getUser()->username,
												 $this->password,
												 $this->path,
												 $this->description
												);
		$this->saved();

		$url = 'index.php?page=FTPList'. SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}
}
?>