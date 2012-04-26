<?php

/* Simple XLS generator. For Wheels4All
25-jan-2011 Léon Melis
*/

function xlsBOF() {
    echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);  
    return;
}

function xlsEOF() {
    echo pack("ss", 0x0A, 0x00);
    return;
}

function xlsWriteNumber($Row, $Col, $Value) {
    echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
    echo pack("d", $Value);
    return;
}

function xlsWriteString($Row, $Col, $Value ) {
    $L = strlen($Value);
    echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
    echo $Value;
return;
} 

function xlsWriteHeader($filename){
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=$filename.xls ");
	header("Content-Transfer-Encoding: binary ");
}
/*
Gebruik deze functie om een ADODB resultset om te zetten naar een XLS file.
Gebruik daarvoor ADODB wel in associative fetch mode, zodat de velden in de resultset alleen strings als idintifier gebruiken.
(default staat de ADODB fetch mode op BOTH, waardoor je dus ook integers als identifiers krijgt)
Gebruik $sql-> SetFetchMode(ADODB_FETCH_ASSOC);

*/
function ADODBresultsToXls($rs, $filename = "output"){
	xlsWriteHeader($filename);
	
	//Headers (titels van de kolommen) genereren
	$coll = 0;
	xlsBOF(); 
	foreach ($rs->fields as $name => $value){
		if(is_string($name)){		//Extra test, voor het geval ADODB niet in associative fetch mode staat
			xlsWriteString(0,$coll++,"$name");
		}
	}

	//rows genereren
	$row = 1;
	while(!$rs->EOF){
		$coll = 0;
		foreach ($rs->fields as $name => $value){
			if(is_string($name)){		//Extra test, voor het geval ADODB niet in associative fetch mode staat
				if(is_string($value)){
					xlsWriteString($row,$coll++,"$value");
				}else{
					if($value) xlsWriteNumber($row,$coll,"$value");	//Null values moeten lege cellen zijn, vandaar de check. Anders cast PHP naar '0'
				$coll++;
				}
			}
		}
		$row++;
		$rs->MoveNext();
	}
	xlsEOF();
	return 0;
}

/*
Deze functie zet een 2-dimentionale array om tot XLS. Deze 2D arrays gebruiken we vaak las rowset om als JSON te encoden en door te sturen naar EXTJS
*/
function arrayToXls($array, $filename = "output"){
	xlsWriteHeader($filename);
	
	$coll = 0;
	xlsBOF(); 
	foreach ($array[0] as $name => $value){
		if(is_string($name)){		//Extra test, voor het geval ADODB niet in associative fetch mode staat
			xlsWriteString(0,$coll++,"$name");
		}
	}

	//rows genereren
	$row = 1;
	while(isset($array[$row-1])){
		$coll = 0;
		foreach ($array[$row-1] as $name => $value){
			if(is_string($name)){		//Extra test, voor het geval ADODB niet in associative fetch mode staat
				if(is_string($value)){
					xlsWriteString($row,$coll++,"$value");
				}else{
					if($value) xlsWriteNumber($row,$coll,"$value");	//Null values moeten lege cellen zijn, vandaar de check. Anders cast PHP naar '0'
				$coll++;
				}
			}
		}
		$row++;
	}
	xlsEOF();
	return 0;
}

/*
Convert JSON string (as used for EXT-JS stores) to XLS
To-Do: Handle errors with a ErrorToXls() functions
Use meta-data field if exists, to auto-configure the field names
*/
function JSONtoXls($jsondata,$filename = null){
	if(!$data = json_decode($jsondata,TRUE)) die("Unable to decode JSON string");
	if($data['success'] == true){
		arrayToXls($data['rows'],$filename);
	}else{
		die("JSON string returned false");
	}
}

?>
