<?php


//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Pick:
//Randomly picks $amount elements out of the specified Array (works with strings aswell!)
//$amount is defaulted to 1
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function pick($pickOneArray, $amount = 1) {
	$output = '';
	$count = count($pickOneArray) - 1;
	if(is_string($pickOneArray)) {
		$count = strlen($pickOneArray) - 1;
	}
	for($i = 0; $i < $amount; $i += 1) {
		$output .= $pickOneArray[rand(0,$count)];
	}
	return $output;
}


//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Fill Entity Attributes
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function fillPersonAttributes($B, $curPro, $person, $attributes) {
	$attributeArray = array();
	foreach($attributes as $key => $attribute) {
		$attributeCreate = array();
		$attributeCreate['key'] = $attribute['key'];
		$attributeCreate['group'] = $attribute['group'];
		$attributeCreate['value'] = pick($attribute['posValues']);
		$attributeCreate['access'] = $attribute['access'];	
		$attributeArray[] = $attributeCreate;	
	}
	$B->ProviderUI($curPro['id'])->Person($person['me'])->Attributes()->ListCreate($attributeArray);
	$response = $B->sendRequest();
	return $response['result'];
}

function fillResource($B, $curPro, $person, $resource, $attributes) {
	$attributeArray = array();
	foreach($attributes as $key => $attribute) {
		$attributeCreate = array();
		$attributeCreate['key'] = $attribute['key'];
		$attributeCreate['group'] = $attribute['group'];
		$attributeCreate['value'] = pick($attribute['posValues']);
		$attributeCreate['access'] = $attribute['access'];	
		$attributeArray[] = $attributeCreate;	
	}
	$B->Supplier($person['me'])->Resource($resource)->Attributes()->ListCreate($attributeArray);
	$response = $B->sendRequest();
	return $response['result'];
}

function updateResource($B, $curPro, $person, $resource, $attributes) {
	$attributeArray = array();
	foreach($attributes as $key => $attribute) {
		$attributeCreate = array();
		$attributeCreate['key'] = $attribute['key'];
		$attributeCreate['group'] = $attribute['group'];
		$attributeCreate['value'] = pick($attribute['posValues']);
		$attributeCreate['access'] = $attribute['access'];	
		$attributeArray[] = $attributeCreate;	
	}
	$B->ProviderSA($curPro['id'])->Resource($resource)->Attributes()->ListUpdate($attributeArray);
	$response = $B->sendRequest();
	return $response['result'];
}


//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Create N Entities Functions
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function createNRandomContractTypes($B, $curPro, $amount = 1) {
	$createdContractTypes = array();
	for($i = 0; $i < $amount; $i++) {
		$response = createContractType($B, $curPro, 'CTYPE-'.pick('ABCDEFGHIJKLMNOQRSTUVWXYZ',rand(2,4)), 'Randomly Generated ContractType');
		if($response['result'] == 0 || $response['result'] == '0') {
			$createdContractTypes[] = $response['contract_type_id'];
			logg('Succesfully generated contracttype with contracttype id: '. $response['contract_type_id']);
		}
	}
	return $createdContractTypes;
}

function createNRandomOfferTypes($B, $curPro, $amount = 1) {
	$createdOfferTypes = array();
	for($i = 0; $i < $amount; $i++) {
		$response = createOfferType($B, $curPro, 'OTYPE-'.pick('ABCDEFGHIJKLMNOQRSTUVWXYZ',rand(2,4)), 'Randomly Generated OfferType');
		if($response['result'] == 0 || $response['result'] == '0') {
			$createdOfferTypes[] = $response['offer_type_id'];
			logg('Succesfully generated offertype with offertype id: '. $response['offer_type_id']);
		}
	}
	return $createdOfferTypes;
}

function createNRandomServices($B, $curPro, $createdContractTypes = array(5), $createdOfferTypes = array(5), $amount = 1) {
	$createdServices = array();
	for($i = 0; $i < $amount; $i++) {
		$response = createService($B, $curPro, pick($createdContractTypes), pick($createdOfferTypes),
									 'Service-'.pick('ABCDEFGHIJKLMNOPQRSTUVWXYZ',rand(2,5)),'Randomly Generated Service');
									 
		if($response['result'] == 0 || $response['result'] == '0') {
			$createdServices[] = $response['offer_type_id'];
			logg('Succesfully generated service with service id: '. $response['offer_type_id']);
		} else {
			logvar($response);
		}
	}
	return $createdServices;
}

