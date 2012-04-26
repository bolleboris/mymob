<?php
include_once('__ini.php'); 

class PersonAttributeMapper extends AttributeMapper {
	private $appKey, $providerID, $providerUserName, $providerPassword, $personID;
	
	public function __construct($provider, $personID) {
		$this->appKey = $provider['uikey'];
		$this->providerID = $provider['id'];
		$this->providerUserName = $provider['admin']['id'];
		$this->providerPassword = $provider['admin']['pass'];
		$this->personID = $personID;
	}
	
	protected function updateAttribute($att) {
		$B = BMUCore::b();
		$B->ProviderSA($this->providerID)->Person($this->personID)->Attributes()->ListUpdate($att);
		$response = BMUCore::b()->sendRequest();
		//var_dump($response['result']);
		return $response['result'];
	}
	
	protected function createAttribute($att) {
		$B = BMUCore::b();
		$B->ProviderSA($this->providerID)->Person($this->personID)->Attributes()->ListCreate($att);
		$response = BMUCore::b()->sendRequest();
		//var_dump($response['result']);
		return $response['result'];
	}
	
	protected function prepareEnvironment() {
		/*$B = BMUCore::b();
		$B->Application()->Connect($this->appKey);
		$response = $B->sendRequest();
		if($response['result']['result'] != '0') {
			die("ERROR, KILLING PHP SCRIPT\nIncorrect Application Key entered.\n");
		}
		$B->ProviderUI($this->providerID)->LoginUser($this->providerUserName, $this->providerPassword);
		$B->sendRequest();
		if($response['result']['result'] != '0') {
			die("Wrong UserName / Password Combination entered for Wrong Provider");
		}*/
		//echo "environment successfully prepared\n";
	}	
}
?>
