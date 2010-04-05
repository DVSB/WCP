{include file="documentHeader"}
<head>
	<title>{lang}cp.email.{@$action}Address{/lang} - {lang}wcf.user.usercp{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	{include file='headInclude' sandbox=false}
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/AjaxRequest.class.js"></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
{include file='header' sandbox=false}

<div id="main">

	<div class="border tabMenuContent">
		<div class="container-1">
			<fieldset>
				<legend>{lang}cp.email.{@$action}Address{/lang}</legend>

				<div class="formElement">
					<div class="formFieldLabel">
						<label for="emailaddress">{lang}cp.email.emailaddress{/lang}</label>
					</div>
					<div class="formField">
						{$emailaddress_full}
					</div>
				</div>

				<div class="formElement">
					<div class="formFieldLabel">
						<label for="domain">{lang}cp.email.account{/lang}</label>
					</div>
					<div class="formField">
						<a href="index.php?form=EmailSetAccountPW&amp;mailID={@$mailID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}">{lang}cp.email.account.{if $accountID}pwchange{else}addaccount{/if}{/lang}</a> {if $accountID}<a href="index.php?action=EmailDeleteAccount&amp;mailID={@$mailID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}">{lang}cp.email.account.delete{/lang}</a>{/if}
					</div>
				</div>

				<div class="formElement">
					<div class="formFieldLabel">
						<label for="description">{lang}cp.email.isCatchall{/lang}</label>
					</div>
					<div class="formField">
						{if $isCatchall}{lang}cp.global.yes{/lang}{else}{lang}cp.global.no{/lang}{/if} <a href="index.php?action=EmailToggleCatchall&amp;mailID={@$mailID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}">{lang}cp.email.isCatchall.{if $isCatchall}deactivate{else}activate{/if}{/lang}</a>
					</div>
				</div>
				
				<div class="formElement">
					<div class="formFieldLabel">
						<label for="domain">{lang}cp.email.forwards{/lang}</label>
					</div>
					<div class="formField">
						<table width="100%">
						{if $destination|is_array}
						{foreach from=$destination item=d}
							{if $d != $emailaddress_full}
							<tr>
								<td>{$d}</td>
								<td><a href="index.php?action=EmailDeleteForward&amp;mailID={@$mailID}&amp;forward={$d}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}">{lang}cp.email.deleteforward{/lang}</a></td>
							</tr>
							{/if}
						{/foreach}
						{/if}
						</table>
						{if $this->user->emailForwards > $this->user->emailForwardsUsed}<a href="index.php?form=EmailAddForward&amp;mailID={@$mailID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}">{lang}cp.email.addforward{/lang}</a>{/if}
					</div>
				</div>
			</fieldset>

			{if $additionalFields|isset}{@$additionalFields}{/if}
		</div>
	</div>

</div>

{include file='footer' sandbox=false}
</body>
</html>