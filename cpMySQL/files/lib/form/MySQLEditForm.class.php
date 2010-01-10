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
	public $neededPermissions = array('cp.mysql.canEditMySQL', 'cp.mysql.canAddMySQL', 'cp.mysql.canListMySQL');
	
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

		AbstractSecureForm :: readData();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		AbstractSecureForm :: assignVariables();

		WCF :: getTPL()->assign(array (
			'password' => '',
			'mysqlID' => $this->mysql->mysqlID,
			'description' => $this->mysql->description,
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
		$this->mysql->update($this->password,
							 $this->description
							);
		$this->saved();

		$url = 'index.php?page=MySQLList'. SID_ARG_2ND_NOT_ENCODED;
		HeaderUtil :: redirect($url);
	}
}
?>