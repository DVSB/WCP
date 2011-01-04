<?php
require_once (CP_DIR . 'lib/system/importer/Exporter.class.php');

/**
 * Exporter implementation for exporting data from a burning board 2.x or lite.
 *
 * @author	Tobias Friebel
 * @copyright	2010 Tobias Friebel
 * @license	GPL
 * @package	com.burningpedia.cp.importer
 * @subpackage	system.importer
 * @category 	CP
 */
class SysCPExporter extends Exporter
{
	public $useDatabase = true;
	public $supportedDatabaseClasses = array (
		'MySQLDatabase',
		'MySQLiDatabase'
	);
	public $needsPasswordConversion = true;

	/**
	 * Creates a new SysCPExporter object.
	 */
	public function __construct()
	{
	}

	/**
	 * @see Exporter::validate()
	 */
	public function validate()
	{
		parent :: validate();

		if ($this->settings['convertPasswords'])
		{
			$this->setEncryptionSettings('md5');

			// save new admin password
			$password = md5(WCF :: getUser()->salt . md5(WCF :: getUser()->salt . md5($this->settings['adminPassword'])));
			$sql = "UPDATE	wcf" . WCF_N . "_user
					SET	password = '" . $password . "'
					WHERE	userID = " . WCF :: getUser()->userID;
			WCF :: getDB()->sendQuery($sql);
		}
	}

	/**
	 * @see Exporter::countUsers()
	 */
	public function countUsers()
	{
		$sql = "SELECT 	COUNT(*) AS count
				FROM	" . $this->dbPrefix . "panel_customers";
		$row = $this->getDB()->getFirstRow($sql);
		return $row['count'];
	}

	/**
	 * Counts the vhosts.
	 *
	 * @return	integer
	 */
	public function countVhosts()
	{
		$sql = "SELECT 	COUNT(*) AS count
				FROM	" . $this->dbPrefix . "panel_ipsandports";
		$row = $this->getDB()->getFirstRow($sql);
		return $row['count'];
	}

	/**
	 * Counts the domains.
	 *
	 * @return	integer
	 */
	public function countDomains()
	{
		$sql = "SELECT 	COUNT(*) AS count
				FROM	" . $this->dbPrefix . "panel_domains";
		$row = $this->getDB()->getFirstRow($sql);
		return $row['count'];
	}

	/**
	 * Counts the emails.
	 *
	 * @return	integer
	 */
	public function countEmails()
	{
		$sql = "SELECT 	COUNT(*) AS count
				FROM	" . $this->dbPrefix . "mail_virtual";
		$row = $this->getDB()->getFirstRow($sql);
		return $row['count'];
	}

	/**
	 * Counts the FTPs.
	 *
	 * @return	integer
	 */
	public function countFTPs()
	{
		$sql = "SELECT 	COUNT(*) AS count
				FROM	" . $this->dbPrefix . "ftp_users";
		$row = $this->getDB()->getFirstRow($sql);
		return $row['count'];
	}

	/**
	 * Counts the MySQL DBs.
	 *
	 * @return	integer
	 */
	public function countMySQLDBs()
	{
		$sql = "SELECT 	COUNT(*) AS count
				FROM	" . $this->dbPrefix . "panel_databases";
		$row = $this->getDB()->getFirstRow($sql);
		return $row['count'];
	}

	/**
	 * @see Exporter::exportUsers
	 */
	public function exportUsers($offset, $limit)
	{
		// get user data
		$sql = "SELECT		customers.*
				FROM		" . $this->dbPrefix . "panel_customers customers
				ORDER BY	customers.customerid";

		$result = $this->getDB()->sendQuery($sql, $limit, $offset);

		while ($row = $this->getDB()->fetchArray($result))
		{
			$data = array (
				'registrationDate' => TIME_NOW,
				'banned' => $row['deactivated'],
				'registrationIpAddress' => '',
			);
			$options = array (
				'addDefaultSubdomain' => 0,
				'city'  => $row['city'],
				'company'  => $row['company'],
				'country'  => $row['country'],
				'customerID'  => $row['customernumber'],
				'customerStart'  => $row['contract_number'],
				'diskspace'  => $row['diskspace'],
				'emailAccounts'  => $row['email_accounts'],
				'emailAddresses' => $row['emails'],
				'emailForwards' => $row['email_forwarders'],
				'emailIMAPenabled' => $row['imap'],
				'emailPOP3enabled' => $row['pop3'],
				'fax' => $row['fax'],
				'firstname' => $row['firstname'],
				'ftpaccounts' => $row['ftps'],
				'lastname' => $row['name'],
				'mysqls' => $row['mysqls'],
				'phone' => $row['phone'],
				'street' => $row['street'],
				'subdomains' => $row['subdomains'],
				'title' => '',
				'ustID' => '',
				'zipCode' => $row['zipcode'],
			);

			$cpData = array(
				'cpLastActivityTime' => $row['lastlogin_succ'],
				'adminID' => $row['adminid'],
				'guid' => $row['guid'],
				'homeDir' => $row['documentroot']
			);

			$this->getImporter()->importUser($row['customerid'], $row['loginname'], $row['email'], $row['password'], $options, $data, $cpData);
		}
	}

