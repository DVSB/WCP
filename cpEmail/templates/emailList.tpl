{include file="documentHeader"}
<head>
	<title>{lang}cp.index.title{/lang} - {PAGE_TITLE}</title>

	{include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">

	<div class="mainHeadline">
		<img src="{icon}ftpL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}cp.acp.email.list{/lang}</h2>
		</div>
	</div>

	<div class="contentHeader">
		{pages print=true assign=pagesLinks link="index.php?page=EmailList&pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"|concat:SID_ARG_2ND_NOT_ENCODED}
		{if $this->user->emailAddresses > $this->user->emailAddressesUsed}
		<div class="largeButtons">
			<ul>
				<li><a href="index.php?form=EmailAdd{@SID_ARG_2ND}">
				<img title="{lang}cp.email.addAccount{/lang}" alt=""
					src="{icon}emailAddressesAddM.png{/icon}" /> <span>{lang}cp.email.addAccount{/lang}</span> </a></li>
			</ul>
		</div>
		{/if}
	</div>
	
	{if $emails|count}
	<div class="border">
		<div class="containerHead"><h3>{lang}cp.email.list{/lang}</h3></div>
	</div>
	<div class="border borderMarginRemove">
		<table class="tableList">
			<thead>
				<tr class="tableHead">
					<th class="columnEmailID"><div>{lang}cp.email.mailID{/lang}</div></th>
					<th class="columnEmailaddress{if $sortField == 'emailaddress'} active{/if}"><div><a href="index.php?page=EmailList&amp;pageNo={@$pageNo}&amp;sortField=emailaddress&amp;sortOrder={if $sortField == 'emailaddress' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.email.emailaddress{/lang}{if $sortField == 'emailaddress'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></div></th>
					<th class="columnDestination"><div>{lang}cp.email.destination{/lang}</div></th>
					<th class="columnAccount"><div>{lang}cp.email.account{/lang}</div></th>
					<th class="columnIsCatchall"><div>{lang}cp.email.isCatchall{/lang}</div></th>

					{if $additionalColumnHeads|isset}{@$additionalColumnHeads}{/if}
				</tr>
			</thead>
			<tbody>
			{foreach from=$emails item=email}
				<tr class="{cycle values="container-1,container-2"}">
					<td class="columnEmailID columnID"><a href="index.php?action=EmailDelete&amp;mailID={@$email->mailID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" onclick="return confirm(LANG_DELETE_CONFIRM);"><img src="{icon}deleteS.png{/icon}" alt="" title="{lang}cp.ftp.deleteAccount{/lang}" /></a></td>
					<td class="columnEmailaddress columnText"><a href="index.php?form=EmailEdit&amp;mailID={@$email->mailID}{@SID_ARG_2ND}">{$email->emailaddress}</a></td>
					<td class="columnDestination columnText">{$email->destination}</td>
					<td class="columnAccount columnText"></td>
					<td class="columnIsCatchall columnText">{if $email->isCatchall}{lang}cp.global.yes{/lang}{else}{lang}cp.global.no{/lang}{/if}</td>

					{if $additionalColumns.$email->mailID|isset}{@$additionalColumns.$email->mailID}{/if}
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