<?php
require_once (WCF_DIR . 'lib/acp/package/Package.class.php');

/**
 * Imports from a source exported data into Wcp.
 *
 * @author		Tobias Friebel
 * @copyright	2010 Tobias Friebel
 * @license		GPL
 * @package		com.burningpedia.cp.importer
 * @subpackage	system.importer
 * @category 	WCP
 */
class Importer
{
	public $idMappingCache = array ();
	public $encoding;
	public $userMergeMode;

	public $modules = array (
		'vhostcontainer',
		'domain',
		'ftp',
		'email',
		'mysql'
	);

	public $activeModules = array ();

	public static $domainAdditionalFields = array();

	/**
	 * Creates a new Importer object.
	 *
	 * @param	string		$encoding		character encoding of the source
	 * @param	integer		$userMergeMode
	 * @param	integer		$boardID
	 */
	public function __construct($encoding = 'ISO-8859-1', $userMergeMode = 1, $boardID = 0)
	{
		$this->encoding = $encoding;
		$this->userMergeMode = $userMergeMode;
	}

	/**
	 * Converts string from the source forum encoding to destination forum encoding.
	 * Mostly ISO-8859-1 to UTF-8
	 */
	protected function encodeString($string)
	{
		if (is_array($string))
		{
			return array_map(array (
				$this,
				'encodeString'
			), $string);
		}

		if (CHARSET != $this->encoding)
		{
			return StringUtil :: convertEncoding($this->encoding, CHARSET, $string);
		}

		// do nothing
		return $string;
	}

	/**
	 * Saves an id mapping.
	 *
	 * @param	string		$type
	 * @param	integer		$oldID
	 * @param	integer		$newID
	 */
	public function saveMapping($type, $oldID, $newID)
	{
		$sql = "INSERT IGNORE INTO	cp" . CP_N . "_import_mapping
						(idType, oldID, newID)
				VALUES	('" . escapeString($type) . "', '" . escapeString($oldID) . "', " . $newID . ")";
		WCF :: getDB()->sendQuery($sql);
		unset($this->idMappingCache[$type][$oldID]);
	}

