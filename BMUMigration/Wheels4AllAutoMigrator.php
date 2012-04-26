<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Wheels4AllAutoMigrator
 *
 * @author erwin
 */
//include_once('W4AAutos.php');
class Wheels4AllAutoMigrator {

   public function test() {
	  $dbConn = $this->connectDatabase();
	  $this->getAutoRecords($dbConn);
   }

   public function getAutoRecords($dbConn) {
	  $query = "SELECT *
			    FROM W4AAutos AS wat
				JOIN W4AAdressen AS wad
				ON wat.AdresId = wad.AdresId";
	  $rs = $dbConn->Execute($query);
	  return $rs;
   }

   public function run() {
	  $dbConn = $this->connectDatabase();
	  $records = $this->getAutoRecords($dbConn);
	  $resourceAttributeMapping = $this->createAttributeMapping();
	  $curPro = array('id' => '100', 'uikey' => 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a15', 'admin' => array('id' => 'stefan@test.nl', 'pass' => 'stefan2'));
	  $supplier_id = 100;
	  $B = BMUCore::b();
	  $resources = array();
	  $vloten = $this->migrateVloten($dbConn, $curPro, $supplier_id);
	  while (!$records->EOF) {
		 $car = $records->fields;
		 echo "\n****************************************\n\n";

		 if (!isset($resources[$car['AutoId']])) {
			echo "Attempting to create Resource with AutoId = " . $car['AutoId'], "\n\n";

			$B->Supplier($supplier_id)->ResourceItem()->Create((isset($vloten[$car['VlootId']]))? $vloten[$car['VlootId']]: 4 , 'Car', BMU_MIGRATION_TEST_NR . '-Wheels4AllCar-' . $car['AutoId'], 'No Info');
			//echo $B->__toString()."\n";
			$result = $B->sendRequest();
			//$result = createResourceItem($B, $curPro, $supplier_id, 4, 'Car', BMU_MIGRATION_TEST_NR . '-Wheels4AllCar-' . $car['AutoId'], 'No Info');
			if ($result['result']['result'] != 0) {
			   echo 'Failed to create Resource\n Error Message: ' . $result['result']['message'] . "\n\n";
			   break;
			} else {
			   echo 'Successfully created Resource' . $result['result']['resource_id'];
			}
			$resources[$car['AutoId']] = $result['result']['resource_id'];
		 }

		 //***Parse Options***
		 $options = $car['Opties'] . (($car['OptiesAanvullend'] == '') ? '' : (($car['Opties'] == '') ? '' : ',' . $car['OptiesAanvullend']));
		 $exploded = explode(',', $options);
		 if ($this->removeElement('Boordcomputer', $exploded)) {
			$car['BoardComputer'] = 'convadis';
		 } else {
			$car['BoardComputer'] = 'no';
		 }
		 $car['Options'] = implode(',', $exploded);
		 //$mapper = new ResourceAttributeMapper($curPro,11,4);
		 $car['PrijsPerUur'] = 2.5;
		 $car['MaxTeBetalenUren'] = 10;
		 $car['ToeslagPerKilometer'] = ($car['ToeslagPerKilometer']) / 1000 + 0.25;
		 $car['Afbeelding'] = 'https://www.wheels4all.nl/images-autos/groot/'.$car['Afbeelding'];
		 //***Add Attributes to Resource
		 $mapper = new ResourceAttributeMapper($curPro, $result['result']['resource_id'], $supplier_id);
		 $mapper->setMappingArray($resourceAttributeMapping);
		 $mapper->executeMappings($car);

		 //***Add location to resource
		 $B->Supplier($supplier_id)->Resource($resources[$car['AutoId']])->Location()
				 ->Create('((' . $car['Latitude'] . ',' . $car['Longitude'] . '))',
						 '' . $car['Straatnaam'] . ' ' . $car['Huisnr'] . ', ' . $car['Postcode'] . ', ' . $car['Woonplaats'],
						 'No Extra Remarks', true);
		 $result = $B->sendRequest();
		 if ($result['result']['result'] == 0) {
			echo "Successfully Set " . $car['Straatnaam'] . ' ' . $car['Huisnr'] . ', ' . $car['Woonplaats'] . ', ' . $car['Postcode'] . " as the Default Location\n\n";
		 }
		 echo "Done Creating Resource!\n";
		 $records->MoveNext();
	  }
	  var_dump($resources);
	  return $resources;
   }

   public function createAttributeMapping() {
	  $resourceAttributeMapping = new AttributeMappingArray();
	  $resourceAttributeMapping->setType('SQLDBMapping');

	  $resourceAttributeMapping->addMapping(array('Kenteken'), array('group' => 'General', 'key' => 'LicensePlateNumber', 'access' => 'public'));
	  $resourceAttributeMapping->addMapping(array('Bijnaam'), array('group' => 'General', 'key' => 'Alias', 'access' => 'public'));
	  $resourceAttributeMapping->addMapping(array('Merk'), array('group' => 'General', 'key' => 'Brand', 'access' => 'public'));
	  $resourceAttributeMapping->addMapping(array('Model'), array('group' => 'General', 'key' => 'Model', 'access' => 'public'));
	  $resourceAttributeMapping->addMapping(array(), array('group' => 'General', 'key' => 'Color', 'access' => 'public'));
	  $resourceAttributeMapping->addMapping(array('Brandstof'), array('group' => 'General', 'key' => 'Fuel', 'access' => 'public'));
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
	  $resourceAttributeMapping->addMapping(array('BeheerdersId'), array('group' => 'Settings', 'key' => 'CaretakerId', 'access' => 'private'));
	  //Aan ronald vragen over BeheerdersId
	  $resourceAttributeMapping->addMapping(array('PrijsPerUur'), array('group' => 'Prices', 'key' => 'HourRate', 'access' => 'protected'));
	  $resourceAttributeMapping->addMapping(array('MaxTeBetalenUren'), array('group' => 'Prices', 'key' => 'MaxHours', 'access' => 'protected'));
	  $resourceAttributeMapping->addMapping(array('ToeslagPerKilometer'), array('group' => 'Prices', 'key' => 'KilometerRate', 'access' => 'protected'));
	  $resourceAttributeMapping->addMapping(array('AutoId'), array('group' => 'Settings', 'key' => 'W4AAutoId', 'access' => 'private'));
	  return $resourceAttributeMapping;
   }

   public function removeElement($needle, &$Haystack) {
	  $output = false;
	  foreach ($Haystack as $key => $value) {
		 if ($needle == $value) {
			unset($Haystack[$key]);
			$output = true;
		 }
	  }
	  return $output;
   }

   public function connectDatabase() {
	  require_once('/home/websites/secure/adodb5/adodb.inc.php');
	  $servertype = 'mysql';
	  $server = 'localhost';
	  $user = 'root';
	  $password = 'IHe4eing';
	  $database = 'w4a';
	  $dbConn = &ADONewConnection($servertype);
	  $dbConn->Connect($server,
			  $user, // username
			  $password, // password
			  $database); // database
	  return $dbConn;
   }

   public function migrateVloten($dbConn, $curPro, $supplierId) {
	  $vloten = array();
	  $query = "SELECT *
				FROM W4AVloten";
	  $records = $dbConn->Execute($query);
	  $B = BMUCore::b();
	  while (!$records->EOF) {
		 $B->Supplier($supplierId)->ResourceGroup()->Create('Car', BMU_MIGRATION_TEST_NR . "-{$records->fields['Beschrijving']}-Vloot", "No Info");
		 $response = $B->sendRequest();
		 if ($response['result']['result'] == 0) {
			$vloten[$records->fields['VlootId']] = $response['result']['resource_id'];
		 } else {
			echo "\n" . $response['result']['message'] . "\n";
		 }
		 $records->MoveNext();
	  }
	  var_export($vloten);
	  return $vloten;
   }

   

}

?>
