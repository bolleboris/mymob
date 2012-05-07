<?php

require_once('MyMobility.inc');
require_once('resource_attributes.inc.php');

/**
 * Should yield:
 * - $offer_id
 * - $supplier_id
 */
extract($_POST);

$rows = array();
$metaData = array(
  "idProperty" => "OfferNr",
	"root" => "rows",
	"totalProperty" => "results",
	"successProperty" => "success",
	"fields" => array(
		array('name' => 'OfferNr'),
		array('name' => 'Merk',),
		array('name' => 'Bijnaam',),
		array('name' => 'Kenteken',),
		array('name' => 'Plaats',),
		array('name' => 'Straat'),
		array('name' => 'SupplierId')
	)
);

$EmptyRows[] = array();

// filling $EmptyRows skeleton
foreach ($metaData["fields"] as $field) {
	$EmptyRows[0][$field["name"]] = null;
}

$BMUCore->Wheels4All()->ProviderSA(MYMOB_APP_ID)->OfferResources($offer_id);
$OfferResourcesRs = $BMUCore->sendRequest();

foreach ($OfferResourcesRs['result']['resources'] as $OfferResource) {
	$ResourceDetailsRs = get_resource_attributes($supplier_id, $OfferResource['resource_id']);

	/*
	{header: "AutoId", width: 60, sortable: true, dataIndex: 'AutoId'},
	{header: "Merk", width: 100, sortable: true, dataIndex: 'Merk'},
	{header: "Bijnaam", width: 125, sortable: true, dataIndex: 'Bijnaam', groupable: false},
	{header: "Kenteken", width: 70, sortable: true, dataIndex: 'Kenteken', groupable: false},
	{header: "Plaats", width: 80, sortable: true, dataIndex: 'Plaats'},
	{header: "Straat", width: 100, sortable: true, dataIndex: 'Straat', groupable: false},
	{header: "Supplier", width: 100, sortable: true, dataIndex: 'SupplierId'}
	 */

	// getting location
	$BMUCore->ProviderSA(MYMOB_APP_ID)->Supplier($supplier_id)->Resource($OfferResource['resource_id'])->Location()->GetList();
	$LocationsResponse = $BMUCore->sendRequest();

	if ($response['result']['result'] == 0) {
		$locations = $LocationsResponse['result']['locations'];

		foreach ($locations as $location) {
			if ($location['is_default'] == '1') {
				$location_parts = explode(', ', $location['location_txt']);
				$streetaddress_parts = explode(' ', $location_parts[0]);
				$ResourceDetailsRs['Woonplaats'] = $location_parts[2];
				$resource['Toevoeging'] = false;
				$tglue = '';
				
				for($i = 0; $i < count($streetaddress_parts) - 1; $i++) {
					$ResourceDetailsRs['Straatnaam'] .= $tglue.$streetaddress_parts[$i];
					$tglue = ' ';
				}
				//$resource['Huisnr'] = $streetaddress_parts[count($streetaddress_parts) - 1];
				//$resource['Postcode'] = $location_parts[1];
				//$temp = explode(',', $location['location_geo']);
				//$resource['Latitude'] = str_replace('(', '', $temp[0]);
				//$resource['Longitude'] = str_replace(')','',$temp[1]);
			}
		}
	}

	 $rows[] = array(
	     //"AutoId"	=> $OfferResource['AutoId'],
	     "AutoId"	=> $ResourceDetailsRs['resource_id'],
	     "Merk"	=> $ResourceDetailsRs['Merk'],
	     "Bijnaam"	=> $ResourceDetailsRs['Bijnaam'],
	     "Kenteken"	=> $ResourceDetailsRs['Kenteken'],
	     "Plaats"	=> $ResourceDetailsRs['Woonplaats'],
	     "Straat"	=> $ResourceDetailsRs['Straatnaam'],
	     "Supplier"	=> $supplier_id
	 );
}

$jsondata = array(
    'metaData'	=> $metaData,
    'success'	=> $rows ? true : false,
    'results'	=> count($rows),
    'rows'	=> count($rows) ? $rows : $EmptyRows,
    'msg'	=> $rows ? null : 'Geen resultaten gevonden'
);

echo(json_encode($jsondata));