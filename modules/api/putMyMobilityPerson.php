<?php

//Saves the specified Person's attributes. This only does the work for fucking n
include_once 'MyMobility.inc';

if(isset($_POST['PersonId'])) {
   $personid = $_POST['PersonId'];
   unset($_POST['PersonId']);
} else {

}

$att = AttributeMapping::getAttributesScoreSeperated($_POST);


$BMUCore->ProviderSA(MYMOB_APP_ID)->Person($personid)->Attributes()->ListUpdate($att);
$res = $BMUCore->sendRequest();
if($res['result']['result'] == 0) {
   $jsondata['success'] = true;
   $jsondata['error'] = NULL;
} else {
   $jsondata['success'] = false;
   $jsondata['error'] = $res['result']['message'];
}

echo json_encode($jsondata);
?>
