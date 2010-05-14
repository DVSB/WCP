{include file="documentHeader"}
<head>
	<title>{lang}cp.mysql.{@$action}{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">

	<ul class="breadCrumbs">
		<li><a href="index.php{@SID_ARG_1ST}"><img alt="" src="{icon}wcpS.png{/icon}"> <span>{lang}cp.header.menu.start{/lang}</span></a> &raquo;</li>
		<li><a href="index.php?page=MySQLList{@SID_ARG_2ND}"><img alt="" src="{icon}databaseS.png{/icon}"> <span>{lang}cp.header.menu.mysql{/lang}</span></a> &raquo;</li>
	</ul>	
	
	<div class="mainHeadline">
		<img src="{icon}mysqlL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}cp.mysql.{@$action}{/lang}</h2>
		</div>
	</div>
	
	{capture append=userMessages}
		{if $errorField}
			<p class="error">{lang}wcf.global.form.error{/lang}</p>
		{/if}
	{/capture}

	<form method="post" action="index.php?form=MySQL{@$action|ucfirst}">
		<div class="border tabMenuContent">
			<div class="container-1">
				<fieldset>
					<legend><label for="password">{lang}cp.mysql.{@$action}{/lang}</label></legend>

					<div class="formElement{if $errorField == 'password'} formError{/if}">
						<div class="formFieldLabel">
							<label for="dbpassword">{lang}cp.mysql.password{/lang}</label>
						</div>
						<div class="formField">
							<input type="password" class="inputText" name="dbpassword" value="{$password}" id="dbpassword" />

							{if $errorField == 'password'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>

					<div class="formElement">
						<div class="formFieldLabel">
							<label for="description">{lang}cp.mysql.description{/lang}</label>
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
			{if $mysqlID|isset}<input type="hidden" name="mysqlID" value="{@$mysqlID}" />{/if}
			<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
			<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		</div>
	</form>

</div>

{include file='footer' sandbox=false}
</body>
</html>