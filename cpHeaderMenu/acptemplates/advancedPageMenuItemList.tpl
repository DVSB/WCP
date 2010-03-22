{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/ItemListEditor.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function init() {
		{if $headerMenuItemList|count > 0 || $footerMenuItemList|count > 0 && $this->user->getPermission('admin.pageMenu.canEditPageMenuItem')}
			PageMenuEditor = Class.create(ItemListEditor, {
				updateTree: function($super, itemList) {
					$super(itemList);
					if (itemList.down('li')) {
						if (itemList.up('.container-1').down('.description')) {
							itemList.up('.container-1').down('.description').remove();
						}
					}
					else {
						itemList.up('.container-1').insert({ 'top': '<p class="description">{lang}wcf.acp.pageMenuItem.empty{/lang}</p>' });
					}
				}
			});

			new PageMenuEditor('headerMenuList', { itemTitleEdit: true, itemTitleEditURL: 'index.php?action=PageMenuItemRename&menuItemID=', containment: [ 'headerMenuList', 'footerMenuList' ], tree: true, treeTag: 'ol' });

			new PageMenuEditor('footerMenuList', { itemTitleEdit: true, itemTitleEditURL: 'index.php?action=PageMenuItemRename&menuItemID=', containment: [ 'headerMenuList', 'footerMenuList' ] });
		{/if}
	}
	
	// when the dom is fully loaded, execute these scripts
	document.observe("dom:loaded", init);
	
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/pageMenuItemL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.pageMenuItem.view{/lang}</h2>
	</div>
</div>

{if $deletedPageMenuItemID}
	<p class="success">{lang}wcf.acp.pageMenuItem.delete.success{/lang}</p>	
{/if}

{if $successfullSorting}
	<p class="success">{lang}wcf.acp.pageMenuItem.sort.success{/lang}</p>	
{/if}

{if $this->user->getPermission('admin.pageMenu.canAddPageMenuItem')}
	<div class="contentHeader">
		<div class="largeButtons">
			<ul><li><a href="index.php?form=AdvancedPageMenuItemAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/pageMenuItemAddM.png" alt="" title="{lang}wcf.acp.menu.link.pageMenuItem.add{/lang}" /> <span>{lang}wcf.acp.menu.link.pageMenuItem.add{/lang}</span></a></li></ul>
		</div>
	</div>
{/if}

{if $headerMenuItemList|count > 0 || $footerMenuItemList|count > 0}
	<form method="post" action="index.php?action=AdvancedPageMenuItemSort">		
			<div class="border titleBarPanel">
				<div class="containerHead">{lang}wcf.acp.pageMenuItem.position.header{/lang}</h3></div>
			</div>
			<div class="border content borderMarginRemove">
				<div class="container-1">
                                        {if $headerMenuItemList|count == 0}
                                                <p class="description">{lang}wcf.acp.pageMenuItem.empty{/lang}</p>
                                        {/if}
					<ol id="headerMenuList" class="itemList">
						{foreach from=$headerMenuItemList item=child}
						{* define *}
						{assign var="menuItem" value=$child.menuItem}
						
						<li id="item_{@$menuItem->menuItemID}">
							
							<div class="buttons">							
									{if $this->user->getPermission('admin.pageMenu.canEditPageMenuItem')}
										{if !$menuItem->isDisabled}
											<a href="index.php?action=AdvancedPageMenuItemDisable&amp;pageMenuItemID={@$menuItem->menuItemID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/enabledS.png" alt="" title="{lang}wcf.acp.pagemMenuItem.disable{/lang}" /></a>
										{else}
											<a href="index.php?action=AdvancedPageMenuItemEnable&amp;pageMenuItemID={@$menuItem->menuItemID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/disabledS.png" alt="" title="{lang}wcf.acp.pagemMenuItem.enable{/lang}" /></a>
										{/if}
										
										<a href="index.php?form=AdvancedPageMenuItemEdit&amp;pageMenuItemID={@$menuItem->menuItemID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}wcf.acp.pageMenuItem.edit{/lang}" /></a>
									{else}
										{if !$menuItem->isDisabled}
											<img src="{@RELATIVE_WCF_DIR}icon/enabledDisabledS.png" alt="" title="{lang}wcf.acp.pageMenuItem.disable{/lang}" />
										{else}
											<img src="{@RELATIVE_WCF_DIR}icon/disabledDisabledS.png" alt="" title="{lang}wcf.acp.pageMenuItem.enable{/lang}" />
										{/if}
									
										<img src="{@RELATIVE_WCF_DIR}icon/editDisabledS.png" alt="" title="{lang}wcf.acp.pageMenuItem.editDisabled{/lang}" />
									{/if}
									{if $this->user->getPermission('admin.pageMenu.canAddPageMenuItem')}
										<a href="index.php?form=AdvancedPageMenuItemAdd&amp;parentMenuItem={@$menuItem->menuItem}&amp;position=header&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/addS.png" alt="" title="{lang}wcf.acp.pageMenuItem.add{/lang}" /></a>
									{else}
										<img src="{@RELATIVE_WCF_DIR}icon/addDisabledS.png" alt="" title="{lang}wcf.acp.pageMenuItem.edit{/lang}" />
									{/if}
									{if $this->user->getPermission('admin.pageMenu.canDeletePageMenuItem')}
										<a onclick="return confirm('{lang}wcf.acp.pageMenuItem.delete.sure{/lang}')" href="index.php?action=AdvancedPageMenuItemDelete&amp;pageMenuItemID={@$menuItem->menuItemID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" title="{lang}wcf.acp.pageMenuItem.delete{/lang}" /></a>
									{else}
										<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}wcf.acp.pageMenuItem.deleteDisabled{/lang}" />
									{/if}
								
									{if $child.additionalButtons|isset}{@$child.additionalButtons}{/if}
							</div>
							
							<h3 class="itemListTitle">
								
								{if $this->user->getPermission('admin.rule.canEditItem')}
									<select name="headerMenuListPositions[{@$menuItem->menuItemID}][{@$child.parentMenuItemID}]">
										{section name='positions' loop=$child.maxPosition}
											<option value="{@$positions+1}"{if $positions+1 == $child.position} selected="selected"{/if}>{@$positions+1}</option>
										{/section}
									</select>
								{/if}
								
								ID-{@$menuItem->menuItemID} 
								{if $this->user->getPermission('admin.pageMenu.canEditPageMenuItem')}
									<a href="index.php?form=AdvancedPageMenuItemEdit&amp;pageMenuItemID={@$menuItem->menuItemID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" class="title">{lang}{$menuItem->menuItem}{/lang}</a>
								{else}
									{lang}{$menuItem->menuItem}{/lang}
								{/if}
							</h3>
							
						{if $child.hasChildren}<ol id="parentItem_{@$menuItem->menuItemID}">{else}<ol id="parentItem_{@$menuItem->menuItemID}"></ol></li>{/if}
						{if $child.openParents > 0}{@"</ol></li>"|str_repeat:$child.openParents}{/if}
						{/foreach}					
					</ol>
				</div>
			</div>				
			<div class="border titleBarPanel">
				<div class="containerHead">{lang}wcf.acp.pageMenuItem.position.footer{/lang}</h3></div>
			</div>
			<div class="border content borderMarginRemove">
				<div class="container-1">
                                        {if $footerMenuItemList|count == 0}
                                                <p class="description">{lang}wcf.acp.pageMenuItem.empty{/lang}</p>
                                        {/if}
					<ol id="footerMenuList" class="itemList">
						{foreach from=$footerMenuItemList item=child}
						{* define *}
						{assign var="menuItem" value=$child.menuItem}
						
						<li id="item_{@$menuItem->menuItemID}">
							
							<div class="buttons">
									{if $this->user->getPermission('admin.pageMenu.canEditPageMenuItem')}
										{if !$menuItem->isDisabled}
											<a href="index.php?action=AdvancedPageMenuItemDisable&amp;pageMenuItemID={@$menuItem->menuItemID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/enabledS.png" alt="" title="{lang}wcf.acp.pagemMenuItem.disable{/lang}" /></a>
										{else}
											<a href="index.php?action=AdvancedPageMenuItemEnable&amp;pageMenuItemID={@$menuItem->menuItemID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/disabledS.png" alt="" title="{lang}wcf.acp.pagemMenuItem.enable{/lang}" /></a>
										{/if}
										
										<a href="index.php?form=AdvancedPageMenuItemEdit&amp;pageMenuItemID={@$menuItem->menuItemID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}wcf.acp.pageMenuItem.edit{/lang}" /></a>
									{else}
										{if !$menuItem->isDisabled}
											<img src="{@RELATIVE_WCF_DIR}icon/enabledDisabledS.png" alt="" title="{lang}wcf.acp.pageMenuItem.disable{/lang}" />
										{else}
											<img src="{@RELATIVE_WCF_DIR}icon/disabledDisabledS.png" alt="" title="{lang}wcf.acp.pageMenuItem.enable{/lang}" />
										{/if}
									
										<img src="{@RELATIVE_WCF_DIR}icon/editDisabledS.png" alt="" title="{lang}wcf.acp.pageMenuItem.editDisabled{/lang}" />
									{/if}									
									{if $this->user->getPermission('admin.pageMenu.canDeletePageMenuItem')}
										<a onclick="return confirm('{lang}wcf.acp.pageMenuItem.delete.sure{/lang}')" href="index.php?action=AdvancedPageMenuItemDelete&amp;pageMenuItemID={@$menuItem->menuItemID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" title="{lang}wcf.acp.pageMenuItem.delete{/lang}" /></a>
									{else}
										<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}wcf.acp.pageMenuItem.deleteDisabled{/lang}" />
									{/if}
								
									{if $child.additionalButtons|isset}{@$child.additionalButtons}{/if}
							</div>
							
							<h3 class="itemListTitle">
								
								{if $this->user->getPermission('admin.rule.canEditItem')}
									<select name="footerMenuListPositions[{$menuItem->menuItemID}]">
										{section name='positions' loop=$child.maxPosition}
											<option value="{@$positions+1}"{if $positions+1 == $child.position} selected="selected"{/if}>{@$positions+1}</option>
										{/section}
									</select>
								{/if}
								
								ID-{@$menuItem->menuItemID} 
								{if $this->user->getPermission('admin.pageMenu.canEditPageMenuItem')}
									<a href="index.php?form=AdvancedPageMenuItemEdit&amp;pageMenuItemID={@$menuItem->menuItemID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" class="title">{lang}{$menuItem->menuItem}{/lang}</a>
								{else}
									{lang}{$menuItem->menuItem}{/lang}
								{/if}
							</h3>
							
						{if $child.hasChildren}<ol id="parentItem_{@$menuItem->menuItemID}">{else}<ol id="parentItem_{@$menuItem->menuItemID}"></ol></li>{/if}
						{if $child.openParents > 0}{@"</ol></li>"|str_repeat:$child.openParents}{/if}
						{/foreach}
					</ol>
				</div>			
			</div>		

		{if $this->user->getPermission('admin.pageMenu.canEditPageMenuItem')}
			<div class="formSubmit">
				<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
				<input type="reset" id="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
				<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
				{@SID_INPUT_TAG}
			</div>
		{/if}
	</form>
{/if}

{include file='footer'}