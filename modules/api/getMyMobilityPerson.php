<?php
include_once 'MyMobility.inc';
if(isset($_REQUEST['PersonId'])) {
   $personid = $_REQUEST['PersonId'];
} else {
   echo json_encode(array('success' => 0));
   exit();
}

$BMUCore->ProviderSA(MYMOB_APP_ID)->Person($personid)->Attributes()->GetList();
$res = $BMUCore->sendRequest();

if($res['result']['result'] == 0) {
   $row = AttributeMapping::getValuesScoreSeperated($res['result']['attributes']);
   $row['PersonId'] = $personid;
} else {
}

$rows[] = $row;
$success = true;

$jsondata['success'] = $success;
$jsondata['results'] = count($rows);
$jsondata['rows'] = $rows ? $rows : array();
$jsondata['msg'] = $response['result']['message'];

echo json_encode($jsondata);
?>