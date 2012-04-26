<?php

include "../../BMUMigration/lib/BMUCore.php";
define('MYMOB_APP_KEY','a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a15');
define('MYMOB_APP_ID','100');
define('MYMOB_APP_CODE','MyWheels-SA');
define('MYMOB_ACCESS_TOKEN','wesfsgsdhcnnsthbvnethdgnedthtndf');

$BMUCore = BMUCore::b();
$BMUCore->addToArray('access_token',MYMOB_ACCESS_TOKEN);
$res = $BMUCore->sendRequest();


class AttributeMapping {
	private $field, $key, $group, $access;
	public function __construct($field, $attributeDef) {
		$this->field = $field;
		$this->key = $attributeDef['key'];
		$this->group = $attributeDef['group'];
		$this->access = $attributeDef['access'];
	}
	public function getValue($attributes) {
		foreach($attributes as $attribute) {
			if($attribute['key'] == $this->key && $attribute['group'] == $this->group && $attribute['access'] == $this->access) {
				return $attribute['value'];
			}
		}
	}

	public static function getValuesScoreSeperated($attributes) {
	   $output = array();
	   foreach($attributes as $attribute) {
		 $output[$attribute['group'].'_'.$attribute['key'].'_'.$attribute['access']] = $attribute['value'];
	   }
	   return $output;
	}

	public static function getAttributesScoreSeperated($values) {
	   $output = array();
	   $att = array();
	   foreach($values as $key => $value) {
		  if($value != '') {
			 $exp = explode('_',$key);
			 $att['group'] = $exp[0];
			 $att['key'] = $exp[1];
			 $att['access'] = $exp[2];
			 $att['value'] = $value;
			 $output[] = $att;
		  }
	   }
	   return $output;
	}

	public function getAttribute(&$values) {
		foreach($values as $key => $value) {
			if($this->field == $key) {
				$attribute = array('key' => $this->key, 'group' => $this->group, 'access' => $this->access, 'value' => $value);
				unset($values[$key]);
				return $attribute;
			}
		}
	}

	public function getField() {
		return $this->field;
	}
}

?>