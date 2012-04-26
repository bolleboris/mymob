<?php
require_once('machtigNieuwePas.inc.php');
require_once('includes.inc.php');

if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");

if(isset($_POST['UserId'])){
  $PersoonNr = $_POST['UserId'];
} else {
  sendErrorJSON("FOUT: Geen PersoonNr gedefinieerd!");
}

$rs = verwijderMachtigGebruiker($PersoonNr);

if($rs < 0){
	sendErrorJSON("FOUT: Kan niet verwijderen");
}

$jsondata['code'] = $rs;
$jsondata['success'] = true;

echo json_encode($jsondata);
?>
