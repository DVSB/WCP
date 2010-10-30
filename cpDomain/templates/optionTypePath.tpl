<input id="{$optionData.optionName}" type="{@$inputType}" class="inputText" name="values[{$optionData.optionName}]" value="{$optionData.optionValue}" />
<script type="text/javascript">
	//<![CDATA[
	suggestion.enableMultiple(false);
	suggestion.source = 'index.php?page=PathSuggest'+SID_ARG_2ND;
	suggestion.init('{$optionData.optionName}');
	//]]>
</script>