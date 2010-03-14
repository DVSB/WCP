<?php
// wcf imports
require_once (WCF_DIR . 'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches domainoptions
 *
 * @author		Tobias Friebel
 * @copyright	2009 Tobias Friebel
 * @license		GNU General Public License <http://opensource.org/licenses/gpl-2.0.php>
 * @package		com.toby.cp.domain
 * @subpackage	system.cache
 * @category 	Control Panel
 * @id			$Id$
 */
class CacheBuilderDomainOption implements CacheBuilder
{
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource)
	{	
		$data = array (
			'categories' => array (), 
			'options' => array (), 
			'categoryStructure' => array (), 
			'optionToCategories' => array ()
		);
		
		// option categories
		// get needed option categories
		$sql = "SELECT		option_category.*
				FROM		cp" . CP_N . "_domain_option_category option_category
				ORDER BY	showOrder";
		$result = WCF :: getDB()->sendQuery($sql);
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			$data['categories'][$row['categoryName']] = $row;
			if (!isset($data['categoryStructure'][$row['parentCategoryName']]))
			{
				$data['categoryStructure'][$row['parentCategoryName']] = array ();
			}
			
			$data['categoryStructure'][$row['parentCategoryName']][] = $row['categoryName'];
		}
		
		// get needed options
		$sql = "SELECT		*
				FROM		cp" . CP_N . "_domain_option
				ORDER BY	showOrder";
		$result = WCF :: getDB()->sendQuery($sql);
		while ($row = WCF :: getDB()->fetchArray($result))
		{
			// unserialize additional data
			$row['additionalData'] = (empty($row['additionalData']) ? array () : @unserialize($row['additionalData']));
			
			$data['options'][$row['optionName']] = $row;
			if (!isset($data['optionToCategories'][$row['categoryName']]))
			{
				$data['optionToCategories'][$row['categoryName']] = array ();
			}
			
			$data['optionToCategories'][$row['categoryName']][] = $row['optionName'];
		}
		
		return $data;
	}
}
?>