<?php
require 'MyMobility.inc';
extract($_POST);
$StartDT = $startDate.' '.$startTime;
$EndDT = $endDate.' '.$endTime;
$BMUCore->Wheels4All()->ProviderSA(100)->RequestBooking($PersonId, $contractId, $offerId, $serviceId, $ResourceId, $StartDT, $EndDT);

$res = $BMUCore->sendRequest();
$jsondata = array();
if($res['result']['result'] == 0) {
   $jsondata['success'] = true;
} else {
   $jsondata['success'] = false;
   $jsondata['message'] = $res['result']['message'];
}
echo json_encode($jsondata);
?>
