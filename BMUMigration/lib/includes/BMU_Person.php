<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class BMU_Person {
   public function  __construct() {
   }
   public function Attributes() {
	  return BMUCore::b()->Attributes();
   }
   public function Supplier() {
	  return BMUCore::b()->Supplier();
   }
   public function GetList() {
	  BMUcore::b()->push('List()');
   }
   public function RoleList() {
	  BMUCore::b()->push('RoleList()');
   }
   public function Info($options = true) {
	  BMUCore::b()->push('Info('.$options.')');
   }
   public function ChangeEmail($NewEmail) {
	  BMUCore::b()->push('ChangeEmail('.$NewEmail.')');
   }
   public function ChangeAdmin($Email) {
	  BMUCore::b()->push('ChangeAdmin('.$Email.')');
   }
   public function Friends() {
      BMUCore::b()->push('Friends()');
   }

   public function SubscribeLegal($EntityKey) {
	  BMUCore::b()->push("SubscribeLegal('$EntityKey')");
   }
   
}

?>
