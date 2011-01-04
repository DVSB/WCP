{include file='header'}

<script type="text/javascript">
	//<![CDATA[
	onloadEvents.push(function() {
		{if !$data.users}disableOptions('avatars', 'userMergeMode', 'userOptions');{/if}
		{if !$data.boards}disableOptions('moderators', 'boardSubscriptions', 'boardPermissions');{else}disableOptions('boardID');{/if}
		{if !$data.threads}disableOptions('threadRatings', 'attachments', 'threadSubscriptions', 'polls');{/if}
		{if !$data.privateMessages}disableOptions('privateMessageFolders');{/if}
		{if !$data.calendars}disableOptions('calendarEvents');{/if}
		{if !$settings.convertPasswords}disableOptions('adminPasswords');{/if}
	});
	
	function toggleChecked(check, parent) {
		if (check) {
			enableOptions('avatars', 'userMergeMode', 'userOptions', 'moderators', 'boardSubscriptions', 'boardPermissions', 'threadRatings', 'attachments', 'threadSubscriptions', 'polls', 'privateMessageFolders', 'calendarEvents');
			{if $supportedData.boards}disableOptions('boardID');{/if}
		}
		else {
			disableOptions('avatars', 'userMergeMode', 'userOptions', 'moderators', 'boardSubscriptions', 'boardPermissions', 'threadRatings', 'attachments', 'threadSubscriptions', 'polls', 'privateMessageFolders', 'calendarEvents');
			enableOptions('boardID');
		}
		checkUncheckAll(parent);
	}
	//]]>
</script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WBB_DIR}icon/importerL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wbb.acp.importer{/lang}</h2>
		<p>{lang}wbb.acp.importer.{@$sourceName}{/lang}</p>
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

<p class="warning">{lang}wbb.acp.importer.info.updateCounters{/lang}</p>

