<?php
/*
 * Copyright (c) 2009 Tobias Friebel  
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

// wcf imports
require_once (WCF_DIR . 'lib/acp/form/DynamicOptionListForm.class.php');

/**
 * This class provides default implementations for a list of dynamic user options.
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	acp.form
 * @category 	Control Panel
 */
abstract class DomainOptionListForm extends DynamicOptionListForm
{
	public $cacheName = 'domain-option-';

	/**
	 * Validates an option.
	 * 
	 * @param	string		$key		option name
	 * @param	array		$option		option data
	 */
	protected function validateOption($key, $option)
	{
		parent :: validateOption($key, $option);
		
		if ($option['required'] && empty($this->activeOptions[$key]['optionValue']))
		{
			throw new UserInputException($option['optionName']);
		}
	}
}
?>