<?php

class BMU_Application {
   public function __construct() {   }

   public function Connect($applicationKey) {
	  BMUCore::b()->setApplicationConnected(true);
	  BMUCore::b()->push("Connect('" . $applicationKey . "')");   }

   public function Disconnect() {
	  BMUCore::b()->setApplicationConnected(false);
	  BMUCore::b()->push('Disconnect()');
   }
}
?>
