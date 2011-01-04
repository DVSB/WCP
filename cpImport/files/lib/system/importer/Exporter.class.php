<?php

/**
 * Provides default implementations for data exporter classes.
 *
 * @author	Marcel Werk
 * @copyright	2001-2009 WoltLab GmbH
 * @license	WoltLab Burning Board License <http://www.woltlab.com/products/burning_board/license.php>
 * @package	com.woltlab.wbb.importer
 * @subpackage	system.importer
 * @category 	Burning Board
 */
abstract class Exporter
{
	/**
	 * database object
	 *
	 * @var Database
	 */
	private $database;

	/**
	 * importer object
	 *
	 * @var Importer
	 */
	private $importer;

	/**
	 * database table prefix
	 *
	 * @var string
	 */
	public $dbPrefix = '';

	/**
	 * true, if this exporter uses database access
	 *
	 * @var boolean
	 */
	public $useDatabase = false;

	/**
	 * list of supported databases
	 *
	 * @var	array<string>
	 */
	public $supportedDatabaseClasses = array (
		'MSAccessDatabase',
		'MSSQLDatabase',
		'MySQLDatabase',
		'MySQLiDatabase',
		'PostgreSQLDatabase',
		'SQLite2Database'
	);

	/**
	 * labels for supported databases
	 *
	 * @var	array<string>
	 */
	public $databaseClassLabels = array (
		'MSAccessDatabase' => 'Microsoft Access',
		'MSSQLDatabase' => 'Microsoft SQL Server',
		'MySQLDatabase' => 'MySQL 3+',
		'MySQLiDatabase' => 'MySQL 4+',
		'PostgreSQLDatabase' => 'PostgreSQL',
		'SQLite2Database' => 'SQLite'
	);

	/**
	 * true, if this exporter needs a password conversion
	 *
	 * @var boolean
	 */
	public $needsPasswordConversion = false;

	/**
	 * list of data
	 *
	 * @var	array
	 */
	public $data = array (
		'domains' => 0,
		'users' => 0,
		'emails' => 0,
		'ftps' => 0,
		'mysqls' => 0,
	);

	/**
	 * list of supported data
	 *
	 * @var	array
	 */
	public $supportedData = array (
		'domains' => 0,
		'users' => 0,
		'emails' => 0,
		'ftps' => 0,
		'mysqls' => 0,
	);

	/**
	 * exporter settings
	 *
	 * @var	array
	 */
	public $settings = array (
		'encoding' => 'ISO-8859-1',
		'userMergeMode' => 1,
		'dbClass' => '',
		'dbHost' => 'localhost',
		'dbUser' => '',
		'dbPassword' => '',
		'dbName' => '',
		'convertPasswords' => 0,
		'adminPassword' => '',
		'confirmAdminPassword' => ''
	);

	/**
	 * list of sql limits
	 *
	 * @var	array<integer>
	 */
	public $limits = array (
		'domains' => 100,
		'users' => 100,
		'emails' => 100,
		'ftps' => 100,
		'mysqls' => 100,
	);

