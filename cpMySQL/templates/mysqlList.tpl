{include file="documentHeader"}
<head>
	<title>{lang}cp.mysql.list{/lang} - {PAGE_TITLE}</title>

	{include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">

	<ul class="breadCrumbs">
		<li><a href="index.php{@SID_ARG_1ST}"><img alt="" src="{icon}wcpS.png{/icon}"> <span>{lang}cp.header.menu.start{/lang}</span></a> &raquo;</li>
	</ul>

	<div class="mainHeadline">
		<img src="{icon}databaseL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}cp.mysql.list{/lang}</h2>
		</div>
	</div>

	<div class="contentHeader">
		{pages print=true assign=pagesLinks link="index.php?page=MySQLList&pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"|concat:SID_ARG_2ND_NOT_ENCODED}
		{if $this->user->mysqls > $this->user->mysqlsUsed && $this->user->getPermission('cp.mysql.canAddMySQL')}
		<div class="largeButtons">
			<ul>
				<li><a href="index.php?form=MySQLAdd{@SID_ARG_2ND}">
				<img title="{lang}cp.mysql.add{/lang}" alt=""
					src="{icon}databaseAddM.png{/icon}" /> <span>{lang}cp.mysql.add{/lang}</span> </a></li>
			</ul>
		</div>
		{/if}
	</div>

	{if $mysqls|count}
	<div class="subTabMenu">
		<div class="containerHead"><h3>{lang}cp.mysql.list{/lang}</h3></div>
	</div>
	<div class="border tabMenuContent">
		<table class="tableList">
			<thead>
				<tr class="tableHead">
					<th class="columnMySQLID">{lang}cp.mysql.mysqlID{/lang}</th>
					<th class="columnMySQLname{if $sortField == 'mysqlname'} active{/if}"><div><a href="index.php?page=MySQLList&amp;pageNo={@$pageNo}&amp;sortField=mysqlname&amp;sortOrder={if $sortField == 'mysqlname' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.mysql.mysqlname{/lang}{if $sortField == 'mysqlname'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></div></th>

					{if $additionalColumnHeads|isset}{@$additionalColumnHeads}{/if}
				</tr>
			</thead>
			<tbody>
			{foreach from=$mysqls item=mysql}
				<tr class="{cycle values="container-1,container-2"}">
					<td class="columnMySQLID columnID">{if $this->user->getPermission('cp.mysql.canDeleteMySQL')}<a href="index.php?action=MySQLDelete&amp;mysqlID={@$mysql->mysqlID}{@SID_ARG_2ND}"><img src="{icon}deleteS.png{/icon}" alt="" title="{lang}cp.mysql.delete{/lang}" /></a>{else}<img src="{icon}deleteDisabledS.png{/icon}" alt="" title="{lang}cp.mysql.delete{/lang}" />{/if}</td>
					<td class="columnMySQLname columnText"><a href="index.php?form=MySQLEdit&amp;mysqlID={@$mysql->mysqlID}{@SID_ARG_2ND}">{$mysql->mysqlname}</a></td>

					{if $additionalColumns.$mysql->mysqlID|isset}{@$additionalColumns.$mysql->mysqlID}{/if}
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