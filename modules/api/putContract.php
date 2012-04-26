<?php
require_once('MyMobility.inc');
var_dump($_POST);
exit();
if(isset($_POST['ContractNr'])){
	$ContractNr = $_POST['ContractNr'];
} else {
	sendErrorJSON("FOUT: Geen ContractNr gedefinieerd!");
}

if(isset($_POST['Verantwoordelijke'])){
	$Verantwoordelijke = $_POST['Verantwoordelijke'];
} else {
	sendErrorJSON("FOUT: Geen Verantwoordelijke gedefinieerd!");
}

if(isset($_POST['Status'])){
	$Status = $_POST['Status'];
} else {
	sendErrorJSON("FOUT: Geen Status gedefinieerd!");
}

if(isset($_POST['AbonnementSoort'])){
	$AbonnementSoort = $_POST['AbonnementSoort'];
} else {
	sendErrorJSON("FOUT: Geen AbonnementSoort gedefinieerd!");
}

if(isset($_POST['AbonnementGraad'])){
	$AbonnementGraad = $_POST['AbonnementGraad'];
} else {
	sendErrorJSON("FOUT: Geen AbonnementGraad gedefinieerd!");
}
$q = "
	UPDATE
		W4AContracten c
	SET
		Verantwoordelijke = ?,
		Status = ?,
		AbonnementSoort = ?,
		AbonnementGraad = ?
	WHERE ContractNr = ?";

$b = array(
	$Verantwoordelijke,
	$Status,
	$AbonnementSoort,
	$AbonnementGraad,
	$ContractNr
);

if(!$rs = $sql->Execute($q,$b)) sendErrorJSON($sql->ErrorMsg());

$jsondata['success'] = true;
$jsondata['error'] = NULL;

echo json_encode($jsondata);
?>