	/**
	 * Validates form inputs.
	 */
	public function validate()
	{
		require_once (WCF_DIR . 'lib/system/exception/UserInputException.class.php');
		// data
		if (!$this->data['users'])
			$this->data['userOptions'] = 0;

		$result = false;
		foreach ($this->data as $data)
		{
			if ($data)
			{
				$result = true;
				break;
			}
		}
		if (!$result)
		{
			throw new UserInputException('data');
		}

		// settings
		// encoding
		$encodings = self :: getAvailableEncodings();
		if (!isset($encodings[$this->settings['encoding']]))
		{
			$this->settings['encoding'] = 'ISO-8859-1';
		}

		// userMergeMode
		if ($this->settings['userMergeMode'] < 1 || $this->settings['userMergeMode'] > 3)
		{
			$this->settings['userMergeMode'] = 1;
		}

		// database
		if ($this->useDatabase)
		{
			try
			{
				$this->initDB();
			}
			catch (SystemException $e)
			{
				throw new UserInputException('db', $e);
			}

			// mysql4.1+ makes the conversion of character encoding automatically :)
			if ($this->settings['dbClass'] == 'MySQLDatabase' || $this->settings['dbClass'] == 'MySQLiDatabase' || $this->settings['dbClass'] == 'MySQLPDODatabase')
			{
				$dbVersion = $this->database->getVersion();
				if (preg_match('~(\d+\.\d+\.\d+)~', $dbVersion, $match))
				{
					$dbVersion = $match[1];
					if (version_compare($dbVersion, '4.1.0', '>='))
					{
						$this->settings['encoding'] = CHARSET;
					}
				}
				else
				{
					$this->settings['encoding'] = CHARSET;
				}
			}
		}

		// passwords
		if ($this->needsPasswordConversion)
		{
			if ($this->settings['convertPasswords'] == 1)
			{
				// validate admin password
				if (empty($this->settings['adminPassword']))
				{
					throw new UserInputException('adminPassword');
				}
				if (empty($this->settings['confirmAdminPassword']))
				{
					throw new UserInputException('confirmAdminPassword');
				}

				if (!WCF :: getUser()->checkPassword($this->settings['adminPassword']))
				{
					throw new UserInputException('adminPassword', 'false');
				}
				if ($this->settings['adminPassword'] != $this->settings['confirmAdminPassword'])
				{
					throw new UserInputException('confirmAdminPassword', 'notEqual');
				}
			}
		}
		else
		{
			$this->settings['convertPasswords'] = 0;
		}
	}

	/**
	 * Returns a list of available character encodings.
	 *
	 * @return	array
	 */
	public static function getAvailableEncodings()
	{
		// get all encodings
		$encodings = array_keys(Language :: $supportedCharsets);

		// remove unsupported encodings
		//if (!function_exists('iconv')) {
		if (!extension_loaded('mbstring'))
		{
			foreach ($encodings as $key => $encoding)
			{
				if ($encoding != 'ISO-8859-1')
				{
					unset($encodings[$key]);
				}
			}
		}

		// add utf-8
		$encodings[] = 'UTF-8';

		// sort list
		sort($encodings);

		return $encodings;
	}

	/**
	 * Returns a list of available database classes.
	 *
	 * @return	array
	 */
	public function getSupportedDatabaseClasses()
	{
		$classes = array ();

		foreach ($this->supportedDatabaseClasses as $class)
		{
			require_once (WCF_DIR . 'lib/system/database/' . $class . '.class.php');
			if (call_user_func(array (
				$class,
				'isSupported'
			)))
			{
				$classes[$class] = $this->databaseClassLabels[$class];
			}
		}

		return $classes;
	}

	/**
	 * Saves password encryption settings.
	 *
	 * @param	string		$method
	 * @param	integer		$salting
	 * @param	string		$position
	 * @param	integer		$encryptBeforeSalting
	 */
	protected function setEncryptionSettings($method = 'sha1', $salting = 1, $position = 'before', $encryptBeforeSalting = 1)
	{
		$sql = "UPDATE	wcf" . WCF_N . "_option
				SET	optionValue = '" . escapeString($method) . "'
				WHERE	optionName = 'encryption_method'";
		WCF :: getDB()->registerShutdownUpdate($sql);

		$sql = "UPDATE	wcf" . WCF_N . "_option
				SET	optionValue = " . $salting . "
				WHERE	optionName = 'encryption_enable_salting'";
		WCF :: getDB()->registerShutdownUpdate($sql);

		$sql = "UPDATE	wcf" . WCF_N . "_option
				SET	optionValue = '" . escapeString($position) . "'
				WHERE	optionName = 'encryption_salt_position'";
		WCF :: getDB()->registerShutdownUpdate($sql);

		$sql = "UPDATE	wcf" . WCF_N . "_option
				SET	optionValue = " . $encryptBeforeSalting . "
				WHERE	optionName = 'encryption_encrypt_before_salting'";
		WCF :: getDB()->registerShutdownUpdate($sql);

		// delete options file
		@unlink(WBB_DIR . 'options.inc.php');
	}

