{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/cronjobsLogL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}cp.acp.jobhandlerLog.detail{/lang}</h2>
	</div>
</div>

<div class="contentHeader">
	<div class="largeButtons">
		<ul>
			<li><a href="index.php?page=JobhandlerTaskLogList{@SID_ARG_2ND}" title="{lang}cp.acp.jobhandlerLog.list{/lang}"><span>{lang}cp.acp.jobhandlerLog.list{/lang}</span></a></li>
			{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}			
		</ul>
	</div>
</div>

<fieldset>
	<legend>{lang}cp.acp.jobhandlerLog.detail{/lang}</legend>
	<div class="formElement">
		<p class="formFieldLabel">{lang}cp.acp.jobhandlerLog.jobhandlerTaskLogID{/lang}</p>
		<p class="formField">{$log.jobhandlerTaskLogID}</p>
	</div>
	<div class="formElement">
		<p class="formFieldLabel">{lang}cp.acp.jobhandlerLog.success{/lang}</p>
		<p class="formField">
				{if $log.success == 1}
					{lang}cp.global.yes{/lang}
				{else}
					{lang}cp.global.no{/lang}
				{/if}
		</p>
	</div>
	<div class="formElement">
		<p class="formFieldLabel">{lang}cp.acp.jobhandlerLog.execTimeStart{/lang}</p>
		<p class="formField">{$log.execTimeStart|fulldate}</p>
	</div>
	<div class="formElement">
		<p class="formFieldLabel">{lang}cp.acp.jobhandlerLog.execTimeEnd{/lang}</p>
		<p class="formField">{$log.execTimeEnd|fulldate}</p>
	</div>
	<div class="formElement">
		<p class="formFieldLabel">{lang}cp.acp.jobhandlerLog.execJobhandler{/lang}</p>
		<p class="formField">{@$log.execJobhandler|nl2br}</p>
	</div>
	<div class="formElement">
		<p class="formFieldLabel">{lang}cp.acp.jobhandlerLog.data{/lang}</p>
		<p class="formField">{@$log.data|nl2br}</p>
	</div>
	
	{if $additionalFields|isset}{@$additionalFields}{/if}
</fieldset>
	
{include file='footer'}