	/**
	 * Exports the vhosts.
	 *
	 * @param	integer		$offset
	 * @param	integer		$limit
	 */
	public function exportVhosts($offset, $limit)
	{
		// get data
		$sql = "SELECT		vhost.*
				FROM		" . $this->dbPrefix . "panel_ipsandports vhost
				ORDER BY	vhost.id";

		$result = $this->getDB()->sendQuery($sql, $limit, $offset);

		while ($row = $this->getDB()->fetchArray($result))
		{
			$additionalFields = array (
				'isContainer' => 1,
				'isIPv6' => 0,
				'isSSL' => $row['ssl'],
				'addListenStatement' => $row['listen_statement'],
				'addNameStatement' => $row['namevirtualhost_statement'],
				'addServerName' => $row['vhostcontainer_servername_statement'],
				'vhostTemplate' => $row['specialsettings'],
				'vhostComments' => 'Imported from SysCP',
				'sslCertFile' => $row['ssl_cert'],
			);

			$this->getImporter()->importVhost($row['id'], $row['ip'] . ':' . $row['port'], $row['ip'], $row['port'], $additionalFields);
		}
	}

	/**
	 * Exports the domains.
	 *
	 * @param	integer		$offset
	 * @param	integer		$limit
	 */
	public function exportDomains($offset, $limit)
	{
		// get data
		$sql = "SELECT		domain.*
				FROM		" . $this->dbPrefix . "panel_domains domain
				ORDER BY	domain.customerid";

		$result = $this->getDB()->sendQuery($sql, $limit, $offset);

		while ($row = $this->getDB()->fetchArray($result))
		{
			$additionalFields = array(
				'addDate' => strtotime($row['add_date']),
				'registrationDate' => strtotime($row['registration_date']),
				'deactivated' => $row['deactivated'],
				'isEmailDomain' => $row['isemaildomain']
			);

			if (stripos($row['documentroot'], 'http://') === 0)
			{
				$redirectDomain = $row['documentroot'];
				$isAliasDomain = 1;
				$documentroot = '';
			}
			else
			{
				$documentroot = $row['documentroot'];
				$redirectDomain = '';
			}

			$options = array(
				'aliasDomainID'	=> $row['aliasdomain'],
				'documentroot' => $documentroot,
				'isAliasDomain' => (($redirectDomain || $row['aliasdomain']) ? 1 : 0),
				'isWildcardDomain' => $row['iswildcarddomain'],
				'noWebDomain' => $row['email_only'],
				'redirectDomain' => $redirectDomain,
				'specialSettings' => $row['specialsettings'],
				'vhostContainerID' => $row['ipandport'],
				'wwwServerAlias' => $row['wwwserveralias'],
			);

			$this->getImporter()->importDomain($row['id'], $row['domain'], $row['customerid'], $row['adminid'], $row['parentdomainid']);
		}
	}

	/**
	 * Exports the emails.
	 *
	 * @param	integer		$offset
	 * @param	integer		$limit
	 */
	public function exportEmails($offset, $limit)
	{
		// get data
		$sql = "SELECT		virtual.*, users.*
				FROM		" . $this->dbPrefix . "mail_virtual virtual
				LEFT JOIN	" . $this->dbPrefix . "mail_users users ON (virtual.popaccountid = users.id)
				ORDER BY	virtual.id";

		$result = $this->getDB()->sendQuery($sql, $limit, $offset);

		while ($row = $this->getDB()->fetchArray($result))
		{
			$email = explode('@', $row['email_full']);

			$additionalData = array(
				'destination' => explode("\n", $row['destination']),
				'username' => $row['username'],
				'password_enc' => $row['password_enc'],
				'password' => $row['password'],
			);

			$this->getImporter()->importEmail($row['customerid'], $email[0], $email[1], $row['domainid'], $row['iscatchall'], $additionalData);
		}
	}

	/**
	 * Exports the FTP accounts.
	 *
	 * @param	integer		$offset
	 * @param	integer		$limit
	 */
	public function exportFTPs($offset, $limit)
	{
		// get data
		$sql = "SELECT		users.*
				FROM		" . $this->dbPrefix . "ftp_users users
				ORDER BY	users.id";

		$result = $this->getDB()->sendQuery($sql, $limit, $offset);

		while ($row = $this->getDB()->fetchArray($result))
		{
			$additionalData = array(
				'shell' => $row['shell'],
				'loginEnabled' => $row['login_enabled'],
				'loginCount' => $row['login_count'],
				'lastLogin' => $row['last_login'],
				'upcount' => $row['up_count'],
				'upbytes' => $row['up_bytes'],
				'downcount' => $row['down_count'],
				'downbytes' => $row['down_bytes'],
			);

			$this->getImporter()->importFTP($row['customerid'], $row['username'], $row['password'], $row['homedir'], $additionalData);
		}
	}

	/**
	 * Exports the mysqls.
	 *
	 * @param	integer		$offset
	 * @param	integer		$limit
	 */
	public function exportMySQLs($offset, $limit)
	{
		// get data
		$sql = "SELECT		db.*
				FROM		" . $this->dbPrefix . "panel_databases db
				ORDER BY	db.customerid";

		$result = $this->getDB()->sendQuery($sql);

		while ($row = $this->getDB()->fetchArray($result))
		{
			$this->getImporter()->importMySQLDB($row['customerid'], $row['databasename'], $row['description']);
		}
	}
}
?>