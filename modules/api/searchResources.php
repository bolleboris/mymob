<?php

require_once 'MyMobility.inc';
require_once 'resource_attributes.inc.php';
$personId = $_POST['personId'];
$array = array(array('group' => 'HomePosition', 'key' => 'Longitude', 'access' => 'Public'),
			   array('group' => 'HomePosition', 'key' => 'Latitude', 'access' => 'Public'));

$BMUCore->Wheels4All()->ProviderSA(100)->FindAttributesForEntities($array,array($personId));
$res = $BMUCore->sendRequest();

if($res['result']['result'] == 0) {
   $attributes = AttributeMapping::getValuesScoreSeperated($res['result']['entities'][2]['attributes']);
   $position = '('.$attributes['HomePosition_Latitude_Public'].','.$attributes['HomePosition_Longitude_Public'].')';

   $BMUCore->Wheels4All()->ProviderSA(100)->FindResources($personId, '', $position, 25000, 'now','now');
   $res = $BMUCore->sendRequest();
   if($res['result']['result'] != 0) {
	  exit(json_encode(array('Success' => false, 'message' => $res['result']['message'])));
   }
   $rows = array();
   //$result = $res['result']['search_results'][0];
   foreach($res['result']['search_results'] as $result ) {
	  $row = array();
	  $values = get_resource_attributes($result['supplier_id'],$result['resource_id']);
	  $row['AutoId'] = $result['resource_id'];
	  $row['Bijnaam'] = $values['Bijnaam'];
	  $row['Kenteken'] = $values['Kenteken'];
	  $row['SupplierCode'] = $result['supplier_code'];
	  $row['CustomerCode'] = $result['customer_code'];
	  $row['distance'] = round($result['ref_distance']);
	  $row['service'] = $result['service_code'];
	  $row['contractid'] = $result['contract_id'];
	  $row['offerid'] = $result['offer_id'];
	  $row['serviceid'] = $result['service_id'];
	  $rows[] = $row;
   }
}

$metaData = array(
	"idProperty" => "AutoId",
	"root" => "rows",
	"totalProperty" => "results",
	"successProperty" => "success",
	"fields" => array(
		array('name' => 'AutoId'),
		array('name' => 'Bijnaam',),
		array('name' => 'Kenteken'),
		array('name' => 'SupplierCode'),
		array('name' => 'CustomerCode'),
		array('name' => 'distance'),
		array('name' => 'service'),
	)
);

$jsondata['metaData'] = $metaData;
$jsondata['success'] = $rows ? true : false;
$jsondata['results'] = $rows ? count($rows) : 0;
$jsondata['rows'] = $rows;
$jsondata['msg'] = $rows ? null : 'Geen resultaten gevonden';

echo json_encode($jsondata);
?>
