<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ContractTypeMapper
 *
 * @author erwin
 */
class ContractTypeMapper extends Mapper {

   //put your code here
   private $appKey, $providerID, $providerUserName, $providerPassword, $mappings;

   public function __construct(AttributeMappingArray $array, $provider) {
	  $this->appKey = $provider['uikey'];
	  $this->providerID = $provider['id'];
	  $this->providerUserName = $provider['admin']['id'];
	  $this->providerPassword = $provider['admin']['pass'];
	  $this->mappings = $array;
   }

   public function deleteMappings() {
	  $this->mappings = new AttributeMappingArray();
   }

   public function setMappingArray(AttributeMappingArray $array) {
	  $this->mappings = $array;
   }

   public function executeMappings($input, $ContractTypes) {
	  //$this->prepareEnvironment();
	  $mappings = $this->mappings->getArray();
	  $ContractTypeArray = array();
	  foreach ($mappings as $mapping) {
		 $ContractTypeArray = $this->concatenateArrays($ContractTypeArray, $mapping->getMappedValue($input));
	  }
	  $B = BMUCore::b();
	  $B->ProviderSA($this->providerID)->ContractType()->
			  Create('MyWheels-ContractType-'. $ContractTypeArray['code'].BMU_MIGRATION_TEST_NR, $ContractTypeArray['info']);
	  echo "\n".$B->__toString()."\n";
	  $response = $B->sendRequest();
	  if ($response['result']['result'] == 0) {
		 echo "Successfully generated new ContractType: {$input['AbonnementSoort']}\n";
		 $ContractTypes[$input['Omschrijving'].$input['AbonnementGraad']] = $response['result']['contract_type_id'];
	  } else {
		 var_export($response);
	  }
	  return $ContractTypes;
   }

   protected function prepareEnvironment() {
	  $B = BMUCore::b();
	  $B->Application()->Connect($this->appKey);
	  $response = $B->sendRequest();
	  if ($response['result']['result'] != '0') {
		 die("ERROR, KILLING PHP SCRIPT\nIncorrect Application Key entered.\n");
	  }
	  $B->ProviderUI($this->providerID)->LoginUser($this->providerUserName, $this->providerPassword);
	  $B->sendRequest();
	  if ($response['result']['result'] != '0') {
		 die("Wrong UserName / Password Combination entered for Wrong Provider");
	  }
	  //echo "environment successfully prepared\n";
   }

   public function concatenateArrays($array1, $array2) {
	  foreach ($array2 as $key => $value) {
		 $array1[$key] = $value;
	  }
	  return $array1;
   }

}

?>
