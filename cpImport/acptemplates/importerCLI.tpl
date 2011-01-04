{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WBB_DIR}icon/importerL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wbb.acp.importer{/lang}</h2>
		<p>{lang}wbb.acp.importer.{@$sourceName}{/lang}</p>
	</div>
</div>

<div class="border content">
	<div class="container-1">
		{lang}wbb.acp.importer.cli{/lang}
		<pre>php {@WBB_DIR}acp/import.php {@SID}</pre>
	</div>
</div>

{include file='footer'}