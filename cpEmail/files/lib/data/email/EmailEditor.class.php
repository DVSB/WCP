<?php
/*
 * Copyright (c) 2009 Tobias Friebel
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

require_once (CP_DIR . 'lib/data/email/Email.class.php');
require_once (CP_DIR . 'lib/data/user/CPUser.class.php');

/**
 * Creates/manipulates one email account
 *
 * @author		Tobias Friebel
 * @copyright	2010 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.email
 * @subpackage	data.email
 * @category 	Control Panel
 * @id			$Id$
 */
class EmailEditor extends Email
{
	/**
	 * create a new email
	 *
	 * @param int $userID
	 * @param string $emailaddress
	 * @param int $domainID
	 * @param boolean $isCatchall
	 *
	 * @return object
	 */
	public static function create($userID, $emailaddress, $domainID, $isCatchall)
	{
		$user = new CPUser($userID);
		
		$sql = "INSERT INTO	cp" . CP_N . "_mail_virtual
						(userID, emailaddress, emailaddress_full,
						 domainID, isCatchall)
				VALUES	(" . $user->userID . ", '" . escapeString($emailaddress) . "', '" . escapeString($emailaddress) . "',
						 " . intval($domainID) . ", " . intval($isCatchall) . ")";
		WCF :: getDB()->sendQuery($sql);

		$mailID = WCF :: getDB()->getInsertID('cp' . CP_N . '_mail_virtual', 'mailID');

		$user = new CPUser($userID);
		$user->getEditor()->updateOptions(array('emailAddressesUsed' => ++$user->emailAddressesUsed));

		return new EmailEditor($mailID);
	}

	/**
	 * update email
	 *
	 * @param string $password
	 */
	public function update($isCatchall)
	{
		// Update
		$sql = "UPDATE	cp" . CP_N . "_mail_virtual
				SET		isCatchall = " . intval($isCatchall) . "
				WHERE 	mailID = " . $this->mailID;
		WCF :: getDB()->sendQuery($sql);
	}
	
	/**
	 * add email account
	 *
	 * @param string $password
	 */
	public function addAccount($password)
	{
		$user = new CPUser($this->userID);
		
		$sql = "INSERT INTO	cp" . CP_N . "_mail_accounts
							(userID, emailaddress, username, 
							 password_enc," . (MAIL_STORE_PLAIN_PASSWORD ? 'password, ' : '') . " 
							 mailID, uid, gid, homeDir, mailDir, 
							 domainID, quota, pop3, imap
							)
				VALUES		(" . $this->userID . ", '" . $this->emailaddress . "', '" . $this->emailaddress . "', 
							 ENCRYPT('" . escapeString($password) . "')," . (MAIL_STORE_PLAIN_PASSWORD ? "'" . escapeString($password) . "', " : '') . "
							 " . $this->mailID . ", " . MAIL_UID . ", " . MAIL_GID . ", '" . MAIL_HOMEDIR . "', 
							 " . $this->domainID . ", 0, " . intval($user->emailIMAPenabled) . ", " . intval($user->emailPOP3enabled) . "
							)";
		WCF :: getDB()->sendQuery($sql);
		
		$accountID = WCF :: getDB()->getInsertID('cp' . CP_N . '_mail_accounts', 'accountID');
		
		$this->alterDestination($this->emailaddress); 
		
		$sql = "UPDATE 	cp" . CP_N . "_mail_virtual
				SET		accountID = " . $accountID . ",
						destination = '" . $this->data['destination'] . "'
				WHERE 	mailID = " . $this->mailID;
		WCF :: getDB()->sendQuery($sql);
		
		$user->getEditor()->updateOptions(array('emailAccountsUsed' => ++$user->emailAccountsUsed));
	}
	
	/**
	 * remove email account
	 */
	public function removeAccount()
	{
		$sql = "DELETE FROM	cp" . CP_N . "_mail_accounts
				WHERE accountID = " . $this->accountID;
		WCF :: getDB()->sendQuery($sql);
		
		$this->alterDestination($this->emailaddress, false);
		
		$sql = "UPDATE 	cp" . CP_N . "_mail_virtual
				SET		accountID = 0,
						destination = '" . $this->data['destination'] . "'
				WHERE 	accountID = " . $this->accountID;
		WCF :: getDB()->sendQuery($sql);
		
		$user = new CPUser($this->userID);
		$user->getEditor()->updateOptions(array('emailAccountsUsed' => --$user->emailAccountsUsed));
	}
	
