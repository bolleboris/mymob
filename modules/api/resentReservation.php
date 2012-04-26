<?php
require_once('reserveringen.inc.php');
require_once('includes.inc.php');

if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");

if(isset($_POST['RitNr'])){
  $RitNr = $_POST['RitNr'];
} else {
  sendErrorJSON("FOUT: Geen RitNr gedefinieerd!");
}


if(ReserveringInit()) sendErrorJSON("FOUT: Init reservering is mislukt");

$rs = stuurRitOpnieuw($RitNr);

if($rs != 0){
	if($rs === -31){
		sendErrorJSON("Deze rit is nog niet klaar om te worden verstuurd, is geannuleerd of is al voorbij (code: $rs)");
	} else {
		sendErrorJSON("Onbekende fout: (code $rs)");
	}
}

$jsondata['success'] = true;
echo json_encode($jsondata);
?>
