<?php
/*
 * +-----------------------------------------+
 * | Copyright (c) 2009 Tobias Friebel		 |
 * +-----------------------------------------+
 * | Authors: Tobias Friebel <TobyF@Web.de>  |
 * +-----------------------------------------+
 *
 * Project: WCF Control Panel
 *
 * $Id$
 */

require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

class IndexPage extends AbstractPage
{
	public $templateName = 'index';

	/**
	 * @see Page::assignVariables();
	 */
	public function assignVariables()
	{
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'selfLink' => 'index.php?page=Index'.SID_ARG_2ND_NOT_ENCODED,
			'allowSpidersToIndexThisPage' => false,
		));

	}
}
?>