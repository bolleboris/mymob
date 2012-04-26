<?php
require_once("./includes.inc.php");

if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");

//2 losse queries, 1 voor reserveringen en 1 voor annuleringen

$QUERY = "
	SELECT 
		r.RitNr,
		r.AutoId,
		r.ReserveringVerstuurd,
		r.ReserveringBegin,
		a.Bijnaam,
		TIMEDIFF(NOW(),r.ReserveringVerstuurd) as difference
	FROM W4ARitten r 
	LEFT JOIN W4AAutos a ON a.AutoId = r.AutoId 
	WHERE 
		ReserveringVerstuurd IS NOT NULL AND 
		ReserveringBevestigd IS NULL 
	ORDER BY 
		difference ASC";
    
$rs = $sql->Execute($QUERY);

if(!$rs)	sendErrorJSON($sql->ErrorMsg());

while( !$rs->EOF ) {
	$rows[] = array(
		"Type" => "Reservering",
		"RitNr" => $rs->fields['RitNr'],
		"AutoId" => $rs->fields['AutoId'],
		"Bijnaam" => $rs->fields['Bijnaam'],
		"ReserveringBegin" => W4ATijd($rs->fields['ReserveringBegin']),		
		"verstuurd" => W4ATijd($rs->fields['ReserveringVerstuurd']),
		"difference" => $rs->fields['difference']
	);
	$rs->MoveNext();
}

$QUERY2 = "
	SELECT 
		r.RitNr,
		r.AutoId,
		r.AnnuleringVerstuurd,
		r.ReserveringBegin,
		a.Bijnaam,
		TIMEDIFF(NOW(),r.AnnuleringVerstuurd) as difference
	FROM W4ARitten r 
	LEFT JOIN W4AAutos a ON a.AutoId = r.AutoId 
	WHERE 
		AnnuleringVerstuurd IS NOT NULL AND 
		AnnuleringBevestigd IS NULL 
	ORDER BY 
		difference ASC";
    
$rs2 = $sql->Execute($QUERY2);

if(!$rs2)	sendErrorJSON($sql->ErrorMsg());

while( !$rs2->EOF ) {
	$rows[] = array(
		"Type" => "Annulering",
		"RitNr" => $rs2->fields['RitNr'],
		"AutoId" => $rs2->fields['AutoId'],
		"Bijnaam" => $rs2->fields['Bijnaam'],
		"ReserveringBegin" =>  W4ATijd($rs2->fields['ReserveringBegin']),
		"verstuurd" => W4ATijd($rs2->fields['AnnuleringVerstuurd']),
		"difference" => $rs2->fields['difference']
	);
	$rs2->MoveNext();
}

$EmptyRows[] = array(
	"Type" => null,
	"RitNr" => null,
	"AutoId" => null,
	"Bijnaam" => null,
	"ReserveringBegin" => null,
	"verstuurd" => null,
	"difference" => null
);
	
$jsondata['success'] = true;
$jsondata['results'] = count($rows) ? count($rows) : 0;
$jsondata['rows'] = $rows ? $rows : $EmptyRows;
$jsondata['error'] = NULL;

echo json_encode($jsondata);
?>