function createNRandomSubscribers($B, $curPro, $amount = 1) {
	$createdPersons = array();
	for($i = 0; $i < $amount; $i++) {	
		$curUser = array('id'=>'RandomUser-'.pick('ABCDEFGH',5).'@'.pick(array('gmail','hotmail','hotmale','live','brothom')).'.com',
						'pass'=>'password-'.pick('ABCDEFGHIJLKMNOQRSTUA@$%^@#$%235462',5),
						'me' => '');
		$curUser = createPerson($B, $curPro, $curUser);
		if($curUser != false) {
			logg('Successfully created Person '.$curUser['me'].' with e-mail "'.$curUser['id'] . '" and password "'. $curUser['pass']. '"');
			$createdPersons[] = $curUser;
		}		
	}
	
	return $createdPersons;	
}

function createSupplier($B, $curPro, $curUser) {
	$B->Me()->Supplier()->Create();
	//logg($B->__toString());
	$response = $B->sendRequest();
	return $response['result'];
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Subscribe Functions
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function subscribePerson($B, $curPro, $Email) {
	$B->ProviderUI($curPro['id'])->SubscribePerson($Email);
	$response = $B->sendRequest();
	return $response['result'];
}

function subscriptionConfirm($B, $curPro, $subKey) {
	$B->ProviderUI($curPro['id'])->SubscriptionConfirm($subKey);
	$response = $B->sendRequest();
	return $response['result'];
}

function subscriptionComplete($B, $curPro, $subKey, $pass) {
	$B->ProviderUI($curPro['id'])->SubscriptionComplete($subKey, $pass);
	$response = $B->sendRequest();
	return $response['result'];
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Create Entity Functions
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function createContractType($B, $curPro, $Code, $Info = ' ') {
	//setProviderSA($B, $curPro);
	$B->ProviderSA($curPro['id'])->ContractType()->Create($Code,$Info);
	$response = $B->sendRequest();
	return $response['result'];
}
function createOfferType($B, $curPro, $Code, $Info = ' ') {
	//setProviderSA($B, $curPro);
	$B->ProviderSA($curPro['id'])->OfferType()->Create($Code,$Info);
	$response = $B->sendRequest();
	return $response['result'];
}
function createService($B, $curPro, $ContractType, $OfferType, $Code, $Info = ' ') {
	//setProviderSA($B, $curPro);
	$B->ProviderSA($curPro['id'])->Service()->Create($ContractType, $OfferType, $Code, $Info);
	logg($B->__toString());
	$response = $B->sendRequest();
	return $response['result'];
}
function createResourceGroup($B, $curPro, $Supplier_id, $ResourceType, $Code, $Info) {
	//loginUser($B, $curPro, $curUser);
	$B->Supplier($Supplier_id)->ResourceGroup()->Create($ResourceType, $Code, $Info);
	$response = $B->sendRequest();
	return $response['result'];
}
function createResourceItem($B, $curPro, $Supplier_id, $Group, $ResourceType, $Code, $Info) {
	//loginUser($B, $curPro, $curUser);
	$B->Supplier($Supplier_id)->ResourceItem()->Create($Group, $ResourceType, $Code, $Info);
	$response = $B->sendRequest();
	return $response['result'];
}
function createPerson($B, $curPro, $curUser) {
		
	$response = subscribePerson($B, $curPro, $curUser['id']);		
	if($response['result'] != 0 && $response['result'] != '0') {
		return false;
	}
		
	$subKey = $response['subscribe_key'];		
	$response = subscriptionConfirm($B, $curPro, $subKey);		
	if($response['result'] != 0 && $response['result'] != '0') {
		return false;
	}
			
	$response = subscriptionComplete($B, $curPro, $subKey, $curUser['pass']);		
	if($response['result'] != 0 && $response['result'] != '0') {
		return false;
	}
	$curUser['me'] = $response['me_person_id'];
	
	return $curUser;
}


//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Login User Functions
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function loginUser($B, $curPro, $curUser) {
	$B->ProviderUI($curPro['id'])->LoginUser($curUser['id'],$curUser['pass']);
	$result = $B->sendRequest();
	//var_export($result);
	
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Change Provider Functions
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Set Provider to uikey and log in as System Administrator
function setProvider($B, $curPro) {
	$B->Application()->Connect($curPro['uikey']);
	$B->sendRequest();
}

//Log in as Provider SA
function setProviderSA($B, $curPro) {
	loginUser($B, $curPro, $curPro['admin']);	
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Change Provider Functions
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

function logg($string = '', $newline = true) {
	if(DEBUG_MODE) {
		echo (($newline)? "\n" : '').$string;
	}
}
function logvar($var) {
	if(DEBUG_MODE) {
		var_dump($var);
	}
}

?>
