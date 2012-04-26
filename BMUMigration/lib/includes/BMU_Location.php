<?php
class BMU_Location {
	public function __construct() {}
	public function GetList() {
		BMUCore::b()->push('List()');
	}
	public function Info($Options) {
		BMUCore::b()->push("Info('".$Options."')");
	}
	public function Create($GeoLocation, $TextLocation, $Remarks, $isDefault) {
		BMUCore::b()->pushFunc('Create',func_get_args());
	}
	public function Update($GeoLocation, $TextLocation, $Remarks) {
		BMUCore::b()->pushFunc('Update',func_get_args());
	}
	public function Delete() {
		BMUCore::b()->push('Delete()');
	}
	public function setDefault() {
		BMUCore::b()->push('setDefault()');
	}
}
?>
