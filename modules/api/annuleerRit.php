<?php
require_once('includes.inc.php');

if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");

if(isset($_POST['RitNr'])){
  $Reservering['RitNr'] = $_POST['RitNr'];
} else {
  sendErrorJSON("FOUT: Geen RitNr gedefinieerd!");
}


if(isset($_POST['UserId'])){
  $Reservering['PersoonNr'] = $_POST['UserId'];
} else {
  sendErrorJSON("FOUT: Geen PersoonNr gedefinieerd!");
}

if(ReserveringInit()) sendErrorJSON("FOUT: Init reservering heeft gefaald");

$rs = ReserveringAnnuleren($Reservering,true);

if($rs != 0){
	if($rs === -26){
		sendErrorJSON("Deze rit is reeds geannuleerd (code -26)");
	} else {
		sendErrorJSON("Onbekende fout: (code $rs)");
	}
}

$jsondata['success'] = true;

echo json_encode($jsondata);
?>