	/**
	 * Returns the importer class.
	 *
	 * @return	Importer
	 */
	protected function getImporter()
	{
		return $this->importer;
	}

	/**
	 * Initializes the exporter.
	 */
	public function init()
	{
		if ($this->useDatabase)
		{
			$this->initDB();
		}

		require_once (WBB_DIR . 'lib/system/importer/Importer.class.php');
		$this->importer = new Importer($this->settings['encoding'], $this->settings['userMergeMode'], $this->settings['boardID']);
	}

	/**
	 * Initializes the database connection.
	 */
	protected function initDB()
	{
		$dbHost = $dbUser = $dbPassword = $dbName = $dbCharset = '';
		$dbClass = 'MySQLDatabase';
		require (WCF_DIR . 'config.inc.php');

		if ($dbClass != $this->settings['dbClass'] || $dbHost != $this->settings['dbHost'] || $dbUser != $this->settings['dbUser'])
		{
			require_once (WCF_DIR . 'lib/system/database/' . $this->settings['dbClass'] . '.class.php');
			$this->database = new $this->settings['dbClass']($this->settings['dbHost'], $this->settings['dbUser'], $this->settings['dbPassword'], $this->settings['dbName'], Database :: $dbCharsets[$this->settings['encoding']]);
		}
		else
		{
			$this->database = WCF :: getDB();
			if ($dbName != $this->settings['dbName'])
			{
				$this->dbPrefix = '`' . $this->settings['dbName'] . '`.';
			}
		}
	}

	/**
	 * Returns the database object.
	 *
	 * @return	Database
	 */
	protected function getDB()
	{
		return $this->database;
	}

	/**
	 * Counts the users.
	 *
	 * @return	integer
	 */
	public function countUsers()
	{
		return 0;
	}

	/**
	 * Counts the users.
	 *
	 * @return	integer
	 */
	public function countVhosts()
	{
		return 0;
	}

	/**
	 * Counts the domains.
	 *
	 * @return	integer
	 */
	public function countDomains()
	{
		return 0;
	}

	/**
	 * Counts the emails.
	 *
	 * @return	integer
	 */
	public function countEmails()
	{
		return 0;
	}

	/**
	 * Counts the FTPs.
	 *
	 * @return	integer
	 */
	public function countFTPs()
	{
		return 0;
	}

	/**
	 * Counts the MySQL DBs.
	 *
	 * @return	integer
	 */
	public function countMySQLDBs()
	{
		return 0;
	}

	/**
	 * Exports the users.
	 *
	 * @param	integer		$offset
	 * @param	integer		$limit
	 */
	public function exportUsers($offset, $limit)
	{
		return false;
	}

	/**
	 * Exports the vhosts.
	 *
	 * @param	integer		$offset
	 * @param	integer		$limit
	 */
	public function exportVhosts($offset, $limit)
	{
		return false;
	}

	/**
	 * Exports the domains.
	 *
	 * @param	integer		$offset
	 * @param	integer		$limit
	 */
	public function exportDomains($offset, $limit)
	{
		return false;
	}

	/**
	 * Exports the emails.
	 *
	 * @param	integer		$offset
	 * @param	integer		$limit
	 */
	public function exportEmails($offset, $limit)
	{
		return false;
	}

	/**
	 * Exports the FTP accounts.
	 *
	 * @param	integer		$offset
	 * @param	integer		$limit
	 */
	public function exportFTPs($offset, $limit)
	{
		return false;
	}

	/**
	 * Exports the mysqls.
	 *
	 * @param	integer		$offset
	 * @param	integer		$limit
	 */
	public function exportMySQLs($offset, $limit)
	{
		return false;
	}
}
?>