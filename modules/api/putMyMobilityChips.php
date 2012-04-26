<?php

include_once 'MyMobility.inc';
//var_dump($_REQUEST);
define('MAX_AMOUNT_OF_CHIPS', 8);

$personid = $_REQUEST['userId'];
var_dump($_REQUEST);
$rows = json_decode($_REQUEST['rows'], true);
$verified = true;
$atts = array();
for ($i = 0; $i < MAX_AMOUNT_OF_CHIPS; $i++) {
   if (isset($rows[$i])) {
	  $personid = $rows[$i]['id'];
	  $atts[] = array('group' => 'Chips',
		  'key' => 'UID' . $i,
		  'access' => 'public',
		  'value' => $rows[$i]['uid']);
	  $atts[] = array('group' => 'Chips',
		  'key' => 'Blocked' . $i,
		  'access' => 'public',
		  'value' => $rows[$i]['blocked']);
	  if ($rows[$i]['blocked'] != 0 && $rows[$i]['blocked'] != 1) {
		 $verified = false;
	  }
   } else {

	  $atts[] = array('group' => 'Chips',
		  'key' => 'UID' . $i,
		  'access' => 'public',
		  'value' => '');
	  $atts[] = array('group' => 'Chips',
		  'key' => 'Blocked' . $i,
		  'access' => 'public',
		  'value' => '');
   }
}
$jsondata = array();
if ($verified) {
   var_dump($atts);
   $BMUCore->ProviderSA(MYMOB_APP_ID)->Person($personid)->Attributes()->ListUpdate($atts);
   //$res = $BMUCore->sendRequest();
   $jsondata['success'] = true;
   $jsondata['error'] = NULL;
} else {
   $jsondata['success'] = false;
   $jsondata['error'] = 'Waarde van Geblokkeerd veldje moet 0 (actief) of 1 (geblokkeerd) zijn';
}

echo json_encode($jsondata);
?>
