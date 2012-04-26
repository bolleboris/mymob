<?php
class class1 {
	public function __construct() {}
	public function getClass2() {
		return new class2();
	}
}
class class2 {
	public function __construct() {}
	public function getClass3() {
		return new class3();
	}
}

class class3 {
	public function __construct() {}
	public function getClass4() {
		echo "\nGreat Success.\n";
	}
}

	$b = new class1();
	$blaat = array('getClass2','getClass3','getClass4');
	$temp = $b;
	for($i = 0; $i < count($blaat); $i++) {
		$temp = $temp->$blaat[$i]();
	}
	//$string = '$b->getClass2()->getClass3()->getClass4';
	//$string();

?>
