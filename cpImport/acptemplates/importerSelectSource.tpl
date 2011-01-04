{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WBB_DIR}icon/importerL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wbb.acp.importer{/lang}</h2>
	</div>
</div>

<form method="get" action="index.php">
	<div class="border content">
		<div class="container-1">
			<fieldset>
				<legend>{lang}wbb.acp.importer.selectSource{/lang}</legend>
				
				<div class="formGroup" id="userMergeModeDiv">
					<div class="formGroupLabel">
						<label>{lang}wbb.acp.importer.selectSource.select{/lang}</label>
					</div>
					<div class="formGroupField">
						<fieldset>
							<legend>{lang}wbb.acp.importer.selectSource.select{/lang}</legend>
							
							<div class="formField">
								{foreach from=$availableSources item=$sourceName}
									<label><input type="radio" name="sourceName" value="{@$sourceName}" /> {lang}wbb.acp.importer.{@$sourceName}{/lang}</label>
								{/foreach}
							</div>
						</fieldset>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	
	<div class="formSubmit">
		<input type="submit" accesskey="s" name="submitButton" value="{lang}wcf.global.button.next{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
		<input type="hidden" name="form" value="Importer" />
 		{@SID_INPUT_TAG}
 	</div>
</form>

{include file='footer'}