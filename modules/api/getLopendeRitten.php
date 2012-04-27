<?php
//require_once("includes.inc.php");
require_once("MyMobility.inc");
require_once('resource_attributes.inc.php');
require_once('person_attributes.inc.php');
require_once('timeConvert.inc.php');

//if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");
$BMUCore->Wheels4All()->ProviderSA(MYMOB_APP_ID)->ActiveBookingList();
$response = $BMUCore->sendRequest();
if($response['result']['result'] != 0) sendErrorJSON($response['result']['message']);
$rows = array();
foreach($response['result']['bookings'] as $booking) {
	$resourceAtt = get_resource_attributes($booking['supplier_id'],$booking['resource_id']);
	$personAtt = get_person_attributes($booking['consumer_id']);
	$rows[] = array(
		"RitNr" => $booking['booking_id'],
		"AutoId" => $booking['resource_id'],
		"Bijnaam" => $resourceAtt['Bijnaam'],		
		"Kenteken"=> $resourceAtt['Kenteken'],
		"Plaats"=> $personAtt['Woonplaats'],
		"ReserveringBegin"=> W4ATijdShort($booking['start_dt']),		
		"ReserveringEind"=> W4ATijdShort($booking['end_dt']),		
		"PersoonNr"=> $personAtt['W4APersoonNr'],
		"UserName"=> utf8_encode($personAtt['Achternaam']).", ".utf8_encode($personAtt['Voornaam']),
		"Status"=>$booking['status'], 
		);
}

$jsondata['success'] = true;
$jsondata['results'] = count($rows);
$jsondata['rows'] = $rows;
$jsondata['error'] = NULL;
echo json_encode($jsondata);

?>
