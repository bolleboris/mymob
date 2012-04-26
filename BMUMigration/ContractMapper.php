<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ContractMapper
 *
 * @author erwin
 */
class ContractMapper extends Mapper {

   //put your code here
   private $appKey, $providerID, $providerUserName, $providerPassword, $CustomerID, $personEmail, $personPassword;
   private $caller, $LegalID, $PersonID;
   public function __construct($provider) {
	  $this->appKey = $provider['uikey'];
	  $this->providerID = $provider['id'];
	  $this->providerUserName = $provider['admin']['id'];
	  $this->providerPassword = $provider['admin']['pass'];
   }

   public function setPerson($personEmail, $personPassword, $PersonID) {

	  $this->personPassword = $personPassword;
	  $this->personEmail = $personEmail;
	  $this->caller = 'Customer';
	  $this->CustomerID = $PersonID;
   }
   public function setLegal($personEmail, $personPassword, $legalID) {

	  $this->personPassword = $personPassword;
	  $this->personEmail = $personEmail;
	  $this->caller = 'Legal';
	  $this->LegalID = $legalID;


   }

   public function deleteMappings() {
	  $this->mappings = new AttributeMappingArray();
   }

   public function setMappingArray(AttributeMappingArray $array) {
	  $this->mappings = $array;
   }

   public function executeMappings($input, $contractTypes) {
	  //$this->prepareEnvironment();
	  //$this->setContractRequestEnvironment();
	  $B = BMUCore::b();
	  $blaat = $this->caller .'ID';
	  $ID = $this->$blaat;
	  $B->ProviderSA($this->providerID)->{$this->caller}($ID)->Contract()->RequestNew($contractTypes[$input['Omschrijving'].$input['AbonnementGraad']],'now');
	  echo "\n".$B->__toString()."\n";
	  $response = $B->sendRequest();
	  if($response['result']['result'] != 0) {
		 var_dump($response);
		 echo "\nContract couldn't be requested\n";
		 return false;
	  }
	  $contractId = $response['result']['contract_id'];
	  //$this->setContractAcceptEnvironment();
	  $B->ProviderSA($this->providerID)->Contract($contractId)->Accept('now','infinity');
	  $response = $B->sendRequest();
	  if($response['result']['result'] != 0) {
		 echo "Could not create contract";
		 //var_export($response['result']);
		 return false;
	  }
	  return $contractId;

   }

   public function setContractAcceptEnvironment() {
	  $B = BMUCore::b();
	  
	  $B->ProviderUI($this->providerID)->LoginUser($this->providerUserName, $this->providerPassword);
	  $response = $B->sendRequest();
	  if ($response['result']['result'] != 0) {
		 die("Wrong UserName / Password Combination entered for Wrong Provider");
	  }
	  //$this->personID = $response['result']['me_person_id'];
   }

   public function prepareEnvironment() {
	  $B = BMUCore::b();

	  $B->Application()->Connect($this->appKey);
	  $response = $B->sendRequest();
	  if ($response['result']['result'] != 0) {
		 die("ERROR, KILLING PHP SCRIPT\nIncorrect Application Key entered.\n");
	  }
	  
   }

   public function setContractRequestEnvironment() {
	  $B = BMUCore::b();
	  $B->ProviderUI($this->providerID)->LoginUser($this->personEmail, $this->personPassword);
	  //echo $B;
	  $response = $B->sendRequest();
	  //var_dump($response);
	  if ($response['result']['result'] != 0) {
		 //var_dump($response);
		 die("Wrong UserName / Password Combination entered for Wrong Provider");
	  }
	  $this->CustomerID = $response['result']['me_person_id'];
   }

   public function concatenateArrays($array1, $array2) {
	  foreach ($array2 as $key => $value) {
		 $array1[$key] = $value;
	  }
	  return $array1;
   }

}

?>
