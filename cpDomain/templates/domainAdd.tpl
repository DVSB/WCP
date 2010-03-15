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
		var calendar = new Calendar('{$monthList}', '{$weekdayList}', {@$startOfWeek});
		//]]>
	</script>
</head>
<body>
{include file='header' sandbox=false}

<div id="main">

	<div class="mainHeadline">
		<img src="{@RELATIVE_WCF_DIR}icon/group{@$action|ucfirst}L.png" alt="" />
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
	<form method="post" action="index.php?form=Domain{@$action|ucfirst}">
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
					
					<div class="formElement{if $errorType.user|isset} formError{/if}" id="usernameDiv">
						<div class="formFieldLabel">
							<label for="username">{lang}cp.acp.domain.username{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" id="username" name="username" value="{$username}" />
							<script type="text/javascript">
								//<![CDATA[
								suggestion.setSource('index.php?page=CustomerSuggest{@SID_ARG_2ND_NOT_ENCODED}');
								suggestion.enableIcon(true);
								suggestion.init('username');
								//]]>
							</script>
							
							{if $errorType.username|isset}
								<p class="innerError">
									{if $errorType.username == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
						<div class="formFieldDesc hidden" id="usernameHelpMessage">
							<p>{lang}cp.acp.domain.username.description{/lang}</p>
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('username');
					//]]></script>
					
					<div class="formElement{if $errorType.adminname|isset} formError{/if}" id="adminnameDiv">
						<div class="formFieldLabel">
							<label for="adminname">{lang}cp.acp.domain.adminname{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" id="adminname" name="adminname" value="{$adminname}" />
							<script type="text/javascript">
								//<![CDATA[
								suggestion.setSource('../index.php?page=AdminSuggest{@SID_ARG_2ND_NOT_ENCODED}');
								suggestion.enableIcon(true);
								suggestion.init('adminname');
								//]]>
							</script>
							
							{if $errorType.adminname|isset}
								<p class="innerError">
									{if $errorType.adminname == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
						<div class="formFieldDesc hidden" id="adminnameHelpMessage">
							<p>{lang}cp.acp.domain.adminname.description{/lang}</p>
						</div>
					</div>
					<script type="text/javascript">//<![CDATA[
						inlineHelp.register('adminname');
					//]]></script>
					
					<div class="formElement{if $errorType.registrationDate|isset} formError{/if}" id="registrationDateDiv">
						<div class="formFieldLabel">
							<label for="registrationDate">{lang}cp.acp.domain.registrationDate{/lang}</label>
						</div>
						<div class="formField">	
							<div class="floatedElement">
								<label for="registrationDateDay">{lang}wcf.global.date.day{/lang}</label>
								{htmlOptions options=$dayOptions selected=$registrationDateDay id=registrationDateDay name=registrationDateDay}
							</div>
							
							<div class="floatedElement">
								<label for="registrationDateMonth">{lang}wcf.global.date.month{/lang}</label>
								{htmlOptions options=$monthOptions selected=$registrationDateMonth id=registrationDateMonth name=registrationDateMonth}
							</div>
							
							<div class="floatedElement">
								<label for="registrationDateYear">{lang}wcf.global.date.year{/lang}</label>
								<input id="registrationDateYear" class="inputText fourDigitInput" type="text" name="registrationDateYear" value="{@$registrationDateYear}" maxlength="4" />
							</div>
							
							<div class="floatedElement">
								<a id="registrationDateButton"><img src="{@RELATIVE_WCF_DIR}icon/datePickerOptionsM.png" alt="" /></a>
								<div id="registrationDateCalendar" class="inlineCalendar"></div>
								<script type="text/javascript">
									//<![CDATA[
									calendar.init('registrationDate');
									//]]>
								</script>
							</div>
						</div>
					</div>
					
					{if $additionalFields|isset}{@$additionalFields}{/if}
				</fieldset>
			
				{if $additionalFieldSets|isset}{@$additionalFieldSets}{/if}
			
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