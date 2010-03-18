{include file="documentHeader"}
<head>
	<title>{lang}cp.ftp.{@$action}Account{/lang} - {lang}wcf.user.usercp{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
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

	<form method="post" action="index.php?form=FTP{@$action|ucfirst}">
		<div class="border tabMenuContent">
			<div class="container-1">
				<fieldset>
					<legend><label for="password">{lang}cp.ftp.createAccount{/lang}</label></legend>

					<div class="formElement{if $errorField == 'password'} formError{/if}">
						<div class="formFieldLabel">
							<label for="password">{lang}cp.ftp.password{/lang}</label>
						</div>
						<div class="formField">
							<input type="password" class="inputText" name="password" value="{$password}" id="password" />

							{if $errorField == 'password'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>

					<div class="formElement{if $errorField == 'path'} formError{/if}">
						<div class="formFieldLabel">
							<label for="path">{lang}cp.ftp.path{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" name="path" value="{$path}" id="path" />
							<script type="text/javascript">
								//<![CDATA[
								suggestion.enableMultiple(false);
								suggestion.source = 'index.php?page=PathSuggest'+SID_ARG_2ND;
								suggestion.init('path');
								//]]>
							</script>
							{if $errorField == 'path'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									{if $errorType == 'invalid'}{lang}cp.ftp.invalidPath{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>

					<div class="formElement">
						<div class="formFieldLabel">
							<label for="description">{lang}cp.ftp.description{/lang}</label>
						</div>
						<div class="formField">
							<textarea id="description" rows="15" cols="40" name="description">{$description}</textarea>
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