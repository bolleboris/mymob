<?php
require_once('adodb5/adodb.inc.php');
require_once('/home/websites/secure/database.inc');


$sql = ADONewConnection($servertype);
$connectiontest = $sql->Connect($server,$user,$password,$database);
if(!$connectiontest) die("Could not connect to database");


echo "Deze module kijkt of kilometerstanden van opvolgende ritten aansluiten.<br>";

function TestKmConsist($autoid){
	global $sql;
	$query = "SELECT 
		r. * , 
		(r.KilometerstandEind - r.KilometerstandBegin) AS afstand
	FROM `W4ARitten` r
		LEFT JOIN W4AAutos a ON a.AutoId = r.AutoId
		WHERE r.AutoId = ? AND r.KilometerstandEind IS NOT NULL
	ORDER BY `r`.`GebruikBegin` ASC";

	$rs = $sql->Execute($query,$autoid);

	if(!$rs) die("Query mislukt: ".$sql->ErrorMsg());
	if($rs->EOF){
		echo("Er zijn nog geen ritten bekend voor auto ".$autoid."<br>");
		return;
	}


	$standEind = $rs->fields['KilometerstandEind'];
	$rs->MoveNext();
	
	while( !$rs->EOF ) {
		if($rs->fields['KilometerstandBegin'] != $standEind){
			echo "Auto ".$autoid." Ritnr: ".$rs->fields['RitNr']." van: ".$standEind." naar: ".$rs->fields['KilometerstandBegin']." (".abs($rs->fields['KilometerstandBegin']-$standEind)."km";
			echo ($rs->fields['KilometerstandBegin'] - $standEind) > 0 ? " verloren)<br>" : " teveel)<br>";
			return;
		}
		$standEind = $rs->fields['KilometerstandEind'];
		$rs->MoveNext();
	}
	echo "Auto ".$autoid.": OK!<br>";
}

$query = "SELECT a.AutoId
	FROM W4AAutos a";
$rs = $sql->Execute($query,$autoid);
if(!$rs) die("Query mislukt: ".$sql->ErrorMsg());
if($rs->EOF) die("Geen autos gevonden");

while( !$rs->EOF ) {
	TestKmConsist($rs->fields['AutoId']);
	$rs->MoveNext();
}
echo "DONE!";
?>


