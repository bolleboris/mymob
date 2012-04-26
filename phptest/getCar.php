<?php
//require_once('includes.inc.php');
require_once('MyMobility.inc');
if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");
$_POST['AutoId'] = 343;
if(isset($_POST['AutoId'])){
  $AutoId = $_POST['AutoId'];
} else {
  sendErrorJSON("FOUT: Geen AutoId gedefinieerd!");
}

$BMUCore->ProviderSA(MYMOB_APP_ID)->Supplier(19)->Resource($AutoId)->Attributes()->GetList();
$response = $BMUCore->sendRequest();

$attributeMappings = array();
$attributeMappings[] = new AttributeMapping('Kenteken', array('group' => 'General', 'key' => 'LicensePlateNumber', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Bijnaam', array('group' => 'General', 'key' => 'Alias', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Merk', array('group' => 'General', 'key' => 'Brand', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Model', array('group' => 'General', 'key' => 'Model', 'access' => 'Protected'));
//$attributeMappings[] = new AttributeMapping('', array('group' => 'General', 'key' => 'Color', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Brandstof', array('group' => 'General', 'key' => 'Fuel', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Tankpascode', array('group' => 'General', 'key' => 'FuelCardCode', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Opmerkingen', array('group' => 'General', 'key' => 'Notes', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Handleiding', array('group' => 'General', 'key' => 'Manual', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('AantalZitplaatsen', array('group' => 'General', 'key' => 'NrSeats', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('BoardComputer', array('group' => 'General', 'key' => 'BoardComputer', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Opties', array('group' => 'General', 'key' => 'Options', 'access' => 'Protected'));
//$attributeMappings[] = new AttributeMapping('', array('group' => 'General', 'key' => 'CarAdvertisement', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Afbeelding', array('group' => 'General', 'key' => 'PrimaryPicture', 'access' => 'Protected'));
//$attributeMappings[] = new AttributeMapping('', array('group' => 'General', 'key' => 'Pictures', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('EigenRisicoWA', array('group' => 'General', 'key' => 'Deductible', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('EigenRisicoAllRisk', array('group' => 'General', 'key' => 'DeductibleCasco', 'access' => 'Protected'));
//$attributeMappings[] = new AttributeMapping('', array('group' => 'General', 'key' => 'RoadAssistance', 'access' => 'Protected'));
//$attributeMappings[] = new AttributeMapping('', array('group' => 'General', 'key' => 'KnownDamage', 'access' => 'Protected'));

$attributeMappings[] = new AttributeMapping('ToeslagPerKilometer', array('group' => 'Prices', 'key' => 'HourRate', 'access' => 'Protected'));
//$attributeMappings[] = new AttributeMapping('MaxTeBetalenUren', array('group' => 'Prices', 'key' => 'MaxHours', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('ToeslagPerUur', array('group' => 'Prices', 'key' => 'KilometerRate', 'access' => 'Protected'));
$attributes = $response['result']['attributes'];


$resource = array();
foreach($attributeMappings as $attributeMapping) {
	$resource[$attributeMapping->getField()] = $attributeMapping->getValue($attributes);
}
if($resource['BoardComputer'] != 'no') {$resource['Opties'] = 'Boordcomputer,'.$resource['Opties'];}



$opties = explode(",",$resource['Opties']);
$adres = ($resource['Toevoeging']) ? $resource['Straatnaam']." ".$resource['Huisnr']." ".$resource['Toevoeging'] : $resource['Straatnaam']." ".$resource['Huisnr'];
$rows[] = array(
  	"AutoId" => $resource['AutoId'], 
  	"BeheerdersId" => $resource['BeheerdersId'],
	"Merk" => $resource['Merk'], 
	"Model" => $resource['Model'],
	"Bijnaam" => $resource['Bijnaam'], 
	"Kenteken" => $resource['Kenteken'], 
	"Brandstof" => $resource['Brandstof'],
	"Kluiscode" => $resource['Kluiscode'], 
	"Tankpascode" => $resource['Tankpascode'],  
	"ToeslagPerKilometer" => $resource['ToeslagPerKilometer'],  
	"ToeslagPerUur" => $resource['ToeslagPerUur'],  
	"Opmerkingen" => $resource['Opmerkingen'],  
	"Afbeelding" => $resource['Afbeelding'],	
	"Actief" => $resource['Actief'],	
	"Latitude" => $resource['Latitude'],  
	"Longitude" => $resource['Longitude'],  				
	"AantalZitplaatsen" => $resource['AantalZitplaatsen'], 
	"Boordcomputer" => (in_array('Boordcomputer',$opties)) ? TRUE : FALSE,
	"Trekhaak" => (in_array('Trekhaak',$opties)) ? TRUE : FALSE,
	"Airco" => (in_array('Airco',$opties)) ? TRUE : FALSE,
	"MP3Aansluiting" => (in_array('MP3Aansluiting',$opties)) ? TRUE : FALSE,				
	"OptiesAanvullend" => $resource['OptiesAanvullend'],
	"Adres" => $adres,
	"Plaats" => $resource['Woonplaats'],
	"Postcode" => $resource['Postcode'],
	"ToestemmingVereist" => $resource['ToestemmingVereist']
);

$EmptyRows = array(
	"AutoId" => null,
	"BeheerdersId" => null,
	"Merk" => null,
	"Model" => null,
	"Bijnaam" => null,
	"Kenteken" => null,
	"Brandstof" => null,
	"Kluiscode" => 'Unknown in MyMob',
	"ToeslagPerKilometer" => null,
	"ToeslagPerUur" => null,
	"Opmerkingen" => null,
	"Afbeelding" => null,
	"Actief" => null,
	"Latitude" => null,
	"Longitude" => null,									
	"AantalZitplaatsen" => null,	
	"Boordcomputer" => null,	
	"Trekhaak" => null,	
	"Airco" => null,	
	"MP3Aansluiting" => null,						
	"OptiesAanvullend" => null,		
	"Adres" => null,		
	"Plaats" => null,		
	"Postcode"=> null
);
$rows[0] = $rows[0] + $EmptyRows;
$jsondata['success'] = true;
$jsondata['results'] = count($rows) ? count($rows) : 0;
$jsondata['rows'] = $rows ? $rows : $EmptyRows;
$jsondata['error'] = NULL;

echo json_encode($jsondata);
?>
