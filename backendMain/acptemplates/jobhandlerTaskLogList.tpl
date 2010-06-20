{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/cronjobsLogL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}cp.acp.jobhandlerLog.list{/lang}</h2>
	</div>
</div>

<div class="contentHeader">
	{pages print=true assign=pagesLinks link="index.php?page=JobhandlerTaskLogList&pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"|concat:SID_ARG_2ND_NOT_ENCODED}
</div>

{if !$logs|empty}
	<div class="border titleBarPanel">
		<div class="containerHead"><h3>{lang}cp.acp.jobhandlerLog.listcount{/lang}</h3></div>
	</div>
	
	<div class="border borderMarginRemove">
		<table class="tableList">
			<thead>
				<tr class="tableHead">
					<th class="columnJobhandlerTaskLogID{if $sortField == 'jobhandlerTaskLogID'} active{/if}"><div><a href="index.php?page=JobhandlerTaskLogList&amp;pageNo={@$pageNo}&amp;sortField=jobhandlerTaskLogID&amp;sortOrder={if $sortField == 'jobhandlerTaskID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.acp.jobhandlerLog.jobhandlerTaskLogID{/lang}{if $sortField == 'jobhandlerTaskLogID'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnExecTimeStart{if $sortField == 'execTimeStart'} active{/if}"><div><a href="index.php?page=JobhandlerTaskLogList&amp;pageNo={@$pageNo}&amp;sortField=execTimeStart&amp;sortOrder={if $sortField == 'execTimeStart' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.acp.jobhandlerLog.execTimeStart{/lang}{if $sortField == 'execTimeStart'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnExecTimeEnd{if $sortField == 'execTimeEnd'} active{/if}"><div><a href="index.php?page=JobhandlerTaskLogList&amp;pageNo={@$pageNo}&amp;sortField=execTimeEnd&amp;sortOrder={if $sortField == 'execTimeEnd' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.acp.jobhandlerLog.execTimeEnd{/lang}{if $sortField == 'execTimeEnd'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnExecJobhandler{if $sortField == 'execJobhandler'} active{/if}"><div><a href="index.php?page=JobhandlerTaskLogList&amp;pageNo={@$pageNo}&amp;sortField=execJobhandler&amp;sortOrder={if $sortField == 'execJobhandler' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.acp.jobhandlerLog.execJobhandler{/lang}{if $sortField == 'execJobhandler'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnSuccess{if $sortField == 'success'} active{/if}"><div><a href="index.php?page=JobhandlerTaskLogList&amp;pageNo={@$pageNo}&amp;sortField=success&amp;sortOrder={if $sortField == 'success' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.acp.jobhandlerLog.success{/lang}{if $sortField == 'success'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnData{if $sortField == 'data'} active{/if}"><div>{lang}cp.acp.jobhandler.data{/lang}</div></th>
					
					{if $additionalColumns|isset}{@$additionalColumns}{/if}
				</tr>
			</thead>
			<tbody>
			{foreach from=$logs item=log}
				<tr class="{cycle values="container-1,container-2"}">
					<td class="columnJobhandlerTaskLogID"><a href="index.php?page=JobhandlerTaskLogDetail&amp;logID={@$log.jobhandlerTaskLogID}{@SID_ARG_2ND}">{@$log.jobhandlerTaskLogID}</a></td>
					<td class="columnExecTimeStart">{@$log.execTimeStart|shorttime}</td>
					<td class="columnExecTimeEnd">{@$log.execTimeEnd|shorttime}</td>
					<td class="columnExecJobhandler">{$log.execJobhandler|truncate:50:' ...'}</td>
					<td class="columnSuccess">
						{if $log.success == 1}
							{lang}cp.global.yes{/lang}
						{else}
							{lang}cp.global.no{/lang}
						{/if}
					</td>
					<td class="columnData">{$log.data|truncate:50:' ...'}</td>
	
					{if $log.additionalColumns|isset}{@$log.additionalColumns}{/if}
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
	
	{if $this->user->getPermission('admin.cp.canClearJobhandlerLog')}
		<form method="post" action="index.php?action=JobhandlerLogDelete">
			<div class="formSubmit">
				{@SID_INPUT_TAG}
				<input type="submit" accesskey="c" value="{lang}cp.acp.jobhandlerLog.clear{/lang}" onclick="return confirm('{lang}cp.acp.jobhandlerLog.clearConfirm{/lang}')" />
			</div>
		</form>
	{/if}
{/if}

{include file='footer'}