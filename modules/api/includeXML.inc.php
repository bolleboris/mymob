<?php
require_once('adodb5/adodb.inc.php');
require_once('database.inc');
require_once('timeConvert.inc.php');

header("Content-type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: 0");

date_default_timezone_set('Europe/Amsterdam');
setlocale(LC_ALL, 'nl_NL');

//Voor lange sessies is het belangrijk dat de garbage collector niet de cookie op eet. (Om nom nom!)
ini_set('session.gc_maxlifetime',12*60*60);		//Zet de max lifetime van de garbage collector op 12 uur
ini_set('session.gc_probability',1);			
ini_set('session.gc_divisor',1);


session_start();
if(strcmp($_SERVER['REMOTE_ADDR'],$_SERVER['SERVER_ADDR']) != 0){
	if(!isset($_SESSION['username'])) sendErrorXML("User not logged in");
}

if(!isset($sql)){
	$sql = ADONewConnection($servertype);
	if(!$sql->Connect($server,$user,$password,$database)) sendErrorXML("Could not connect to database");
	$sql->Execute("SET NAMES 'UTF8';");
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";

function sendErrorXML($errorMsg){
	die("<result><error>$errorMsg</error></result>");
}

$WarnDesc = array(
	2	=>	"GSM fout bij \"Xon/Xoff\" commando.",
	7	=>	"GSM fout bij invoeren van PIN",
	8	=>	"GSM fout bij het vragen van PIN",
	10 => "Registratie fout met GSM netwerk.",
	13 => "GSM fout: \"stopping a connection\".",
	16 => "GSM fout: \"fout bij betreden slaapstand.",
	18 => "GSM fout bij \"Switch off\" commando.",	
	20 => "Fout tijdens decoderen van SMS bericht.",
	28 => "GSM fout bij \"Set modem type for data connection\" commando.",
	30 => "GSM fout: \"Signaleer inkomend SMS bericht.",
	34 => "GSM fout: \"fout bij verlaten slaapstand.",
	35 => "GSM fout: \"fout tijdens verkrijgen van GPS locatie.",
	36 => "GSM fout: \"fout tijdens zetten van GPS modus.",	
	46 => "GSM fout bij \"Set/clear result codes for changing net registration state\" commando.",
	54 => "Fout in functie \"Reservation checks\" bij het aanmelden.",
	76 => "Tijd verkeerd ingesteld op boordcomputer.",
	77 => "Boordcomputer reset na softwarecrash.",
	78 => "Boordcomputer reset na softwarecrash.",
	79 => "Boordcomputer reset na softwarecrash.",
	80 => "Boordcomputer is opnieuw opgestart na spanningsverlies.",
	81 => "Boordcomputer is opnieuw opgestart na lege accu.",
	82 => "Onverwachte reset van boordcomputer",
	83 => "Auto gereden zonder dat een gebruiker zich heeft aangemeld.",
	85 => "Autoaccu bijna leeg!",
	86 => "Kilometerstand verloren gegaan.",
	87 => "Verkeerde tankpas geplaatst.",
	94 => "Boardcomputer reset na firmware update",
	96 => "Tankpas verwijderd buiten reservering om en zonder aanmelding."
);

function ConvertWarning($WarnNr){
	global $WarnDesc;
	if(isset($WarnDesc[$WarnNr])){
		return $WarnDesc[$WarnNr];
	}else{
		return "Onbekend";			//Laagst actieve auto
	}
}
?>
