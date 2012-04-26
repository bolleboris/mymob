<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BMU_Customer
 *
 * @author erwin
 */
class BMU_Customer {
    //put your code here
   public function __construct() {}

   public function Contract($contract_id = '') {
	  return BMUCore::b()->Contract($contract_id);
   }
   public function ContractType($contracttype_id = '') {
	  return BMUCore::b()->ContractType($contracttype_id);
   }
}
?>
