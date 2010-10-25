{include file="documentHeader"}
<head>
	<title>{lang}cp.email.list{/lang} - {PAGE_TITLE}</title>

	{include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">

	<ul class="breadCrumbs">
		<li><a href="index.php{@SID_ARG_1ST}"><img alt="" src="{icon}wcpS.png{/icon}"> <span>{lang}cp.header.menu.start{/lang}</span></a> &raquo;</li>
	</ul>

	<div class="mainHeadline">
		<img src="{icon}emailL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}cp.email.list{/lang}</h2>
		</div>
	</div>

	<div class="contentHeader">
		{pages print=true assign=pagesLinks link="index.php?page=EmailList&pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"|concat:SID_ARG_2ND_NOT_ENCODED}
		{if $this->user->emailAddresses > $this->user->emailAddressesUsed && $this->user->getPermission('cp.email.canAddAddress')}
		<div class="largeButtons">
			<ul>
				<li><a href="index.php?form=EmailAdd{@SID_ARG_2ND}">
				<img title="{lang}cp.email.addAddress{/lang}" alt=""
					src="{icon}emailAddM.png{/icon}" /> <span>{lang}cp.email.addAddress{/lang}</span> </a></li>
			</ul>
		</div>
		{/if}
	</div>
	
	{if $emails|count}
	<div class="subTabMenu">
		<div class="containerHead"><h3>{lang}cp.email.list{/lang}</h3></div>
	</div>
	<div class="border tabMenuContent">
		<table class="tableList">
			<thead>
				<tr class="tableHead">
					<th class="columnEmailID"><div>{lang}cp.email.mailID{/lang}</div></th>
					<th class="columnEmailaddress{if $sortField == 'emailaddress'} active{/if}"><div><a href="index.php?page=EmailList&amp;pageNo={@$pageNo}&amp;sortField=emailaddress&amp;sortOrder={if $sortField == 'emailaddress' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.email.emailaddress{/lang}{if $sortField == 'emailaddress'} <img src="{icon}sort{@$sortOrder}S.png{/icon}" alt="" />{/if}</a></div></th>
					<th class="columnForwards"><div>{lang}cp.email.forwards{/lang}</div></th>
					<th class="columnAccount"><div>{lang}cp.email.account{/lang}</div></th>
					<th class="columnIsCatchall"><div>{lang}cp.email.isCatchall{/lang}</div></th>

					{if $additionalColumnHeads|isset}{@$additionalColumnHeads}{/if}
				</tr>
			</thead>
			<tbody>
			{foreach from=$emails item=email}
				{assign var=forwardCount value=$email->destination|count}
				<tr class="{cycle values="container-1,container-2"}">
					<td class="columnEmailID columnID">{if $this->user->getPermission('cp.email.canDeleteAddresses')}<a href="index.php?action=EmailDelete&amp;mailID={$email->mailID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" onclick="return confirm(LANG_DELETE_CONFIRM);"><img src="{icon}deleteS.png{/icon}" alt="" title="{lang}cp.email.deleteEmailaddress{/lang}" /></a>{else}<img src="{icon}deleteDisabledS.png{/icon}" alt="" title="{lang}cp.email.deleteEmailaddress{/lang}" />{/if}</td>
					<td class="columnEmailaddress columnText"><a href="index.php?page=EmailDetail&amp;mailID={$email->mailID}{@SID_ARG_2ND}">{$email->emailaddress_full}</a></td>
					<td class="columnForwards columnText">{if $forwardCount > 0 && $email->accountID}{$forwardCount - 1}{else}{$forwardCount}{/if} {if $this->user->emailForwards > $this->user->emailForwardsUsed && $this->user->getPermission('cp.email.canAddForward')}<a href="index.php?form=EmailAddForward&amp;mailID={$email->mailID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}">{lang}cp.email.addForward{/lang}</a>{/if}</td>
					<td class="columnAccount columnText">{if $this->user->getPermission('cp.email.canAddAccount')}<a href="index.php?form=EmailSetAccountPW&amp;mailID={$email->mailID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}">{lang}cp.email.account.{if $email->accountID}pwchange{else}addAccount{/if}{/lang}</a>{/if} {if $email->accountID && $this->user->getPermission('cp.email.canDeleteAccounts')}<a href="index.php?action=EmailDeleteAccount&amp;mailID={$email->mailID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}">{lang}cp.email.account.delete{/lang}</a>{/if}</td>
					<td class="columnIsCatchall columnText">{if $email->isCatchall}{lang}cp.global.yes{/lang}{else}{lang}cp.global.no{/lang}{/if} {if $this->user->getPermission('cp.email.canUseCatchall') && (!$domainsWithCatchall[$email->domainID]|isset || $email->isCatchall)}<a href="index.php?action=EmailToggleCatchall&amp;mailID={$email->mailID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}">{lang}cp.email.isCatchall.{if $email->isCatchall}deactivate{else}activate{/if}{/lang}</a>{/if}</td>

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