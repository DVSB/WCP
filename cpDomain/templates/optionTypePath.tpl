<script type="text/javascript">
	//<![CDATA[
	var {$optionData.optionName} = new Suggestion();
	//]]>
</script>
<input id="{$optionData.optionName}" type="{@$inputType}" class="inputText" name="values[{$optionData.optionName}]" value="{$optionData.optionValue}" />
<script type="text/javascript">
	//<![CDATA[
	{$optionData.optionName}.enableMultiple(false);
	{$optionData.optionName}.source = 'index.php?page=PathSuggest'+SID_ARG_2ND;
	{$optionData.optionName}.init('{$optionData.optionName}');
	// add event listeners
	var element = document.getElementById('{$optionData.optionName}'); 
	element.onkeyup = function(e) { return {$optionData.optionName}.handleInput(e); };
	element.onkeydown = function(e) { return {$optionData.optionName}.handleBeforeInput(e); };
	element.onclick = function(e) { return {$optionData.optionName}.handleClick(e); };
	element.onfocus = function(e) { return {$optionData.optionName}handleClick(e); };
	element.onblur = function(e) { return {$optionData.optionName}.closeList(); } 
	//]]>
</script>