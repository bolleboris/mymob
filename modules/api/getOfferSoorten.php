<?php
//require_once('includes.inc.php');
require_once('MyMobility.inc');

//$BMUCore->ProviderSA(MYMOB_APP_ID)->ContractType()->GetList();
$BMUCore->ProviderSA(MYMOB_APP_ID)->OfferType()->GetList();
$response = $BMUCore->sendRequest();

if($response['result']['result'] != 0) sendErrorJSON($response['result']['message']);

$metaData = array(
	"idProperty" => "",
	"root" => "rows",
	"totalProperty" => "results",
	"successProperty" => "success",
	"fields" => array(
		array('name' => 'OfferSoort'),
		array('name' => 'Omschrijving',)
/*
• provider id - Integer that contains the ID of the Provider who created
this OfferType
• provider code - String containing a readable description of the Provider
• code - String with which you can uniquely identify this OfferType.
• info - String that can be used for anything.
• cond value - Integer, unsure of its meaning
• create dt - The date on which the OfferType was created.
• is active - If 1, Offers can be made with this OfferType.
 */

	)
);
foreach($response['result']['types'] as $type) {
	$rows[] = array(
		'OfferSoort' => $type['offer_type_id'],
		'Omschrijving' => $type['code']
	);
}

$EmptyRows[] = array(
	"" => null,
	"" => null,
);

$jsondata['metaData'] = $metaData;
$jsondata['success'] = $rows ? true : false;
$jsondata['results'] = $rows ? count($rows) : 0;
$jsondata['rows'] = $rows ? $rows : $EmptyRows;
$jsondata['msg'] = $rows ? null : 'Geen resultaten gevonden';

echo json_encode($jsondata);