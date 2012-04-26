<?php
require_once('includes.inc.php');

$dagen = 30;

if($_SESSION['userLevel'] == 2){
	$extra = "AND (a.Latitude BETWEEN '".number_format($_SESSION['baseLat']-$radius,6,'.','')."' AND '".number_format($_SESSION['baseLat']+$radius,6,'.','')."') AND (a.Longitude BETWEEN '".number_format($_SESSION['baseLon']-$radius,6,'.','')."' AND '".number_format($_SESSION['baseLon']+$radius,6,'.','')."') ";
}else{
	$extra = "";
}

function getCarHours($autoid,$dagen){
	global $sql;
	$query = "
		SELECT 
			RitNr,
			ReserveringBegin,
			ReserveringEind
		FROM 
			W4ARitten
		WHERE AutoId = ? AND
		ReserveringBegin > (NOW()- INTERVAL ? DAY) AND
		ReserveringEind < NOW() AND
		Geannuleerd IS NULL
		";
	$rs = $sql->Execute($query,array($autoid,$dagen));

	if(!$rs){
		sendErrorJSON("Geen resulaten: ".$sql->ErrorMsg());
	}

	while( !$rs->EOF ) {
		$daybegin = date('z',strtotime($rs->fields['ReserveringBegin']));			//Dag van jaar
		$dayend = date('z',strtotime($rs->fields['ReserveringEind']));				//Dag van jaar
		$hourbegin = date('G',strtotime($rs->fields['ReserveringBegin']));
		$minbegin = date('i',strtotime($rs->fields['ReserveringBegin']))/60;	
		$hourend = date('G',strtotime($rs->fields['ReserveringEind']));
		$minend = date('i',strtotime($rs->fields['ReserveringEind']))/60;	
		
		$totaldays = ($dayend - $daybegin)+1;		//Dit werkt niet wanneer een reservering een jaarwisseling overspant.
		if($totaldays <= 0) $totaldays += 365;		//Daarom deze test. Indien het aantal dagen negatief is, dan betreft dit een overspanning van een jaarwisseling.
		
		if($totaldays == 1){		//1 dag
			$totalhours = getHoursFromDay(($hourbegin+$minbegin),($hourend+$minend));
		}
		else{		//Meerdere dagen
			$totalhours = 
				getHoursFromDay(($hourbegin+$minbegin),23)+		//Eerste dag
				(($totaldays-2)*10)+										//Middelste hele dagen (altijd 10 uur)
				getHoursFromDay(0,($hourend+$minend));				//Laatste dag
		}
		$subtotalhours += $totalhours;
		$rs->MoveNext();
	}
	return $subtotalhours;
}

/*
Deze functie berekend het aantal factureerbare uren binnen 1 etmaal. 
Deze functie verwacht de tijd als decimaal getal, bijvoorbeeld 6,5 is 07:30 uur.
*/
function getHoursFromDay($begintime,$endtime){
	if($begintime < 7) $begintime = 7;
	if($begintime > 23) $begintime = 23;
	
	if($endtime < 7) $endtime = 7;
	if($endtime > 23) $endtime = 23;
	
	if($endtime < $begintime) sendErrorJSON("Begintijd ligt na eindtijd");
	$uren = $endtime - $begintime;
	if($uren > 10) $uren = 10;
	return $uren;
}

$rs = $sql->execute("
SELECT
		a.AutoId,
		a.Kenteken,
		a.Bijnaam,
		ad.Woonplaats
	FROM 
		W4AAutos a
	LEFT JOIN W4AAdressen ad ON a.AdresId = ad.AdresId
	WHERE 
		Actief = 1 $extra");
		
if(!$rs)sendErrorJSON("Fout in query: ".$sql->ErrorMsg());

/*
while(!$rs->EOF){
	$subtotalhours = getCarHours($rs->fields['AutoId'],$dagen);
	echo "<p>Van alle ritten waarvan de EINDtijd verstreken is, en de BEGINtijd maximaal ".$dagen." geleden is van auto ".$rs->fields['AutoId']." is een totaal van ".$subtotalhours." uur.<br>";
	echo "Dat is gemiddeld ".round(($subtotalhours/$dagen),2)." uur per dag, wat een bezetting is van ".round(($subtotalhours/$dagen)*10)."%<br></p><br>";
	$rs->MoveNext();
}*/

while( !$rs->EOF ) {
	$hours = getCarHours($rs->fields['AutoId'],$dagen);
	$rows[] = array(
		"AutoId" => $rs->fields['AutoId'],
		"Kenteken"=> $rs->fields['Kenteken'],
		"Bijnaam"=> $rs->fields['Bijnaam'],
		"Plaats"=> $rs->fields['Woonplaats'],		
		"Uren" => $hours,
		"Bezetting" => round(($hours/$dagen)*10)
	);
	$rs->MoveNext();
}			

$jsondata['success'] = true;
$jsondata['results'] = count($rows);
$jsondata['rows'] = $rows;
$jsondata['error'] = NULL;

echo json_encode($jsondata);

?>
