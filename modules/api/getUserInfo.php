<?php
require_once('MyMobility.inc');
require_once('person_attributes.inc.php');
require_once('resource_attributes.inc.php');

if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");

if(isset($_POST['userId'])){
	$userId = $_POST['userId'];
} else {
	sendErrorJSON("Geen userID!");
}

/*$BMUCore->ProviderSA(MYMOB_APP_ID)->Person($userId)->Attributes()->GetList();
$person_attributes = attributes_convert($BMUCore->sendRequest());*/

$person_attributes = get_person_attributes($userId);

$success = true;
$opgezegdString = ($person_attributes['OpgezegdPer']) ? "Opgezegd per: ".W4ADate($person_attributes['OpgezegdPer']) : "";

if($person_attributes['FavouriteCar']){
	$car_attributes = get_resource_attributes($person_attributes['FavorieteAuto']);
}

$rows[] = array(
	"PersoonNr"=> $userId,
	"W4APersoonNr"=> $person_attributes['W4APersoonNr'],
	"Voornaam" =>  $person_attributes['Voornaam'],
	"Achternaam" =>  $person_attributes['Achternaam'],
	"Initialen" =>  $person_attributes['Initialen'],
	"FavorieteAuto" => $person_attributes['FavorieteAuto'],
	"FavorieteAutoBijnaam" =>  $car_attributes['Alias'],
	"Email" => $person_attributes['Email'],
	"Telefoon1" => $person_attributes['Telefoon1'],
	"Telefoon2" => $person_attributes['Telefoon2'],
	"Woonplaats" =>  $person_attributes['Woonplaats'],
	"Adres" =>  $person_attributes['Straatnaam']." ".$person_attributes['Huisnr'],
	"Postcode" => $person_attributes['Postcode']
);

$jsondata['success'] = $success;
$jsondata['results'] = count($rows) ? count($rows) : 0;
$jsondata['rows'] = $rows ? $rows : $EmptyRows;
$jsondata['msg'] = $rs['result']['message'];
	
echo json_encode($jsondata);

?>
