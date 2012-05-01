<?php
//require_once('includes.inc.php');
require_once('MyMobility.inc');

if(isset($_POST['OfferNr'])){
  $OfferNr = $_POST['OfferNr'];
} else {
  sendErrorJSON("FOUT: Geen OfferNr gedefinieerd!");
}



	
$BMUCore->ProviderSA(MYMOB_APP_ID)->Offer($OfferNr)->Info();
$response = $BMUCore->sendRequest();

if($result['result']['result'] != 0) sendErrorJSON($result['result']['message']);


$rows[] = array(
	"OfferNr" => $OfferNr,
	"Verantwoordelijke" => $response['result']['supplier_id'],
	"Status" => $response['result']['status'], 
	"OfferSoort" => $response['result']['offer_type_id'],
);

$metaData = array(
	"idProperty" => "OfferNr",
	"root" => "rows",
	"totalProperty" => "results",
	"successProperty" => "success",
	"fields" => array(
		array('name' => 'OfferNr'),
		array('name' => 'Verantwoordelijke'),
		array('name' => 'Status'),
		array('name' => 'OfferSoort'),
	)
);

$EmptyRows[] = array(
	"OfferNr" => null,
	"Verantwoordelijke" => null,
	"Status" => null,
	"OfferSoort" => null,
);

$jsondata['metaData'] = $metaData;
$jsondata['success'] = $rows ? true : false;
$jsondata['results'] = $rows ? count($rows) : 0;
$jsondata['rows'] = $rows ? $rows : $EmptyRows;
$jsondata['msg'] = $rows ? null : 'Geen resultaten gevonden';

echo json_encode($jsondata);