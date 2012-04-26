<?php
require_once("includes.inc.php");


if($_SESSION['userLevel'] == 2){
	$extra = "AND (a.Latitude BETWEEN '".number_format($_SESSION['baseLat']-$radius,6,'.','')."' AND '".number_format($_SESSION['baseLat']+$radius,6,'.','')."') AND (a.Longitude BETWEEN '".number_format($_SESSION['baseLon']-$radius,6,'.','')."' AND '".number_format($_SESSION['baseLon']+$radius,6,'.','')."') ";
}else{
	$extra = "";
}


//LM 28-06-2011 extra controle in WHERE clause toegevoegd zodat er geen ritten worden getoond waarvan de rit nog bezig is.

//Een geheel nieuwe opzet van de query.
//We selecteren eerst alle auto's die voldoen aan de eisen boordcomputer en actief. Dan zoeken per auto met een subquery het laatste instapmoment (Audit status = 5).
$QUERY = "
SELECT 
	r3.RitNr,
	r3.AutoId,
	r3.ReserveringBegin,
	r3.ReserveringEind,
	rs1.Bijnaam,
	rs1.Woonplaats
FROM (
SELECT
	auto1.Bijnaam,
	ad.Woonplaats,
	(SELECT
		r2.RitNr
	FROM
		W4ARitten r2
	LEFT JOIN W4ARittenAudit aud ON aud.RitNr = r2.RitNr
	WHERE
		r2.AutoId = auto1.AutoId AND
		Geannuleerd IS NULL AND
		aud.Status = 5
	ORDER BY
		ReserveringBegin DESC
	LIMIT 1) AS RitNr
FROM
	W4AAutos auto1
LEFT JOIN W4AAdressen ad ON ad.AdresId = auto1.AdresId	
WHERE
	FIND_IN_SET('Boordcomputer',auto1.Opties) AND
	auto1.Actief = 1
ORDER BY AutoId) rs1
LEFT JOIN W4ARitten r3 ON r3.RitNr = rs1.RitNr
WHERE
	r3.ReserveringEind <= now() AND
	r3.BevestigdDoorAuto IS NULL;";


/*$QUERY = "
	SELECT
		MAX(aud.Tijdstip) AS tijd,
		r.AutoId,
		a.Bijnaam,
		ad.Woonplaats
	FROM 
		W4ARittenAudit aud
	LEFT JOIN W4ARitten r ON r.RitNr = aud.RitNr
	LEFT JOIN W4AAutos a ON a.AutoId = r.AutoId
	LEFT JOIN W4AAdressen ad ON ad.AdresId = a.AdresId	
	WHERE 
		aud.Status = 5 AND
		a.Actief = 1 AND
		FIND_IN_SET('Boordcomputer',a.Opties) AND
		r.AutoId NOT IN (SELECT AutoId FROM W4ARitten WHERE ReserveringBegin <= NOW() AND ReserveringEind >= NOW() AND Geannuleerd IS NULL)
		$extra
	GROUP BY
		r.AutoId
	ORDER BY 
		tijd ASC";*/
    
$rs = $sql->Execute($QUERY,$AutoId);

if(!$rs)	sendErrorJSON($sql->ErrorMsg());

while( !$rs->EOF ) {
	$geparkeerd = time()-strtotime($rs->fields['ReserveringEind']);
	
	$dagen = floor($geparkeerd / 86400);
	$uren = floor(($geparkeerd % 86400) / 3600);
	$minuten = floor((($geparkeerd % 86400) % 3600) / 60);
	
	/*if($dagen == 0) $geparkeerdStr = $uren." uur ".$minuten." min";
	else if($dagen == 1) $geparkeerdStr = $dagen." dag, ".$uren." uur ".$minuten." min";
	else $geparkeerdStr = $dagen." dagen, ".$uren." uur ".$minuten." min";*/
	
	if($dagen >= 5){
		$rows[] = array(
			"AutoId" => $rs->fields['AutoId'],
			"Bijnaam" => $rs->fields['Bijnaam'],
			"Plaats" => $rs->fields['Woonplaats'],
			"LaatsteInstap" => w4atijd($rs->fields['ReserveringBegin']),
			"ReserveringEind" => w4atijd($rs->fields['ReserveringEind']),
			"Geparkeerd" => $dagen." dagen, ".$uren." uur en ".$minuten." minuten"
		);
	}
	$rs->MoveNext();
}

$metaData = array(
	"idProperty" => "AutoId",
	"root" => "rows",
	"totalProperty" => "results",
	"successProperty" => "success",
	"fields" => array(
		array('name' => 'AutoId'),
		array('name' => 'Bijnaam'),
		array('name' => 'Plaats'),
		array('name' => 'LaatsteInstap'),
		array('name' => 'ReserveringEind'),
		array('name' => 'Geparkeerd')
	)
);

$EmptyRows[] = array(
	"AutoId" => null,
	"Bijnaam" => null,
	"Woonplaats" => null,
	"LaatsteInstap" => null,
	"ReserveringEind" => null,
	"Geparkeerd" => null
);

$jsondata['metaData'] = $metaData;
$jsondata['success'] = $rows ? true : false;
$jsondata['results'] = $rows ? count($rows) : 0;
$jsondata['rows'] = $rows ? $rows : $EmptyRows;
$jsondata['msg'] = $rows ? null : 'Geen resultaten gevonden';

echo json_encode($jsondata);
?>
