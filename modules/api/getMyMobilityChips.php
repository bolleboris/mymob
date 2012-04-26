<?php
include_once 'MyMobility.inc';
if(isset($_REQUEST['userId'])) {
   $personid = $_REQUEST['userId'];
} else {
   echo json_encode(array('success' => 0));
   exit();
}
$BMUCore->ProviderSA(MYMOB_APP_ID)->Person($personid)->Attributes()->GetList();
$res = $BMUCore->sendRequest();
$atts = AttributeMapping::getValuesScoreSeperated($res['result']['attributes']);

$rows = array();
foreach($atts as $key => $value) {
   if(strstr($key,'Chips_UID')) {
	  $exp = explode('_',$key);
	  if($value != '') {
		 $rows[] = array('uid' => $value, 
						 'blocked' => $atts['Chips_Blocked'.$exp[1][3].'_'.$exp[2]],
						 'id' => $personid);
	  }
   }
}
$success = true;

$jsondata['success'] = $success;
$jsondata['results'] = count($rows);
$jsondata['rows'] = $rows ? $rows : array();
$jsondata['msg'] = $response['result']['message'];

echo json_encode($jsondata);
?>
