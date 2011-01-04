<?php
/**
 * @author	Marcel Werk
 * @copyright	2001-2008 WoltLab GmbH
 * @license	WoltLab Burning Board License <http://www.woltlab.com/products/burning_board/license.php>
 */
// prevent time out
@set_time_limit(0);

// prevent redirect to login form
$_REQUEST['form'] = 'Login';
$_SERVER['REQUEST_METHOD'] = 'GET';

// prevent session validation error
define('SESSION_VALIDATE_USER_AGENT', false);
define('SESSION_VALIDATE_IP_ADDRESS', 0);

// set dir
$dir = dirname(__FILE__);
chdir($dir);

// require options.inc.php
try {
	@require_once('./../options.inc.php');
}
catch (Exception $e) {}

// include wcf/wbb acp
try {
	require_once('./global.php');
	
	// include classes
	require_once(WBB_DIR.'lib/acp/action/CLIImporterAction.class.php');
	
	// start import
	new CLIImporterAction();
}
catch (NamedUserException $e) {
	echo "\r".$e->getMessage()."\n";
}
catch (SystemException $e) {
	echo "\r".'Fatal error: '.$e->getMessage()."\n";
	if ($e instanceof DatabaseException) {
		echo $e->getErrorDesc()." (".$e->getErrorNumber().")\n";
	}
	echo $e->getTraceAsString()."\n";
}
?>