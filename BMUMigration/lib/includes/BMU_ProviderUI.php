<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class BMU_ProviderUI {
   public function __construct() {
   }

   //ProviderUI Roles
   
   public function Person($person_id = '') {
	  return BMUCore::b()->Person($person_id);
   }
   public function Legal($legal_id = '') {
	  return BMUCore::b()->Legal($legal_id);
   }
   public function ContractType($contractType_id = '') {
	  return BMUCore::b()->ContractType($contractType_id);
   }
   public function Contract($contract_id = '') {
	  return BMUCore::b()->Contract($contract_id);
   }
   public function Customer($customer_id = '') {
	  return BMUCore::b()->Customer($customer_id);
   }
   public function OfferType($offerType_id = '') {
	  return BMUCore::b()->OfferType($offerType_id);
   }
   public function Offer($offer_id = '') {
	  return BMUCore::b()->Offer($offer_id);
   }
   public function Supplier($supplier_id = '') {
	  return BMUCore::b()->Supplier($supplier_id);
   }

   //ProviderUI Native functions

   public function SubscribePerson($Email) {
	  BMUCore::b()->push("SubscribePerson('".$Email."')");
   }
   public function SubscribeLegal($Email, $LegalName) {
	  BMUCore::b()->pushFunc("SubscribeLegal",func_get_args());
   }
   public function SubscriptionConfirm($SubscribeKey) {
	  BMUCore::b()->push("SubscriptionConfirm('".$SubscribeKey."')");
   }
   public function SubscriptionComplete($SubscribeKey, $Authentication) {
	  BMUCore::b()->pushFunc('SubscriptionComplete',func_get_args());
   }
   public function SubscriberAttributes($SubscribeKey) {
	  BMUCore::b()->push("SubscriberAttributes('".$SubscribeKey."')");
   }
   public function LoginUser($Email,$Authentication) {
	  BMUCore::b()->pushFunc('LoginUser',func_get_args());
   }
   public function ResetAuthentication($Email) {
	  BMUCore::b()->push("ResetAuthentication('".$Email."')");
   }
	public function FindEntities($EntityType,$AttributeGroup,$AttributeKey,$AttributeAccess,$regexp){
		BMUCore::b()->push("FindEntities('".$EntityType."','".$AttributeGroup."','".$AttributeKey."','".$AttributeAccess."','".$regexp."')");
	}
}
?>
