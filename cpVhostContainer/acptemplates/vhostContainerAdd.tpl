{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/vhostContainer{@$action|ucfirst}L.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}cp.acp.vhostContainer.{@$action}{/lang}</h2>
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{lang}cp.acp.vhostContainer.{@$action}.success{/lang}</p>	
{/if}

<div class="contentHeader">
	<div class="largeButtons">
		<ul>
			<li><a href="index.php?page=vhostContainerList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}cp.acp.menu.link.vhostContainers.list{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/groupM.png" alt="" /> <span>{lang}cp.acp.menu.link.vhostContainers.list{/lang}</span></a></li>
			{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
		</ul>
	</div>
</div>
<form method="post" action="index.php?form=vhostContainer{@$action|ucfirst}">
	<div class="border content">
		<div class="container-1">
			<fieldset id="data">
				<legend>{lang}cp.acp.vhostContainer.data{/lang}</legend>
				
				<div class="formElement{if $errorType.vhostName|isset} formError{/if}" id="vhostNameDiv">
					<div class="formFieldLabel">
						<label for="vhostName">{lang}cp.acp.vhostContainer.vhostName{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="vhostName" name="vhostName" value="{$vhostName}" />
						{if $errorType.vhostName|isset}
							<p class="innerError">
								{if $errorType.vhostName == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="vhostNameHelpMessage">
						<p>{lang}cp.acp.vhostContainer.vhostName.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('vhostName');
				//]]></script>
				
				<div class="formElement{if $errorType.ipAddress|isset} formError{/if}" id="ipAddressDiv">
					<div class="formFieldLabel">
						<label for="ipAddress">{lang}cp.acp.vhostContainer.ipAddress{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="ipAddress" name="ipAddress" value="{$ipAddress}" />
						{if $errorType.ipAddress|isset}
							<p class="innerError">
								{if $errorType.ipAddress == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								{if $errorType.ipAddress == 'notValid'}{lang}cp.acp.vhostContainer.ipAddress.notValid{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="ipAddressHelpMessage">
						<p>{lang}cp.acp.vhostContainer.ipAddress.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('ipAddress');
				//]]></script>
				
				<div class="formElement{if $errorType.port|isset} formError{/if}" id="portDiv">
					<div class="formFieldLabel">
						<label for="port">{lang}cp.acp.vhostContainer.port{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="port" name="port" value="{$port}" />
						{if $errorType.port|isset}
							<p class="innerError">
								{if $errorType.port == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								{if $errorType.port == 'notValid'}{lang}cp.acp.vhostContainer.port.notValid{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="portHelpMessage">
						<p>{lang}cp.acp.vhostContainer.port.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('port');
				//]]></script>
				
				<div class="formElement{if $errorType.vhostType|isset} formError{/if}" id="vhostTypeDiv">
					<div class="formFieldLabel">
						<label for="vhostType">{lang}cp.acp.vhostContainer.vhostType{/lang}</label>
					</div>
					<div class="formField">
						{htmlOptions options=$vhostTypes selected=$vhostType id=vhostType name=vhostType}
						{if $errorType.vhostType|isset}
							<p class="innerError">
								{if $errorType.vhostType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								{if $errorType.vhostType == 'notValid'}{lang}cp.acp.vhostContainer.vhostType.notValid{/lang}{/if}
							</p>
						{/if}
					</div>
					<div class="formFieldDesc hidden" id="vhostTypeHelpMessage">
						<p>{lang}cp.acp.vhostContainer.vhostType.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('vhostType');
				//]]></script>
				
				{if $additionalFields|isset}{@$additionalFields}{/if}
			<fieldset>
				
			<fieldset id="settings">
				<legend>{lang}cp.acp.vhostContainer.settings{/lang}</legend>
				
				<div class="formElement">
					<div class="formField">
						<label id="isContainer"><input type="checkbox" name="isContainer" value="1" {if $isContainer}checked="checked" {/if}/> {lang}cp.acp.vhostContainer.isContainer{/lang}</label>
					</div>
				</div>
				
				<div class="formElement">
					<div class="formField">
						<label id="isIPv6"><input type="checkbox" name="isIPv6" value="1" {if $isIPv6}checked="checked" {/if}/> {lang}cp.acp.vhostContainer.isIPv6{/lang}</label>
					</div>
				</div>
				
				<div class="formElement">
					<div class="formField">
						<label id="addListenStatement"><input type="checkbox" name="addListenStatement" value="1" {if $addListenStatement}checked="checked" {/if}/> {lang}cp.acp.vhostContainer.addListenStatement{/lang}</label>
					</div>
				</div>
				
				<div class="formElement">
					<div class="formField">
						<label id="addNameStatement"><input type="checkbox" name="addNameStatement" value="1" {if $addNameStatement}checked="checked" {/if}/> {lang}cp.acp.vhostContainer.addNameStatement{/lang}</label>
					</div>
				</div>
				
				<div class="formElement">
					<div class="formField">
						<label id="addServerName"><input type="checkbox" name="addServerName" value="1" {if $addServerName}checked="checked" {/if}/> {lang}cp.acp.vhostContainer.addServerName{/lang}</label>
					</div>
				</div>
				
				{if $additionalSettings|isset}{@$additionalSettings}{/if}
			</fieldset>
				
			<fieldset id="vhostTemplate">
				<legend>{lang}cp.acp.vhostContainer.template{/lang}</legend>
				
				<div class="formElement" id="overwriteTemplateDiv">
					<div class="formElement">
						<div class="formField">
							<label id="overwriteTemplate"><input type="checkbox" name="overwriteTemplate" value="1" {if $overwriteTemplate}checked="checked" {/if}/> {lang}cp.acp.vhostContainer.overwriteTemplate{/lang}</label>
						</div>
					</div>
					<div class="formFieldDesc hidden" id="overwriteTemplateHelpMessage">
						<p>{lang}cp.acp.vhostContainer.overwriteTemplate.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('overwriteTemplate');
				//]]></script>
				
				<div class="formElement" id="vhostTemplateDiv">
					<div class="formFieldLabel">
						<label for="vhostTemplate">{lang}cp.acp.vhostContainer.vhostTemplate{/lang}</label>
					</div>
					<div class="formField">
						<textarea id="vhostTemplate" rows="15" cols="40" name="vhostTemplate">{$vhostTemplate}</textarea>
					</div>
					<div class="formFieldDesc hidden" id="vhostTemplateHelpMessage">
						<p>{lang}cp.acp.vhostContainer.vhostTemplate.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('vhostTemplate');
				//]]></script>
			</fieldset>
			
			<fieldset id="ssl">
				<legend>{lang}cp.acp.vhostContainer.ssl{/lang}</legend>
				
				<div class="formElement" id="isSSLDiv">
					<div class="formElement">
						<div class="formField">
							<label id="isSSL"><input type="checkbox" name="isSSL" value="1" {if $isSSL}checked="checked" {/if}/> {lang}cp.acp.vhostContainer.isSSL{/lang}</label>
						</div>
					</div>	
				</div>
				
				<div class="formElement" id="sslCertFileDiv">
					<div class="formFieldLabel">
						<label for="sslCertFile">{lang}cp.acp.vhostContainer.sslCertFile{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="sslCertFile" name="sslCertFile" value="{$sslCertFile}" />
					</div>
					<div class="formFieldDesc hidden" id="sslCertFileHelpMessage">
						<p>{lang}cp.acp.vhostContainer.sslCertFile.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('sslCertFile');
				//]]></script>
				
				<div class="formElement" id="sslCertKeyFileDiv">
					<div class="formFieldLabel">
						<label for="sslCertFile">{lang}cp.acp.vhostContainer.sslCertKeyFile{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="sslCertKeyFile" name="sslCertKeyFile" value="{$sslCertKeyFile}" />
					</div>
					<div class="formFieldDesc hidden" id="sslCertKeyFileHelpMessage">
						<p>{lang}cp.acp.vhostContainer.sslCertKeyFile.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('sslCertKeyFile');
				//]]></script>
				
				<div class="formElement" id="sslCertChainFileDiv">
					<div class="formFieldLabel">
						<label for="sslCertChainFile">{lang}cp.acp.vhostContainer.sslCertChainFile{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="sslCertChainFile" name="sslCertChainFile" value="{$sslCertChainFile}" />
					</div>
					<div class="formFieldDesc hidden" id="sslCertChainFileHelpMessage">
						<p>{lang}cp.acp.vhostContainer.sslCertChainFile.description{/lang}</p>
					</div>
				</div>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('sslCertChainFile');
				//]]></script>
			</fieldset>
			
			<fieldset id="vhostComments">
				<legend>{lang}cp.acp.vhostContainer.vhostComments{/lang}</legend>
				
				<div class="formElement">
					<div class="formFieldLabel">
						<label for="vhostComments">{lang}cp.acp.vhostContainer.vhostComments{/lang}</label>
					</div>
					<div class="formField">
						<textarea id="vhostComments" rows="15" cols="40" name="vhostComments">{$vhostComments}</textarea>
					</div>
				</div>
			</fieldset>
		
			{if $additionalFieldSets|isset}{@$additionalFieldSets}{/if}

		</div>
	</div>
	
	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
 		{@SID_INPUT_TAG}
 		<input type="hidden" name="action" value="{@$action}" />
 		{if $vhostContainerID|isset}<input type="hidden" name="vhostContainerID" value="{@$vhostContainerID}" />{/if}
  	</div>
</form>

{include file='footer'}