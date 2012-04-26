<?php

require('MyMobility.inc');


$rows = json_decode($_POST['rows'], true);

$jsondata = array();
foreach ($rows as $row) {
   var_dump($row);
   $BMUCore->ProviderUI(100)->Person($row['PersoonNr'])->Info();
   $res = $BMUCore->sendRequest();
   
   if ($res['result']['result'] == 0) {
	  $BMUCore->ProviderSA(100)->Contract($row['ContractNr'])->ConsumerRemove($res['result']['person_code']);
	  $res = $BMUCore->sendRequest();
	  if ($res['result']['result'] == 0) {
		 $jsondata = array('success' => true, 'error' => NULL);
	  } else {
		 return json_encode(array('success' => false, 'error' => $res['result']['message']));
	  }
   }
}

return json_encode($jsondata);
?>
