<?php
	class BMU_ResourceItem {
		public function __construct() {}
		
		public function Create($Group, $ResourceType, $Code, $Info = ' ') {
			BMUCore::b()->pushFunc('Create',func_get_args());
		}
		public function Update($Group, $Code, $Info = ' ') {
			BMUCore::b()->pushFunc('Update',func_get_args());
		}
		public function Delete() {
			BMUCore::b()->push('Delete()');
		}
	}
?>
