<?php

class Wheels4AllPersonMigrator {

   public function test() {

   }

   public function run() {
	  $dbConn = $this->connectDatabase();
	  $limit = 50;
	  $offset = 0;
	  $records = $this->getPersonRecords($dbConn, 0, $limit);
	  $addresses = $this->getAddresses($dbConn);
	  $chips = $this->getChips($dbConn);
	  $personAttributeMapping = $this->createAttributeMapping();
	  $curPro = array('id' => '100', 'uikey' => 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a15', 'admin' => array('id' => 'stefan@test.nl', 'pass' => 'stefan2'));
	  $B = BMUCore::b();
	  $aaff = new AppendableArrayFileFormat(BMU_MIGRATION_TEST_NR . 'persons');
	  $aaff->openFile();
	  $persons = array();
	  //var_export($person);

	  $count = $records->RecordCount();
	  $dotThreshold = ceil($count / 100);
	  $amountDone = 0;
	  while ($records->RecordCount() != 0) {
		 unset($persons);
		 $persons = array();
		 $records = $this->getPersonRecords($dbConn, $offset, $limit);
		 echo "\n**********************************************";
		 echo "\n\nCreating persons (Total: $count, Done: $offset)";
		 $offset += $limit;

		 while (!$records->EOF) {
			$person = $records->fields;
			if ($person['Email'] != 'xxx@yyy.nl') {
			   if ($person['Email'] == NULL) {
				  $person['Email'] = $person['PersoonNr'] . '@mywheels.nl';
			   }
			   $person['Land'] = 'Netherlands';
			   if (isset($person['AdresId']) && $person['AdresId'] != null) {
				  if (is_array($addresses) && is_array($person)) {
					 $person = $person + $addresses[$person['AdresId']];
				  } else {
					 var_dump($addresses);
					 var_dump($person);
				  }
			   } else {
				  echo "\nFound AdresId == NULL";
			   }
			   if (isset($chips[$person['PersoonNr']])) {
				  foreach ($chips[$person['PersoonNr']] as $chipNr => $chip) {
					 $person['UID' . $chipNr] = $chip['MifareUID'];
					 $person['Blocked' . $chipNr] = $chip['Geblokkeerd'];
				  }
			   }
			   if (!isset($persons[$person['PersoonNr']])) {
				  $personId = $this->createPerson($B, $curPro['id'], BMU_MIGRATION_TEST_NR . $person['Email'], BMU_MIGRATION_TEST_NR . $person['PersoonNr'] . '@mywheels.nl', 'service@mywheels.nl');
				  $personId['w4acontract'] = $person['ContractNr'];
				  $persons[$person['PersoonNr']] = $personId;
			   } else {
				  $personId = $persons[$person['PersoonNr']];
			   }
			   if ($personId['id'] != false) {
				  $mapper = new PersonAttributeMapper($curPro, $personId['id']);
				  $mapper->setMappingArray($personAttributeMapping);
				  $mapper->executeMappings($person);
			   } else {

			   }
			   if ($amountDone % $dotThreshold == 0) {
				  echo ".";
			   }
			}
			$amountDone++;

			$records->MoveNext();
		 }
		 $aaff->appendArray($persons);
	  }
	  echo "\n\n";
	  $aaff->closeFile();
	  //var_export($persons);
	  return $aaff;
   }

   public function getPersonRecords($dbConn, $offset, $limit) {
	  /* $query = "SELECT *
	    FROM W4APersonen AS wat
	    JOIN W4AAdressen AS wad
	    ON wat.AdresId = wad.AdresId";
	   */
	  //Adjust query so that:
	  //TODO: People without a Contract Nr are ignored
	  //Mede-leden without an e-mail address use their super-leden's email
	  $query = "SELECT wp.*
			    FROM W4APersonen AS wp
				WHERE wp.ContractNr IS NOT NULL
				ORDER BY wp.PersoonNr
				LIMIT $limit OFFSET $offset";
	  $rs = $dbConn->Execute($query);
	  return $rs;
	  //var_dump($rs);
   }

   public function getAddresses($dbConn) {
	  $query = "SELECT * FROM W4AAdressen";
	  $rs = $dbConn->Execute($query);
	  $addresses = array();
	  while (!$rs->EOF) {
		 $addresses[$rs->fields['AdresId']] = $rs->fields;
		 $rs->MoveNext();
	  }
	  return $addresses;
   }

   public function getChips($dbConn) {
	  $query = "SELECT * FROM W4APassen";
	  $rs = $dbConn->Execute($query);
	  $chips = array();
	  while (!$rs->EOF) {
		 $chips[$rs->fields['Pashouder']][] = $rs->fields;
		 $rs->MoveNext();
	  }
	  return $chips;
   }

   public function createAttributeMapping() {
	  $personMapping = new AttributeMappingArray();
	  $personMapping->setType('SQLDBMapping');

	  $personMapping->addMapping(array('Voornaam'), array('group' => 'Name', 'key' => 'FirstName', 'access' => 'public'));
	  $personMapping->addMapping(array('Tussenvoegels'), array('group' => 'Name', 'key' => 'Preposition', 'access' => 'public'));
	  $personMapping->addMapping(array('Achternaam'), array('group' => 'Name', 'key' => 'SurName', 'access' => 'public'));
	  $personMapping->addMapping(array('Initialen'), array('group' => 'Name', 'key' => 'Initials', 'access' => 'public'));
	  $personMapping->addMapping(array('IsMan'), array('group' => 'General', 'key' => 'Gender', 'access' => 'public'));
	  $personMapping->addMapping(array('GeboorteDatum'), array('group' => 'General', 'key' => 'BirthDate', 'access' => 'public'));
	  $personMapping->addMapping(array('Email'), array('group' => 'General', 'key' => 'EmailAddress', 'access' => 'public'));
	  $personMapping->addMapping(array('Telefoon1'), array('group' => 'General', 'key' => 'Telephone1', 'access' => 'public'));
	  $personMapping->addMapping(array('Telefoon2'), array('group' => 'General', 'key' => 'Telephone2', 'access' => 'protected'));
	  $personMapping->addMapping(array('Telefoon3'), array('group' => 'General', 'key' => 'Telephone3', 'access' => 'protected'));
	  $personMapping->addMapping(array('RijbewijsNr'), array('group' => 'General', 'key' => 'DriverLicenceNr', 'access' => 'protected'));
	  $personMapping->addMapping(array(''), array('group' => 'General', 'key' => 'DriverLicence', 'access' => 'public'));

	  $personMapping->addMapping(array('Latitude'), array('group' => 'HomePosition', 'key' => 'Latitude', 'access' => 'public'));
	  $personMapping->addMapping(array('Longitude'), array('group' => 'HomePosition', 'key' => 'Longitude', 'access' => 'public'));

	  $personMapping->addMapping(array(), array('group' => 'Settings', 'key' => 'EmailResConf', 'access' => 'protected'));
	  $personMapping->addMapping(array(), array('group' => 'Settings', 'key' => 'AmountOfEmail', 'access' => 'protected'));
	  $personMapping->addMapping(array('PersoonNr'), array('group' => 'Settings', 'key' => 'W4APersoonNr', 'access' => 'private'));
	  $personMapping->addMapping(array('FavorieteAuto'), array('group' => 'Settings', 'key' => 'FavouriteCar', 'access' => 'protected'));
	  $personMapping->addMapping(array('Postcode'), array('group' => 'Address', 'key' => 'Zipcode', 'access' => 'public'));
	  $personMapping->addMapping(array('Huisnr'), array('group' => 'Address', 'key' => 'HouseNr', 'access' => 'public'));
	  $personMapping->addMapping(array('Toevoeging'), array('group' => 'Address', 'key' => 'Affix', 'access' => 'public'));
	  $personMapping->addMapping(array('Woonplaats'), array('group' => 'Address', 'key' => 'City', 'access' => 'public'));
	  $personMapping->addMapping(array('Straatnaam'), array('group' => 'Address', 'key' => 'StreetName', 'access' => 'public'));
	  $personMapping->addMapping(array('Land'), array('group' => 'Address', 'key' => 'Country', 'access' => 'public'));
	  for ($i = 0; $i < 8; $i++) {
		 $personMapping->addMapping(array('UID' . $i), array('group' => 'Chips', 'key' => 'UID' . $i, 'access' => 'public'));
		 $personMapping->addMapping(array('Blocked' . $i), array('group' => 'Chips', 'key' => 'Blocked' . $i, 'access' => 'public'));
	  }
	  return $personMapping;
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

   private function createPerson(BMUCore $B, $provider_id, $email, $secundaryEntityKey, $secundaryEmail) {
	  $password = $this->generatePassword();
	  $B->ProviderSA($provider_id)->RegisterPerson('MyWheels-UI', $email, $email, $password);
	  $res = $B->sendRequest();
	  if (isset($res['result']['result']) && $res['result']['result'] == 0) {
		 $user = array('id' => $res['result']['person_id'], 'email' => $email, 'password' => $password);
	  } else {
		 $B->ProviderSA($provider_id)->RegisterPerson('MyWheels-UI', $secundaryEntityKey, $secundaryEmail, $password);
		 $res = $B->sendRequest();
		 if (isset($res['result']['result']) && $res['result']['result'] == 0) {
			$user = array('id' => $res['result']['person_id'], 'email' => $email, 'password' => $password);
		 } else {
			var_export($res);
			return false;
		 }
	  }
	  return $user;
   }

   function subscribePerson($B, $curPro, $Email) {
	  $B->ProviderUI($curPro)->SubscribePerson($Email);
	  $response = $B->sendRequest();
	  return $response['result'];
   }

   function subscriptionConfirm($B, $curPro, $subKey) {
	  $B->ProviderUI($curPro)->SubscriptionConfirm($subKey);
	  $response = $B->sendRequest();
	  return $response['result'];
   }

   function subscriptionComplete($B, $curPro, $subKey, $pass) {
	  $B->ProviderUI($curPro)->SubscriptionComplete($subKey, $pass);
	  $response = $B->sendRequest();
	  return $response['result'];
   }

   function generatePassword() {
	  $random = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijlkmnopqrstuvwxyz1234567890!?';
	  $blaat = rand(8, 12);
	  $count = 0;
	  $password = '';
	  for ($i = 0; $i < $blaat; $i++) {
		 $password .= $random[rand(0, strlen($random))];
	  }
	  return $password;
   }

}

?>
