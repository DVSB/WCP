{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/pageMenuItemL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.pageMenuItem.import{/lang}</h2>
	</div>
</div>

{if $success|isset}
	<p class="success">{lang}wcf.acp.pageMenuItem.import.success{/lang}</p>	
{/if}

{if $errorField != ''}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

<form method="post" action="index.php?form=AdvancedPageMenuImport" enctype="multipart/form-data">
	<div class="border content">
		<div class="container-1">
			<fieldset>
				<legend>{lang}wcf.acp.pageMenuItem.import{/lang}</legend>
			
				<div class="formElement{if $errorField == 'file'} formError{/if}" id="fileDiv">
					<div class="formFieldLabel">
						<label for="file">{lang}wcf.acp.pageMenuItem.import.upload{/lang}</label>
					</div>
					<div class="formField">
						<input type="file" id="file" name="file" value="" />
						{if $errorField == 'file'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								{if $errorType == 'importFailed'}{lang}wcf.acp.pageMenuItem.import.error.importFailed{/lang}{/if}
								{if $errorType == 'uploadFailed'}{lang}wcf.acp.pageMenuItem.import.error.uploadFailed{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="fileHelpMessage">
						{lang}wcf.acp.pageMenuItem.import.upload.description{/lang}
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('file');
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