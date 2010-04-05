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
	public static function create($userID, $emailaddress, $domainname, $domainID, $isCatchall)
	{
		$user = new CPUser($userID);
		
		$emailaddress_full = $emailaddress . '@' . $domainname;
		
		if ($isCatchall)
		{
			$emailaddress = '@' . $domainname;
		}
		else
		{
			$emailaddress = $emailaddress . '@' . $domainname;
		}
		
		$sql = "INSERT INTO	cp" . CP_N . "_mail_virtual
						(userID, emailaddress, emailaddress_full,
						 domainID, isCatchall)
				VALUES	(" . $user->userID . ", '" . escapeString($emailaddress) . "', '" . escapeString($emailaddress_full) . "',
						 " . intval($domainID) . ", " . intval($isCatchall) . ")";
		WCF :: getDB()->sendQuery($sql);

		$mailID = WCF :: getDB()->getInsertID('cp' . CP_N . '_mail_virtual', 'mailID');

		$user = new CPUser($userID);
		$user->getEditor()->updateOptions(array('emailAddressesUsed' => ++$user->emailAddressesUsed));

		return new EmailEditor($mailID);
	}

	/**
	 * toggleCatchall
	 */
	public function toggleCatchall()
	{		
		$this->isCatchall = !$this->isCatchall;
		
		if ($this->isCatchall)
		{
			$this->emailaddress = preg_replace('/.*@/', '@', $this->emailaddress_full);
		}
		else
		{
			$this->emailaddress = $this->emailaddress_full;
		}
		
		// Update
		$sql = "UPDATE	cp" . CP_N . "_mail_virtual
				SET		emailaddress = '" . escapeString($this->emailaddress) . "',
						isCatchall = " . intval($this->isCatchall) . "
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
		
		$sql = "INSERT INTO	cp" . CP_N . "_mail_account
							(emailaddress, username, 
							 password_enc," . (MAIL_STORE_PLAIN_PASSWORD ? 'password, ' : '') . " 
							 uid, gid, homeDir, mailDir, 
							 domainID, quota, pop3, imap
							)
				VALUES		('" . $this->emailaddress . "', '" . $this->emailaddress . "', 
							 ENCRYPT('" . escapeString($password) . "')," . (MAIL_STORE_PLAIN_PASSWORD ? "'" . escapeString($password) . "', " : '') . "
							 " . MAIL_UID . ", " . MAIL_GID . ", '" . MAIL_HOMEDIR . "', '" . $user->username . '/' . $this->emailaddress . "',
							 " . $this->domainID . ", 0, " . intval($user->emailIMAPenabled) . ", " . intval($user->emailPOP3enabled) . "
							)";
		WCF :: getDB()->sendQuery($sql);
		
		$accountID = WCF :: getDB()->getInsertID('cp' . CP_N . '_mail_accounts', 'accountID');
		
		$this->addDestination($this->emailaddress); 
		
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
		$sql = "DELETE FROM	cp" . CP_N . "_mail_account
				WHERE accountID = " . $this->accountID;
		WCF :: getDB()->sendQuery($sql);
		
		$this->removeDestination($this->emailaddress);
		
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
	 * 
	 * @param string $password
	 */
	public function updateAccount($password)
	{
		$sql = "UPDATE 	cp" . CP_N . "_mail_account
				SET		password_enc = ENCRYPT('" . escapeString($password) . "')
				" . (MAIL_STORE_PLAIN_PASSWORD ? ", password = '" . escapeString($password) . "'" : '') . "
				WHERE	accountID = " . $this->accountID;			 
		WCF :: getDB()->sendQuery($sql);
	}
	
	/**
	 * add forward for this email
	 *
	 * @param string $emailaddress
	 * 
	 * @return boolean
	 */
	public function addForward($emailaddress)
	{
		if (!$this->addDestination($emailaddress))
			return false;
		
		$sql = "UPDATE	cp" . CP_N . "_mail_virtual
				SET		destination = '" . escapeString($this->data['destination']) . "'
				WHERE 	mailID = " . $this->mailID;
		WCF :: getDB()->sendQuery($sql);
		
		$user = new CPUser($this->userID);
		$user->getEditor()->updateOptions(array('emailForwardsUsed' => ++$user->emailForwardsUsed));
	}
	
	/**
	 * add forward for this email
	 *
	 * @param string $emailaddress
	 * 
	 * @return boolean
	 */
	public function removeForward($emailaddress)
	{
		if (!$this->removeDestination($emailaddress))
			return false;
		
		// Update
		$sql = "UPDATE	cp" . CP_N . "_mail_virtual
				SET		destination = '" . escapeString($this->data['destination']) . "'
				WHERE 	mailID = " . $this->mailID;
		WCF :: getDB()->sendQuery($sql);
		
		$user = new CPUser($this->userID);
		$user->getEditor()->updateOptions(array('emailForwardsUsed' => --$user->emailForwardsUsed));
	}
	
	/**
	 * add destination for this address
	 * 
	 * @param string $emailaddress
	 * 
	 * @return boolean
	 */
	protected function addDestination($emailaddress)
	{
		if (!in_array($emailaddress, $this->destination))
		{
			$this->destination[] = $emailaddress;
			$this->data['destination'] = implode(', ', $this->destination);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * remove destination for this address
	 * 
	 * @param string $emailaddress
	 * 
	 * @return boolean
	 */
	protected function removeDestination($emailaddress)
	{
		foreach ($this->destination as $id => $destination)
		{
			if ($destination == $emailaddress)
			{
				unset($this->destination[$id]);
				$this->data['destination'] = implode(', ', $this->destination);
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * delete an email
	 */
	public function delete()
	{
		$user = new UserEditor($this->userID);
		$update = array(
			'emailAddressesUsed' => --$user->emailAddressesUsed,
			'emailForwardsUsed' => $user->emailForwardsUsed,
			'emailAccountsUsed' => $user->emailAccountsUsed,
		);
		
		if ($this->accountID)
		{
			$sql = "DELETE FROM	cp" . CP_N . "_mail_account
					WHERE		accountID = " . $this->accountID;
			WCF :: getDB()->sendQuery($sql);
			
			$update['emailAccountsUsed'] -= 1;
		}
		
		if (!empty($this->destinations))
		{
			$c = count($this->destinations);
			
			if ($this->accountID)
				$c -= 1;
				
			$update['emailForwardersUsed'] -= $c;
		}
		
		$sql = "DELETE FROM cp" . CP_N . "_mail_virtual
				WHERE 	mailID = " . $this->mailID;
		WCF :: getDB()->sendQuery($sql);

		$user->updateOptions($update);
	}

	/**
	 * delete all emails for this user
	 */
	public static function deleteAll($userID)
	{
		$sql = "DELETE 	FROM cp" . CP_N . "_mail_account
				WHERE 		 userID = " . $userID;
		WCF :: getDB()->sendQuery($sql);

		$sql = "DELETE FROM	cp" . CP_N . "_mail_virtual
				WHERE		userID = " . $userID;
		WCF :: getDB()->sendQuery($sql);
		
		$user = new UserEditor($userID);
		$user->updateOptions(array(
			'emailAddressesUsed' => 0,
			'emailForwardsUsed' => 0,
			'emailAccountsUsed' => 0,
		));
	}

	/**
	 * enable email account
	 */
	public function enable()
	{
		$sql = "UPDATE	cp" . CP_N . "_mail_virtual
				SET		enabled = 'Y'
				WHERE	accountID = " . $this->accountID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * disable email account
	 */
	public function disable()
	{
		$sql = "UPDATE	cp" . CP_N . "_mail_virtual
				SET		enabled = 'N'
				WHERE	accountID = " . $this->accountID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * enable all email accounts
	 */
	public static function enableAll($userID)
	{
		$sql = "UPDATE	cp" . CP_N . "_mail_virtual
				SET		enabled = 'Y'
				WHERE	userID = " . $userID;
		WCF :: getDB()->sendQuery($sql);
	}

	/**
	 * disable all email accounts
	 */
	public static function disableAll($userID)
	{
		$sql = "UPDATE	cp" . CP_N . "_mail_virtual
				SET		enabled = 'N'
				WHERE	userID = " . $userID;
		WCF :: getDB()->sendQuery($sql);
	}
}

?>
