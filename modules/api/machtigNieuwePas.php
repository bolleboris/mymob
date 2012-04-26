<?php
require_once('machtigNieuwePas.inc.php');
require_once('includes.inc.php');

if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");

if(isset($_POST['UserId'])){
  $PersoonNr = $_POST['UserId'];
} else {
  sendErrorJSON("FOUT: Geen PersoonNr gedefinieerd!");
}

$rs = machtigGebruiker($PersoonNr);

if($rs < 0){
	switch($rs){
		case ERR_USER_UNKNOWN :
			sendErrorJSON("FOUT: Ongeldige relatienummer");
			break;
		case ERR_TABLE_FULL :
			sendErrorJSON("FOUT: De pincode tabel is vol!");
			break;
		case ERR_USER_ALREADY_GRANTED :
			sendErrorJSON("FOUT: Deze gebruiker is alreeds gemachtigd");
			break;
		default:
			sendErrorJSON("FOUT: Onbekende fout");
			break;
	}
}

$jsondata['code'] = $rs;
$jsondata['success'] = true;

echo json_encode($jsondata);
?>
