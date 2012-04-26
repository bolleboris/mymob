<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BMU_Resource
 *
 * @author erwin
 */
class BMU_Resource {
    //put your code here
   public function __construct() {}

   public function GetList() {
	  BMUCore::b()->push('List()');
   }
   public function Info($option = true) {
	  BMUCore::b()->push('Info('.$option.')');
   }
   public function Assign() {
	  BMUCore::b()->push('Assign()');
   }
   public function Remove() {
	  BMUCore::b()->push('Remove()');
   }
   public function Attributes() {
	  return BMUCore::b()->Attributes();
   }
   public function ItemInfo($info) {
	  BMUCore::b()->push('ItemInfo('.$info.')');
   }
   public function ItemCreate($info) {
	  BMUCore::b()->push('ItemCreate('.$info.')');
   }
   public function ItemUpdate($info) {
	  BMUCore::b()->push('ItemUpdate('.$info.')');
   }
   public function ItemDelete($info) {
	  BMUCore::b()->push('ItemDelete('.$info.')');
   }
   public function GroupInfo($info) {
	  BMUCore::b()->push('GroupInfo('.$info.')');
   }
   public function GroupCreate($info) {
	  BMUCore::b()->push('GroupCreate('.$info.')');
   }
   public function GroupUpdate($info) {
	  BMUCore::b()->push('GroupUpdate('.$info.')');
   }
   public function GroupDelete($info) {
	  BMUCore::b()->push('GroupDelete('.$info.')');
   }
   
   public function ConfigurationGet() {
	  BMUCore::b()->push('ConfigurationGet()');
   }
   public function ConfigurationSet($ConfigurationArray) {
	  BMUCore::b()->pushFuncWithAlias('ConfigurationSet',$ConfigurationArray);
   }
   public function ConditionGet() {
	  BMUCore::b()->push('ConditionGet()');
   }
   public function ConditionSet($ConditionArray) {
	  BMUCore::b()->pushFuncWithAlias('ConditionSet',$ConditionArray);
   }
   public function CheckIn($GeoLocation, $TextLocation, $Remarks) {
	  BMUCore::b()->pushFunc('CheckIn',func_get_args());
   }
   public function CheckOut() {
	  BMUCore::b()->push('CheckOut()');
   }
   public function Schedule() {
	  return BMUCore::b()->Schedule();
   }
   public function Calendar($calendar_id = '') {
	  return BMUCore::b()->Calendar($calendar_id);
   }
   public function Location($location_id = '') {
	  return BMUCore::b()->Location($location_id);
   }
}
?>
