{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/groupL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}cp.acp.domain.list{/lang}</h2>
	</div>
</div>

{if $deletedDomains}
	<p class="success">{lang}wcf.acp.domain.delete.success{/lang}</p>	
{/if}
{if $disabledDomains}
	<p class="success">{lang}wcf.acp.domain.delete.success{/lang}</p>	
{/if}

<div class="contentHeader">
	{pages print=true assign=pagesLinks link="index.php?page=DomainList&pageNo=%d&sortField=$sortField&sortOrder=$sortOrder&packageID="|concat:PACKAGE_ID:SID_ARG_2ND_NOT_ENCODED}
	<div class="largeButtons">
		<ul>
			<li><a href="index.php?form=DomainAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.acp.group.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/groupAddM.png" alt="" /> <span>{lang}wcf.acp.domain.add{/lang}</span></a></li>
			{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}			
		</ul>
	</div>
</div>

{if $domains|count}
	<div class="border titleBarPanel">
		<div class="containerHead"><h3>{lang}wcf.acp.domains.list.data{/lang}</h3></div>
	</div>
	<div class="border borderMarginRemove">
		<table class="tableList">
			<thead>
				<tr class="tableHead">
					<th class="columnDomainID{if $sortField == 'domainID'} active{/if}" colspan="2"><div><a href="index.php?page=DomainList&amp;pageNo={@$pageNo}&amp;sortField=domainID&amp;sortOrder={if $sortField == 'domainID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}wcf.acp.domain.domainID{/lang}{if $sortField == 'domainID'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnDomainname{if $sortField == 'domainname'} active{/if}"><div><a href="index.php?page=DomainList&amp;pageNo={@$pageNo}&amp;sortField=domainname&amp;sortOrder={if $sortField == 'domainname' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}wcf.acp.domain.domainname{/lang}{if $sortField == 'domainname'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnUsernames{if $sortField == 'username'} active{/if}"><div><a href="index.php?page=DomainList&amp;pageNo={@$pageNo}&amp;sortField=username&amp;sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}wcf.acp.domain.username{/lang}{if $sortField == 'username'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnAdmins{if $sortField == 'adminname'} active{/if}"><div><a href="index.php?page=DomainList&amp;pageNo={@$pageNo}&amp;sortField=adminname&amp;sortOrder={if $sortField == 'adminname' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}wcf.acp.domain.adminname{/lang}{if $sortField == 'adminname'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnRegistrationDate{if $sortField == 'registrationDate'} active{/if}"><div><a href="index.php?page=DomainList&amp;pageNo={@$pageNo}&amp;sortField=registrationDate&amp;sortOrder={if $sortField == 'registrationDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}wcf.acp.domain.registrationDate{/lang}{if $sortField == 'registrationDate'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnAddDate{if $sortField == 'addDate'} active{/if}"><div><a href="index.php?page=DomainList&amp;pageNo={@$pageNo}&amp;sortField=addDate&amp;sortOrder={if $sortField == 'addDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@SID_ARG_2ND}">{lang}wcf.acp.domain.addDate{/lang}{if $sortField == 'addDate'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					
					{if $additionalColumns|isset}{@$additionalColumns}{/if}
				</tr>
			</thead>
			<tbody>
			{foreach from=$domains item=domain}
				<tr class="{cycle values="container-1,container-2"}">
					<td class="columnIcon">
						{if $domain->canEditDomain}
							<a href="index.php?form=DomainEdit&amp;domainID={$domain->domainID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}wcf.acp.domain.edit{/lang}" /></a>
						{else}
							<img src="{@RELATIVE_WCF_DIR}icon/editDisabledS.png" alt="" title="{lang}wcf.acp.domain.edit{/lang}" />
						{/if}
						{if $domain->canEditDomain}
							<a onclick="return confirm('{lang}wcf.acp.domain.delete.sure{/lang}')" href="index.php?action=DomainDelete&amp;domainID={@$domain->domainID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" title="{lang}wcf.acp.domain.delete{/lang}" /></a>
						{else}
							<img src="{@RELATIVE_WCF_DIR}icon/deleteDisabledS.png" alt="" title="{lang}wcf.acp.domain.delete{/lang}" />
						{/if}
						
						{if $additionalButtons[$domain->domainID]|isset}{@$additionalButtons[$domain->domainID]}{/if}
					</td>
					<td class="columndomainID columnID">{@$domain->domainID}</td>
					<td class="columnGroupName columnText">{if $domain->canEditDomain}<a title="{lang}wcf.acp.group.edit{/lang}" href="index.php?form=DomainEdit&amp;domainID={@$domain->domainID}{@SID_ARG_2ND}">{$domain->domainname}</a>{else}{$domain->domainname}{/if}</td>
					<td class="columnMembers columnNumbers"><a title="{lang}wcf.acp.group.showMembers{/lang}" href="index.php?form=UserEdit&amp;userID={@$domain->userID}{@SID_ARG_2ND}">{$domain->username}</a></td>
					<td class="columnMembers columnNumbers">{$domain->adminname}</td>
					<td class="columnMembers columnNumbers">{@$domain->registrationDate|date}</td>
					<td class="columnMembers columnNumbers">{@$domain->addDate|date}</td>
					
					{if $additionalColumns[$domain->domainID]|isset}{@$additionalColumns[$domain->domainID]}{/if}
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
{/if}

<div class="contentFooter">
	{@$pagesLinks}
	<div class="largeButtons">
		<ul>
			<li><a href="index.php?form=DomainAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.acp.group.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/groupAddM.png" alt="" /> <span>{lang}wcf.acp.domain.add{/lang}</span></a></li>
			{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
		</ul>
	</div>
</div>

{include file='footer'}
