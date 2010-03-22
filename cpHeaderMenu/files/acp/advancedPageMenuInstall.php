<?php
require_once(WCF_DIR.'lib/data/style/StyleEditor.class.php');
// default variables
$pageMenuVariables = array(
								'menu.advancedPageMenu.padding' => 1,
								'menu.advancedPageMenu.padding.unit' => 'px',
								'menu.advancedPageMenu.item.padding' => 3,
								'menu.advancedPageMenu.item.padding.unit' => 'px',
								'menu.advancedPageMenu.shadow.color' => '#888888',
								'menu.advancedPageMenu.shadow.size' => 2,
								'menu.advancedPageMenu.shadow.size.unit' => 'px',
								'menu.advancedPageMenu.shadow.offset' => 3,
								'menu.advancedPageMenu.shadow.offset.unit' => 'px'
								
			);
$sql = "SELECT styleID FROM wcf".WCF_N."_style";
$result = WCF::getDB()->sendQuery($sql);
$inserts = '';
$styleIDs = array();
while ($row = WCF::getDB()->fetchArray($result)) {	
	// insert default variables for each style
	foreach($pageMenuVariables as $name => $value) {
		if (!empty($inserts)) $inserts .= ','; 
		$inserts .= '('.$row['styleID'].', \''.escapeString($name).'\', \''.escapeString($value).'\')';
	}
	
	$styleIDs[] = $row['styleID'];	
}

if (!empty($inserts)) {
	$sql = "INSERT INTO wcf".WCF_N."_style_variable
				(styleID, variableName, variableValue)
				VALUES
				".$inserts."
			ON DUPLICATE KEY UPDATE
				variableValue = VALUES(variableValue)";
	WCF::getDB()->sendQuery($sql);
}

foreach ($styleIDs as $styleID) {
	// reset styles	
	$editor = new StyleEditor($styleID);
	$editor->writeStyleFile();	
}
?>