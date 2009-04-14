<?php
/*
 * Copyright (c) 2009 Tobias Friebel
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

require_once (CP_DIR . 'lib/form/MySQLAddForm.class.php');

class MySQLEditForm extends MySQLAddForm
{
	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		if (isset($_REQUEST['mysqlID']))
			$this->mysql = new MySQLEditor($_REQUEST['mysqlID']);

		if (is_null($this->mysql))
			throw new SystemException('no such account');

		if ($this->mysql->userID != WCF :: getUser()->userID)
			throw new SystemException('invalid user');

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
			'description' => $this->mysql->description,
		));
	}

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		parent :: save();

		// update
		$this->ftpAccount->update($this->password,
								  $this->description
								 );
		$this->saved();

		$url = 'index.php?page=MySQLList'. SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil::redirect($url);
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
		PageMenu::setActiveMenuItem('cp.header.menu.mysql');

		parent::show();
	}
}
?>