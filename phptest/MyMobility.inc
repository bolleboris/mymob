<?php
include "/home/Bram/BMUMigration/lib/BMUCore.php";
define('MYMOB_APP_KEY','a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a15');
define('MYMOB_APP_ID','5');

$BMUCore = BMUCore::b();
$BMUCore->Application()->Connect(MYMOB_APP_KEY);
$BMUCore->sendRequest();
$BMUCore->ProviderUI(MYMOB_APP_ID)->LoginUser('stefan@test.nl','stefan2');
$BMUCore->sendRequest();

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
	
	public function getAttribute(&$values) {
		foreach($values as $key => $value) {
			if($this->field == $key) {
				unset($values[$key]);
				return array('key' => $this->key, 'group' => $this->group, 'access' => $this->access, 'value' => $value);
			}
		}
	}
	
	public function getField() {
		return $this->field;
	}
}
$attributeMappings[] = new AttributeMapping('Kenteken', array('group' => 'General', 'key' => 'LicensePlateNumber', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Bijnaam', array('group' => 'General', 'key' => 'Alias', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Merk', array('group' => 'General', 'key' => 'Brand', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Model', array('group' => 'General', 'key' => 'Model', 'access' => 'Protected'));
//$attributeMappings[] = new AttributeMapping('', array('group' => 'General', 'key' => 'Color', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Brandstof', array('group' => 'General', 'key' => 'Fuel', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Tankpascode', array('group' => 'General', 'key' => 'FuelCardCode', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Opmerkingen', array('group' => 'General', 'key' => 'Notes', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Handleiding', array('group' => 'General', 'key' => 'Manual', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('AantalZitplaatsen', array('group' => 'General', 'key' => 'NrSeats', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('BoordComputer', array('group' => 'General', 'key' => 'BoardComputer', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Opties', array('group' => 'General', 'key' => 'Options', 'access' => 'Protected'));
//$attributeMappings[] = new AttributeMapping('', array('group' => 'General', 'key' => 'CarAdvertisement', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('Afbeelding', array('group' => 'General', 'key' => 'PrimaryPicture', 'access' => 'Protected'));
//$attributeMappings[] = new AttributeMapping('', array('group' => 'General', 'key' => 'Pictures', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('EigenRisicoWA', array('group' => 'General', 'key' => 'Deductible', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('EigenRisicoAllRisk', array('group' => 'General', 'key' => 'DeductibleCasco', 'access' => 'Protected'));
//$attributeMappings[] = new AttributeMapping('', array('group' => 'General', 'key' => 'RoadAssistance', 'access' => 'Protected'));
//$attributeMappings[] = new AttributeMapping('', array('group' => 'General', 'key' => 'KnownDamage', 'access' => 'Protected'));

$attributeMappings[] = new AttributeMapping('ToeslagPerKilometer', array('group' => 'Prices', 'key' => 'HourRate', 'access' => 'Protected'));
//$attributeMappings[] = new AttributeMapping('MaxTeBetalenUren', array('group' => 'Prices', 'key' => 'MaxHours', 'access' => 'Protected'));
$attributeMappings[] = new AttributeMapping('ToeslagPerUur', array('group' => 'Prices', 'key' => 'KilometerRate', 'access' => 'Protected'));
?>
