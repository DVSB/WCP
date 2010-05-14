{include file="documentHeader"}
<head>
	<title>{lang}cp.email.addForward{/lang} - {lang}wcf.user.usercp{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/AjaxRequest.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">

	<ul class="breadCrumbs">
		<li><a href="index.php{@SID_ARG_1ST}"><img alt="" src="{icon}wcpS.png{/icon}"> <span>{lang}cp.header.menu.start{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=EmailList{@SID_ARG_2ND}"><img alt="" src="{icon}emailS.png{/icon}"> <span>{lang}cp.header.menu.email{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=EmailDetail&amp;mailID={@$mailID}{@SID_ARG_2ND}"><img alt="" src="{icon}emailS.png{/icon}"> <span>{lang}cp.email.details{/lang}</span></a> &raquo;</li>
	</ul>	
	
	<div class="mainHeadline">
		<img src="{icon}emailL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}cp.email.addForward{/lang}</h2>
		</div>
	</div>

	{capture append=userMessages}
		{if $errorField}
			<p class="error">{lang}wcf.global.form.error{/lang}</p>
		{/if}
	{/capture}

	<form method="post" action="index.php?form=EmailAddForward">
		<div class="border tabMenuContent">
			<div class="container-1">
				<fieldset>
					<legend><label for="password">{lang}cp.email.addForward{/lang} {$emailaddress_full}</label></legend>

					<div class="formElement{if $errorField == 'password'} formError{/if}">
						<div class="formFieldLabel">
							<label for="password">{lang}cp.email.forward{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" name="forward" value="" id="forward" />

							{if $errorField == 'forward'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									{if $errorType == 'notValid'}{lang}cp.email.forward.notvalid{/lang}{/if}
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