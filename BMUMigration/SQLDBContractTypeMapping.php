<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SQLDBContractMapping
 *
 * @author erwin
 */
class SQLDBContractTypeMapping {
   /*
	* A ContractType consists of these fields:
	* array('code' => code to uniquely identify a ContractType
	* 		'info' =>
	*/

   private $mapFromArray, $mapToKey;

   private function __construct($mapFromArray, $mapToKey) {
	  $this->mapFromArray = $mapFromArray;
	  $this->mapToKey = $mapToKey;
   }

   public function getMappedValue($input) {
	  $output = array();
	  $tglue = '';
	  foreach ($this->mapFromArray as $key => $tableKey) {
		 if (isset($input[$tableKey])) {
			$value .= $tglue . $input[$tableKey];
			$tglue = '';
		 }
	  }
	  $output[$this->mapToKey] = $value;
	  return $output;

   }

   public static function createMapping($mapFromArray, $mapToKey) {
	  return new SQLDBContractTypeMapping($mapFromArray, $mapToKey);
   }

}

?>