	/**
	 * update email account
	 */
	public function updateAccount($password)
	{
		$sql = "UPDATE 	cp" . CP_N . "_mail_accounts
				SET		password_enc = ENCRYPT('" . escapeString($password) . "')
				" . (MAIL_STORE_PLAIN_PASSWORD ? ", password = '" . escapeString($password) . "'" : '') . "
				WHERE	accountID = " . $this->accountID;			 
		WCF :: getDB()->sendQuery($sql);
	}
	
	/**
	 * add forward for this email
	 *
	 * @param string $password
	 */
	public function addForward($emailaddress)
	{
		$this->alterDestination($emailaddress);
		$sql = "UPDATE	cp" . CP_N . "_mail_virtual
				SET		destination = '" . $this->data['destination'] . "'
				WHERE 	mailID = " . $this->mailID;
		WCF :: getDB()->sendQuery($sql);
		
		$user = new CPUser($this->userID);
		$user->getEditor()->updateOptions(array('emailForwardsUsed' => ++$user->emailForwardsUsed));
	}
	
	/**
	 * add forward for this email
	 *
	 * @param string $password
	 */
	public function removeForward($emailaddress)
	{
		$this->alterDestination($emailaddress, false);
		
		// Update
		$sql = "UPDATE	cp" . CP_N . "_mail_virtual
				SET		destination = '" . $this->data['destination'] . "'
				WHERE 	mailID = " . $this->mailID;
		WCF :: getDB()->sendQuery($sql);
		
		$user = new CPUser($this->userID);
		$user->getEditor()->updateOptions(array('emailForwardsUsed' => --$user->emailForwardsUsed));
	}
	
	/**
	 * alter destinationstring, add or remove emailaddress
	 * 
	 * @param string $emailaddress
	 * @param boolean $add			if false, emailaddress will be removed
	 */
	protected function alterDestination($emailaddress, $add = true)
	{
		if ($add)
		{
			if (!empty($this->data['destination']))
				$this->data['destination'] .= ', ';
			
			$this->data['destination'] .= $this->emailaddress;
		}
		else 
		{
			$this->data['destination'] = str_replace(array($this->emailaddress . ',', $this->emailaddress), array('', ''), $this->data['destination']);
		} 
	}
	
	/**
	 * delete an email
	 */
	public function delete()
	{
		if ($this->accountID)
		{
			$sql = "DELETE FROM	cp" . CP_N . "_mail_accounts
					WHERE		accountID = " . $this->accountID;
			WCF :: getDB()->sendQuery($sql);
		}
		
		$sql = "DELETE FROM cp" . CP_N . "_mail_virtual
				WHERE 	mailID = " . $this->mailID;
		WCF :: getDB()->sendQuery($sql);

		$user = new UserEditor($this->userID);
		$user->updateOptions(array('emailAddressesUsed' => --$user->emailAddressesUsed));
	}

	/**
	 * delete all emails for this user
	 */
	public static function deleteAll($userID)
	{
		$sql = "DELETE 	FROM cp" . CP_N . "_mail_accounts
				WHERE 		 userID = " . $userID;
		WCF :: getDB()->sendQuery($sql);

		$sql = "DELETE FROM	cp" . CP_N . "_mail_virtual
				WHERE		userID = " . $userID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * enable email account
	 */
	public function enable()
	{
		$sql = "UPDATE	cp" . CP_N . "_mail_accounts
				SET		loginEnabled = 'Y'
				WHERE	accountID = " . $this->accountID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * disable email account
	 */
	public function disable()
	{
		$sql = "UPDATE	cp" . CP_N . "_mail_accounts
				SET		loginEnabled = 'N'
				WHERE	accountID = " . $this->accountID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * enable all email accounts
	 */
	public static function enableAll($userID)
	{
		$sql = "UPDATE	cp" . CP_N . "_mail_accounts
				SET		loginEnabled = 'Y'
				WHERE	userID = " . $userID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * disable all email accounts
	 */
	public static function disableAll($userID)
	{
		$sql = "UPDATE	cp" . CP_N . "_mail_accounts
				SET		loginEnabled = 'N'
				WHERE	userID = " . $userID;
		WCF :: getDB()->sendQuery($sql);
	}
}

?>