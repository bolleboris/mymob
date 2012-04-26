<?php

require_once('MyMobility.inc');
require_once('timeConvert.inc.php');

if(isset($_POST['AutoId'])){
  $AutoId = $_POST['AutoId'];
} else {
  sendErrorJSON("FOUT: Geen AutoId gedefinieerd!");
}
//$BMUCore->Supplier(19)->Resource($AutoId)->Calendar()->GetList(date('U',time())-10000,date('U',time()));
$BMUCore->Supplier(19)->Resource($AutoId)->Calendar()->GetList(BMUTime('01-01-2012'),BMUTime('01-01-2013'));
$response = $BMUCore->sendRequest();

$success = false;

if(is_numeric($response['result'])){
	if($response['result'] == 0){
		$success = true;
	}
} else {
	if($response['result']['result'] == 0) {
		$success = true;
		foreach($response['result']['calendar'] as $calendar) {	
			if($calendar['booking_id']){	//Toon alleen boekingen
				$BMUCore->ProviderSA(MYMOB_APP_ID)->Booking($calendar['booking_id'])->info();
				$booking = $BMUCore->sendRequest();
				//print_r($booking);
				$rows[] = array(
					"RitNr" => $calendar['booking_id'],
					"PersoonNr" => $booking['result']['consumer_id'],
					"Persoon" => $booking['result']['consumer_code'],
					"ReserveringBegin" => W4ATijdShort($booking['result']['start_dt']),
					"ReserveringEind" => W4ATijdShort($booking['result']['end_dt']),
					"Status" => $booking['result']['status']
				);
			}
		}
	}
}


$jsondata['success'] = $success;
$jsondata['results'] = count($rows);
$jsondata['rows'] = $rows ? $rows : array();
$jsondata['msg'] = $response['result']['message'];

echo json_encode($jsondata);
exit();

?>
