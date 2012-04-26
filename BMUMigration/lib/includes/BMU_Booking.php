<?php
class BMU_Booking {

	public function __construct() {}
	
	public function GetList() {
		BMUCore::b()->push('List()');
	}
	public function Info($Options = ' ') {
		BMUCore::b()->push("Info('.$Options.')");
	}
	public function RequestNew($contract_id, $offer_id, $service_id, $resource_id, $StartDT, $EndDT) {
	    BMUCore::b()->pushFunc('RequestNew',func_get_args());
	}
	public function RequestAlter($StartDT, $EndDT) {
	    BMUCore::b()->push("RequestAlter('".$StartDT."','".$EndDT."')");
	}
	public function RequestCancel($StartDT, $EndDT) {
	    BMUCore::b()->push("RequestCancel()");
	}    
}
?>
