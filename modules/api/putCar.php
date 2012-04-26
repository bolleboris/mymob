<?php
require_once('includes.inc.php');
require_once('address.inc.php');
require_once('MyMobility.inc');
require_once('resource_attributes.inc.php');

if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");

if(isset($_POST['AutoId'])){
  $AutoId = $_POST['AutoId'];
} else {
  sendErrorJSON("FOUT: Geen AutoId gedefinieerd!");
}
$resource = $_POST;
unset($resource['AutoId']);
unset($resource['Kluiscode']);
$resource['Boordcomputer'] = ($_POST['Boordcomputer'] == 'on') ? 'convadis' : 'no';


if($resource['Trekhaak'] == 'on') $optiesArr[] = 'Trekhaak';
if($resource['Airco'] == 'on') $optiesArr[] = 'Airco';
if($resource['MP3Aansluiting'] == 'on') $optiesArr[] = 'MP3Aansluiting';
if($resource['OptiesAanvullend'] != '') $optiesArr[] = $resource['OptiesAanvullend'];
//LM 02-05-11 Indien er geen opties zijn gedefinieerd zou de implode functie hier een warning geven, wat ext-js weer niet leuk vind (warning != JSON)
if(isset($optiesArr)) {
	$opties = implode(",",$optiesArr);
}else{
	$opties = '';
}
unset($resource['Trekhaak']);
unset($resource['Airco']);
unset($resource['MP3Aansluiting']);
unset($resource['MP3OptiesAanvullend']);
$resource['Opties'] = $opties;

$actief = ($_POST['Actief']) ? 1 : 0;
set_resource_attributes($AutoId, $resource);

$BMUCore->ProviderSA(MYMOB_APP_ID)->Supplier($resource['BeheerdersId'])->Resource($AutoId)->Location()->GetList();
$response = $BMUCore->sendRequest();
foreach($response['result']['locations'] as $location) {
	if($location['is_default'] == '1') {
		$location_txt = "{$resource['Adres']}, {$resource['Postcode']}, {$resource['Plaats']}";
		$location_geo = "(({$resource['Latitude']},{$resource['Longitude']}))";
		$location_rem = $location['location_rem'];
		unset($resource['Latitude']);
		unset($resource['Longitude']);
		unset($resource['Adres']);
		unset($resource['Plaats']);
		unset($resource['Postcode']);
		
		$BMUCore->Supplier($resource['BeheerdersId'])->Resource($AutoId)->Location($location['location_id'])->Update($location_geo,$location_txt,$location_rem, $location['is_default']);
	}
}
$jsondata['success'] = true;
$jsondata['error'] = NULL;

echo json_encode($jsondata);
?>
