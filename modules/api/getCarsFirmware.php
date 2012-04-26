<?php
require_once('includes.inc.php');

if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");

$QUERY = "SELECT
		a.AutoId,
		a.Merk,
		a.Bijnaam,
		a.Kenteken,
		ad.Woonplaats,
		ad.Straatnaam
	FROM 
		W4AAutos a
	LEFT JOIN W4AAdressen ad ON a.AdresId = ad.AdresId
	WHERE
		FIND_IN_SET('Boordcomputer',a.Opties)
	ORDER BY 
		ad.Woonplaats, 
		a.Bijnaam 
	ASC";
$rs = $sql->Execute($QUERY,$AutoId);

if(!$rs)	sendErrorJSON($sql->ErrorMsg());

while( !$rs->EOF ) {
	$autostring = "%<FahrzeugId>".$rs->fields['AutoId']."</FahrzeugId>%";
	$QUERY = "SELECT 
			MsgTarget, 
			MsgContentDA_CCOM as Msg
		FROM 
			`W4ACCOMLog`
		WHERE 
			`MsgContentDA_CCOM` LIKE '".$autostring."' AND
			MsgSubject = 'Fahrdaten/Identifikation Universal [50]'
		ORDER BY `StateTimeStamp` DESC
		LIMIT 1";
	$rs2 = $sql->Execute($QUERY);
	if(!$rs2) sendErrorJSON($sql->ErrorMsg());
	if($rs2->fields['Msg']){			//Soms zitter er wel eens lege velden tussen?
		$XML = new SimpleXMLElement($rs2->fields['Msg']);
	 
		$rows[] = array(
			"AutoId" => $rs->fields['AutoId'], 
			"Merk" => $rs->fields['Merk'], 
			"Bijnaam" => $rs->fields['Bijnaam'], 
			"Kenteken" => $rs->fields['Kenteken'], 
			"Plaats" => $rs->fields['Woonplaats'], 
			"Straat" => $rs->fields['Straatnaam'],
			"TelNr" => $rs2->fields['MsgTarget'],
			"FW" => $XML->{'SWVersion'}.""
		);
	}
	$rs->MoveNext();
}

$jsondata['success'] = true;
$jsondata['results'] = count($rows);
$jsondata['rows'] = $rows;
$jsondata['error'] = NULL;

echo json_encode($jsondata);
?>
