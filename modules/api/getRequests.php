<?php
require_once('includes.inc.php');

if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");

if(isset($_POST['xaction'])){
	switch($_POST['xaction']) {
		case 'update' :
			updatedata($_POST['rows']);
			break;
		case 'read' :
			readdata();
			break;
		default:
			sendErrorJSON("Onbekend actie ".$_POST['xaction']);
			break;
	}



}

function updatedata($json_data){
	global $sql;
	$data = json_decode($json_data,true);
	
	//Note: Deze functie kan worden genomineerd als meest ranzige evarrr... maargoed, Ronald heeft haast, het mag niks kosten en ik weet niet hoe ik het anders moet oplossen...
	
	if(is_numeric(key($data))){		//More then one record is altered, so array is multi dimensional
		foreach ($data as $record) {
			$q = "UPDATE W4ARitten SET ";
			while(key($record) != "RitNr" && $i++ < 10){
				$val = (current($record) == "Goedgekeurd") ? 0 : 1;
				$updates[] = key($record)." = ".$val;		//Store all updates in an array, so it can be exploded with a ',' seperator
				if(next($record) === false) break;
			}
		
			$q .= implode(',', $updates)." WHERE RitNr = ".$record['RitNr'].";";
		
			if(!$rs = $sql->Execute($q)) sendErrorJSON($sql->ErrorMsg()."\nquery = ".$q);
		}
	}else{		//Just one record is altered, so array has 1 dimension
		$record = $data;
		$q = "UPDATE W4ARitten SET ";
		while(key($record) != "RitNr" && $i++ < 10){
			$val = (current($record) == "Goedgekeurd") ? 0 : 1;
			$updates[] = key($record)." = ".$val;		//Store all updates in an array, so it can be exploded with a ',' seperator
			if(next($record) === false) break;
		}
	
		$q .= implode(',', $updates)." WHERE RitNr = ".$record['RitNr'].";";
	
		if(!$rs = $sql->Execute($q)) sendErrorJSON($sql->ErrorMsg()."\nquery = ".$q);
	}
	$jsondata['success'] = true;
	$jsondata['results'] = 0;

	echo json_encode($jsondata);
}

function readdata(){
	global $sql;
	
	$QUERY = "SELECT
			r.RitNr,
			r.AutoId,
			a.Bijnaam,
			r.PersoonNr,
			r.ReserveringBegin,
			r.ReserveringEind,
			CONCAT_WS(' ',p.Initialen,p.Achternaam) AS ReserveerderNaam,
			CONCAT_WS(' ',b.Initialen,b.Achternaam) AS BeheerderNaam,
			r.InAanvraagMywheels,
			r.InAanvraagBeheerder
		FROM 
			W4ARitten r
		LEFT JOIN W4APersonen p ON p.PersoonNr = r.PersoonNr
		LEFT JOIN W4AAutos a ON a.AutoId = r.AutoId
		LEFT JOIN W4APersonen b ON b.PersoonNr = a.BeheerdersId
		WHERE
			InAanvraagMywheels = 1 OR InAanvraagBeheerder = 1 AND
			ReserveringEind > NOW();
		";
	
	if(!$rs = $sql->Execute($QUERY,$AutoId)) sendErrorJSON($sql->ErrorMsg());

	while( !$rs->EOF ) {
		$rs->fields['InAanvraagMywheels'] = ($rs->fields['InAanvraagMywheels'] == 1) ? 'In aanvraag' : 'Goedgekeurd';
		$rs->fields['InAanvraagBeheerder'] = ($rs->fields['InAanvraagBeheerder'] == 1) ? 'In aanvraag' : 'Goedgekeurd';
	
		$rows[] = $rs->fields;
		$rs->MoveNext();
	}

	$metaData = array(
		"idProperty" => "RitNr",
		"root" => "rows",
		"totalProperty" => "results",
		"successProperty" => "success",
		"fields" => array(
			array('name' => 'RitNr'),
			array('name' => 'AutoId',),
			array('name' => 'Bijnaam'),
			array('name' => 'ReserveerderNaam'),
			array('name' => 'BeheerderNaam'),
			array('name' => 'InAanvraagMywheels', 'id' => 'InAanvraagMywheels'),
			array('name' => 'InAanvraagBeheerder', 'id' => 'InAanvraagBeheerder')
		)
	);

	$EmptyRows[] = array(
		"RitNr" => null,
		"AutoId" => null,
		"Bijnaam" => null,
		"ReserveerderNaam" => null,
		"BeheerderNaam" => null,
		"InAanvraagMywheels" => null,
		"InAanvraagBeheerder" => null
	);

	$jsondata['metaData'] = $metaData;
	$jsondata['success'] = true;
	$jsondata['results'] = $rows ? count($rows) : 0;
	$jsondata['rows'] = $rows ? $rows : $EmptyRows;

	echo json_encode($jsondata);
}
?>
