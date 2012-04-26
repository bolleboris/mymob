<?php
include('./BMUCore-include.php')

class BMUWorker {

$current;

public function __construct() {
$current = BMUCore::b();}

public function __call ($name, $arguments) {
$current = $current->callFuncWithName($name, $arguments);
return $this;
}
public function Send() {
$current = BMUCore::b();
return $current->sendRequest();
}

}
?>
