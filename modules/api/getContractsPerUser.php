<?php
//require_once('includes.inc.php');
require_once('MyMobility.inc');
if(isset($_REQUEST['userId'])) {
	$userId = $_REQUEST['userId'];
} else {
	sendErrorJSON("FOUT: Geen UserId gedefinieerd!");
}

$BMUCore->Wheels4All()->ProviderSA(MYMOB_APP_ID)->PersonContractList($userId);

$response = $BMUCore->sendRequest();


$metaData = array(
	"idProperty" => "ContractNr",
	"root" => "rows",
	"totalProperty" => "results",
	"successProperty" => "success",
	"fields" => array(
		array('name' => 'ContractNr'),
		array('name' => 'Soort',)
	)
);
foreach($response['result']['contracts'] as $contract) {
	$rows[] = array('ContractNr' => $contract['contract_id'],
				 'Soort' => $contract['relationType'],);
}

$jsondata['metaData'] = $metaData;
$jsondata['success'] = true;
$jsondata['results'] = count($rows) ? count($rows) : 0;
$jsondata['rows'] = $rows ? $rows : array();
$jsondata['error'] = NULL;

echo json_encode($jsondata);
?>
