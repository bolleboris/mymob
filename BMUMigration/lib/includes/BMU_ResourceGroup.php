<?php
	class BMU_ResourceGroup {
		public function __construct() {}
		
		public function Create($ResourceType, $Code, $Info = ' ') {
			BMUCore::b()->pushFunc('Create',func_get_args());
		}
		public function Update($Code, $Info = ' ') {
			BMUCore::b()->pushFunc('Update',func_get_args());
		}
		public function Delete() {
			BMUCore::b()->push('Delete()');
		}
	}
?>
