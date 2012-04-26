<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BMU_ContractType
 *
 * @author erwin
 */
class BMU_ContractType {
    //put your code here
   public function construct() {}
   public function GetList() {
	  BMUCore::b()->push('List()');
   }
   public function Info($options = true) {
	  BMUCore::b()->push('Info('.$options.')');
   }
   public function Create($Code, $Info) {
	  BMUCore::b()->pushFunc('Create',func_get_args());
   }
   public function Update($Code, $Info) {
	  BMUCore::b()->pushFunc('Update',func_get_args());
   }
   public function Delete() {
	  BMUCore::b()->push('Delete()');
   }
}
?>
