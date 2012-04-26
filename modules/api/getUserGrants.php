<?php
require_once("includes.inc.php");

if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");

if(isset($_POST['sort'])&&($_POST['sort'])){
  $Sort = $_POST['sort'];
} else {
	//sendErrorJSON("FOUT: Geen sort gedefinieerd!");
	$Sort = 'Verloopdatum';
}

if(isset($_POST['dir'])&&($_POST['dir'])){
  $Dir = $_POST['dir'];
} else {
//   sendErrorJSON("FOUT: Geen dir gedefinieerd!");
	$Dir = 'DESC';
}

if(isset($_POST['start'])&&($_POST['start'])){
  $Start = $_POST['start'];
} else {
   $Start = 0;
}

$QUERY = "SELECT
		(SELECT COUNT(*) FROM W4ANieuwePassen) AS aantal,
		np.*,
		p.Voornaam,
		p.Initialen,
		p.Achternaam
	FROM 
		W4ANieuwePassen np
	LEFT JOIN W4APersonen p ON p.PersoonNr = np.PersoonNr
	ORDER BY np.Verloopdatum DESC
	LIMIT 13 OFFSET $Start;";
$rs = $sql->Execute($QUERY);

if(!$rs)	sendErrorJSON($sql->ErrorMsg());

$aantal = $rs->fields['aantal'];
while( !$rs->EOF ) {

	$username = (isset($rs->fields['Voornaam'])) ? $rs->fields['Initialen']." ".$rs->fields['Achternaam']." (".$rs->fields['Voornaam'].")" : $rs->fields['Initialen']." ".$rs->fields['Achternaam'];
	$rows[] = array(
		"PersoonNr" => $rs->fields['PersoonNr'],
		"Persoon" => $username,
		"Pincode" => $rs->fields['Pincode'],
		"Verloopdatum" => $rs->fields['Verloopdatum']
	);
	$rs->MoveNext();
}

$EmptyRows[] = array(
	"PersoonNr" => null,
	"Persoon" => null,
	"Pincode" => null,
	"Verloopdatum" => null
);

$jsondata['success'] = true;
$jsondata['results'] = $aantal;
$jsondata['rows'] = $rows ? $rows : $EmptyRows;
$jsondata['error'] = NULL;

echo json_encode($jsondata);
?>
