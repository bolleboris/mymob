<?php
require_once('includes.inc.php');
require_once('MyMobility.inc');

if(isset($_POST['ContractNr'])){
  $ContractNr = $_POST['ContractNr'];
} else {
  sendErrorJSON("FOUT: Geen ContractNr gedefinieerd!");
}



	
$BMUCore->ProviderSA(100)->Contract($ContractNr)->Info();
$response = $BMUCore->sendRequest();

if($result['result']['result'] != 0) sendErrorJSON($result['result']['message']);


$rows[] = array(
	"ContractNr" => $ContractNr,
	"Verantwoordelijke" => $response['result']['customer_id'],
	"Status" => $response['result']['status'], 
	"AbonnementSoort" => $response['result']['contract_type_id'], 
	"AbonnementGraad" => 'Vol',
);

$metaData = array(
	"idProperty" => "ContractNr",
	"root" => "rows",
	"totalProperty" => "results",
	"successProperty" => "success",
	"fields" => array(
		array('name' => 'ContractNr'),
		array('name' => 'Verantwoordelijke',),
		array('name' => 'Status'),
		array('name' => 'AbonnementSoort'),
		array('name' => 'AbonnementGraad')
	)
);

$EmptyRows[] = array(
	"ContractNr" => null,
	"Verantwoordelijke" => null,
	"Status" => null,
	"AbonnementSoort" => null,
	"AbonnementGraad" => null
);

$jsondata['metaData'] = $metaData;
$jsondata['success'] = $rows ? true : false;
$jsondata['results'] = $rows ? count($rows) : 0;
$jsondata['rows'] = $rows ? $rows : $EmptyRows;
$jsondata['msg'] = $rows ? null : 'Geen resultaten gevonden';

echo json_encode($jsondata);
?>
