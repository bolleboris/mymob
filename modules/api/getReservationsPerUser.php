<?php

require_once('includes.inc.php');
require_once('MyMobility.inc');
require_once('resource_attributes.inc.php');

if(isset($_POST['userId'])){
  $userId = $_POST['userId'];
} else {
  sendErrorJSON("FOUT: Geen userId gedefinieerd!");
}

$BMUCore->Wheels4All()->ProviderSA(MYMOB_APP_ID)->ConsumerBookingList($userId);
$rs = $BMUCore->sendRequest();

//print_r($rs);

foreach($rs['result']['bookings'] as $booking){
	if($booking['resource_id']){
		$car_attributes = get_resource_attributes($booking['resource_id']);
	}
	
	$rows[] = array(
		"RitNr" => $booking['booking_id'],
		"AutoId" => $booking['resource_id'],
		"Bijnaam" =>  $car_attributes['Bijnaam'],
		"ReserveringBegin" => W4ATijd($booking['start_dt']),
		"ReserveringEind" => W4ATijd($booking['end_dt']),
		"Status" => $booking['status']
	);
}

$EmptyRows[] = array(
		"RitNr" => null,
		"AutoId" => null,
		"Bijnaam" => null,
		"ReserveringBegin" => null,
		"ReserveringEind" => null,
		"Status" => null
);

$jsondata['success'] = true;
$jsondata['results'] = count($rows);
$jsondata['rows'] = $rows ? $rows : array();
echo json_encode($jsondata);
		
exit();

/*


{name: 'booking_id',		type: 'int'},
		{name: 'provider_id',		type: 'int'},
		{name: 'provider_code',		type: 'string'},		
		{name: 'customer_id',		type: 'int'},
		{name: 'customer_code',		type: 'string'},		
		{name: 'consumer_id',		type: 'int'},
		{name: 'consumer_code',		type: 'string'},		
		{name: 'supplier_id',		type: 'int'},
		{name: 'supplier_code',		type: 'string'},
		{name: 'contract_id',		type: 'int'},
		{name: 'contract_type_id',	type: 'int'},
		{name: 'contract_type_code',type: 'string'},
		{name: 'service_id',		type: 'int'},
		{name: 'service_code',		type: 'string'},
		{name: 'offer_id',			type: 'int'},				
		{name: 'offer_type_id',		type: 'int'},
		{name: 'offer_type_code',	type: 'string'},
		{name: 'resource_type',		type: 'string'},
		//Geen resource_type_code??
		{name: 'resource_id',		type: 'int'},
		{name: 'resource_code',		type: 'string'},
		{name: 'start_dt',			type: 'date',		dateFormat: 'Y-m-d H:i'},
		{name: 'end_dt',			type: 'date',		dateFormat: 'Y-m-d H:i'},
		{name: 'request_action',	type: 'string'},	//Either 'Book' or 'Alter'
		{name: 'request_start_dt',	type: 'date',		dateFormat: 'Y-m-d H:i'},
		{name: 'request_end_dt',	type: 'date',		dateFormat: 'Y-m-d H:i'},
		{name: 'req_begin',			type: 'string'},
		{name: 'req_end',			type: 'string'},
		{name: 'req_data',			type: 'string'},
		{name: 'create_dt',			type: 'date',		dateFormat: 'Y-m-d H:i:s'},

require_once("includes.inc.php");

if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");

if(isset($_POST['userId'])){
  $userId = $_POST['userId'];
} else {
  sendErrorJSON("FOUT: Geen userId gedefinieerd!");
}

if(isset($_POST['sort'])&&($_POST['sort'])){
  $Sort = $_POST['sort'];
} else {
	sendErrorJSON("FOUT: Geen sort gedefinieerd!");
}

if(isset($_POST['dir'])&&($_POST['dir'])){
  $Dir = $_POST['dir'];
} else {
   sendErrorJSON("FOUT: Geen dir gedefinieerd!");
}

if(isset($_POST['start'])&&($_POST['start'])){
  $Start = $_POST['start'];
} else {
   $Start = 0;
}

$search = '%'.$_POST['query'].'%';

$QUERY = "SELECT
		(SELECT count(*) from W4ARitten WHERE PersoonNr = ?) AS aantal,
		r.RitNr,
		r.Geannuleerd,
		a.AutoId,
		a.Bijnaam,
		r.ReserveringBegin,
		r.ReserveringEind,		
		r.GebruikBegin,
		(SELECT aud.Status FROM W4ARittenAudit aud WHERE aud.RitNr = r.RitNr ORDER BY aud.Tijdstip DESC LIMIT 1) as Status
	FROM 
		W4ARitten r
	LEFT JOIN W4AAutos a ON a.AutoId = r.AutoId
	WHERE r.PersoonNr = ? 
	ORDER BY $Sort $Dir
	LIMIT 13 OFFSET $Start";
	
$rs = $sql->Execute($QUERY,array($userId, $userId));

if(!$rs)	sendErrorJSON($sql->ErrorMsg());

$aantal = $rs->fields['aantal'];

while( !$rs->EOF ) {
	if(isset($rs->fields['Geannuleerd'])){
		$desc = "Geannuleerd";	
	}else{
		if(isset($rs->fields['Status'])){
			if(array_key_exists((int)$rs->fields['Status'],$statusDesc)){
				$desc = $statusDesc[$rs->fields['Status']];
			}
		}else{
			$desc = "Onbekende status";
		}
	}
	$rows[] = array(
		"RitNr" => $rs->fields['RitNr'],
		"AutoId" => $rs->fields['AutoId'],
		"Bijnaam" => $rs->fields['Bijnaam'],
		"ReserveringBegin" => W4ATijd($rs->fields['ReserveringBegin']),
		"ReserveringEind" => W4ATijd($rs->fields['ReserveringEind']),
		"Status" => $desc
	);
	$rs->MoveNext();
}

$EmptyRows[] = array(
		"RitNr" => null,
		"AutoId" => null,
		"Bijnaam" => null,
		"ReserveringBegin" => null,
		"ReserveringEind" => null,
		"Status" => null
);

$jsondata['success'] = true;
$jsondata['results'] = $aantal ? $aantal : 0;
$jsondata['rows'] = $rows ? $rows : $EmptyRows;

echo json_encode($jsondata);*/
?>
