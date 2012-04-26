<?php

require_once('MyMobility.inc');

$BMUCore->Wheels4All()->ProviderSA(MYMOB_APP_ID)->SearchUsers($_POST['query']);
$rs = $BMUCore->sendRequest();

if($rs['result']['result'] == 0){
	$jsondata = $rs['result']['jsondata'];
} else {
	$jsondata['success'] = false;
	$jsondata['results'] = 0;
	$jsondata['rows'] = $rows ? $rows : array();
	$jsondata['msg'] = $rs['result']['message'];
}


echo json_encode($jsondata);

?>