<form method="post" action="index.php?form=Importer">
	<div class="border content">
		<div class="container-1">
			<fieldset>
				<legend>{lang}wbb.acp.importer.configure.data{/lang}</legend>
				
				<div class="formElement{if $errorField == 'data'} formError{/if}">
					<div class="formField">
						<label><input type="checkbox" name="selectAll" onclick="if (this.checked)  toggleChecked(true, document.getElementById('importDataDiv')); else toggleChecked(false, document.getElementById('importDataDiv'));" /> {lang}wbb.acp.importer.configure.data.selectAll{/lang}</label>
					</div>
					
					<div style="margin-top: 15px" id="importDataDiv">
						{if $supportedData.groups}
							<div class="formField">
								<label><input type="checkbox" name="data[groups]" value="1" {if $data.groups == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.data.groups{/lang}</label>
							</div>
						{/if}
						{if $supportedData.users}
							<div class="formField">
								<label><input type="checkbox" name="data[users]" value="1" {if $data.users == 1}checked="checked" {/if}onclick="if (this.checked) enableOptions('avatars', 'userMergeMode', 'userOptions'); else disableOptions('avatars', 'userMergeMode', 'userOptions');" /> {lang}wbb.acp.importer.configure.data.users{/lang}</label>
							</div>
							{if $supportedData.userOptions}
								<div class="formField" id="userOptionsDiv">
									<label><input type="checkbox" name="data[userOptions]" value="1" {if $data.userOptions == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.data.userOptions{/lang}</label>
								</div>
							{/if}
							{if $supportedData.avatars}
								<div class="formField" id="avatarsDiv">
									<label><input type="checkbox" name="data[avatars]" value="1" {if $data.avatars == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.data.avatars{/lang}</label>
								</div>
							{/if}
						{/if}
						{if $supportedData.boards}
							<div class="formField">
								<label><input type="checkbox" name="data[boards]" value="1" {if $data.boards == 1}checked="checked" {/if}onclick="if (this.checked) enableOptions('moderators', 'boardSubscriptions', 'boardPermissions') + disableOptions('boardID'); else disableOptions('moderators', 'boardSubscriptions', 'boardPermissions') + enableOptions('boardID');" /> {lang}wbb.acp.importer.configure.data.boards{/lang}</label>
							</div>
							{if $supportedData.moderators}
								<div class="formField" id="moderatorsDiv">
									<label><input type="checkbox" name="data[moderators]" value="1" {if $data.moderators == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.data.moderators{/lang}</label>
								</div>
							{/if}
							{if $supportedData.boardSubscriptions}
								<div class="formField" id="boardSubscriptionsDiv">
									<label><input type="checkbox" name="data[boardSubscriptions]" value="1" {if $data.boardSubscriptions == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.data.boardSubscriptions{/lang}</label>
								</div>
							{/if}
							{if $supportedData.boardPermissions}
								<div class="formField" id="boardPermissionsDiv">
									<label><input type="checkbox" name="data[boardPermissions]" value="1" {if $data.boardPermissions == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.data.boardPermissions{/lang}</label>
								</div>
							{/if}
						{/if}
						{if $supportedData.threads}
							<div class="formField">
								<label><input type="checkbox" name="data[threads]" value="1" {if $data.threads == 1}checked="checked" {/if}onclick="if (this.checked) enableOptions('threadRatings', 'attachments', 'threadSubscriptions', 'polls'); else disableOptions('threadRatings', 'attachments', 'threadSubscriptions', 'polls');" /> {lang}wbb.acp.importer.configure.data.threads{/lang}</label>
							</div>
							{if $supportedData.threadRatings}
								<div class="formField" id="threadRatingsDiv">
									<label><input type="checkbox" name="data[threadRatings]" value="1" {if $data.threadRatings == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.data.threadRatings{/lang}</label>
								</div>
							{/if}
							{if $supportedData.attachments}
								<div class="formField" id="attachmentsDiv">
									<label><input type="checkbox" name="data[attachments]" value="1" {if $data.attachments == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.data.attachments{/lang}</label>
								</div>
							{/if}
							{if $supportedData.threadSubscriptions}
								<div class="formField" id="threadSubscriptionsDiv">
									<label><input type="checkbox" name="data[threadSubscriptions]" value="1" {if $data.threadSubscriptions == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.data.threadSubscriptions{/lang}</label>
								</div>
							{/if}
							{if $supportedData.polls}
								<div class="formField" id="pollsDiv">
									<label><input type="checkbox" name="data[polls]" value="1" {if $data.polls == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.data.polls{/lang}</label>
								</div>
							{/if}
						{/if}
						{if $supportedData.privateMessages}
							<div class="formField">
								<label><input type="checkbox" name="data[privateMessages]" value="1" {if $data.privateMessages == 1}checked="checked" {/if}onclick="if (this.checked) enableOptions('privateMessageFolders'); else disableOptions('privateMessageFolders');" /> {lang}wbb.acp.importer.configure.data.privateMessages{/lang}</label>
							</div>
							{if $supportedData.privateMessageFolders}
								<div class="formField" id="privateMessageFoldersDiv">
									<label><input type="checkbox" name="data[privateMessageFolders]" value="1" {if $data.privateMessageFolders == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.data.privateMessageFolders{/lang}</label>
								</div>
							{/if}
						{/if}
						{if $supportedData.smilies}
							<div class="formField">
								<label><input type="checkbox" name="data[smilies]" value="1" {if $data.smilies == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.data.smilies{/lang}</label>
							</div>
						{/if}
						{if $supportedData.calendars}
							<div class="formField">
								<label><input type="checkbox" name="data[calendars]" value="1" {if $data.calendars == 1}checked="checked" {/if}onclick="if (this.checked) enableOptions('calendarEvents'); else disableOptions('calendarEvents');" /> {lang}wbb.acp.importer.configure.data.calendars{/lang}</label>
							</div>
						
							{if $supportedData.calendarEvents}
								<div class="formField" id="calendarEventsDiv">
									<label><input type="checkbox" name="data[calendarEvents]" value="1" {if $data.calendarEvents == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.data.calendarEvents{/lang}</label>
								</div>
							{/if}
						{/if}
						{if $errorField == 'data'}
							<p class="innerError">
								{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
				</div>
			</fieldset>
			
			<fieldset>
				<legend>{lang}wbb.acp.importer.configure.settings{/lang}</legend>
				
				<div class="formCheckBox formElement">
					<div class="formField">
						<label><input type="checkbox" name="useCLI" value="1" {if $useCLI == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.settings.useCLI{/lang}</label>
					</div>
					<div class="formFieldDesc">
						<p>{lang}wbb.acp.importer.configure.settings.useCLI.description{/lang}</p>
					</div>
				</div>
				
				<div class="formElement">
					<div class="formFieldLabel">
						<label for="encoding">{lang}wbb.acp.importer.configure.settings.encoding{/lang}</label>
					</div>
					<div class="formField">
						<select name="settings[encoding]" id="encoding">
							{htmlOptions output=$encodings selected=$settings.encoding}
						</select>
					</div>
				</div>
				
				{if $supportedData.users}
					<div class="formGroup" id="userMergeModeDiv">
						<div class="formGroupLabel">
							<label>{lang}wbb.acp.importer.configure.settings.userMergeMode{/lang}</label>
						</div>
						<div class="formGroupField">
							<fieldset>
								<legend>{lang}wbb.acp.importer.configure.settings.userMergeMode{/lang}</legend>
								
								<div class="formField">
									<label><input type="radio" name="settings[userMergeMode]" value="1" {if $settings.userMergeMode == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.settings.userMergeMode.1{/lang}</label>
									<label><input type="radio" name="settings[userMergeMode]" value="2" {if $settings.userMergeMode == 2}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.settings.userMergeMode.2{/lang}</label>
									<label><input type="radio" name="settings[userMergeMode]" value="3" {if $settings.userMergeMode == 3}checked="checked" {/if}/> {lang}wbb.acp.importer.configure.settings.userMergeMode.3{/lang}</label>
								</div>
							</fieldset>
						</div>
					</div>
				{/if}
				
				{if $supportedData.threads}
					<div class="formElement{if $errorField == 'boardID'} formError{/if}" id="boardIDDiv">
						<div class="formFieldLabel">
							<label for="boardID">{lang}wbb.acp.importer.configure.settings.boardID{/lang}</label>
						</div>
						<div class="formField">
							<select name="settings[boardID]" id="boardID">
								<option value=""></option>
								{htmlOptions options=$boards disableEncoding=true}
							</select>
							{if $errorField == 'boardID'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
					</div>
				{/if}
			</fieldset>
			
			{if $needsPasswordConversion}
				<fieldset>
					<legend>{lang}wbb.acp.importer.configure.passwords{/lang}</legend>
					
					<div class="formElement">
						<div class="formField">
							<label><input onclick="if (this.checked) enableOptions('adminPasswords'); else disableOptions('adminPasswords');" type="checkbox" name="settings[convertPasswords]" value="1" {if $settings.convertPasswords == 1}checked="checked" {/if}/> {lang}wbb.acp.importer.{@$sourceName}.configure.settings.convertPasswords{/lang}</label>
						</div>
						<p class="formFieldDesc">
							{lang}wbb.acp.importer.{@$sourceName}.configure.settings.convertPasswords.description{/lang}
						</p>
					</div>
					
					<div id="adminPasswordsDiv">
						<div class="formElement{if $errorField == 'adminPassword'} formError{/if}">
							<div class="formFieldLabel">
								<label for="adminPassword">{lang}wcf.user.password{/lang}</label>
							</div>
							<div class="formField">
								<input type="password" class="inputText" id="adminPassword" name="settings[adminPassword]" value="{$settings.adminPassword}" />
								{if $errorField == 'adminPassword'}
									<p class="innerError">
										{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
										{if $errorType == 'false'}{lang}wcf.user.error.password.false{/lang}{/if}
									</p>
								{/if}
							</div>
						</div>
						<div class="formElement{if $errorField == 'confirmAdminPassword'} formError{/if}">
							<div class="formFieldLabel">
								<label for="confirmAdminPassword">{lang}wcf.user.confirmPassword{/lang}</label>
							</div>
							<div class="formField">
								<input type="password" class="inputText" id="confirmAdminPassword" name="settings[confirmAdminPassword]" value="{$settings.confirmAdminPassword}" />
								{if $errorField == 'confirmAdminPassword'}
									<p class="innerError">
										{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
										{if $errorType == 'notEqual'}{lang}wcf.user.error.confirmPassword.notEqual{/lang}{/if}
									</p>
								{/if}
							</div>
						</div>
						
					</div>
				</fieldset>
			{/if}
			
			{if $sourceData.templateName}{include file=$sourceData.templateName}{/if}
			
			{if $additionalFields|isset}{@$additionalFields}{/if}
		</div>
	</div>
	
	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
		<input type="hidden" name="sourceName" value="{@$sourceName}" />
 		{@SID_INPUT_TAG}
 	</div>
</form>

{include file='footer'}