{include file="documentHeader"}
<head>
	<title>{lang}cp.domain.list{/lang} - {PAGE_TITLE}</title>

	{include file='headInclude' sandbox=false}

	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>
	<script src="{@RELATIVE_WCF_DIR}js/Calendar.class.js" type="text/javascript"></script>
	<script type="text/javascript">
		//<![CDATA[
		var tabMenu = new TabMenu();
		onloadEvents.push(function() { tabMenu.showSubTabMenu("{$activeTabMenuItem}", "{$activeSubTabMenuItem}"); });
		//]]>
	</script>
</head>
<body>
{include file='header' sandbox=false}

<div id="main">

	<div class="mainHeadline">
		<img src="{@RELATIVE_WCF_DIR}icon/domain{@$action|ucfirst}L.png" alt="" />
		<div class="headlineContainer">
			<h2>{lang}cp.acp.domain.{@$action}{/lang}</h2>
		</div>
	</div>
	
	{if $errorField}
		<p class="error">{lang}wcf.global.form.error{/lang}</p>
	{/if}
	
	{if $success|isset}
		<p class="success">{lang}cp.acp.domain.{@$action}.success{/lang}</p>	
	{/if}
	
	<div class="contentHeader">
		<div class="largeButtons">
			<ul>
				<li><a href="index.php?page=DomainList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}cp.acp.menu.link.domains.list{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/groupM.png" alt="" /> <span>{lang}cp.acp.menu.link.domains.list{/lang}</span></a></li>
				{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
			</ul>
		</div>
	</div>
	<form method="post" action="index.php?form=SubDomain{@$action|ucfirst}">
		<div class="border content">
			<div class="container-1">
				<fieldset>
					<legend>{lang}cp.acp.domain.data{/lang}</legend>
					
					<div class="formElement{if $errorType.domainname|isset} formError{/if}" id="domainnameDiv">
						<div class="formFieldLabel">
							<label for="domainname">{lang}cp.acp.domain.domainname{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" id="domainname" name="domainname" value="{$domainname}" />
							{if $errorType.domainname|isset}
								<p class="innerError">
									{if $errorType.domainname == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
						<div class="formFieldDesc hidden" id="domainnameHelpMessage">
							<p>{lang}cp.acp.domain.domainname.description{/lang}</p>
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('domainname');
					//]]></script>
					
					<div class="formElement{if $errorType.parentDomain|isset} formError{/if}" id="parentDomainDiv">
						<div class="formFieldLabel">
							<label for="parentDomain">{lang}cp.acp.domain.parentDomain{/lang}</label>
						</div>
						<div class="formField">
							{htmlOptions options=$parentDomains selected=$parentDomainID id=parentDomains name=parentDomainID}
							{if $errorType.parentDomain|isset}
								<p class="innerError">
									{if $errorType.parentDomain == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
						<div class="formFieldDesc hidden" id="parentDomainHelpMessage">
							<p>{lang}cp.acp.domain.parentDomain.description{/lang}</p>
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('parentDomain');
					//]]></script>
					
					{if $additionalFields|isset}{@$additionalFields}{/if}
				</fieldset>
			
				{if $additionalFieldSets|isset}{@$additionalFieldSets}{/if}
			
				{if !$options|empty}
				<div class="tabMenu">
					<ul>
						{foreach from=$options item=categoryLevel1}
							<li id="{@$categoryLevel1.categoryName}"><a onclick="tabMenu.showSubTabMenu('{@$categoryLevel1.categoryName}');"><span>{lang}cp.acp.domain.category.{@$categoryLevel1.categoryName}{/lang}</span></a></li>
						{/foreach}
					</ul>
				</div>
				<div class="subTabMenu">
					<div class="containerHead">
						{foreach from=$options item=categoryLevel1}
							<ul class="hidden" id="{@$categoryLevel1.categoryName}-categories">
								{foreach from=$categoryLevel1.categories item=categoryLevel2}
									<li id="{@$categoryLevel1.categoryName}-{@$categoryLevel2.categoryName}"><a onclick="tabMenu.showTabMenuContent('{@$categoryLevel1.categoryName}-{@$categoryLevel2.categoryName}');"><span>{lang}cp.acp.domain.category.{@$categoryLevel2.categoryName}{/lang}</span></a></li>
								{/foreach}
							</ul>
						{/foreach}
					</div>
				</div>
				
				{foreach from=$options item=categoryLevel1}
					{foreach from=$categoryLevel1.categories item=categoryLevel2}
						<div class="border tabMenuContent hidden" id="{@$categoryLevel1.categoryName}-{@$categoryLevel2.categoryName}-content">
							<div class="container-1">
								<h3 class="subHeadline">{lang}cp.acp.domain.option.category.{@$categoryLevel2.categoryName}{/lang}</h3>
								<p class="description">{lang}cp.acp.domain.option.category.{@$categoryLevel2.categoryName}.description{/lang}</p>
								
								{if $categoryLevel2.options|isset && $categoryLevel2.options|count}
									{include file='optionFieldList' options=$categoryLevel2.options langPrefix='cp.acp.domain.option.'}
								{/if}
								
								{if $categoryLevel2.categories|isset}
									{foreach from=$categoryLevel2.categories item=categoryLevel3}
										<fieldset>
											<legend>{lang}cp.acp.domain.option.category.{@$categoryLevel3.categoryName}{/lang}</legend>
											<p class="description">{lang}cp.acp.domain.option.category.{@$categoryLevel3.categoryName}.description{/lang}</p>
										
											<div>
												{include file='optionFieldList' options=$categoryLevel3.options langPrefix='cp.acp.domain.option.'}
											</div>
										</fieldset>
									{/foreach}
								{/if}
							</div>
						</div>
					{/foreach}
				{/foreach}
				{/if}
			</div>
		</div>
		
		<div class="formSubmit">
			<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
			<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
			<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
	 		{@SID_INPUT_TAG}
	 		<input type="hidden" name="action" value="{@$action}" />
	 		{if $domainID|isset}<input type="hidden" name="domainID" value="{@$domainID}" />{/if}
	 		
	 		<input type="hidden" id="activeTabMenuItem" name="activeTabMenuItem" value="{$activeTabMenuItem}" />
	 		<input type="hidden" id="activeSubTabMenuItem" name="activeSubTabMenuItem" value="{$activeSubTabMenuItem}" />
	 	</div>
	</form>
	
</div>

{include file='footer'}