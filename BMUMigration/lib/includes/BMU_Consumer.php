<?php

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BMU_Consumer
 *
 * @author erwin
 */
class BMU_Consumer {
    //put your code here
   public function __construct() {}

   public function GetList() {
	  BMUCore::b()->push('List()');
   }
   public function Assign($Email) {
	  BMUCore::b()->push("Assign('".$Email."')");
   }
   public function Remove($Email) {
	  BMUCore::b()->push("Remove('".$Email."')");
   }
   public function FindResources($conditionArray, $areaPolygon, $startDt, $endDt) {
   
   }

   public function Contract($contract_id = '') {
	  return BMUCore::b()->Contract($contract_id);
   }
   public function Booking($booking_id = '') {
      return BMUCore::b()->Booking($booking_id);
   }
}
?>
