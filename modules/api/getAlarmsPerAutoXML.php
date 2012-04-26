<?php
require_once('includeXML.inc.php');

//if($_SESSION['userLevel'] == 2) sendErrorXML("Je bent als coördinator niet gemachtigd deze module te gebruiken!");

if(isset($_POST['AutoId'])&&($_POST['AutoId'])){
  $AutoId = $_POST['AutoId'];
} else {
   sendErrorXML("AutoId niet ontvangen");
}

if(isset($_POST['start'])&&($_POST['start'])){
  $Start = $_POST['start'];
} else {
   $Start = 0;
}

if(isset($_POST['limit'])&&($_POST['limit'])){
  $Limit = $_POST['limit'];
} else {
   $Limit = 13;
}

if(isset($_POST['sort'])&&($_POST['sort'])){
  $Sort = $_POST['sort'];
} else {
   $Sort = 'StateTimeStamp';
}

if(isset($_POST['dir'])&&($_POST['dir'])){
  $Dir = $_POST['dir'];
} else {
   $Dir = 'DESC';
}

//AutoStr in HEREDOC notatie, vanwege alle speciale karakters
$AutoIdStr = <<<STR
%<FahrzeugId>$AutoId</FahrzeugId>%
STR;

$QUERY = "SELECT
		(SELECT COUNT(*) FROM W4ACCOMLog log WHERE log.MsgSubject = 'Alarms [16]' AND	log.MsgContentDA_CCOM LIKE ?) AS totaal,
		log.MsgId,
		log.MsgContentDA_CCOM as Msg,
		log.StateTimeStamp
	FROM 
		W4ACCOMLog log
	WHERE log.MsgSubject = 'Alarms [16]' AND
	log.MsgContentDA_CCOM LIKE ?
	ORDER BY 
		$Sort $Dir
	LIMIT $Limit
	OFFSET $Start";
$rs = $sql->Execute($QUERY,array($AutoIdStr, $AutoIdStr));

if(!$rs)	sendErrorXML($sql->ErrorMsg());

$totaal = ($rs->fields['totaal']) ? $rs->fields['totaal'] : 0;

echo "<dataset><results>".$totaal."</results>";
while( !$rs->EOF ) {
	$XML = new SimpleXMLElement($rs->fields['Msg']);
	echo "<row>";
	echo "<id>".$rs->fields['MsgId']."</id>";
	echo "<autoid>".$XML->{'FahrzeugId'}."</autoid>";
	echo "<StateTimeStamp>".$rs->fields['StateTimeStamp']."</StateTimeStamp>";
	echo "<AlarmDat>".$XML->{'AlarmDat'}."</AlarmDat>";
	echo "<AlarmNummer>".$XML->{'AlarmNummer'}."</AlarmNummer>";
	echo "<AlarmSubNr>".$XML->{'BCMode'}."</AlarmSubNr>";
	echo "<SWVersion>".$XML->{'SWVersion'}."</SWVersion>";
	echo "<SNR>".round($XML->{'SignalQualitaet'}*(100/33))."%</SNR>";
	echo "<AlarmDesc>".ConvertWarning($XML->{'AlarmNummer'}+0)."</AlarmDesc>";
	echo "</row>";
	unset($XML);
	$rs->MoveNext();
}
echo "</dataset>";
?>
