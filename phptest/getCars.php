<?php
//require_once('includes.inc.php');

require_once('MyMobility.inc');

if($_SESSION['userLevel'] == 2){
	$extra = "WHERE (a.Latitude BETWEEN '".number_format($_SESSION['baseLat']-$radius,6,'.','')."' AND '".number_format($_SESSION['baseLat']+$radius,6,'.','')."') 
	AND (a.Longitude BETWEEN '".number_format($_SESSION['baseLon']-$radius,6,'.','')."' AND '".number_format($_SESSION['baseLon']+$radius,6,'.','')."') ";
}else{
	$extra = "";
}
$BMUCore->ProviderSA(MYMOB_APP_ID)->Supplier(19)->Resource()->GetList();
$resourcesResponse = $BMUCore->sendRequest();

$resourceAttributeMappings = array();
$resourceAttributeMappings[] = new AttributeMapping('Merk',array('key' => 'Brand', 'group' => 'General', 'access' => 'Protected'));
$resourceAttributeMappings[] = new AttributeMapping('Bijnaam',array('group' => 'General', 'key' => 'Alias', 'access' => 'protected'));
$resourceAttributeMappings[] = new AttributeMapping('Kenteken',array('group' => 'General', 'key' => 'LicensePlateNumber', 'access' => 'protected'));


$metaData = array(
	"idProperty" => "AutoId",
	"root" => "rows",
	"totalProperty" => "results",
	"successProperty" => "success",
	"fields" => array(
		array('name' => 'AutoId'),
		array('name' => 'Merk',),
		array('name' => 'Bijnaam'),
		array('name' => 'Kenteken'),
		array('name' => 'Plaats'),
		array('name' => 'Straat')
	)
);

$EmptyRows[] = array(
	"AutoId" => null,
	"Merk" => null,
	"Bijnaam" => null,
	"Kenteken" => null,
	//"ReserveringBegin" => null,
	"Plaats" => null,
	"Straatnaam" => null
);
if($resourcesResponse['result']['result']== 0) {
	foreach($resourcesResponse['result']['resources'] as $resource) {		
		if($resource['is_group'] == '0') {
			$row = array();
			$row['AutoId'] = $resource['resource_id'];
			$BMUCore->ProviderSA(MYMOB_APP_ID)->Supplier($resource['supplier_id'])->Resource($resource['resource_id'])->Attributes()->GetList();
			$response = $BMUCore->sendRequest();
			$attributes = $response['result']['attributes'];
			foreach($resourceAttributeMappings as $attributeMapping) {
				$row[$attributeMapping->getField()] = $attributeMapping->getValue($attributes);
			}
			$BMUCore->ProviderSA(MYMOB_APP_ID)->Supplier($resource['supplier_id'])->Resource($resource['resource_id'])->Location()->GetList();
			$response = $BMUCore->sendRequest();
			if($response['result']['result'] == 0) {
				$locations = $response['result']['locations'];
				foreach($locations as $location) {
					if($location['is_default'] == '1') {
						$blaat = explode(', ',$location['location_txt']);
						$bloot = explode(' ',$blaat[0]);
						$row['Woonplaats'] = $blaat[1];
						$row['Straatnaam'] = $bloot[0];
					}
				}
			}
			$rows[] = $row + $EmptyRows;
		}
	}
}
/*$QUERY = "SELECT
		a.AutoId,
		a.Merk,
		a.Bijnaam,
		a.Kenteken,
		ad.Woonplaats,
		ad.Straatnaam
	FROM 
		W4AAutos a
	LEFT JOIN W4AAdressen ad ON a.AdresId = ad.AdresId
	$extra
	ORDER BY 
		ad.Woonplaats, 
		a.Bijnaam 
	ASC";
if(!$rs = $sql->Execute($QUERY,$AutoId)) sendErrorJSON($sql->ErrorMsg());

while( !$rs->EOF ) {
	$rows[] = array(
		"AutoId" => $rs->fields['AutoId'], 
		"Merk" => $rs->fields['Merk'], 
		"Bijnaam" => $rs->fields['Bijnaam'], 
		"Kenteken" => $rs->fields['Kenteken'], 
		"Plaats" => $rs->fields['Woonplaats'], 
		"Straat" => $rs->fields['Straatnaam']
	);
	$rs->MoveNext();
}

$metaData = array(
	"idProperty" => "AutoId",
	"root" => "rows",
	"totalProperty" => "results",
	"successProperty" => "success",
	"fields" => array(
		array('name' => 'AutoId'),
		array('name' => 'Merk',),
		array('name' => 'Bijnaam'),
		array('name' => 'Kenteken'),
		array('name' => 'Plaats'),
		array('name' => 'Straat')
	)
);

$EmptyRows[] = array(
	"AutoId" => null,
	"Merk" => null,
	"Bijnaam" => null,
	"Kenteken" => null,
	"ReserveringBegin" => null,
	"Plaats" => null,
	"Straatnaam" => null
);*/

$jsondata['metaData'] = $metaData;
$jsondata['success'] = $rows ? true : false;
$jsondata['results'] = $rows ? count($rows) : 0;
$jsondata['rows'] = $rows ? $rows : $EmptyRows;
$jsondata['msg'] = $rows ? null : 'Geen resultaten gevonden';

echo json_encode($jsondata);
?>
