<?php
include '__ini.php';
$curPro = array('id' => '5', 'uikey' => 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a15', 'admin' => array('id' => 'stefan@test.nl', 'pass' => 'stefan2'));
$B = BMUCore::b();
setProvider($B, $curPro);
setProviderSA($B, $curPro);


$startTime = microtime(true);
$B->ProviderSA(5)->TimeTest('blasdfalsdkfjasdfasd');
$B->sendRequest();
$endTime = microtime(true);

echo "startTime Core TimeTest: $startTime\n";
echo "endTime Core TimeTest: $endTime\n";
echo "duration Core TimeTest: ".($endTime - $startTime)."\n";

$startTime = microtime(true);
$B->Wheels4All()->ProviderSA(5)->TimeTest('asdfasASDdfcvbDASD,','50');
$B->sendRequest();
$endTime = microtime(true);

echo "startTime Core TimeTest: $startTime\n";
echo "endTime Core TimeTest: $endTime\n";
echo "duration Core TimeTest: ".($endTime - $startTime)."\n";

?>
