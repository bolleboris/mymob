<?php

class BMU_Schedule {
	public function __construct() {}
	public function ListDay($Day) {
		BMUCore::b()->push("ListDay('".$Day."')");
	}
	public function ListWeek() {
		BMUCore::b()->push("ListWeek()");
	}
	public function CreateDay($Day, $PeriodArray) {
		BMUCore::b()->pushFunc('CreateDay',array($Day, 'alias'));
		BMUCore::b()->addToArray('alias', $PeriodArray);
	}
	public function CreateWeek($DayArray) {
		BMUCore::b()->pushFuncWithAlias('CreateWeek',$DayArray);
	}
	public function DeleteDay($Day) {
		BMUCore::b()->push("DeleteDay('".$Day."')");
	}
	public function DeleteWeek($DayArray) {
		BMUCore::b()->pushFuncWithAlias("DeleteWeek",$DayArray);
	}
}
?>
