{include file="documentHeader"}
<head>
	<title>{lang}cp.email.addAccount{/lang} - {lang}wcf.user.usercp{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
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

	<form method="post" action="index.php?form=EmailSetAccountPW">
		<div class="border tabMenuContent">
			<div class="container-1">
				<fieldset>
					<legend><label for="password">{lang}cp.email.addAccount{/lang} {$emailaddress_full}</label></legend>

					<div class="formElement{if $errorField == 'password'} formError{/if}">
						<div class="formFieldLabel">
							<label for="password">{lang}cp.email.account.password{/lang}</label>
						</div>
						<div class="formField">
							<input type="password" class="inputText" name="password" value="" id="password" />

							{if $errorField == 'password'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>
					
					<div class="formElement{if $errorField == 'passwordcheck'} formError{/if}">
						<div class="formFieldLabel">
							<label for="password">{lang}cp.email.account.passwordcheck{/lang}</label>
						</div>
						<div class="formField">
							<input type="password" class="inputText" name="passwordcheck" value="" id="passwordcheck" />

							{if $errorField == 'password'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									{if $errorType == 'noMatch'}{lang}cp.email.account.pwnomatch{/lang}{/if}
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
			<input type="hidden" name="mailID" value="{@$mailID}" />
			<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
			<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		</div>
	</form>

</div>

{include file='footer' sandbox=false}
</body>
</html>