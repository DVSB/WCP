<script type="text/javascript">
	//<![CDATA[
	var {$optionData.optionName} = new Suggestion();
	//]]>
</script>
<input id="{$optionData.optionName}" type="{@$inputType}" class="inputText" name="values[{$optionData.optionName}]" value="{$optionData.optionValue}" />
<script type="text/javascript">
	//<![CDATA[
	{$optionData.optionName}.enableMultiple(false);
	{$optionData.optionName}.source = 'index.php?page=PathSuggest'+SID_ARG_2ND{if IS_ACP|defined}+'&amp;showFullPath=1'{/if};
	{$optionData.optionName}.init('{$optionData.optionName}');
	//]]>
</script>