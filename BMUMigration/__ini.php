<?php
include_once('lib/BMUCore.php');
include_once('lib/BMUMigration.php');

$BMUCore = BMUCore::b();
//$BMUCore->addToArray('application_code','');
//$BMUCore->addToArray('application_key','');
$BMUCore->addToArray('access_token','wesfsgsdhcnnsthbvnethdgnedthtndf');

$BMUCore->Application()->Connect('');
echo $BMUCore->__toString();
$BMUCore->sendRequest();

function __autoload($className) {
	loadClass($className);
}
	
function loadClass($className) {
	//str_replace("\\","/",$className);
	include_once($className.'.php');
}
?>
