<?php

include_once('__ini.php');

class SQLDBMapping extends AttributeMapping {

   private $mapFromArray, $mapToAttribute;

   private function __construct($mapFromArray, $mapToAttribute) {
	  $this->mapFromArray = $mapFromArray;
	  $this->mapToAttribute = $mapToAttribute;
   }

   public function getAttribute($input) {
	  $value = '';
	  $tglue = '';
	  foreach ($this->mapFromArray as $key => $tableKey) {
		 if (isset($input[$tableKey])) {
			$value .= $tglue . $input[$tableKey];
			$tglue = ' ';
		 }
	  }
	  $this->mapToAttribute['value'] = $value;
	  return $this->mapToAttribute;
   }

   public static function createMapping($mapFromArray, $mapToAttribute) {
	  return new SQLDBMapping($mapFromArray, $mapToAttribute);
   }

}

?>
