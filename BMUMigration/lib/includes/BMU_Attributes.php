<?php

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BMU_Attributes
 *
 * @author erwin
 */
class BMU_Attributes {
    //put your code here
   public function __construct() {}

   public function GetList() {
	  BMUCore::b()->push('List()');
   }
   private function callFuncWithAlias($funcName, $AttributesArray, $aliasName = 'alias') {
	  BMUCore::b()->push($funcName.'('.$aliasName.')');
	  BMUCore::b()->addToArray($aliasName,$AttributesArray);
   }
   public function ListCreate($AttributesArray) {
	  $this->callFuncWithAlias('ListCreate', $AttributesArray);
   }
   public function ListUpdate($AttributesArray) {
	  $this->callFuncWithAlias('ListUpdate', $AttributesArray);
   }
   public function ListDelete($AttributesArray) {
	  $this->callFuncWithAlias('ListDelete', $AttributesArray);
   }
   public function ItemInfo($GroupName,$itemKey,$AccessSpecifier) {
	  BMUCore::b()->pushFunc('ItemInfo',func_get_args());
   }
   public function ItemCreate($GroupName, $ItemKey, $ItemValue, $AccessSpecifier) {
	  BMUCore::b()->pushFunc('ItemCreate',func_get_args());
   }
   public function ItemUpdate($GroupName, $ItemKey, $ItemValue, $AccessSpecifier) {
	  BMUCore::b()->pushFunc('ItemUpdate',func_get_args());
   }
   public function ItemDelete($GroupName, $ItemKey, $AccessSpecifier) {
	  BMUCore::b()->pushFunc('ItemDelete',func_get_args());
   }
   public function GroupInfo($GroupName) {
	  BMUCore::b()->push("GroupInfo('".$GroupName."')");
   }

}
?>
