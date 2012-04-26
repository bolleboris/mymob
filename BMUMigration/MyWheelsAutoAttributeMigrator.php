<?php

include_once('W4AAutos.php');
include_once('__ini.php');

$resourceAttributeMapping = new AttributeMappingArray();
$resourceAttributeMapping->setType('SQLDBMapping');

$resourceAttributeMapping->addMapping(array('Kenteken'), array('group' => 'General', 'key' => 'LicensePlateNumber', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array('Bijnaam'), array('group' => 'General', 'key' => 'Alias', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array('Merk'), array('group' => 'General', 'key' => 'Brand', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array('Model'), array('group' => 'General', 'key' => 'Model', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array(), array('group' => 'General', 'key' => 'Color', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array('Brandstof'), array('group' => 'General', 'key' => 'Fuel', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array('Tankpascode'), array('group' => 'General', 'key' => 'FuelCardCode', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array('Opmerkingen'), array('group' => 'General', 'key' => 'Notes', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array('Handleiding'), array('group' => 'General', 'key' => 'Manual', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array('AantalZitplaatsen'), array('group' => 'General', 'key' => 'NrSeats', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array('BoardComputer'), array('group' => 'General', 'key' => 'BoardComputer', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array('Options'), array('group' => 'General', 'key' => 'Options', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array(), array('group' => 'General', 'key' => 'CarAdvertisement', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array('Afbeelding'), array('group' => 'General', 'key' => 'PrimaryPicture', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array(), array('group' => 'General', 'key' => 'Pictures', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array('EigenRisicoWA'), array('group' => 'General', 'key' => 'Deductible', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array('EigenRisicoAllRisk'), array('group' => 'General', 'key' => 'DeductibleCasco', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array(), array('group' => 'General', 'key' => 'RoadAssistance', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array(), array('group' => 'General', 'key' => 'KnownDamage', 'access' => 'protected'));

$resourceAttributeMapping->addMapping(array('PrijsPerUur'), array('group' => 'Prices', 'key' => 'HourRate', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array('MaxTeBetalenUren'), array('group' => 'Prices', 'key' => 'MaxHours', 'access' => 'protected'));
$resourceAttributeMapping->addMapping(array('PrijsPerKm'), array('group' => 'Prices', 'key' => 'KilometerRate', 'access' => 'protected'));
$curPro = array('id' => '5', 'uikey' => 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a15', 'admin' => array('id' => 'stefan@test.nl', 'pass' => 'stefan2'));

$B = BMUCore::b();
$resources = array();

foreach ($W4AAutos as $key => $car) {
   //$car = $W4AAutos['0'];
   setProvider($B, $curPro);
   setProviderSA($B, $curPro);

   $result = createResourceItem($B, $curPro, 4, 8, 'Car', 'Wheels4AllCar-' . $car['AutoId'], 'No Info');
   var_dump($result);
   $resources[] = $result['resource_id'];

   $options = $car['Opties'] . (($car['OptiesAanvullend'] == '') ? '' : (($car['Opties'] == '') ? '' : ',' . $car['OptiesAanvullend']));
   $exploded = explode(',', $options);
   if (removeElement('Boordcomputer', $exploded)) {
	  $car['BoardComputer'] = 'convadis';
   } else {
	  $car['BoardComputer'] = 'no';
   }
   $car['Options'] = implode(',', $exploded);
   //$mapper = new ResourceAttributeMapper($curPro,11,4);
   $mapper = new ResourceAttributeMapper($curPro, $result['resource_id'], 4);
   $mapper->setAttributeMappingArray($resourceAttributeMapping);
   $mapper->executeMappings($car);
}




echo '(' . implode(',', $resources) . ')';

function removeElement($needle, &$Haystack) {
   $output = false;
   foreach ($Haystack as $key => $value) {
	  if ($needle == $value) {
		 unset($Haystack[$key]);
		 $output = true;
	  }
   }
   return $output;
}

?>
