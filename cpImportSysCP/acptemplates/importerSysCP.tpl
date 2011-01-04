{capture assign=additionalDBFields}
	<div class="formElement{if $errorField == 'dbNumber'} formError{/if}">
		<div class="formFieldLabel">
			<label for="dbNumber">{lang}wbb.acp.importer.wbb2x.configure.db.number{/lang}</label>
		</div>
		<div class="formField">
			<input type="text" class="inputText" id="dbNumber" name="settings[dbNumber]" value="{$settings.dbNumber}" />
			{if $errorField == 'dbNumber'}
				<p class="innerError">
					{if $errorType == 'invalid'}{lang}wbb.acp.importer.wbb2x.configure.db.number.error.invalid{/lang}{/if}
				</p>
			{/if}
		</div>
	</div>
{/capture}
{include file=importerConfigDB}

<fieldset>
	<legend>{lang}wbb.acp.importer.wbb2x.configure.source{/lang}</legend>
	
	<div class="formElement{if $errorField == 'sourcePath'} formError{/if}">
		<div class="formFieldLabel">
			<label for="sourcePath">{lang}wbb.acp.importer.wbb2x.configure.source.path{/lang}</label>
		</div>
		<div class="formField">
			<input type="text" class="inputText" id="sourcePath" name="settings[sourcePath]" value="{$settings.sourcePath}" />
			{if $errorField == 'sourcePath'}
				<p class="innerError">
					{if $errorType == 'invalid'}{lang}wbb.acp.importer.wbb2x.configure.source.path.error.invalid{/lang}{/if}
				</p>
			{/if}
		</div>
	</div>
</fieldset>