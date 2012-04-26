<?php

class BMU_Service {
	public function __construct() {}
	public function GetList() {
		BMUCore::b()->push('List()');
	}
	public function Info($options = '') {
		BMUCore::b()->push('Info('.$options.')');
	}
	public function Create($ContractType, $OfferType, $Code, $Info = '') {
		BMUCore::b()->pushFunc('Create',func_get_args());
	}
	public function Update($Code, $Info = '') {
		BMUCore::b()->pushFunc('Update',func_get_args());
	}
	public function Delete() {
		BMUCore::b()->push('Delete()');
	}
}
?>
