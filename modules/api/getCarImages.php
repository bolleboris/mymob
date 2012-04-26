<?php
require_once("./includes.inc.php");

if($_SESSION['userLevel'] == 2) sendErrorJSON("Je bent als coördinator niet gemachtigd deze module te gebruiken!");

# De mappen waar deze functie toegang tot gekregen heeft.
//$Directory	= "/home/websites/www.wheels4all.nl/https/www/images-autos/groot";
$Directory	= "/home/websites/www.wheels4all.nl/https/www/images-autos/groot";
if(!$Uitlezen = @opendir($Directory)){
	sendErrorJSON("Kan afbeeldingen map niet vinden!");
}

# Zolang er een bestand in de map staat doorgaan met indexeren.
while(($Bestand = readdir($Uitlezen)) !== FALSE && $i++ < 100)
{
	clearstatcache();
   if(is_file($Directory.'/'.$Bestand)){
   	$rows[] = array(
//		  	"Filename" => $Bestand,
//		  	"Filename" => substr($Bestand, 0, (strlen($Bestand) - strpos($Bestand, '.'))),
			"Filename" => strstr($Bestand, '.', TRUE),			//Return bestandeel voor eerste punt (trim de extensie)
		  	"Num" => $i++
		 );
	}
}

# Als eenmaal alle bestanden geindexeerd zijn kan de verbinding met de map op de server gesloten worden.
closedir($Uitlezen);
sort($rows);

$jsondata['success'] = true;
$jsondata['results'] = count($rows);
$jsondata['rows'] = $rows;
$jsondata['error'] = NULL;

echo json_encode($jsondata);
?>
