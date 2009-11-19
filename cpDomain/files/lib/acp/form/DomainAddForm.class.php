<?php
/*
 * Copyright (c) 2009 Tobias Friebel  
 * Authors: Tobias Friebel <TobyF@Web.de>
 *
 * Lizenz: GPL
 *
 * $Id$
 */

require_once (WCF_DIR . 'lib/acp/form/DynamicOptionListForm.class.php');

/**
 * Shows the domain add form.
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	acp.form
 * @category 	Control Panel
 */
class DomainAddForm extends DynamicOptionListForm
{
	public $templateName = 'domainAdd';
	public $menuItemName = 'cp.acp.menu.link.domains.add';
	public $permission = 'admin.cp.canAddDomain';
	
	public $cacheClass = 'CacheBuilderDomainOption';

	public $cacheName = 'domain-option-';
	public $additionalFields = array();
	public $domain;
	public $options;
	
	public $domainname = '';
	public $activeTabMenuItem = '';
	public $activeSubTabMenuItem = '';

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters()
	{
		parent :: readFormParameters();
		
		if (isset($_POST['domainname']))
			$this->domainname = StringUtil :: trim($_POST['domainname']);
			
		if (isset($_POST['activeTabMenuItem'])) 
			$this->activeTabMenuItem = $_POST['activeTabMenuItem'];
		if (isset($_POST['activeSubTabMenuItem'])) 
			$this->activeSubTabMenuItem = $_POST['activeSubTabMenuItem'];
	}

	/**
	 * @see Form::validate()
	 */
	public function validate()
	{
		// validate static user options 
		try
		{
			$this->validateDomainname($this->domainname);
		}
		catch (UserInputException $e)
		{
			$this->errorType[$e->getField()] = $e->getType();
		}
		
		// validate dynamic options
		parent :: validate();
	}
	
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

	/**
	 * @see Form::save()
	 */
	public function save()
	{
		AbstractForm :: save();
		
		// create
		require_once (CP_DIR . 'lib/data/domains/DomainEditor.class.php');
		$this->domain = DomainEditor :: create($this->domainname, $this->activeOptions, $this->additionalFields);
		$this->saved();
		
		// show empty add form
		WCF :: getTPL()->assign(array (
			'success' => true, 
			'newDomain' => $this->domain
		));
		
		// reset values
		$this->domainname = '';
	
		foreach ($this->activeOptions as $key => $option)
		{
			unset($this->activeOptions[$key]['optionValue']);
		}
	}

	/**
	 * Throws a InputException if the domainname is not unique or not valid.
	 * 
	 * @param	string		$domainname
	 */
	protected function validateDomainname($domainname)
	{
		if (empty($domainname))
		{
			throw new UserInputException('domainname');
		}

		if (!DomainUtil :: isValidDomainname($domainname))
		{
			throw new UserInputException('domainname', 'notValid');
		}
		
		if (!DomainUtil :: isAvailableDomainname($domainname))
		{
			throw new UserInputException('domainname', 'notUnique');
		}
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData()
	{
		parent :: readData();
		
		$this->options = $this->getOptionTree();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables()
	{
		parent :: assignVariables();
		
		WCF :: getTPL()->assign(array (
			'domainname' => $this->domainname, 
			'options' => $this->options, 
			'action' => 'add',
			'activeTabMenuItem' 	=> $this->activeTabMenuItem,
			'activeSubTabMenuItem' 	=> $this->activeSubTabMenuItem
		));
	}

	/**
	 * @see Page::show()
	 */
	public function show()
	{
		// set active menu item
		WCFACP :: getMenu()->setActiveMenuItem($this->menuItemName);
		
		// check permission
		WCF :: getUser()->checkPermission($this->permission);
		
		// get domain options and categories from cache
		$this->readCache();
		
		// show form
		parent :: show();
	}

	/**
	 * @see DynamicOptionListForm::getOptionTree()
	 */
	protected function getOptionTree($parentCategoryName = '', $level = 0)
	{
		$options = array ();
		
		if (isset($this->cachedCategoryStructure[$parentCategoryName]))
		{
			// get super categories
			foreach ($this->cachedCategoryStructure[$parentCategoryName] as $superCategoryName)
			{
				$superCategory = $this->cachedCategories[$superCategoryName];
				$superCategory['options'] = array ();
				
				if ($this->checkCategory($superCategory))
				{
					if ($level <= 0)
					{
						$superCategory['categories'] = $this->getOptionTree($superCategoryName, $level + 1);
					}
					if ($level > 0 || count($superCategory['categories']) == 0)
					{
						$superCategory['options'] = $this->getCategoryOptions($superCategoryName);
					}
					
					if ((isset($superCategory['categories']) && count($superCategory['categories']) > 0) || (isset($superCategory['options']) && count($superCategory['options']) > 0))
					{
						$options[] = $superCategory;
					}
				}
			}
		}
		
		return $options;
	}
}
?>