<?php
require_once('includes.inc.php');
require_once('MyMobility.inc');

$BMUCore->ProviderSA(MYMOB_APP_ID)->ContractType()->GetList();
$response = $BMUCore->sendRequest();

if($response['result']['result'] != 0) sendErrorJSON($response['result']['message']);

$metaData = array(
	"idProperty" => "AbonnementSoort",
	"root" => "rows",
	"totalProperty" => "results",
	"successProperty" => "success",
	"fields" => array(
		array('name' => 'AbonnementSoort'),
		array('name' => 'Omschrijving',)
	)
);
foreach($response['result']['types'] as $type) {
	$rows[] = array('AbonnementSoort' => $type['contract_type_id'], 'Omschrijving' => $type['info']);
}

$EmptyRows[] = array(
	"AbonnementSoort" => null,
	"Omschrijving" => null,
);

$jsondata['metaData'] = $metaData;
$jsondata['success'] = $rows ? true : false;
$jsondata['results'] = $rows ? count($rows) : 0;
$jsondata['rows'] = $rows ? $rows : $EmptyRows;
$jsondata['msg'] = $rows ? null : 'Geen resultaten gevonden';

echo json_encode($jsondata);
?>
