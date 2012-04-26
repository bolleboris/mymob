<?php

class Wheels4AllLegalMigrator {

   private $persons;

   public function __construct($legals) {
	  $this->persons = $legals;
   }

   public function test() {

   }

   public function run() {
	  $dbConn = $this->connectDatabase();
	  $addresses = $this->getAddresses($dbConn);
	  $legalAttributeMapping = $this->createAttributeMapping();
	  $curPro = array('id' => '100', 'uikey' => 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a15', 'admin' => array('id' => 'stefan@test.nl', 'pass' => 'stefan2'));
	  $B = BMUCore::b();
	  $aaff = new AppendableArrayFileFormat(BMU_MIGRATION_TEST_NR . 'legals');
	  $aaff->openFile();
	  $legals = array();
	  //var_export($legal);
	  setProvider($B, $curPro);
	  setProviderSA($B, $curPro);



	  unset($legals);
	  $legals = array();
	  $records = $this->getLegalRecords($dbConn);
	  $count = $records->RecordCount();
	  $dotThreshold = ceil($count / 100);
	  $amountDone = 0;
	  echo "\n**********************************************";
	  echo "\n\nCreating legals (Total: $count)";

	  while (!$records->EOF) {
		 $legal = $records->fields;
		 $person = $this->persons->getKey($legal['Verantwoordelijke']);
		 if (!$person) {
			$failedLegal[] = $legal['Verantwoordelijke'];
		 } else {
			$B->ProviderSA($curPro['id'])->Person($person['id'])->SubscribeLegal('Test' . BMU_MIGRATION_TEST_NR . '-' . $legal['Bedrijfsnaam']);
			$response = $B->sendRequest();
			if ($response['result']['result'] != 0) {
			   var_dump($response);
			   echo 'Creating legal failed';
			}
			$response['result']['legal_id'];
			$legals[$response['result']['legal_id']] = array('w4acontract' => $legal['ContractNr'], 'contact' => $person['id']);

			if ($amountDone % $dotThreshold == 0) {
			   echo ".";
			}
		 }
		 $amountDone++;


		 $records->MoveNext();
	  }
	  $aaff->appendArray($legals);

	  echo "\n\n";
	  $aaff->closeFile();
	  var_export($legals);
	  return $aaff;
   }

   public function getLegalRecords($dbConn) {
	  /* $query = "SELECT *
	    FROM W4APersonen AS wat
	    JOIN W4AAdressen AS wad
	    ON wat.AdresId = wad.AdresId";
	   */
	  $query = "SELECT W4AContracten.ContractNr, W4ABedrijven.Bedrijfsnaam, W4AContracten.Verantwoordelijke
			    FROM W4ABedrijven JOIN W4AContracten
			    ON W4ABedrijven.ContractNr = W4AContracten.ContractNr";
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

   public function createAttributeMapping() {
	  $legalMapping = new AttributeMappingArray();
	  $legalMapping->setType('SQLDBMapping');


	  //$legalMapping->addMapping(array('Land'), array('group' => 'Address', 'key' => 'Country', 'access' => 'protected'));

	  return $legalMapping;
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

}

?>
