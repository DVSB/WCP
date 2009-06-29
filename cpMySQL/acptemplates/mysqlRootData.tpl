{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/loginL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}cp.acp.dbrootdata{/lang}</h2>
	</div>
</div>


{if $errorField != ''}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

<form method="post" action="index.php?form=MySQLRootData">
	<div class="border content">
		<div class="container-1">
			<fieldset>
				<legend>{lang}cp.acp.mysql.userdata{/lang}</legend>

				<div class="formElement{if $errorField == 'rootUser'} formError{/if}" id="rootUserDiv">
					<div class="formFieldLabel">
						<label for="rootUser">{lang}cp.acp.rootUser{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="rootUser" name="rootUser" value="{$rootUser}" />
						{if $errorField == 'rootUser'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="rootUserHelpMessage">
						{lang}cp.acp.rootUser.description{/lang}
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('rootUser');
				//]]></script>

				<div class="formElement{if $errorField == 'rootPassword'} formError{/if}" id="rootPasswordDiv">
					<div class="formFieldLabel">
						<label for="rootPassword">{lang}cp.acp.rootPassword{/lang}</label>
					</div>
					<div class="formField">
						<input type="password" class="inputText" id="rootPassword" name="rootPassword" value="{$rootPassword}" />
						{if $errorField == 'rootPassword'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="rootPasswordHelpMessage">
						{lang}cp.acp.rootPassword.description{/lang}
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('rootPassword');
				//]]></script>
			</fieldset>

			{if $additionalFields|isset}{@$additionalFields}{/if}
		</div>
	</div>

	<div class="formSubmit">
		<input type="submit" accesskey="s" name="submitButton" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 	</div>
</form>

{include file='footer'}