<?php

include_once 'MyMobility.inc';

extract($_POST);
$OfferNr = 1;
$BMUCore->ProviderSA(MYMOB_APP_ID)->Offer($OfferNr)->Resources();
echo $BMUCore->__toString();
echo $BMUCore->__toString();
$res = $BMUCore->sendRequest();

var_dump($res);
?>