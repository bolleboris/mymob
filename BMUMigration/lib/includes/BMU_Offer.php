<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BMU_Offer
 *
 * @author erwin
 */
class BMU_Offer {
    //put your code here
   public function __construct() {}

   public function GetList() {
	  BMUCore::b()->push('List()');
   }
   public function Info($option = true) {
	  BMUCore::b()->push('Info('.$option.')');
   }
   
   public function RequestNew($offerTypeid) {
	  BMUCore::b()->pushFunc('RequestNew',func_get_args());
   }
   public function RequestEnd() {
	  BMUCore::b()->push("RequestEnd()");
   }
   public function RequestCancel() {
	  BMUCore::b()->push("RequestCancel()");
   }
   public function Block() {
	  BMUCore::b()->push("Block()");
   }
   public function Release() {
	  BMUCore::b()->push("Release()");
   }
   public function Accept() {
	  BMUCore::b()->push("Accept()");
   }
   public function Reject() {
	  BMUCore::b()->push("Reject()");
   }
   public function Start($StartDT) {
	  BMUCore::b()->push("Start('".$StartDT."')");
   }
   public function End() {
	  BMUCore::b()->push("End()");
   }
   public function Resources() {
      BMUCore::b()->push('Resources()');
   }
   public function ResourceAssign($resource_id) {
      BMUCore::b()->push('ResourceAssign('.$resource_id.')');
   }
   public function ResourceRemove($resource_id) {
      BMUCore::b()->push('ResourceRemove('.$resource_id.')');
   }

   public function Resource() {
	  return BMUCore::b()->Resource();
   }
}
?>
