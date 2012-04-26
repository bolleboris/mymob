<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BMU_Legal
 *
 * @author erwin
 */
class BMU_Legal {
    //put your code here
   public function __construct() {}

   public function GetList() {
	  BMUCore::b()->push('List()');
   }
   public function Info($options = true) {
	  BMUCore::b()->push('Info('.$options.')');
   }
   public function ChangeContact($Email) {
	  BMUCore::b()->push('ChangeContact('.$Email.')');
   }
   public function ChangeName($NewName) {
	  BMUCore::b()->push('ChangeName('.$NewName.')');
   }
   public function Supplier() {
	  return BMUCore::b()->Supplier();
   }
   public function Attributes() {
	  return BMUCore::b()->Attributes();
   }
   public function Friends() {
      BMUCore::b()->push('Friends()');
   }
   public function FriendAssign($EntityKey) {
      BMUCore::b()->push('FriendAssign('.$EntityKey.')');	
   }
   public function FriendRemove($EntityKey) {
      BMUCore::b()->push('FriendRemove('.$EntityKey.')');
   }
   public function Unsubscribe() {
      BMUCore::b()->push('Unsubscribe()');
   }
   public function Contract($contract_id = '') {
	  return BMUCore::b()->Contract($contract_id);
   }
}
?>
