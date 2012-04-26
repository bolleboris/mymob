<?php
require_once('database.inc');
require_once('xlslib.inc.php');

$data = fopen('https://www.wheels4all.nl/backoffice/modules/api/getCarsFirmware.php','r');
if($data === false) die("Could not read data");

JSONtoXls(fgets($data),"autobezetting");

?>
