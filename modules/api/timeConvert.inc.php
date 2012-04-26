<?php

/* Zet tijd om in een string, met maandnaam in NL afgekort (hiervoor moet de timezone wel op europe/amsterdam staan). */
/* Door aan te roepen met W4ATijd(&$var), hoef je geen readback te doen. */

date_default_timezone_set('Europe/Amsterdam');
setlocale(LC_ALL, 'nl_NL');

function W4ATijd($Datum){
	if($Datum){
		$timestamp = strtotime($Datum);
		return $Datum = strftime("%d %b %Y %H:%M",$timestamp);			//Zet om als "01 dec 2010 12:34"
	}
	return null;
}

//Zonder jaartal
function W4ATijdShort($Datum){
	if($Datum){
		$timestamp = strtotime($Datum);
		return $Datum = strftime("%d %b %H:%M",$timestamp);			//Zet om als "01 dec 12:34"
	}
	return null;
}

//Alleen datum
function W4ADate($Datum){
	if($Datum){
		$timestamp = strtotime($Datum);
		return $Datum = strftime("%d %b %Y",$timestamp);			//Zet om als "01 dec 2010"
	}
	return null;
}

//Lang, met seconden
function W4ATijdLong($Datum){
	if($Datum){
		$timestamp = strtotime($Datum);
		return $Datum = strftime("%d %b %Y %H:%M:%S",$timestamp);			//Zet om als "01 dec 2010 12:34:56"
	}
	return null;
}

function BMUTime($Datum){	
	if($Datum){
		$timestamp = strtotime($Datum);
		//Amerikaanse notatie MM-DD-YYYY HH:MM:SS
		return $Datum = strftime("%m-%d-%Y %H:%M:%S",$timestamp);			//Zet om als "01 dec 2010 12:34:56"
	}
	return null;
}
?>
