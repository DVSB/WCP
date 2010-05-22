{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>
<script src="{@RELATIVE_WCF_DIR}js/Calendar.class.js" type="text/javascript"></script>
<script type="text/javascript">
	//<![CDATA[
	var tabMenu = new TabMenu();
	{if $options|count}onloadEvents.push(function() { tabMenu.showSubTabMenu('{@$options.0.categoryName}') });{/if}
	var calendar = new Calendar('{$monthList}', '{$weekdayList}', {@$startOfWeek});
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_CP_DIR}icon/domain{@$action|ucfirst}L.png" alt="" />
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
			<li><a href="index.php?page=DomainList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}cp.acp.domain.list{/lang}"><img src="{@RELATIVE_CP_DIR}icon/domainM.png" alt="" /> <span>{lang}cp.acp.domain.list{/lang}</span></a></li>
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
						{if $errorField == 'domainname'}
							<p class="innerError">
								{if $errorType.domainname == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								{if $errorType.domainname == 'notValid'}{lang}cp.acp.domain.domainname.notValid{/lang}{/if}
								{if $errorType.domainname == 'notUnique'}{lang}cp.acp.domain.domainname.notUnique{/lang}{/if}
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
				
				<div class="formElement{if $errorType.username|isset} formError{/if}" id="usernameDiv">
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
								{if $errorType.username == 'notFound'}{lang}cp.acp.domain.username.notFound{/lang}{/if}
								{if $errorType.username == 'invalidUser'}{lang}cp.acp.domain.username.invalidUser{/lang}{/if}
								{if $errorType.username == 'noCustomer'}{lang}cp.acp.domain.username.noCustomer{/lang}{/if}
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

			{if $options|count || $additionalTabs|isset}
				<div class="tabMenu">
					<ul>
						{foreach from=$options item=categoryLevel1}
							<li id="{@$categoryLevel1.categoryName}"><a onclick="tabMenu.showSubTabMenu('{@$categoryLevel1.categoryName}');"><span>{lang}cp.domain.option.category.{@$categoryLevel1.categoryName}{/lang}</span></a></li>
						{/foreach}
						
						{if $additionalTabs|isset}{@$additionalTabs}{/if}
					</ul>
				</div>
				<div class="subTabMenu">
					<div class="containerHead"><div> </div></div>
				</div>
				
				{foreach from=$options item=categoryLevel1}
					<div class="border tabMenuContent hidden" id="{@$categoryLevel1.categoryName}-content">
						<div class="container-1">
							<h3 class="subHeadline">{lang}cp.domain.option.category.{@$categoryLevel1.categoryName}{/lang}</h3>
							
							{foreach from=$categoryLevel1.categories item=categoryLevel2}
								<fieldset>
									<legend>{lang}cp.domain.option.category.{@$categoryLevel2.categoryName}{/lang}</legend>
									
									{include file='optionFieldList' options=$categoryLevel2.options langPrefix='cp.domain.option.'}
								</fieldset>
							{/foreach}
						</div>
					</div>
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
  	</div>
</form>

{include file='footer'}