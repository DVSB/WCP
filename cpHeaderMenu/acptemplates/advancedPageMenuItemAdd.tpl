{include file='header'}

<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>
<script type="text/javascript">
	//<![CDATA[
	var tabMenu = new TabMenu();
	onloadEvents.push(function() { tabMenu.showSubTabMenu("{$activeTabMenuItem}") });
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/pageMenuItem{@$action|ucfirst}L.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.pageMenuItem.{@$action}{/lang}</h2>
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{lang}wcf.acp.pageMenuItem.{@$action}.success{/lang}</p>	
{/if}

<div class="contentHeader">
	<div class="largeButtons">
		<ul><li><a href="index.php?page=AdvancedPageMenuItemList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/pageMenuItemM.png" alt="" title="{lang}wcf.acp.menu.link.pageMenuItem.view{/lang}" /> <span>{lang}wcf.acp.menu.link.pageMenuItem.view{/lang}</span></a></li></ul>
	</div>
</div>

<form method="post" action="index.php?form=AdvancedPageMenuItem{@$action|ucfirst}">
	<div class="tabMenu">
		<ul>
			<li id="data"><a onclick="tabMenu.showSubTabMenu('data');"><span>{lang}wcf.acp.pageMenuItem.data{/lang}</span></a></li>
			<li id="itemPermissions"><a onclick="tabMenu.showSubTabMenu('itemPermissions');"><span>{lang}wcf.acp.pageMenuItem.permissions{/lang}</span></a></li>		
			{if $additionalTabs|isset}{@$additionalTabs}{/if}
		</ul>
	</div>
	<div class="subTabMenu">
		<div class="containerHead"><div> </div></div>
	</div>
	<div class="border tabMenuContent hidden" id="data-content">
		<div class="container-1">
			<fieldset>
				<legend>{lang}wcf.acp.pageMenuItem.data{/lang}</legend>
				
				{if $action == 'edit'}
					<div class="formElement" id="languageIDDiv">
						<div class="formFieldLabel">
							<label for="languageID">{lang}wcf.acp.pageMenuItem.language{/lang}</label>
						</div>
						<div class="formField">
							<select name="languageID" id="languageID" onchange="location.href='index.php?form=PageMenuItemEdit&amp;pageMenuItemID={@$pageMenuItemID}&amp;languageID=' + this.value + '&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}'">
								{foreach from=$languages key=availableLanguageID item=languageCode}
									<option value="{@$availableLanguageID}"{if $availableLanguageID == $languageID} selected="selected"{/if}>{lang}wcf.global.language.{@$languageCode}{/lang}</option>
								{/foreach}
							</select>
						</div>
						<div class="formFieldDesc hidden" id="languageIDHelpMessage">
							{lang}wcf.acp.pageMenuItem.language.description{/lang}
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('languageID');
					//]]></script>
				{/if}
				
				<div class="formElement{if $errorField == 'name'} formError{/if}" id="nameDiv">
					<div class="formFieldLabel">
						<label for="name">{lang}wcf.acp.pageMenuItem.name{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="name" name="name" value="{$name}" />
						{if $errorField == 'name'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="nameHelpMessage">
						{lang}wcf.acp.pageMenuItem.name.description{/lang}
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('name');
				//]]></script>
				
				<div class="formElement{if $errorField == 'link'} formError{/if}" id="linkDiv">
					<div class="formFieldLabel">
						<label for="link">{lang}wcf.acp.pageMenuItem.link{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="link" name="link" value="{$link}" />
						{if $errorField == 'link'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="linkHelpMessage">
						{lang}wcf.acp.pageMenuItem.link.description{/lang}
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('link');
				//]]></script>
								
			</fieldset>
							
			<fieldset>
				<legend>{lang}wcf.acp.pageMenuItem.display{/lang}</legend>
					
				<div class="formElement" id="parentMenuItemDiv">
					<div class="formFieldLabel">
						<label for="parentMenuItem">{lang}wcf.acp.pageMenuItem.parentMenuItem{/lang}</label>
					</div>
					<div class="formField">
						<select name="parentMenuItem" id="parentMenuItem">
							{htmlOptions options=$menuItemSelect disableEncoding=true selected=$parentMenuItem}
						</select>
					</div>
					<div class="formFieldDesc hidden" id="parentMenuItemHelpMessage">
						{lang}wcf.acp.pageMenuItem.parentMenuItem.description{/lang}
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('parentMenuItem');
				//]]></script>	
				
				<div class="formElement{if $errorField == 'iconS'} formError{/if}" id="iconSDiv">
					<div class="formFieldLabel">
						<label for="iconS">{lang}wcf.acp.pageMenuItem.iconS{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="iconS" name="iconS" value="{$iconS}" />
						{if $errorField == 'iconS'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="iconSHelpMessage">
						{lang}wcf.acp.pageMenuItem.iconS.description{/lang}
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('iconS');
				//]]></script>
				
				<div class="formElement{if $errorField == 'iconM'} formError{/if}" id="iconMDiv">
					<div class="formFieldLabel">
						<label for="iconM">{lang}wcf.acp.pageMenuItem.iconM{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="iconM" name="iconM" value="{$iconM}" />
						{if $errorField == 'iconM'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="iconMHelpMessage">
						{lang}wcf.acp.pageMenuItem.iconM.description{/lang}
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('iconM');
				//]]></script>
				
				<div class="formElement" id="showOrderDiv">
					<div class="formFieldLabel">
						<label for="showOrder">{lang}wcf.acp.pageMenuItem.showOrder{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="showOrder" name="showOrder" value="{@$showOrder}" />
					</div>
					<div class="formFieldDesc hidden" id="showOrderHelpMessage">
						{lang}wcf.acp.pageMenuItem.showOrder.description{/lang}
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('showOrder');
				//]]></script>	
			</fieldset>
			
			{if $additionalFields|isset}{@$additionalFields}{/if}
		</div>
	</div>
	
	<div class="border tabMenuContent hidden" id="itemPermissions-content">
		<div class="container-1">
			<fieldset>
				<legend>{lang}wcf.acp.pageMenuItem.permissions{/lang}</legend>
				
				{if $options|count}
					<div class="formElement{if $errorField == 'permissions'} formError{/if}" id="permissionsDiv">
						<div class="formFieldLabel">
							<label for="permissions">{lang}wcf.acp.pageMenuItem.groupPermissions{/lang}</label>
						</div>
						<div class="formField">
							<select name="permissions[]" id="permissions" multiple="multiple" size="10">
								<option value=""></option>
								{extendedhtmloptions options=$options selected=$permissions}
							</select>
							{if $errorField == 'permissions'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}								
								</p>
							{/if}
						</div>
						<div class="formFieldDesc hidden" id="permissionsHelpMessage">
							{lang}wcf.acp.pageMenuItem.groupPermissions.description{/lang}
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('permissions');
					//]]></script>
				{/if}
				
				<div class="formElement">
					<div class="formFieldLabel">
							<label for="groupIDs">{lang}wcf.acp.pageMenuItem.groupIDs{/lang}</label>
					</div>
					<div class="formField">						
						{htmlcheckboxes options=$groupSelect selected=$groupIDs name=groupIDs}																	
					</div>
					<div class="formFieldDesc hidden" id="groupIDsHelpMessage">
						{lang}wcf.acp.pageMenuItem.groupIDs.description{/lang}					
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('groupIDs');
				//]]></script>
				
			</fieldset>
		</div>
	</div>
	
	{if $additionalTabContents|isset}{@$additionalTabContents}{/if}
	
	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
		{if $pageMenuItemID|isset}<input type="hidden" name="pageMenuItemID" value="{@$pageMenuItemID}" />{/if}
 		{@SID_INPUT_TAG}
 	</div>
</form>

{include file='footer'}