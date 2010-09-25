{include file="documentHeader"}
<head>
	<title>{lang}cp.domain.list{/lang} - {PAGE_TITLE}</title>

	{include file='headInclude' sandbox=false}
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">

	<ul class="breadCrumbs">
		<li><a href="index.php{@SID_ARG_1ST}"><img alt="" src="{icon}wcpS.png{/icon}"> <span>{lang}cp.header.menu.start{/lang}</span></a> &raquo;</li>
	</ul>	

	<div class="mainHeadline">
		<img src="{icon}domainL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}cp.domain.list{/lang}</h2>
		</div>
	</div>

	{if $deletedDomains}
		<p class="success">{lang}cp.domain.delete.success{/lang}</p>	
	{/if}
	{if $disabledDomains}
		<p class="success">{lang}cp.domain.disabled.success{/lang}</p>	
	{/if}
	
	<div class="contentHeader">
		{pages print=true assign=pagesLinks link="index.php?page=DomainList&pageNo=%d&sortField=$sortField&sortOrder=$sortOrder&packageID="|concat:PACKAGE_ID:SID_ARG_2ND_NOT_ENCODED}
		{if $this->user->subdomains > $this->user->subdomainsUsed && $this->user->getPermission('cp.domain.canAddSubDomain')}
		<div class="largeButtons">
			<ul>
				<li><a href="index.php?form=SubDomainAdd{@SID_ARG_2ND}" title="{lang}cp.domain.subDomainAdd{/lang}"><img src="{icon}domainAddM.png{/icon}" alt="" /> <span>{lang}cp.domain.subDomainAdd{/lang}</span></a></li>
				{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}			
			</ul>
		</div>
		{/if}
	</div>
	
	{if $domains|count}
	<div class="border">
		<div class="containerHead"><h3>{lang}cp.domain.list{/lang}</h3></div>
	</div>
	<div class="border borderMarginRemove">
		<table class="tableList">
			<thead>
				<tr class="tableHead">
					<th class="columnDomainID{if $sortField == 'domainID'} active{/if}"><div><a href="index.php?page=DomainList&amp;pageNo={@$pageNo}&amp;sortField=domainID&amp;sortOrder={if $sortField == 'domainID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.domain.domainID{/lang}{if $sortField == 'domainID'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnDomainname{if $sortField == 'domainname'} active{/if}"><div><a href="index.php?page=DomainList&amp;pageNo={@$pageNo}&amp;sortField=domainname&amp;sortOrder={if $sortField == 'domainname' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.domain.domainname{/lang}{if $sortField == 'domainname'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnParentDomainID{if $sortField == 'parentDomainID'} active{/if}"><div><a href="index.php?page=DomainList&amp;pageNo={@$pageNo}&amp;sortField=parentDomainID&amp;sortOrder={if $sortField == 'parentDomainID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.domain.parentDomainName{/lang}{if $sortField == 'parentDomainID'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnRegistrationDate{if $sortField == 'registrationDate'} active{/if}"><div><a href="index.php?page=DomainList&amp;pageNo={@$pageNo}&amp;sortField=registrationDate&amp;sortOrder={if $sortField == 'registrationDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.domain.registrationDate{/lang}{if $sortField == 'registrationDate'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnAddDate{if $sortField == 'addDate'} active{/if}"><div><a href="index.php?page=DomainList&amp;pageNo={@$pageNo}&amp;sortField=addDate&amp;sortOrder={if $sortField == 'addDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}cp.domain.addDate{/lang}{if $sortField == 'addDate'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					
					{if $additionalColumns|isset}{@$additionalColumns}{/if}
				</tr>
			</thead>
			<tbody>
			{foreach from=$domains item=domain}
				<tr class="{cycle values="container-1,container-2"}">
					<td class="columnDomainID columnID">{if $domain->parentDomainID != 0 && $this->user->getPermission('cp.domain.canDeleteSubDomains')}<a href="index.php?action=SubDomainDelete&amp;domainID={@$domain->domainID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}" onclick="return confirm(LANG_DELETE_CONFIRM);"><img src="{icon}deleteS.png{/icon}" alt="" title="{lang}cp.domain.deleteSubDomain{/lang}" /></a>{else}<img src="{icon}deleteDisabledS.png{/icon}" alt="" title="{lang}cp.domain.deleteSubDomain{/lang}" />{/if}{if $domain->deactivated == '1'}<a href="index.php?action=DomainEnable&amp;domainID={@$domain->domainID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}"><img src="{icon}disabledS.png{/icon}" alt="" title="{lang}cp.domain.enableDomain{/lang}" /></a>{else}<a href="index.php?action=DomainDisable&amp;domainID={@$domain->domainID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}"><img src="{icon}enabledS.png{/icon}" alt="" title="{lang}cp.domain.disableDomain{/lang}" /></a>{/if}</td>
					<td class="columnDomainname columnText">{if $this->user->getPermission('cp.domain.canEditDomain') && $domain->parentDomainID == 0 || $this->user->getPermission('cp.domain.canEditSubDomain') && $domain->parentDomainID != 0}<a title="{lang}wcf.acp.group.edit{/lang}" href="index.php?form={if $domain->parentDomainID != 0}Sub{/if}DomainEdit&amp;domainID={@$domain->domainID}{@SID_ARG_2ND}">{$domain->domainname}</a>{else}{$domain->domainname}{/if}</td>
					<td class="columnParentDomain columnText">{$domain->parentDomainName}</td>
					<td class="columnRegistrationDateDomain columnText">{if $domain->registrationDate != 0}{@$domain->registrationDate|date}{/if}</td>
					<td class="columnAddDate columnText">{@$domain->addDate|date}</td>
					
					{if $additionalColumns.$domain->domainID|isset}{@$additionalColumns.$domain->domainID}{/if}
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

{include file='footer'}
