<?php
include_once('__ini.php');

$aaff = new AppendableArrayFileFormat('test');
$aaff->openFile();
$aaff->appendArrayToFile(array("Blaat1"=>array("Bl{:},oo\oooot")));
$aaff->appendArrayToFile(array("Blaat2"=>array("Bloooooot")));
$aaff->appendArrayToFile(array("Blaat3"=>array("Bloooooot")));
$aaff->appendArrayToFile(array("Blaat4"=>array("Bloooooot")));
$aaff->closeFile();

var_dump($aaff->getKey('Blaat1'));

?>

