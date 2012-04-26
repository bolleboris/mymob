<?php
//require_once('includes.inc.php');

require_once('MyMobility.inc');

$BMUCore->Wheels4All()->ProviderSA(MYMOB_APP_ID)->GetCars(100);
$resourcesResponse = $BMUCore->sendRequest();

//var_dump($resourceAttributeMappings);

echo json_encode($resourcesResponse['result']['jsondata']);
?>
