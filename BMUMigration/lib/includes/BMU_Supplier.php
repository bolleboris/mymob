<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BMU_Supplier
 *
 * @author erwin
 */
class BMU_Supplier {
    //put your code here
   public function __construct() {}

   public function Offer($offer_id = '') {
	  return BMUCore::b()->Offer($offer_id);
   }

   public function OfferType($offertype_id = '') {
	  return BMUCore::b()->OfferType($offertype_id);
   }
   public function Create() {
	  BMUCore::b()->push('Create()');
   }

   public function Resource($resource_id = '') {
	  return BMUCore::b()->Resource($resource_id);
   }
   public function ResourceItem($resourceItem_id = '') {
      return BMUCore::b()->ResourceItem($resourceItem_id);
   }
   public function ResourceGroup($resourceGroup_id = '') {
      return BMUCore::b()->ResourceGroup($resourceGroup_id);
   }
}
?>
