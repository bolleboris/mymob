<?php
require_once("./includes.inc.php");
require_once('MyMobility.inc');
require_once('person_attributes.inc.php');
require_once('resource_attributes.inc.php');

if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");

if(isset($_POST['RitNr'])){
  $RitNr = $_POST['RitNr'];
} else {
  sendErrorJSON("FOUT: Geen RitNr gedefinieerd!");
}


$BMUCore->ProviderSA(MYMOB_APP_ID)->Booking($RitNr)->Info();
$response = $BMUCore->sendRequest();

if($response['result']['result'] != 0){
	sendErrorJSON($response['result']['message']);
}

$res = $response['result'];

//print_r($res);

$car_att = get_resource_attributes($res['resource_id']);
$pers_att = get_person_attributes($res['consumer_id']);

$rows[] = array(
	'RitNr'							=> $RitNr,
	'AutoId'						=> $res['resource_id'],
	'Geannuleerd' 					=> ($res['status'] == 'Revoked') ? 'Geannuleerd' : '',
	'Bijnaam'						=> $car_att['Bijnaam'],
	'Kenteken'						=> $car_att['Kenteken'],
	'AutoPlaats'					=> null,
	'AutoStraat'					=> null,
	'PersoonNr'						=> $res['consumer_id'],
	'Voornaam'						=> $pers_att['Voornaam'],
	'Achternaam'					=> $pers_att['Achternaam'],
	'Initialen'						=> $pers_att['Initialen'],
	'Verantwoordelijke'				=> null,
	'verantwoordelijkeVoornaam' 	=> 'verantwoordelijke',
	'verantwoordelijkeAchternaam' 	=> 'verantwoordelijke',
	'verantwoordelijkeInitialen' 	=> 'verantwoordelijke',
	'Optie'							=> null,		//Zit dit nog ergens in BMU?
	'OpmerkingReservering'			=> null,
	'OpmerkingValidatie'			=> null,
	'ReserveringBegin'				=> $res['start_dt'],
	'ReserveringEind'				=> $res['end_dt'],
	'GebruikBegin'					=> $res['reg_info'][0][0],		//TODO: maak een functie om de juiste data uit het reg_info veld te plukken
	'GebruikEind'					=> $res['reg_info'][1][0],		//TODO: maak een functie om de juiste data uit het reg_info veld te plukken
	'KilometerstandBegin'			=> null,
	'KilometerstandEind'			=> null,
	'GebruiktMifareUID'				=> null,
	'Boordcomputer'					=> null
);		

/*

$QUERY = "SELECT
		r.RitNr,
		r.AutoId,
		r.Geannuleerd,
		a.Bijnaam,
		a.Kenteken,
		ad.Woonplaats AS AutoPlaats,
		ad.Straatnaam AS AutoStraat,
		r.PersoonNr,
		p.Voornaam,
		p.Achternaam,
		p.Initialen,
		c.Verantwoordelijke,
		p2.Voornaam AS verantwoordelijkeVoornaam,
		p2.Achternaam AS verantwoordelijkeAchternaam,
		p2.Initialen AS verantwoordelijkeInitialen,
		r.Optie,
		r.OpmerkingReservering,
		r.OpmerkingValidatie,
		r.ReserveringBegin,
		r.ReserveringEind,		
		r.GebruikBegin,
		r.GebruikEind,
		r.KilometerstandBegin,
		r.KilometerstandEind,				
		r.GebruiktMifareUID,
		FIND_IN_SET('Boordcomputer',a.Opties) AS Boordcomputer
	FROM 
		W4ARitten r
	LEFT JOIN W4AAutos a ON a.AutoId = r.AutoId
	LEFT JOIN W4AAdressen ad ON ad.AdresId = a.AdresId
	LEFT JOIN W4APersonen p ON p.PersoonNr = r.PersoonNr
	LEFT JOIN W4AContracten c ON c.ContractNr = r.ContractNr
	LEFT JOIN W4APersonen p2 ON p2.PersoonNr = c.Verantwoordelijke
	WHERE r.RitNr = ?;";

$rs = $sql->Execute($QUERY,array($RitNr));

if(!$rs)	sendErrorJSON($sql->ErrorMsg());

while( !$rs->EOF ) {
//	$rs->fields['Voornaam'] = utf8_encode($rs->fields['Voornaam']);

	$rs->fields['ReserveringBegin'] = W4ATijd($rs->fields['ReserveringBegin']);
	$rs->fields['ReserveringEind'] = W4ATijd($rs->fields['ReserveringEind']);	
	$rs->fields['GebruikBegin'] = W4ATijd($rs->fields['GebruikBegin']);
	$rs->fields['GebruikEind'] = W4ATijd($rs->fields['GebruikEind']);		
	$rs->fields['Optie'] = ($rs->fields['Optie'] == 1) ? "Optioneel" : "";
	$rs->fields['Boordcomputer'] = ($rs->fields['Boordcomputer']) ? "(Heeft boordcomputer)" : "";		//Dit is een optionele string, die wordt weergegeven in het rit details veld
	$rs->fields['Geannuleerd'] = (isset($rs->fields['Geannuleerd'])) ? "Geannuleerd" : "";		//Dit is een optionele string, die wordt weergegeven in het rit details veld
	if($rs->fields['OpmerkingReservering']) $rs->fields['OpmerkingReservering'] = "Opmerking: ".$rs->fields['OpmerkingReservering'];
	if($rs->fields['OpmerkingValidatie']) $rs->fields['OpmerkingValidatie'] = "Opmerking: ".$rs->fields['OpmerkingValidatie'];
	$rows[] = $rs->fields;
	
	$rs->MoveNext();
}
*/
$jsondata['success'] = true;
$jsondata['results'] = count($rows);
$jsondata['rows'] =  $rows ? $rows : array();
$jsondata['error'] = NULL;

echo json_encode($jsondata);


?>
