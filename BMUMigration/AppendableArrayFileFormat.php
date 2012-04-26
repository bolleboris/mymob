<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PersonsFileFormat
 *
 * @author erwin
 */
define('PARSE_KEY_STRING', 0);
define('PARSE_VALUE_STRING', 1);

class AppendableArrayFileFormat {

// { } to indicate an array
// : to indicate a split between key and value
// , to indicate a split between key-value pairs
   private $myFile;
   private $isOpened = false;
   private $fileHandler = false;

   public function __construct($fileName) {
	  $this->myFile = $fileName . ".aaff";
	  $this->fileHandler = false;
   }

   public function openFile() {
	  $fh = fopen($this->myFile, 'w') or die("can't open file");
	  $stringData = "";
	  fwrite($fh, $stringData);
	  $this->isOpened = true;
	  fclose($fh);
   }

   public function closeFile() {
	  $fh = fopen($this->myFile, 'a+') or die("can't open file");
	  $stringData = "";
	  $this->isOpened = false;
	  fwrite($fh, $stringData);
	  fclose($fh);
   }

   public function appendArray($array) {
	  $fh = fopen($this->myFile, 'a+') or die("can't open file");
	  if ($this->isOpened) {
		 fwrite($fh, json_encode($array) . "\n");
	  }
	  fclose($fh);
   }

   public function getKey($key) {
	  $fh = fopen($this->myFile, 'r') or die("can't open file");
	  while($line = fgets($fh)) {
		 $arr = json_decode($line, true);
		 if(isset($arr[$key])) {
			return $arr[$key];
		 }
	  }
	  return false;
	  fclose($fh);
   }
   public function startIteration() {
	  $this->fileHandler = fopen($this->myFile, 'r') or die("can't open file");
   }
   public function iterate() {
	  if($this->fileHandler) {
		 $s = fgets($this->fileHandler);
		 if($s) {
			return json_decode($s, true);
		 } else {
			return false;
		 }
	  }
	  return false;
   }
   public function endIteration() {
	  fclose($this->fileHandler);
	  $this->fileHandler = false;
   }
}

?>
