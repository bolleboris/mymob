<?php 

require_once('MyMobility.inc');

function get_person_attributes($PersonId){
	global $BMUCore, $person_attribute_mapping;
	$BMUCore->ProviderSA(MYMOB_APP_ID)->Person($PersonId)->Attributes()->GetList();

	$request = $BMUCore->__toString();

	$response = $BMUCore->sendRequest();
	
	if($response['result']['result'] != 0){
		//sendErrorJSON($response['result']['message']);
		return false;
		exit();
	}
	$attributes = $response['result']['attributes'];
	
	foreach($person_attribute_mapping as $attributeMapping) {
		$person_att[$attributeMapping->getField()] = $attributeMapping->getValue($attributes);
	}

	return $person_att;
}

/*$person_attribute_mapping->addMapping(array('Voornaam'), array('group' => 'Name', 'key' => 'FirstName', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('Tussenvoegels'), array('group' => 'Name', 'key' => 'Preposition', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('Achternaam'), array('group' => 'Name', 'key' => 'SurName', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('Initialen'), array('group' => 'Name', 'key' => 'Initials', 'access' => 'Protected'));

$person_attribute_mapping->addMapping(array('IsMan'), array('group' => 'General', 'key' => 'Gender', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('GeboorteDatum'), array('group' => 'General', 'key' => 'BirthDate', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('Email'), array('group' => 'General', 'key' => 'EmailAddress', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('Telefoon1'), array('group' => 'General', 'key' => 'Telephone1', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('Telefoon2'), array('group' => 'General', 'key' => 'Telephone2', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('Telefoon3'), array('group' => 'General', 'key' => 'Telephone3', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('RijbewijsNr'), array('group' => 'General', 'key' => 'DriverLicenceNr', 'access' => 'Protected'));

$person_attribute_mapping->addMapping(array('Latitude'), array('group' => 'HomePosition', 'key' => 'Latitude', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('Longitude'), array('group' => 'HomePosition', 'key' => 'Longitude', 'access' => 'Protected'));

$person_attribute_mapping->addMapping(array(), array('group' => 'Settings', 'key' => 'EmailResConf', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array(), array('group' => 'Settings', 'key' => 'AmountOfEmail', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('PersoonNr'), array('group' => 'Settings', 'key' => 'W4APersoonNr', 'access' => 'private'));
$person_attribute_mapping->addMapping(array('FavorieteAuto'), array('group' => 'Settings', 'key' => 'FavouriteCar', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('Postcode'), array('group' => 'Address', 'key' => 'Zipcode', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('Huisnr'), array('group' => 'Address', 'key' => 'HouseNr', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('Toevoeging'), array('group' => 'Address', 'key' => 'Affix', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('Woonplaats'), array('group' => 'Address', 'key' => 'City', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('Straatnaam'), array('group' => 'Address', 'key' => 'StreetName', 'access' => 'Protected'));
$person_attribute_mapping->addMapping(array('Land'), array('group' => 'Address', 'key' => 'Country', 'access' => 'Protected'));*/


$person_attribute_mapping[] = new AttributeMapping('Voornaam', 		array('group' => 'Name', 			'key' => 'FirstName',		'access' => 'Public'));
$person_attribute_mapping[] = new AttributeMapping('Tussenvoegels',	array('group' => 'Name', 			'key' => 'Preposition',		'access' => 'Public'));
$person_attribute_mapping[] = new AttributeMapping('Achternaam',	array('group' => 'Name', 			'key' => 'SurName', 		'access' => 'Public'));
$person_attribute_mapping[] = new AttributeMapping('Initialen',		array('group' => 'Name', 			'key' => 'Initials', 		'access' => 'Public'));

$person_attribute_mapping[] = new AttributeMapping('IsMan', 		array('group' => 'General',			'key' => 'Gender',			'access' => 'Public'));
$person_attribute_mapping[] = new AttributeMapping('GeboorteDatum', array('group' => 'General',			'key' => 'BirthDate',		'access' => 'Public'));
$person_attribute_mapping[] = new AttributeMapping('Email', 		array('group' => 'General',			'key' => 'EmailAddress',	'access' => 'Public'));
$person_attribute_mapping[] = new AttributeMapping('Telefoon1', 	array('group' => 'General',			'key' => 'Telephone1',		'access' => 'Public'));
$person_attribute_mapping[] = new AttributeMapping('Telefoon2',		array('group' => 'General',			'key' => 'Telephone2',		'access' => 'Protected'));
$person_attribute_mapping[] = new AttributeMapping('Telefoon3',		array('group' => 'General',			'key' => 'Telephone3',		'access' => 'Protected'));
$person_attribute_mapping[] = new AttributeMapping('RijbewijsNr',	array('group' => 'General',			'key' => 'DriverLicenceNr',	'access' => 'Protected'));

$person_attribute_mapping[] = new AttributeMapping('Latitude',		array('group' => 'HomePosition',	'key' => 'Latitude',		'access' => 'Public'));
$person_attribute_mapping[] = new AttributeMapping('Longitude',		array('group' => 'HomePosition',	'key' => 'Longitude',		'access' => 'Public'));

$person_attribute_mapping[] = new AttributeMapping('FavorieteAuto',	array('group' => 'Settings',		'key' => 'FavouriteCar',	'access' => 'Protected'));
$person_attribute_mapping[] = new AttributeMapping('W4APersoonNr',	array('group' => 'Settings',		'key' => 'W4APersoonNr',	'access' => 'Private'));

$person_attribute_mapping[] = new AttributeMapping('Postcode',		array('group' => 'Address',			'key' => 'Zipcode',			'access' => 'Public'));
$person_attribute_mapping[] = new AttributeMapping('Huisnr',		array('group' => 'Address',			'key' => 'HouseNr',			'access' => 'Public'));
$person_attribute_mapping[] = new AttributeMapping('Toevoeging',	array('group' => 'Address',			'key' => 'Affix',			'access' => 'Public'));
$person_attribute_mapping[] = new AttributeMapping('Woonplaats',	array('group' => 'Address',			'key' => 'City',			'access' => 'Public'));
$person_attribute_mapping[] = new AttributeMapping('Straatnaam',	array('group' => 'Address',			'key' => 'StreetName',		'access' => 'Public'));
$person_attribute_mapping[] = new AttributeMapping('Land',			array('group' => 'Address',			'key' => 'Country',			'access' => 'Public'));



?>
