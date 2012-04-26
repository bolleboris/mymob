<?php
require_once('includes.inc.php');

if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");

if(isset($_POST['RitNr'])){
	$RitNr = $_POST['RitNr'];
} else {
	sendErrorJSON("Geen RitNr!");
}

$QUERY = "
	SELECT 
		Tijdstip,
		Status
	FROM 
		`W4ARittenAudit`
	WHERE `RitNr` = ?
	ORDER BY 
		`Tijdstip` DESC,
		`Status` ASC;";
$rs = $sql->Execute($QUERY,array($RitNr));

if(!$rs)	sendErrorJSON($sql->ErrorMsg());

while( !$rs->EOF ) {
	if(array_key_exists((int)$rs->fields['Status'],$statusDesc)){
		$desc = $statusDesc[$rs->fields['Status']];
	}else{
		$desc = "Onbekende status";
	}
	$rows[] = array(
		"Tijdstip" => w4atijd($rs->fields['Tijdstip']),
		"Status" => $desc
	);
	$rs->MoveNext();
}

$jsondata['success'] = true;
$jsondata['results'] = count($rows);
$jsondata['rows'] = $rows ? $rows : array();
$jsondata['error'] = NULL;

echo json_encode($jsondata);
?>
