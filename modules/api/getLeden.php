<?php
require_once('includes.inc.php');

if($_SESSION['userLevel'] == 2){
	$extra = "WHERE (ad.Latitude BETWEEN '".number_format($_SESSION['baseLat']-$radius,6,'.','')."' AND '".number_format($_SESSION['baseLat']+$radius,6,'.','')."') AND (ad.Longitude BETWEEN '".number_format($_SESSION['baseLon']-$radius,6,'.','')."' AND '".number_format($_SESSION['baseLon']+$radius,6,'.','')."') AND";
}else{
	$extra = "";
}

$QUERY = "SELECT
		p.PersoonNr,
		CONCAT_WS(', ',p.Achternaam,p.Tussenvoegsels) as Achternaam,
		p.Voornaam,
		CONCAT_WS(' ',ad.Straatnaam,ad.Huisnr) as Adres,
		ad.Woonplaats,
		CASE WHEN p.AnoniemEmail = 1 THEN \"Anoniem\" ELSE p.Email END AS Email,
		CASE WHEN p.anoniemTelefoon1 = 1 THEN \"Anoniem\" ELSE p.Telefoon1 END AS Telefoon1,
		CASE WHEN p.anoniemTelefoon2 = 1 THEN \"Anoniem\" ELSE p.Telefoon2 END AS Telefoon2
	FROM 
		W4APersonen p
	LEFT JOIN W4AAdressen ad ON p.AdresId = ad.AdresId
		$extra (p.opgezegdPer IS NULL OR p.opgezegdPer > NOW())
	ORDER BY 
		ad.Woonplaats
	ASC";
if(!$rs = $sql->Execute($QUERY,$AutoId)) sendErrorJSON($sql->ErrorMsg());

while( !$rs->EOF ) {
	$rows[] = array(
		"PersoonNr" => $rs->fields['PersoonNr'], 
		"Achternaam" => $rs->fields['Achternaam'], 
		"Voornaam" => $rs->fields['Voornaam'], 
		"Adres" => $rs->fields['Adres'], 
		"Woonplaats" => $rs->fields['Woonplaats'], 
		"Email" => $rs->fields['Email'],
		"Telefoon1" => $rs->fields['Telefoon1'],
		"Telefoon2" => $rs->fields['Telefoon2'],
	);
	$rs->MoveNext();
}

$metaData = array(
	"idProperty" => "PersoonNr",
	"root" => "rows",
	"totalProperty" => "results",
	"successProperty" => "success",
	"fields" => array(
		array('name' => 'PersoonNr'),
		array('name' => 'Achternaam',),
		array('name' => 'Voornaam'),
		array('name' => 'Adres'),
		array('name' => 'Woonplaats'),
		array('name' => 'Email'),
		array('name' => 'Telefoon1'),
		array('name' => 'Telefoon2')
	)
);

$EmptyRows[] = array(
	"PersoonNr" => null,
	"Achternaam" => null,
	"Voornaam" => null,
	"Adres" => null,
	"Woonplaats" => null,
	"Email" => null,
	"Telefoon1" => null,
	"Telefoon2" => null
);

$jsondata['metaData'] = $metaData;
$jsondata['success'] = $rows ? true : false;
$jsondata['results'] = $rows ? count($rows) : 0;
$jsondata['rows'] = $rows ? $rows : $EmptyRows;
$jsondata['msg'] = $rows ? null : 'Geen resultaten gevonden';

echo json_encode($jsondata);
?>
