<fieldset>
	<legend>{lang}wbb.acp.importer.configure.db{/lang}</legend>
	
	{if $supportedDatabaseClasses|count > 0}
		<div class="formElement{if $errorField == 'db'} formError{/if}">
			<div class="formFieldLabel">
				<label for="dbClass">{lang}wbb.acp.importer.configure.db.class{/lang}</label>
			</div>
			<div class="formField">
				<select id="dbClass" name="settings[dbClass]">
					{htmlOptions options=$supportedDatabaseClasses selected=$settings.dbClass}
				</select>
			</div>
		</div>
	{/if}
	
	<div class="formElement{if $errorField == 'db'} formError{/if}">
		<div class="formFieldLabel">
			<label for="dbHost">{lang}wbb.acp.importer.configure.db.host{/lang}</label>
		</div>
		<div class="formField">
			<input type="text" class="inputText" id="dbHost" name="settings[dbHost]" value="{$settings.dbHost}" />
		</div>
	</div>
	
	<div class="formElement{if $errorField == 'db'} formError{/if}">
		<div class="formFieldLabel">
			<label for="dbUser">{lang}wbb.acp.importer.configure.db.user{/lang}</label>
		</div>
		<div class="formField">
			<input type="text" class="inputText" id="dbUser" name="settings[dbUser]" value="{$settings.dbUser}" />
		</div>
	</div>
	
	<div class="formElement{if $errorField == 'db'} formError{/if}">
		<div class="formFieldLabel">
			<label for="dbPassword">{lang}wbb.acp.importer.configure.db.password{/lang}</label>
		</div>
		<div class="formField">
			<input type="password" class="inputText" id="dbPassword" name="settings[dbPassword]" value="{$settings.dbPassword}" />
		</div>
	</div>
	
	<div class="formElement{if $errorField == 'db'} formError{/if}">
		<div class="formFieldLabel">
			<label for="dbName">{lang}wbb.acp.importer.configure.db.name{/lang}</label>
		</div>
		<div class="formField">
			<input type="text" class="inputText" id="dbName" name="settings[dbName]" value="{$settings.dbName}" />
			{if $errorField == 'db'}
				<p class="innerError">
					{lang}wbb.acp.importer.configure.db.error{/lang} {$errorType->getMessage()}
				</p>
			{/if}
		</div>
	</div>
	
	{if $additionalDBFields|isset}{@$additionalDBFields}{/if}
</fieldset>