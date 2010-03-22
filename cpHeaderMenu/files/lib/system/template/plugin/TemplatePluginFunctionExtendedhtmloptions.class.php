<?php

require_once(WCF_DIR.'lib/system/exception/SystemException.class.php');
require_once(WCF_DIR.'lib/system/template/TemplatePluginFunction.class.php');
require_once(WCF_DIR.'lib/system/template/Template.class.php');


/**
 * Outputs a group options multi select
 *
 * @package	net.hawkes.advancedheadermenu
 * @author	Oliver Kliebisch
 * @copyright	2008 Oliver Kliebisch
 * @license	Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 Unported <http://creativecommons.org/licenses/by-nc-nd/3.0/>
 */

class TemplatePluginFunctionExtendedhtmloptions implements TemplatePluginFunction {
	public $html = '';
	public $selected = array();
	/**
	 * @see TemplatePluginFunction::execute()
	 */
	public function execute($tagArgs, Template $tplObj) {

		if (!isset($tagArgs['options']) || !is_array($tagArgs['options'])) {
			throw new SystemException("missign 'options' argument in htmlCheckboxes tag", 12001);
		}

		if (isset($tagArgs['disableEncoding']) && $tagArgs['disableEncoding']) {
			$this->disableEncoding = true;
		}
		else {
			$this->disableEncoding = false;
		}

		// get selected values
		if (isset($tagArgs['selected'])) {
			$this->selected = $tagArgs['selected'];
		}
		

		if (!isset($tagArgs['separator'])) {
			$tagArgs['separator'] = '';
		}

		// build html
		foreach ($tagArgs['options'] as $key => $value) {
			$this->buildHtml($value);			
		}

		return $this->html;
	}

	protected function buildHtml($item, $depth=0) {		
		if(isset($item['categoryID'])) {
			$first = false;
			if($depth == 0) {
				$first = true;
				$this->html .= "<optgroup label='".WCF::getLanguage()->get('wcf.acp.group.option.category.'.$item['categoryName'])."'>";				
			}					
			foreach($item['options'] as $option) {
				$this->buildHtml($option, $depth);
			}
			if(isset($item['categories'])) {
				$depth++;
				foreach($item['categories'] as $category) {
					$this->buildHtml($category, $depth);
				}
			}
			if($first) $this->html .= "</optgroup>";
		}
		else if(isset($item['optionID'])) {
			$selected='';
			if(in_array($item['optionName'], $this->selected)) $selected='selected="selected"';
			$this->html .= "<option label='".WCF::getLanguage()->get('wcf.acp.group.option.'.$item['optionName'])."' value='".$item['optionName']."' ".$selected.">".WCF::getLanguage()->get('wcf.acp.group.option.'.$item['optionName'])."</option>";			
		}		
	}
}

?>