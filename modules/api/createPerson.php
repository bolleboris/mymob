<?php

include_once 'MyMobility.inc';

$email = $_POST['General_EmailAddress_Public'];
$entitykey = $_POST['entitykey'];
$password = $_POST['authentication'];
unset($_POST['entitykey']);
unset($_POST['authentication']);

$BMUCore->ProviderSA(100)->RegisterPerson('MyWheels-UI', $entitykey, $email, $password);
$res = $BMUCore->sendRequest();

if (false && isset($res['result']['result']) && $res['result']['result'] == 0) {
   $standard = array(array('group' => 'Name', 'key' => 'Preposition', 'value' => '', 'access' => 'Public'),
	   array('group' => 'General', 'key' => 'Gender', 'value' => '', 'access' => 'Public'),
	   array('group' => 'General', 'key' => 'BirthDate', 'value' => '', 'access' => 'Public'),
	   array('group' => 'General', 'key' => 'Telephone1', 'value' => '', 'access' => 'Public'),
	   array('group' => 'General', 'key' => 'Telephone2', 'value' => '', 'access' => 'Protected'),
	   array('group' => 'General', 'key' => 'Telephone3', 'value' => '', 'access' => 'Protected'),
	   array('group' => 'General', 'key' => 'DriverLicenceNr', 'value' => '', 'access' => 'Protected'),
	   array('group' => 'HomePosition', 'key' => 'Latitude', 'value' => '', 'access' => 'Public'),
	   array('group' => 'HomePosition', 'key' => 'Longitude', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Settings', 'key' => 'AmountOfEmail', 'value' => '', 'access' => 'Protected'),
	   array('group' => 'Settings', 'key' => 'W4APersoonNr', 'value' => '', 'access' => 'Private'),
	   array('group' => 'Address', 'key' => 'HouseNr', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Address', 'key' => 'City', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Address', 'key' => 'StreetName', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Address', 'key' => 'Country', 'value' => 'Nethrlands', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'UID0', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'Blocked0', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'UID1', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'Blocked1', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'UID2', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'Blocked2', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'UID3', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'Blocked3', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'UID4', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'Blocked4', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'UID5', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'Blocked5', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'UID6', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'Blocked6', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'UID7', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Chips', 'key' => 'Blocked7', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Address', 'key' => 'Zipcode', 'value' => '', 'access' => 'Public'),
	   array('group' => 'Settings', 'key' => 'EmailResConf', 'value' => '', 'access' => 'Protected'),
	   array('group' => 'Settings', 'key' => 'FavouriteCar', 'value' => '', 'access' => 'Protected'),
   );

   $atts = AttributeMapping::getAttributesScoreSeperated($_POST);

   $BMUCore->ProviderSA(100)->Person($res['result']['person_id'])->Attributes()->ListCreate($atts + $standard);
   $BMUCore->sendRequest();

   $jsondata = array();
   $jsondata['success'] = true;
   $jsondata['error'] = NULL;
   $jsondata['id'] = $res['result']['person_id'];
} else {
   $jsondata = array();
   $jsondata['success'] = false;
   $jsondata['error'] = $res['result']['message'];
   $jsondata['error'] = 'Persoon kon niet toegevoegd worden';
}

echo json_encode($jsondata);
?>