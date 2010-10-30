<?php

require_once (WCF_DIR . 'lib/acp/form/DynamicOptionListForm.class.php');

/**
 * This class provides default implementations for a list of dynamic domainoptions.
 *
 * @author		Tobias Friebel
 * @copyright	2010 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	acp.form
 * @category 	Control Panel
 * @id			$Id$
 */
abstract class DynamicDomainOptionListForm extends DynamicOptionListForm
{
	public $cacheClass = 'CacheBuilderDomainOption';

	public $cacheName = 'domain-option';

	public $options;

	/**
	 * Gets all options and option categories from cache.
	 */
	protected function readCache()
	{
		// get cache contents
		$cacheName = $this->cacheName;
		WCF::getCache()->addResource($cacheName, CP_DIR.'cache/cache.'.$cacheName.'.php', CP_DIR.'lib/system/cache/'.$this->cacheClass.'.class.php');
		$this->cachedCategories = WCF::getCache()->get($cacheName, 'categories');
		$this->cachedOptions = WCF::getCache()->get($cacheName, 'options');
		$this->cachedCategoryStructure = WCF::getCache()->get($cacheName, 'categoryStructure');
		$this->cachedOptionToCategories = WCF::getCache()->get($cacheName, 'optionToCategories');

		// get active options
		$this->loadActiveOptions($this->activeCategory);
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

	/**
	 * Returns an object of the requested option type.
	 *
	 * @param	string			$type
	 * @return	OptionType
	 */
	protected function getTypeObject($type)
	{
		if (!isset($this->typeObjects[$type]))
		{
			$className = 'DomainOptionType'.ucfirst($type);
			$classPath = CP_DIR.'lib/data/domain/options/'.$className.'.class.php';

			// include class file
			if (!file_exists($classPath))
			{
				throw new SystemException("unable to find class file '".$classPath."'", 11000);
			}
			require_once($classPath);

			// create instance
			if (!class_exists($className))
			{
				throw new SystemException("unable to find class '".$className."'", 11001);
			}
			$this->typeObjects[$type] = new $className();
			$this->typeObjects[$type]->form = $this;
		}

		return $this->typeObjects[$type];
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
}

?>