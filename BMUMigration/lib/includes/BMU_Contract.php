<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BMU_Contract
 *
 * @author erwin
 */
class BMU_Contract {
    //put your code here
   public function __construct() {}

   public function GetList() {
	  BMUCore::b()->push('List()');
   }
   public function Info($options = true) {
	  BMUCore::b()->push('Info('.$options.')');
   }
   public function RequestNew($ContractTypeId, $StartDT) {
	  BMUCore::b()->push('RequestNew('.$ContractTypeId.",'".$StartDT.'\')');
   }
   public function RequestEnd($EndDT) {
	  BMUCore::b()->push("Requestend('".$EndDT."')");
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
   public function Accept($startDt, $endDt) {
	  BMUCore::b()->push("Accept('".$startDt."','".$endDt."')");
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
   public function Consumers() {
      BMUCore::b()->push('Consumers()');
   }
   public function ConsumerAssign($EntityKey) {
      BMUCore::b()->push("ConsumerAssign('".$EntityKey."')");
   }
   public function ConsumerRemove($EntityKey) {
      BMUCore::b()->push('ConsumerRemove(\''.$EntityKey.'\')');
   }

   public function Consumer() {
	  return BMUCore::b()->Consumer();
   }
}
?>
