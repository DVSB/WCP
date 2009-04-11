<?php
/*
 * Copyright (c) 2009 Tobias Friebel
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

require_once (CP_DIR . 'lib/form/FTPAddForm.class.php');

class FTPEditForm extends FTPAddForm
{
	public $ftpUser;

	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		if (isset($_POST['ftpUserID']))
			$this->ftpUser = new FTPUserEditor($_POST['ftpUserID']);

		if (is_null($this->ftpUser))
			throw new SystemException('no such account');

		if ($this->ftpUser->userID != WCF :: getUser()->userID)
			throw new SystemException('no such account');

		parent :: readData();
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		parent :: save();

		// create
		$this->ftpUser = $this->ftpUser->update($this->password,
												$this->path,
												$this->description
												);
		$this->saved();

		$url = 'index.php?page=FTPList'. SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}
}
?>