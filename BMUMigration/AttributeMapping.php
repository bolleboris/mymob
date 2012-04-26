<?php
include_once('__ini.php');
abstract class AttributeMapping {
	public abstract function getAttribute($input);
	public static function createMapping($mapFromArray, $mapToAttribute) {

	}
}
?>
