<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Wheels4AllContractMigrator
 *
 * @author erwin
 */
class Wheels4AllContractMigrator {

   //put your code here
   private $persons, $legals;

   public function __construct($persons, $legals) {
	  $this->persons = $persons;
	  $this->legals = $legals;
   }

   public function run() {
	  $contracts = array();
	  $contractTypes = array();
	  $dbConn = $this->connectDatabase();
	  $records = $this->getContractRecords($dbConn);
	  $ContractTypeMapping = $this->createContractTypeMapping();
	  $curPro = array('id' => '100', 'uikey' => 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a15', 'admin' => array('id' => 'stefan@test.nl', 'pass' => 'stefan2'));

	  $contractMapper = new ContractMapper($curPro);
	  $this->legals->startIteration();
	  $createdContracts = array();
	  while ($legalArray = $this->legals->iterate()) {
		 foreach ($legalArray as $legalNr => $legal) {
			$createdContracts[$legal['w4acontract']] = array('legal' => $legalNr);
			/*
			  $B->ProviderSA($curPro['id'])->Contract($createdContracts[$person['w4acontract']])->ConsumerAssign($person['email']);
			  $B->sendRequest(); */
		 }
	  }

	  $typeMapper = new ContractTypeMapper($ContractTypeMapping, $curPro);
	  echo "\nInitiated ContractMigrator, attempting to migrate";
	  while (!$records->EOF) {
		 $contract = $records->fields;
		 $person = $this->persons->getKey($contract['Verantwoordelijke']);
		 if (!isset($contractTypes[$contract['Omschrijving'].$contract['AbonnementGraad']])) {
			echo "\nContractType not found, creating new ContractType";
			$contractTypes = $typeMapper->executeMappings($contract, $contractTypes);
		 }
		 if ($person) {
			if (isset($createdContracts[$contract['ContractNr']])) {
			   $contractMapper->setLegal($person['email'], $person['password'], $createdContracts[$contract['ContractNr']]['legal']);
			} else {
			   $contractMapper->setPerson($person['email'], $person['password'], $person['id']);
			}
			$output = $contractMapper->executeMappings($contract, $contractTypes);
			if ($output === false) {
			   echo "Could not create Contract";
			} else {
			   $createdContracts[$person['w4acontract']] = array('id' => $output);
			}
		 }
		 $records->MoveNext();
	  }
	  $B = BMUCore::b();
	  $B->Application()->Connect($curPro['uikey']);
	  $B->sendRequest();
	  $B->ProviderUI($curPro['id'])->LoginUser($curPro['admin']['id'], $curPro['admin']['pass']);
	  $B->sendRequest();

	  $this->persons->startIteration();
	  while ($personArray = $this->persons->iterate()) {
		 foreach ($personArray as $persoonNr => $person) {
			$B->ProviderSA($curPro['id'])->Contract($createdContracts[$person['w4acontract']]['id'])->ConsumerAssign($person['email']);
			$B->sendRequest();
			echo "\nAdded person: {$person['email']} to contract: {$createdContracts[$person['w4acontract']]['id']}";
		 }
	  }
	  $this->persons->endIteration();
	  return $createdContracts;
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

   public function getContractRecords($dbConn) {
	  $query = "SELECT * FROM W4AAbonnementTypen AS abbo
			    JOIN W4AContracten AS cont
				ON abbo.AbonnementSoort = cont.AbonnementSoort";
	  $rs = $dbConn->Execute($query);
	  $dbConn->Close();
	  return $rs;
	  //var_dump($rs);
   }

   public function createContractTypeMapping() {
	  $contractTypeMappingArray = new AttributeMappingArray();
	  $contractTypeMappingArray->setType('SQLDBContractTypeMapping');

	  $contractTypeMappingArray->addMapping(array('Omschrijving'), 'info');
	  $contractTypeMappingArray->addMapping(array('Omschrijving','AbonnementGraad'), 'code');

	  return $contractTypeMappingArray;
   }

}

?>
