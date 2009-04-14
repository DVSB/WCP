<?php
require_once (WCF_DIR . 'lib/acp/form/ACPForm.class.php');

class MySQLRootDataForm extends ACPForm
{
	// system
	public $templateName = 'mysqlRootData';

	private $rootUser = '';
	private $rootPassword = '';

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters()
	{
		parent :: readFormParameters();

		if (isset($_POST['rootUser']))
			$this->rootUser = $_POST['rootUser'];

		if (isset($_POST['rootPassword']))
			$this->rootPassword = $_POST['rootPassword'];
	}

	/**
	 * @see Form::validate()
	 */
	public function validate()
	{
		parent :: validate();

		// confirm master password
		if (empty($this->rootUser))
		{
			throw new UserInputException('rootUser');
		}

		if (empty($this->rootPassword))
		{
			throw new UserInputException('rootPassword');
		}
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		$file = new File(CP_DIR . 'mysqlrootconfig.inc.php');
		$file->write('<?php'."\n".'
$root_user = "' . $this->rootUser . '"'."\n".'
$root_password = "' . $this->rootPassword . '"'."\n".'
?>');
		$file->close();
		@chmod(CP_DIR . 'mysqlrootconfig.inc.php', 0777);

		parent :: save();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();

		WCF :: getTPL()->assign(array (
			'rootUser' => $this->rootUser,
			'rootPassword' => $this->rootPassword
		));
	}
}
?>