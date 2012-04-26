<?php
//require_once('includes.inc.php');
require_once('MyMobility.inc');
require_once('resource_attributes.inc.php');

if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");
if(isset($_POST['AutoId'])){
  $AutoId = $_POST['AutoId'];
} else {
  sendErrorJSON("FOUT: Geen AutoId gedefinieerd!");
}

$resource = get_resource_attributes($AutoId);

/*$BMUCore->ProviderSA(MYMOB_APP_ID)->Supplier(19)->Resource($AutoId)->Attributes()->GetList();
$response = $BMUCore->sendRequest();

$attributes = $response['result']['attributes'];

foreach($attributeMappings as $attributeMapping) {
	$resource[$attributeMapping->getField()] = $attributeMapping->getValue($attributes);
}*/

$resource['AutoId'] = $AutoId;
$resource['BeheerdersId'] = 100;

if($resource['BoardComputer'] != 'no') {$resource['Opties'] = 'Boordcomputer,'.$resource['Opties'];}

$BMUCore->ProviderSA(MYMOB_APP_ID)->Supplier(100)->Resource($AutoId)->Location()->GetList();
$response = $BMUCore->sendRequest();
if($response['result']['result'] == 0) {
	$locations = $response['result']['locations'];
	//var_dump($locations);
	foreach($locations as $location) {
		if($location['is_default'] == '1') {
			

			$blaat = explode(', ',$location['location_txt']);
			$bloot = explode(' ',$blaat[0]);
			$resource['Woonplaats'] = $blaat[2];
			$resource['Toevoeging'] = false;
			$tglue = '';
			for($i = 0; $i < count($bloot) - 1; $i++) {
				$resource['Straatnaam'] .= $tglue.$bloot[$i];
				$tglue = ' ';
			}
			$resource['Huisnr'] = $bloot[count($bloot) - 1];
			$resource['Postcode'] = $blaat[1];
			$temp = explode(',',$location['location_geo']);
			$resource['Latitude'] = str_replace('(','',$temp[0]);			
			$resource['Longitude'] = str_replace(')','',$temp[1]);	
		}
	}
}

$BMUCore->ProviderSA(MYMOB_APP_ID)->Supplier(100)->Resource($AutoId)->Info();
$response = $BMUCore->sendRequest();
$info = $response['result']['resource'];
$resource['Actief'] = ($info['is_active'])? true : false;

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
	"Handleiding" => $resource['Handleiding'], 
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


$jsondata['success'] = true;
$jsondata['results'] = count($rows) ? count($rows) : 0;
$jsondata['rows'] = $rows ? $rows : array();
$jsondata['error'] = NULL;

echo json_encode($jsondata);
?>
