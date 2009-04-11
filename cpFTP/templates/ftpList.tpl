{include file="documentHeader"}
<head>
	<title>{lang}cp.index.title{/lang} - {PAGE_TITLE}</title>

	{include file='headInclude' sandbox=false}
</head>
<body>
{include file='header' sandbox=false}

<div id="main">

<div class="contentHeader">
	{pages print=true assign=pagesLinks link="index.php?page=FTPList&pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"|concat:SID_ARG_2ND_NOT_ENCODED}
</div>

{if $ftpAccounts|count}
	<div class="border">
		<div class="containerHead"><h3>{lang}cp.ftp.list{/lang}</h3></div>
	</div>
	<div class="border borderMarginRemove">
		<table class="tableList">
			<thead>
				<tr class="tableHead">
					<th class="columnFtpUserID{if $sortField == 'ftpUserID'} active{/if}"><div><a href="index.php?page=FTPList&amp;pageNo={@$pageNo}&amp;sortField=ftpUserID&amp;sortOrder={if $sortField == 'ftpUserID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.ftp.ftpUserID{/lang}{if $sortField == 'ftpUserID'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></div></th>
					<th class="columnUsername{if $sortField == 'username'} active{/if}"><div><a href="index.php?page=FTPList&amp;pageNo={@$pageNo}&amp;sortField=username&amp;sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.ftp.username{/lang}{if $sortField == 'username'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></div></th>
					<th class="columnHomedir{if $sortField == 'homedir'} active{/if}"><div><a href="index.php?page=FTPList&amp;pageNo={@$pageNo}&amp;sortField=homedir&amp;sortOrder={if $sortField == 'homedir' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.ftp.homedir{/lang}{if $sortField == 'homedir'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></div></th>
					<th class="columnLoginCount{if $sortField == 'loginCount'} active{/if}"><div><a href="index.php?page=FTPList&amp;pageNo={@$pageNo}&amp;sortField=loginCount&amp;sortOrder={if $sortField == 'loginCount' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.ftp.loginCount{/lang}{if $sortField == 'loginCount'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></div></th>
					<th class="columnLastLogin{if $sortField == 'lastLogin'} active{/if}"><div><a href="index.php?page=FTPList&amp;pageNo={@$pageNo}&amp;sortField=lastLogin&amp;sortOrder={if $sortField == 'lastLogin' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.ftp.lastLogin{/lang}{if $sortField == 'lastLogin'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></div></th>

					{if $additionalColumnHeads|isset}{@$additionalColumnHeads}{/if}
				</tr>
			</thead>
			<tbody>
			{foreach from=$ftpAccounts item=ftpAccount}
				<tr class="{cycle values="container-1,container-2"}">
					<td class="columnFtpUserID columnID">{@$ftpAccount->ftpUserID}</td>
					<td class="columnUsername columnText"><a href="index.php?form=FTPEdit&amp;ftpUserID={@$ftpAccount->ftpUserID}{@SID_ARG_2ND}">{$ftpAccount->username}</a></td>
					<td class="columnHomedir columnText">{$ftpAccount->homedir}</td>
					<td class="columnLoginCount columnText">{@$ftpAccount->loginCount}</td>
					<td class="columnLastLogin columnText">{@$ftpAccount->lastLogin|time}</td>

					{if $additionalColumns.$ftpAccount->ftpUserID|isset}{@$additionalColumns.$ftpAccount->ftpUserID}{/if}
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>

	<div class="contentFooter">
		{@$pagesLinks}
	</div>
{/if}
</div>

{include file='footer' sandbox=false}

</body>
</html>
