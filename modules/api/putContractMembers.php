<?php

require_once('MyMobility.inc');

extract($_POST);
$jsondata = array('success' => false,'error' => $res['result']['message']);
if ($xaction == 'create') {
   $rows = json_decode($rows, true);
   foreach ($rows as $row) {
	  $BMUCore->ProviderUI(100)->Person($row['PersoonNr'])->Info();
	  $res = $BMUCore->sendRequest();
	  if ($res['result']['result'] == 0) {
		 $BMUCore->ProviderSA(100)->Contract($row['ContractNr'])->ConsumerAssign($res['result']['person_code']);
		 $res = $BMUCore->sendRequest();
		 var_dump($res);
		 if($res['result']['result'] == 0) {
			$jsondata = array('success' => true,'error' => NULL);
		 } else {
			return json_encode(array('success' => false,'error' => $res['result']['message']));
		 }
	  }
   }
}

return json_encode($jsondata);
?>
