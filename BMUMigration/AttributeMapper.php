<?php

include_once('__ini.php');

abstract class AttributeMapper extends Mapper {

   protected $mappings;
   protected $attributesArray;

   protected abstract function createAttribute($attributeItem);

   protected abstract function updateAttribute($attributeItem);

   public function __construct() {
	  $this->mappings = array();
	  $this->attributesArray();
   }

   public function deleteMappings() {
	  $this->mappings = new AttributeMappingArray();
   }

   public function setMappingArray(AttributeMappingArray $array) {
	  $this->mappings = $array;
   }

   public final function executeMappings($inputTableArray) {
	  $this->prepareEnvironment();
	  foreach ($this->mappings->getArray() as $key => $mapping) {
		 $attribute = $mapping->getAttribute($inputTableArray);
		 //var_dump(json_encode($attribute));
		 $attributesArray[] = $attribute;
		 //echo "Successfully retrieved Attribute\n";
	  }
	  $result = $this->updateAttribute($attributesArray);
	  $count = 0;
	  $createAttributesArray = array();
	  if (isset($result['results'])) {
		 foreach ($result['results'] as $value) {
			if ($value['result'] != 0) {
			   $createAttributesArray[] = $attributesArray[$count];
			}
			$count++;
		 }
		 if (count($createAttributesArray) > 0) {
			//echo "Couldn't Update Some Attributes, because they haven't been created yet.\n\n";
			$result = $this->createAttribute($createAttributesArray);
			if($result['result'] != 0) {
			   echo "Failed to Create *some* attributes.\n\n";
			} else {
			   //echo "Successfully Created All remaining Attributes\n\n";
			}
		 } else {
			//echo "Updated Attributes Successfully\n\n";
		 }
	  }
   }

}

?>
