<?php 

require_once('MyMobility.inc');
//require_once('includes.inc.php');

function get_resource_attributes($Supplier_id,$Resource_id){
	global $BMUCore, $resource_attribute_mapping;
	
	$BMUCore->ProviderSA(MYMOB_APP_ID)->Supplier($Supplier_id)->Resource($Resource_id)->Attributes()->GetList();
	$response = $BMUCore->sendRequest();

	if($response['result']['result'] != 0){
		sendErrorJSON($response['result']['message']);
		exit();
	}
	$attributes = $response['result']['attributes'];

	$resource_att = array();
	foreach($resource_attribute_mapping as $attributeMapping) {
		$resource_att[$attributeMapping->getField()] = $attributeMapping->getValue($attributes);
	}
	
	return $resource_att;
}

function set_resource_attributes($Resource_id, &$values) {
	global $BMUCore, $resource_attribute_mapping;
	$attributes = array();
	foreach($resource_attribute_mapping as $mapping) {
		$attributes[] = $mapping->getAttribute($values);
	}
	$BMUCore->ProviderSA(MYMOB_APP_ID)->Supplier(100)->Resource($Resource_id)->Attributes()->ListUpdate($attributes);
	$response = $BMUCore->sendRequest();
	return $response;
}

$resource_attribute_mapping[] = new AttributeMapping('Kenteken', 			array('group' => 'General', 'key' => 'LicensePlateNumber',	'access' => 'Public'));
$resource_attribute_mapping[] = new AttributeMapping('Bijnaam', 			array('group' => 'General', 'key' => 'Alias',				'access' => 'Public'));
$resource_attribute_mapping[] = new AttributeMapping('Merk', 				array('group' => 'General', 'key' => 'Brand',				'access' => 'Public'));
$resource_attribute_mapping[] = new AttributeMapping('Model', 				array('group' => 'General', 'key' => 'Model',				'access' => 'Public'));
//$resource_attribute_mapping[] = new AttributeMapping('', 					array('group' => 'General', 'key' => 'Color',				'access' => 'Public'));
$resource_attribute_mapping[] = new AttributeMapping('Brandstof', 			array('group' => 'General', 'key' => 'Fuel',				'access' => 'Public'));
$resource_attribute_mapping[] = new AttributeMapping('Tankpascode', 		array('group' => 'General', 'key' => 'FuelCardCode',		'access' => 'Protected'));
$resource_attribute_mapping[] = new AttributeMapping('Opmerkingen', 		array('group' => 'General', 'key' => 'Notes',				'access' => 'Protected'));
$resource_attribute_mapping[] = new AttributeMapping('Handleiding', 		array('group' => 'General', 'key' => 'Manual',				'access' => 'Protected'));
$resource_attribute_mapping[] = new AttributeMapping('AantalZitplaatsen',	array('group' => 'General', 'key' => 'NrSeats',				'access' => 'Protected'));
$resource_attribute_mapping[] = new AttributeMapping('Boordcomputer', 		array('group' => 'General', 'key' => 'BoardComputer', 		'access' => 'Protected'));
$resource_attribute_mapping[] = new AttributeMapping('Opties', 				array('group' => 'General', 'key' => 'Options', 			'access' => 'Protected'));
//$resource_attribute_mapping[] = new AttributeMapping('', 					array('group' => 'General', 'key' => 'CarAdvertisement',	'access' => 'Protected'));
$resource_attribute_mapping[] = new AttributeMapping('Afbeelding', 			array('group' => 'General', 'key' => 'PrimaryPicture',		'access' => 'Protected'));
//$resource_attribute_mapping[] = new AttributeMapping('', 					array('group' => 'General', 'key' => 'Pictures',			'access' => 'Protected'));
$resource_attribute_mapping[] = new AttributeMapping('EigenRisicoWA',		array('group' => 'General', 'key' => 'Deductible',			'access' => 'Protected'));
$resource_attribute_mapping[] = new AttributeMapping('EigenRisicoAllRisk',	array('group' => 'General', 'key' => 'DeductibleCasco',		'access' => 'Protected'));
//$resource_attribute_mapping[] = new AttributeMapping('',					array('group' => 'General', 'key' => 'RoadAssistance', 		'access' => 'Protected'));
//$resource_attribute_mapping[] = new AttributeMapping('',					array('group' => 'General', 'key' => 'KnownDamage', 		'access' => 'Protected'));

$resource_attribute_mapping[] = new AttributeMapping('ToeslagPerKilometer',	array('group' => 'Prices',	'key' => 'HourRate', 			'access' => 'Protected'));
//$resource_attribute_mapping[] = new AttributeMapping('MaxTeBetalenUren',	array('group' => 'Prices',	'key' => 'MaxHours', 			'access' => 'Protected'));
$resource_attribute_mapping[] = new AttributeMapping('ToeslagPerUur',		array('group' => 'Prices',	'key' => 'KilometerRate', 		'access' => 'Protected'));

?>