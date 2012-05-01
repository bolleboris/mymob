<?php

require_once 'MyMobility.inc';

extract($_POST);

$BMUCore->Wheels4All()->ProviderSA(MYMOB_APP_ID)->CreateResource($supplierId, $vloot, 'Car', uniqid() . '-' . uniqid(), $info);
$res = $BMUCore->sendRequest();
if ($res['result']['result'] != 0) {
   $jsondata = array();
   $jsondata['success'] = false;
   $jsondata['error'] = $res['result']['message'];
   $jsondata['error'] = 'Updaten van chips is tijdelijk uitgeschakeld';
}

$attributes = array(
	array('group' => 'General', 'key' => 'Alias', 'access' => 'Public', 'value' => 'Nog in te vullen'),
	array('group' => 'General', 'key' => 'Brand', 'access' => 'Public', 'value' => 'Nog in te vullen'),
	array('group' => 'General', 'key' => 'Model', 'access' => 'Public', 'value' => 'Nog in te vullen'),
	array('group' => 'General', 'key' => 'Color', 'access' => 'Public', 'value' => 'Nog in te vullen'),
	array('group' => 'General', 'key' => 'Fuel', 'access' => 'Public', 'value' => 'Nog in te vullen'),
	array('group' => 'General', 'key' => 'FuelCardCode', 'access' => 'Protected', 'value' => 'Nog in te vullen'),
	array('group' => 'General', 'key' => 'Notes', 'access' => 'Protected', 'value' => 'Nog in te vullen'),
	array('group' => 'General', 'key' => 'Manual', 'access' => 'Protected', 'value' => 'Nog in te vullen'),
	array('group' => 'General', 'key' => 'NrSeats', 'access' => 'Protected', 'value' => '-1'),
	array('group' => 'General', 'key' => 'BoardComputer', 'access' => 'Protected', 'value' => 'no'),
	array('group' => 'General', 'key' => 'Options', 'access' => 'Protected', 'value' => 'Nog in te vullen'),
	array('group' => 'General', 'key' => 'CarAdvertisement', 'access' => 'Protected', 'value' => 'Nog in te vullen'),
	array('group' => 'General', 'key' => 'PrimaryPicture', 'access' => 'Protected', 'value' => 'Nog in te vullen'),
	array('group' => 'General', 'key' => 'Pictures', 'access' => 'Protected', 'value' => 'Nog in te vullen'),
	array('group' => 'General', 'key' => 'Deductible', 'access' => 'Protected', 'value' => 'Nog in te vullen'),
	array('group' => 'General', 'key' => 'DeductibleCasco', 'access' => 'Protected', 'value' => 'Nog in te vullen'),
	array('group' => 'General', 'key' => 'RoadAssistance', 'access' => 'Protected', 'value' => 'Nog in te vullen'),
	array('group' => 'General', 'key' => 'KnownDamage', 'access' => 'Protected', 'value' => 'Nog in te vullen'),
	array('group' => 'Prices', 'key' => 'HourRate', 'access' => 'Protected', 'value' => '2.5'),
	array('group' => 'Prices', 'key' => 'MaxHours', 'access' => 'Protected', 'value' => '10'),
	array('group' => 'Prices', 'key' => 'KilometerRate', 'access' => 'Protected', 'value' => '0.25'),
	array('group' => 'Settings', 'key' => 'W4AAutoId', 'access' => 'Private', 'value' => '5' . $res['result']['resource_id']),
	array('group' => 'General', 'key' => 'LicensePlateNumber', 'access' => 'Public', 'value' => $kenteken),
);

$id = $res['result']['resource_id'];
$BMUCore->ProviderSA(MYMOB_APP_ID)->Supplier($supplierId)->Resource($id)->Attributes()->ListCreate($attributes);
$res = $BMUCore->sendRequest();

$BMUCore->ProviderSA(MYMOB_APP_ID)->Supplier($supplierId)->Resource($id)->Location()->Create('((0,0))', 'Vul In 0, Vul In, Vul In', 'Geen Extra Commentaar', true);
$res = $BMUCore->sendRequest();

$jsondata = array();
$jsondata['success'] = true;
$jsondata['error'] = NULL;
$jsondata['id'] = $id;
echo json_encode($jsondata);
?>