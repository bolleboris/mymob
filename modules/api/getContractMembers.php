<?php
require_once('includes.inc.php');
require_once('MyMobility.inc');
require_once('person_attributes.inc.php');

if(isset($_POST['ContractNr'])){
  $ContractNr = $_POST['ContractNr'];
} else {
  sendErrorJSON("FOUT: Geen ContractNr gedefinieerd!");
}

/*** hoofdlid (contracthouder) komt eerst ***/

$BMUCore->ProviderSA(MYMOB_APP_ID)->Contract($ContractNr)->Info();
$response = $BMUCore->sendRequest();
if($response['result']['result'] != 0) sendErrorJSON($response['result']['message']);

$contract = $response['result'];

$person_att = get_person_attributes($contract['customer_id']);

/*$rows[] = array(
	"PersoonNr" => $contract['customer_id'],
	"Naam" => $person_att['Initialen'] . ' ' . $person_att['Achternaam'],
	"Email" => $person_att['Email'],
	"Telefoon1" => $person_att['Telefoon1'],
	"Type" => "Klant",
	"ContractNr" => $ContractNr
);*/

/*** dan alle medeleden ***/

$BMUCore->ProviderSA(MYMOB_APP_ID)->Contract($ContractNr)->Consumers();
$response = $BMUCore->sendRequest();
if($response['result']['result'] != 0) sendErrorJSON($response['result']['message']);

foreach($response['result']['consumers'] as $consumer) {
	//if($consumer['consumer_id'] != $contract['customer_id']) {
	
		$person_att = get_person_attributes($consumer['consumer_id']);

		$rows[] = array(
			"PersoonNr" => $consumer['consumer_id'],
			"Naam" => $person_att['Initialen'] . ' ' . $person_att['Achternaam'],
			"Email" => $person_att['Email'],
			"Telefoon1" => $person_att['Telefoon1'],
			"Type" => "Consument",
			"ContractNr" => $ContractNr
		);
	//}
}

$metaData = array(
	"idProperty" => "PersoonNr",
	"root" => "rows",
	"totalProperty" => "results",
	"successProperty" => "success",
	"fields" => array(
		array('name' => 'PersoonNr'),
		array('name' => 'Naam'),
		array('name' => 'Email'),
		array('name' => 'Telefoon1'),
		array('name' => 'Type'),
		array('name' => 'ContractNr')
	)
);

$EmptyRows[] = array(
	"PersoonNr" => null,
	"Naam" => null,
	"Email" => null,
	"Telefoon1" => null,
	"Type" => null,
	'ContractNr' => null
);

$jsondata['metaData'] = $metaData;
$jsondata['success'] = $rows ? true : false;
$jsondata['results'] = $rows ? count($rows) : 0;
$jsondata['rows'] = $rows ? $rows : $EmptyRows;
$jsondata['msg'] = $rows ? null : 'Geen resultaten gevonden';

echo json_encode($jsondata);
?>
