<?php
require_once('adodb5/adodb.inc.php');
require_once('database.inc');
require_once('timeConvert.inc.php');

$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
date_default_timezone_set('Europe/Amsterdam');
setlocale(LC_ALL, 'nl_NL');

//Voor lange sessies is het belangrijk dat de garbage collector niet de cookie op eet. (Om nom nom!)
ini_set('session.gc_maxlifetime',12*60*60);		//Zet de max lifetime van de garbage collector op 12 uur
ini_set('session.gc_probability',1);			
ini_set('session.gc_divisor',1);

header('Content-Type: text/plain; charset=utf-8');
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: 0");

//$radius = 0.161987041;		//Circa 30 km in GPS graden
$radius = 0.11;		//Circa 20 km in GPS graden

session_start();

//Omdat de XLS export functies via fopen de output van een proxyfile aanroepen, zouden die de onderstaande error krijgen. De server zelf mag daarom zonder in te loggen toegang hebben.
if(strcmp($_SERVER['REMOTE_ADDR'],$_SERVER['SERVER_ADDR']) != 0){
	//if(!isset($_SESSION['username'])) sendErrorJSON("Je bent niet ingelogd of je sessie is verlopen.<br/>Log opnieuw in. IP: ".$_SERVER['REMOTE_ADDR']);
	if(!isset($_COOKIE["loginCookie"])) sendErrorJSON("Je bent niet ingelogd of je sessie is verlopen.<br/>Log opnieuw in. IP: ".$_SERVER['REMOTE_ADDR']);
}
$sql = ADONewConnection($servertype);
if(!@$sql->Connect($server,$user,$password,$database)) sendErrorJSON("Kan niet verbinden met de database<br/>".$sql->ErrorMsg());
if(!@$sql->Execute("SET NAMES 'UTF8';")) sendErrorJSON("Fout in query:<br/>".$sql->ErrorMsg());

//Stadia voor ritten audit tabel
$statusDesc = array(
	0 => "Reservering gemaakt",
	1 => "Reservering gewijzigd",
	2 => "Reservering verstuurd naar CCOM",
	3 => "Reservering verstuurd naar auto",
	4 => "Aankomst reservering bij auto bevestigd",
	5 => "Gebruiker is ingestapt",
	6 => "Ritinfo van auto ontvangen",
	7 => "Reservering handmatig opnieuw verstuurd",
	8 => "Annulering verstuurd naar CCOM",
	9 => "Annulering verstuurd naar auto",
	10 => "Aankomst annulering bij auto bevestigd",
	11 => "Rit gestopt via mobiele reserveerpagina",
	12 => "Rit verlengt via mobiele reserverpagina",
	98 => "Papieren ritstaat verwerkt",
	99 => "Geannuleerd"
);

$ContractStatusDesc = array(
	0 => "Niet actief",
	1 => "Aangemeld",
	2 => "Actief",
	3 => "Opgezegd"
);


?>
