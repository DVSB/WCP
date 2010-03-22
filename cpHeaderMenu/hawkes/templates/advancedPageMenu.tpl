<script type="text/javascript">
	//<![CDATA[
		var transMenuOptions = new Object();
		transMenuOptions['spacerGif'] = '{icon}spacer.png{/icon}';
 		transMenuOptions['dingbatOn'] = '{icon}subMenuOn.gif{/icon}'; 
 		transMenuOptions['dingbatOff'] = '{icon}subMenuOff.gif{/icon}';
 		transMenuOptions['dingbatSize'] = 14; // is assumed to be constant. change it if you replace the dingbat
 		transMenuOptions['menuPadding'] = {$this->style->getVariable('menu.advancedPageMenu.padding')|intval}; 
 		transMenuOptions['itemPadding'] = {$this->style->getVariable('menu.advancedPageMenu.item.padding')|intval};
 		transMenuOptions['shadowSize'] = {$this->style->getVariable('menu.advancedPageMenu.shadow.size')|intval};
 		transMenuOptions['shadowOffset'] = {$this->style->getVariable('menu.advancedPageMenu.shadow.offset')|intval};
 		transMenuOptions['shadowColor'] = "{$this->style->getVariable('menu.advancedPageMenu.shadow.color')}";
 		transMenuOptions['shadowPng'] = '{icon}greyShadow.png{/icon}';
 		transMenuOptions['backgroundColor'] = 'white'; // overwritten by CSS. Don't make any changes here
 		transMenuOptions['backgroundPng'] = '{icon}whiteBackground.png{/icon}';
 		transMenuOptions['hideDelay'] = {@ADVANCED_PAGE_MENU_HIDE_DELAY};
 		transMenuOptions['slideTime'] = {@ADVANCED_PAGE_MENU_SLIDE_TIME}; 		
	//]]>
</script>
<script type="text/javascript" src="{RELATIVE_WCF_DIR}js/TransMenu.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
		onloadEvents.push(function () {			
			if (TransMenu.isSupported()) {
				TransMenu.initialize();
				{foreach from=$pageMenu->getMenuItems('') item=item}
					{if $pageMenu->getMenuItems($item.menuItem)|count > 0}
					menu{$item.menuItemID}.onactivate = function() { document.getElementById('mainMenuItem{@$item.menuItemID}').className = "transMenuHover"; };
					menu{$item.menuItemID}.ondeactivate = function() { document.getElementById('mainMenuItem{@$item.menuItemID}').className = ""; };					
					{/if}
				{/foreach}				
			}
		});		
	//]]>
</script>
	<div id="mainMenu" class="mainMenu">
		<div class="mainMenuInner">{assign var='menuItemCounter' value=0}{assign var='menuItemCount' value=$pageMenu->getMenuItems('')|count}{assign var='activepageMenuItem' value=$this->getPageMenu()->getActiveMenuItem()}<ul>{foreach from=$pageMenu->getMenuItems('') item=item}{assign var='menuItemCounter' value=$menuItemCounter+1}<li id="mainMenuItem{@$item.menuItemID}" {if $activepageMenuItem == $item.menuItem || $menuItemCounter == 1 || $menuItemCounter == $menuItemCount} class="{if $menuItemCounter == 1}first{elseif $menuItemCounter == $menuItemCount}last{/if}{if $activepageMenuItem == $item.menuItem}{if $menuItemCounter == 1 || $menuItemCounter == $menuItemCount}Active{else}active{/if}{/if}"{/if}><a href="{$item.menuItemLink}" title="{lang}{@$item.menuItem}{/lang}">{if $item.menuItemIconM}<img src="{$item.menuItemIconM}" alt="" /> {/if}<span>{lang}{@$item.menuItem}{/lang}</span></a></li>{/foreach}</ul>
		</div>
	</div>
<script type="text/javascript">
	//<![CDATA[
	if (TransMenu.isSupported()) {
		var ms = new TransMenuSet(TransMenu.direction.down, 1, 0, TransMenu.reference.bottomLeft);
		{advancedmenu}		
		TransMenu.renderAll();
	}
	//]]>
</script>	