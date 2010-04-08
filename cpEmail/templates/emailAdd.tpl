{include file="documentHeader"}
<head>
	<title>{lang}cp.email.{@$action}Address{/lang} - {lang}wcf.user.usercp{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/AjaxRequest.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">

	{capture append=userMessages}
		{if $errorField}
			<p class="error">{lang}wcf.global.form.error{/lang}</p>
		{/if}
	{/capture}

	<form method="post" action="index.php?form=Email{@$action|ucfirst}">
		<div class="border tabMenuContent">
			<div class="container-1">
				<fieldset>
					<legend>{lang}cp.email.{@$action}Address{/lang}</legend>

					<div class="formElement{if $errorField == 'emailaddress'} formError{/if}">
						<div class="formFieldLabel">
							<label for="emailaddress">{lang}cp.email.emailaddress{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" name="emailaddress" value="{$emailaddress}" id="emailaddress" />

							{if $errorField == 'emailaddress'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									{if $errorType == 'notValid'}{lang}cp.email.emailaddress.notvalid{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>

					<div class="formElement{if $errorField == 'domainID'} formError{/if}">
						<div class="formFieldLabel">
							<label for="domain">{lang}cp.email.domain{/lang}</label>
						</div>
						<div class="formField">
							{htmlOptions options=$domains selected=$domainID id=domainID name=domainID}
							{if $errorField == 'domainID'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									{if $errorType == 'notValid'}{lang}cp.email.domain.notvalid{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>

					<div class="formElement{if $errorField == 'isCatchall'} formError{/if}">
						<div class="formFieldLabel">
							<label for="description">{lang}cp.email.isCatchall{/lang}</label>
						</div>
						<div class="formField">
							<input type="checkbox" name="isCatchall" value="1" {if $isCatchall}checked="checked" {/if}/>
							{if $errorField == 'isCatchall'}
								<p class="innerError">
									{if $errorType == 'notValid'}{lang}cp.email.isCatchall.notvalid{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>
				</fieldset>

				{if $additionalFields|isset}{@$additionalFields}{/if}
			</div>
		</div>

		<div class="formSubmit">
			{@SID_INPUT_TAG}
			{@SECURITY_TOKEN_INPUT_TAG}
			{if $ftpUserID|isset}<input type="hidden" name="ftpUserID" value="{@$ftpUserID}" />{/if}
			<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
			<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		</div>
	</form>

</div>

{include file='footer' sandbox=false}
</body>
</html>