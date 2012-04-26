<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BMU_ProviderSA
 *
 * @author erwin
 */
class BMU_ProviderSA {
    //put your code here
   public function __construct() {}
   public function Person($Person_id = '') {
	  return BMUCore::b()->Person($Person_id);
   }
   public function Legal($Legal_id = '') {
	  return BMUCore::b()->Legal($Legal_id);
   }
   public function ContractType($ContractType_id = '') {
	  return BMUCore::b()->ContractType($ContractType_id);
   }
   public function Customer($Customer_id = '') {
	  return BMUCore::b()->Customer($Customer_id);
   }
   public function Contract($Contract_id = '') {
	  return BMUCore::b()->Contract($Contract_id);
   }
   public function OfferType($OfferType_id = '') {
	  return BMUCore::b()->OfferType($OfferType_id);
   }
   public function Offer($Offer_id = '') {
	  return BMUCore::b()->Offer($Offer_id);
   }
   public function Supplier($Supplier_id = '') {
	  return BMUCore::b()->Supplier($Supplier_id);
   }
   public function Service($Service_id = '') {
   	return BMUCore::b()->Service($Service_id);
   }
   public function CreateLegal($legalName, $contact) {
	  BMUCore::b()->push("CreateLegal('$legalName','$contact')");
   }
   public function TimeTest() {
	  BMUCore::b()->push("TimeTest()");
   }
   public function GetCars($supplierId) {
	  BMUCore::b()->push("GetCars($supplierId)");
   }
	public function FindEntities($EntityType,$AttributeGroup,$AttributeKey,$AttributeAccess,$regexp){
		BMUCore::b()->push("FindEntities('".$EntityType."','".$AttributeGroup."','".$AttributeKey."','".$AttributeAccess."','".$regexp."')");
	}
	public function SearchUsers($regEx, $Limit = 0, $Offset = 0) {
		BMUCore::b()->push("SearchUsers('$regEx',$Limit,$Offset)");
	} 
	public function ConsumerBookingList($userId) {
		BMUCore::b()->push("ConsumerBookingList($userId)");
	} 
	public function Booking($booking_id = '') {
      return BMUCore::b()->Booking($booking_id);
    }
    public function PersonContractList($userId) {
		BMUCore::b()->push("PersonContractList($userId)");
	} 
	public function ActiveBookingList() {
		BMUCore::b()->push("ActiveBookingList()");
	}
	public function RegisterPerson($appCode,$EntityKey,$Email,$Password) {
		BMUCore::b()->push("RegisterPerson('$appCode','$EntityKey','$Email','$Password')");
	}
}
?>