	/**
	 * detect which modules are installed
	 */
	function detectInstalledModules()
	{
		$sql = "SELECT 	packageID, package
				FROM 	wcf" . WCF_N . "_packages
				WHERE 	package IN ('com.burningpedia.cp." . implode("','com.burningpedia.cp.", $this->modules) . "')";

		$result = WCF :: getDB()->sendQuery($sql);

		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$package = str_replace('com.burningpedia.cp.', '', $row['package']);
			if (in_array($package, $this->modules))
				$this->activeModules[] = $package;
		}
	}

	/**
	 * Gets a new id from the id mapping.
	 *
	 * @param	string		$type
	 * @param	mixed		$oldID
	 * @return	integer		$newID
	 */
	public function getNewID($type, $oldID)
	{
		if (!$oldID)
			return 0;

		if (!isset($this->idMappingCache[$type][$oldID]))
		{
			$sql = "SELECT	newID
					FROM	cp" . CP_N . "_import_mapping
					WHERE	idType = '" . escapeString($type) . "'
						AND oldID = '" . escapeString($oldID) . "'";

			$row = WCF :: getDB()->getFirstRow($sql);

			if (!empty($row['newID']))
				$this->idMappingCache[$type][$oldID] = $row['newID'];
			else
				$this->idMappingCache[$type][$oldID] = 0;
		}

		return $this->idMappingCache[$type][$oldID];
	}

	/**
	 * Imports an user.
	 *
	 * @param	integer			$oldUserID
	 * @param	string			$username
	 * @param	string			$email
	 * @param	string			$password		hashed password
	 * @param	array			$options		list of user options (name => value)
	 * @param	array			$data			additional data
	 * @param	array			$cpData			additional data for CP
	 * @return	UserEditor		new user
	 */
	public function importUser($oldUserID, $username, $email, $password, $options = array(), $data = array())
	{
		$username = $this->encodeString($username);
		$email = $this->encodeString($email);
		$options = $this->encodeString($options);
		$data = $this->encodeString($data);

		// includes
		require_once (WCF_DIR . 'lib/data/user/UserEditor.class.php');

		// resolve duplicates
		$existingUser = new UserEditor(null, null, $username);
		if ($existingUser->userID)
		{
			if ($this->userMergeMode == 1 || ($this->userMergeMode == 3 && StringUtil :: toLowerCase($existingUser->email) != StringUtil :: toLowerCase($email)))
			{
				// rename user
				$username = self :: resolveDuplicate($username);
			}
			else
			{
				// merge user
				// save mapping
				$this->saveMapping('user', $oldUserID, $existingUser->userID);
				return $existingUser;
			}
		}

		// get language id
		$data['languageID'] = 0;
		if (!empty($data['languageCode']))
		{
			require_once (WCF_DIR . 'lib/system/language/LanguageEditor.class.php');
			if (($language = LanguageEditor :: getLanguageByCode($data['languageCode'])) !== null)
			{
				$data['languageID'] = $language->getLanguageID();
			}
		}
		unset($data['languageCode']);

		// format options
		$newOptions = array ();
		foreach ($options as $optionName => $optionValue)
		{
			if (is_int($optionName))
				$optionID = $this->getNewID('userOption', $optionName);
			else
				$optionID = User :: getUserOptionID($optionName);

			if ($optionID)
			{
				$newOptions[] = array (
					'optionID' => $optionID,
					'optionValue' => $optionValue
				);
			}
		}

		$user = UserEditor :: create($username, $email, '', array (), $newOptions, $data);

		// encrypt password
		$password = StringUtil :: encrypt($user->salt . StringUtil :: encrypt($user->salt . $password));

		// update password
		$sql = "UPDATE	wcf" . WCF_N . "_user
				SET		password = '" . $password . "'
				WHERE	userID = " . $user->userID;
		WCF :: getDB()->sendQuery($sql);

		// insert cpX_user
		$sql = "INSERT IGNORE INTO	cp" . CP_N . "_user
						(userID, adminID, isCustomer, cpLastActivityTime, guid, homeDir)
				VALUES	(" . $user->userID . ", iert, 1, " . TIME_NOW . ", " . TIME_NOW . ", '', '')";
		WCF :: getDB()->sendQuery($sql);

		// save mapping
		$this->saveMapping('user', $oldUserID, $user->userID);

		return $user;
	}

	/**
	 * Import one Vhost
	 *
	 * @param int $oldVhostID
	 * @param string $vhostName
	 * @param string $ipAddress
	 * @param int $port
	 * @param array $additionalFields
	 */
	function importVhost($oldVhostID, $vhostName, $ipAddress, $port, $additionalFields = array())
	{
		// includes
		require_once (CP_DIR . 'lib/data/vhost/VhostContainerEditor.class.php');

		$vhostName = $this->encodeString($vhostName);

		$vhost = VhostContainerEditor :: create($vhostName, $ipAddress, $port, $vhostType, $additionalFields);

		// save mapping
		$this->saveMapping('vhost', $oldVhostID, $vhost->vhostContainerID);
	}

	/**
	 * Import one Domain
	 *
	 * @param int $oldDomainID
	 * @param string $domainname
	 * @param int $userID
	 * @param int $adminID
	 * @param int $parentDomainID
	 * @param array $options
	 * @param array $additionalFields
	 */
	function importDomain($oldDomainID, $domainname, $userID, $adminID, $parentDomainID, $options = array(), $additionalFields = array())
	{
		// includes
		require_once (CP_DIR . 'lib/data/domain/DomainEditor.class.php');

		$domainname = $this->encodeString($domainname);
		$userID = $this->getNewID('user', $userID);
		$adminID = $this->getNewID('user', $adminID);

		if ($parentDomainID != 0)
			$parentDomainID = $this->getNewID('domain', $parentDomainID);

		// format options
		$newOptions = array ();
		foreach ($options as $optionName => $optionValue)
		{
			$optionID = Domain :: getDomainOptionID($optionName);

			if ($optionID)
			{
				$newOptions[] = array (
					'optionID' => $optionID,
					'optionValue' => $optionValue
				);
			}
		}

		if (empty(self :: $domainAdditionalFields))
		{
			$sql = "SELECT COLUMN_NAME
					FROM INFORMATION_SCHEMA.COLUMNS
					WHERE TABLE_NAME = 'cp" . CP_N . "_domain'";
			$result = WCF :: getDB()->sendQuery($sql);

			while ($row = $this->getDB()->fetchArray($result))
			{
				self :: $domainAdditionalFields[] = $row['COLUMN_NAME'];
			}
		}

		$newAdditionalFields = array();
		foreach ($additionalFields as $fieldName => $fieldValue)
		{
			if (in_array($fieldName, self :: $domainAdditionalFields))
			{
				if ($fieldName == 'vhostContainerID')
					$fieldValue = $this->getNewID('vhost', $fieldValue);

				$newAdditionalFields[$fieldName] = $fieldValue;
			}
		}

		$domain = DomainEditor :: create($domainname, $userID, $adminID, $parentDomainID, $newOptions, $newAdditionalFields);

		// save mapping
		$this->saveMapping('domain', $oldDomainID, $domain->domainID);
	}

	/**
	 * Imports one email
	 *
	 * @param int $userID
	 * @param string $emailaddress
	 * @param string $domainname
	 * @param int $domainID
	 * @param int $isCatchall
	 * @param array $additionalData
	 */
	function importEmail($userID, $emailaddress, $domainname, $domainID, $isCatchall, $additionalData)
	{
		// includes
		require_once (CP_DIR . 'lib/data/email/EmailEditor.class.php');

		$emailaddress = $this->encodeString($emailaddress);
		$domainname = $this->encodeString($domainname);
		$userID = $this->getNewID('user', $userID);
		$domainID = $this->getNewID('domain', $domainID);

		$email = EmailEditor :: create($userID, $emailaddress, $domainname, $domainID, $isCatchall);

		if (!empty($additionalData['username']))
		{
			$email->addAccount('foobar');
			$email = new Email($email->mailID);

			$sql = "UPDATE 	cp" . CP_N . "_mail_account
					SET		password_enc = '" . escapeString($additionalData['password_enc']) . "'
					" . (MAIL_STORE_PLAIN_PASSWORD ? ", password = '" . escapeString($additionalData['password']) . "'" : '') . "
					WHERE	accountID = " . $email->accountID;
			WCF :: getDB()->sendQuery($sql);
		}

		if (!empty($additionalData['destination']))
		{
			foreach ($additionalData['destination'] as $destination)
			{
				if ($destination == $emailaddress . '@' . $domainname)
					continue;

				$email->addForward($destination);
			}
		}
	}

	function importFTP($userID, $username, $password, $homedir, $additionalData = array())
	{
		// includes
		require_once (CP_DIR . 'lib/data/ftp/FTPUserEditor.class.php');

		$username = $this->encodeString($username);
		$homedir = $this->encodeString($homedir);
		$userID = $this->getNewID('user', $userID);

		if (preg_match('/ftp\d+/', $row['username']))
			$undeletable = false;
		else
			$undeletable = true;

		$ftp = FTPUserEditor :: create($userID, $username, $password, $homedir, '', $undeletable, false);

		$add = '';
		foreach ($additionalData as $key => $value)
		{
			if (!empty($add))
				$add .= ',';

			$add .= $key . ' = ';
			$add .= ((is_int($value)) ? $value : "'" . escapeString($value) . "'");
		}

		$sql = "UPDATE 	cp" . CP_N . "_ftp_users
				SET " . $add . "
				WHERE ftpUserID = " . $ftp->ftpUserID;
		WCF :: getDB()->sendQuery($sql);
	}

	function importMySQLDB($userID, $dbname, $description)
	{
		$sql = "INSERT INTO	cp" . CP_N . "_mysql
						(userID, mysqlname, description)
				VALUES
						(" . $this->getNewId('user', $userID) . ", '" . $this->encodeString($dbname) . "', '" . $this->encodeString($description) . "')";
		WCF :: getDB()->sendQuery($sql);

		$user = new CPUser($userID);
		$user->getEditor()->updateOptions(array (
			'mysqlsUsed' => ++$user->mysqlsUsed
		));
	}

	/**
	 * Revolves duplicate user names.
	 *
	 * @param	string		$username
	 * @return 	string		new username
	 */
	private static function resolveDuplicate($username)
	{
		$i = 0;
		$newUsername = '';
		do
		{
			$i++;
			$newUsername = 'Duplicate' . ($i > 1 ? $i : '') . ' ' . $username;

			// try username
			$sql = "SELECT	userID
					FROM	wcf" . WCF_N . "_user
					WHERE	username = '" . escapeString($newUsername) . "'";
			$row = WCF :: getDB()->getFirstRow($sql);
			if (empty($row['userID']))
				break;
		} while (true);

		return $newUsername;
	}
}
?>