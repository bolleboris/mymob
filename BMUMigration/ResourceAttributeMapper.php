<?php
include_once('__ini.php'); 

class ResourceAttributeMapper extends AttributeMapper {
	private $appKey, $providerID, $providerUserName, $providerPassword, $personID, $resourceID;
	
	public function __construct($provider, $resourceID, $personID) {
		$this->appKey = $provider['uikey'];
		$this->providerID = $provider['id'];
		$this->providerUserName = $provider['admin']['id'];
		$this->providerPassword = $provider['admin']['pass'];
		$this->resourceID = $resourceID;
		$this->personID = $personID;
	}
	
	protected function updateAttribute($att) {
		$B = BMUCore::b();
		$B->ProviderSA($this->providerID)->Supplier($this->personID)->Resource($this->resourceID)->Attributes()->ListUpdate($att);
		$response = $B->sendRequest();
		return $response['result'];
	}
	
	protected function createAttribute($att) {
		$B = BMUCore::b();
		$B->ProviderSA($this->providerID)->Supplier($this->personID)->Resource($this->resourceID)->Attributes()->ListCreate($att);
		$response = $B->sendRequest();
		//var_dump($response['result']);
		echo "created attributes\n";
		return $response['result'];
	}
	protected function prepareEnvironment() {
		/*$B = BMUCore::b();
		$B->Application()->Connect($this->appKey);
		$response = $B->sendRequest();
		if($response['result']['result'] != 0) {
			die("ERROR, KILLING PHP SCRIPT\nIncorrect Application Key entered.\n");
		}
		$B->ProviderUI($this->providerID)->LoginUser($this->providerUserName, $this->providerPassword);
		$B->sendRequest();
		if($response['result']['result'] != 0) {
			die("Wrong UserName / Password Combination entered for Wrong Provider\n");
		}
		//$this->personID = $response['result']['me_person_id'];
		echo "environment successfully prepared\n";*/
	}	
}
?>
