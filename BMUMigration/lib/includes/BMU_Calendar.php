<?php
class BMU_Calendar {
	public function __construct() {}
	public function GetList($fromdt, $uptodt) {
		BMUCore::b()->push("List('" . $fromdt . "','" . $uptodt . "')");
	}
	public function Info($Options) {
		BMUCore::b()->push("Info('".$Options."')");
	}
	public function Create($Description, $StartDT, $EndDT) {
		BMUCore::b()->pushFunc('Create',func_get_args());
	}
	public function Update($Description, $StartDT, $EndDT) {
		BMUCore::b()->pushFunc('Update',func_get_args());
	}
	public function Delete() {
		BMUCore::b()->push('Delete()');
	}
}
?>
