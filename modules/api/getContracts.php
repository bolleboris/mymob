<?php

require_once('MyMobility.inc');
require_once('person_attributes.inc.php');
$BMUCore->ProviderSA(MYMOB_APP_ID)->Contract()->GetList();
$rs = $BMUCore->sendRequest();


foreach ($rs['result']['contracts'] as $contract) {
   $person_attributes = get_person_attributes($contract['customer_id']);
   if ($person_attributes == false) {
	  $person_attributes = array('Voornaam' => $contract['customer_code']);
   }
   $rows[] = array(
	   "ContractNr" => $contract['contract_id'],
	   "Contractant" => $contract['customer_id'],
	   "ContractantNaam" => $person_attributes['Voornaam'] . " " . $person_attributes['Achternaam'],
	   "Status" => $contract['status'],
	   "AbonnementSoort" => $contract['contract_type_code'],
	   "AbonnementGraad" => null
   );
   $i++;

   //Limit it, anders duurt het 20 minuten ofzo
   if ($i == 100)
	  break;
}

$metaData = array(
	"idProperty" => "ContractNr",
	"root" => "rows",
	"totalProperty" => "results",
	"successProperty" => "success",
	"fields" => array(
		array('name' => 'ContractNr'),
		array('name' => 'Contractant',),
		array('name' => 'ContractantNaam',),
		array('name' => 'Status'),
		array('name' => 'AbonnementSoort'),
		array('name' => 'AbonnementGraad')
	)
);

$EmptyRows[] = array(
	"ContractNr" => null,
	"Contractant" => null,
	"ContractantNaam" => null,
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

exit();









/* $q = "SELECT
  c.ContractNr,
  c.Verantwoordelijke AS Contractant,
  CONCAT_WS(' ',p.Initialen,p.Achternaam) AS ContractantNaam,
  c.AbonnementGraad,
  a.Omschrijving AS AbonnementSoort,
  CASE
  WHEN c.status = 0 THEN 'Actief'
  WHEN c.status = 1 THEN 'Aangemeld'
  WHEN c.status = 2 THEN 'Actief'
  WHEN c.status = 3 THEN 'Opgezegd'
  END AS Status
  FROM
  W4AContracten c
  LEFT JOIN W4APersonen p ON p.PersoonNr = c.Verantwoordelijke
  LEFT JOIN W4AAbonnementTypen a ON a.AbonnementSoort = c.AbonnementSoort
  ORDER BY
  ContractNr
  ASC";
  if(!$rs = $sql->Execute($q)) sendErrorJSON($sql->ErrorMsg());

  while( !$rs->EOF ) {
  $rows[] = array(
  "ContractNr" => $rs->fields['ContractNr'],
  "Contractant" => $rs->fields['Contractant'],
  "ContractantNaam" => $rs->fields['ContractantNaam'],
  "Status" => $rs->fields['Status'],
  "AbonnementSoort" => $rs->fields['AbonnementSoort'],
  "AbonnementGraad" => $rs->fields['AbonnementGraad']
  );
  $rs->MoveNext();
  }

  $metaData = array(
  "idProperty" => "ContractNr",
  "root" => "rows",
  "totalProperty" => "results",
  "successProperty" => "success",
  "fields" => array(
  array('name' => 'ContractNr'),
  array('name' => 'Contractant',),
  array('name' => 'ContractantNaam',),
  array('name' => 'Status'),
  array('name' => 'AbonnementSoort'),
  array('name' => 'AbonnementGraad')
  )
  );

  $EmptyRows[] = array(
  "ContractNr" => null,
  "Contractant" => null,
  "ContractantNaam" => null,
  "Status" => null,
  "AbonnementSoort" => null,
  "AbonnementGraad" => null
  );

  $jsondata['metaData'] = $metaData;
  $jsondata['success'] = $rows ? true : false;
  $jsondata['results'] = $rows ? count($rows) : 0;
  $jsondata['rows'] = $rows ? $rows : $EmptyRows;
  $jsondata['msg'] = $rows ? null : 'Geen resultaten gevonden';

  echo json_encode($jsondata); */
?>
