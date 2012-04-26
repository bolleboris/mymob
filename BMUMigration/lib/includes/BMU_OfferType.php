<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BMU_OfferType
 *
 * @author erwin
 */
class BMU_OfferType {
    //put your code here
   public function __construct() {}

   public function GetList() {
	  BMUCore::b()->push('List()');
   }
   public function Info($option = true) {
	  BMUCore::b()->push('Info(' . $option . ')');
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
