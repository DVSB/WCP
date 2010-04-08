<?php
/*
 * Copyright (c) 2009 Tobias Friebel
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id: EmailEditForm.class.php 72 2010-03-26 15:01:12Z toby $
 */

require_once (WCF_DIR . 'lib/page/AbstractPage.class.php');
require_once (CP_DIR . 'lib/data/email/EmailEditor.class.php');

class EmailDetailPage extends AbstractPage
{
	public $templateName = 'emailDetail';
	
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
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();

		WCF :: getTPL()->assign(array (
			'mailID' => $this->email->mailID,
			'accountID' => $this->email->accountID,
			'emailaddress' => $this->email->emailaddress,
			'emailaddress_full' => $this->email->emailaddress_full,
			'destination' => $this->email->destination,
			'isCatchall' => $this->email->isCatchall,
			'catchallAvailable' => EmailUtil :: isAvailableCatchall($this->email->domainID, $this->email->mailID),
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
		PageMenu :: setActiveMenuItem('cp.header.menu.email');

		parent :: show();
	}
}
?>