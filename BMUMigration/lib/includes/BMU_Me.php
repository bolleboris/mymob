<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BMU_Me
 *
 * @author erwin
 */
class BMU_Me {
    //put your code here
   public function __construct() {}

   public function Logout() {
	  BMUCore::b()->push('Logout()');
   }
   public function Info($options = true) {
	  BMUCore::b()->push('Info('.$options.')');
   }
   public function RoleList() {
	  BMUCore::b()->push('RoleList()');
   }
   public function ChangeAuthentication($OldAuthentication,$NewAuthentication) {
	  BMUCore::b()->pushFunc('ChangeAuthentication',func_get_args());
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
   public function FriendAssign($EntityKey) {
      BMUCore::b()->push('FriendAssign('.$EntityKey.')');	
   }
   public function FriendRemove($EntityKey) {
      BMUCore::b()->push('FriendRemove('.$EntityKey.')');
   }
   public function Unsubscribe() {
      BMUCore::b()->push('Unsubscribe()');
   }
   public function SubscribeLegal($LegalName) {
      BMUCore::b()->push('SubscribeLegal('.$LegalName.')');
   }

   public function Attributes() {
	  return BMUCore::b()->Attributes();
   }

   public function Supplier() {
	  return BMUCore::b()->Supplier();
   }
}
?>
