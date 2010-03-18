<?php
/*
 * Copyright (c) 2009 Tobias Friebel
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

require_once (CP_DIR . 'lib/form/EmailAddForm.class.php');

class EmailEditForm extends FTPAddForm
{
	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		if (isset($_REQUEST['mailID']))
			$this->ftpAccount = new FTPUserEditor($_REQUEST['mailID']);

		if (is_null($this->ftpAccount))
			throw new SystemException('no such account');

		if ($this->ftpAccount->userID != WCF :: getUser()->userID)
			throw new SystemException('invalid user');

		//remove homedir-path from ftp-path, we show only relative paths
		$this->path = '/' . StringUtil :: replace(WCF :: getUser()->homeDir, '', $this->ftpAccount->homedir);

		parent :: readData();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();

		WCF :: getTPL()->assign(array (
			'password' => '',
			'path' => $this->path,
			'description' => $this->ftpAccount->description,
			'ftpUserID' => $this->ftpAccount->ftpUserID,
			'action' => 'edit',
		));
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		AbstractSecureForm :: save();

		// update
		$this->ftpAccount->update($this->password,
								  WCF :: getUser()->homeDir . '/'. $this->path,
								  $this->description
								 );
		$this->saved();

		$url = 'index.php?page=FTPList'. SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
		PageMenu::setActiveMenuItem('cp.header.menu.ftp');

		AbstractSecureForm :: show();
	}
}
?>