{include file="documentHeader"}
<head>
	<title>{lang}cp.index.title{/lang} - {PAGE_TITLE}</title>

	{include file='headInclude' sandbox=false}
</head>
<body>
{include file='header' sandbox=false}

<div id="main">

<div class="contentHeader">
	{pages print=true assign=pagesLinks link="index.php?page=MySQLList&pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"|concat:SID_ARG_2ND_NOT_ENCODED}
</div>
{if $this->user->mysqls > $this->user->mysqlsUsed}
	<div class="contentHeader">
		<div class="largeButtons">
			<ul>
				<li><a href="index.php?form=MySQLAdd{@SID_ARG_2ND}">
				<img title="{lang}cp.mysql.add{/lang}" alt=""
					src="{iconmysqlAddM.png{/icon}" /> <span>{lang}cp.mysql.add{/lang}</span> </a></li>
			</ul>
		</div>
	</div>
{/if}
{if $mysqls|count}
	<div class="border">
		<div class="containerHead"><h3>{lang}cp.mysql.list{/lang}</h3></div>
	</div>
	<div class="border borderMarginRemove">
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
					<td class="columnMySQLID columnID"><a href="index.php?action=MySQLDelete&amp;mysqlID={@$mysql->mysqlID}{@SID_ARG_2ND}"><img src="{icon}deleteS.png{/icon}" alt="" title="{lang}cp.mysql.delete{/lang}" /></a></td>
					<td class="columnMySQLname columnText"><a href="index.php?form=FTPEdit&amp;mysqlID={@$mysql->mysqlID}{@SID_ARG_2ND}">{$mysql->mysqlname}</a></td>

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
