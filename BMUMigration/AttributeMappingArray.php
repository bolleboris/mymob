<?php
include_once('__ini.php');
class AttributeMappingArray {
	private $mappings,$type;
	public function __construct() {
		$this->mappings = array();
	}
	public function add($mapping) {
		$this->mappings[] = $mapping;
	}
	public function setType($type) {
		$this->type = $type;
	}
	public function getArray() {
		return $this->mappings;
	}
	public function addMapping($mapFromArray, $mapToArray, $type = null) {
		$type = ($type != null)?: $this->type;
		$this->mappings[] = $type::createMapping($mapFromArray, $mapToArray);
	}
}
?>
