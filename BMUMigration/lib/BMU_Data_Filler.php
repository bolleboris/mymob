<?php
include('./BMUCore-include.php');
include('./BMUMigration.php');
echo "\n";
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Variable definitions
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Providers defined, id = provider_id, sakey = key for connecting as SA, uikey = Key for connect as UI.

define('DEBUG_MODE',true);
$providers = array(array('id' => '5','uikey' => 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a15', 'admin' => array('id' => 'admin@provider2.nl', 'pass' => 'provider2SA')));
$B = BMUCore::b();
$createdContractTypes = array();
$createdOfferTypes = array();
$createdServices = array();
$createdPersons = array();
array('group' => '', 'key' => '', 'access' => '');
$personAttributes = array(array('group' => 'persoon', 'key' => 'voornaam', 'access' => 'public',
								'posValues' => array('Jaap','Jan','Piet','Erwin','Sjaak','Bert','Slawa','Viatcheslav','Dmitrievitch','Ronald','Bert','Bert','Bert','Trixie', 'Saren')),
						  array('group' => 'persoon', 'key' => 'achternaam', 'access' => 'public', 
						  		'posValues' => array('Haverman','Haasnoot','Thomas','Thomas','Thomas','Radtchenko','Melis','Kraaij','Kraaij','Kus','de Jong','Bert','Bertje','Bertus','Berteman','Bertmans','Bert van Ernie','Inden')),
						  array('group' => 'persoon', 'key' => 'voorletters', 'access' => 'public',
						  		'posValues' => array('AA','AB','AC','AD','BC','DD','ASD','WE','AD','KL','LK','MM','AA','PP','ASDASD','DFM','ADSKL','WE')),
						  array('group' => 'persoon', 'key' => 'geboortedatum', 'access' => 'public', 
						  		'posValues' => array('04-11-1988','14-12-1987','05-01-2012','23-23-23','22-22-22')));
						  		
$carAttributes = array(array('group' => 'General', 'key' => 'KnownDamage', 'access' => 'protected',
								'posValues' => array('Schade aan de voorgevel','Geen schade','Remmen Kapot','Gas Pedaal kan maar op 2 standen, geen gas of vol gas','')),
					   array('group' => 'General', 'key' => 'Color', 'access' => 'protected',
							    'posValues' => array('Blue','Geen schade','Purper','Turquoise','Groen')),
					   array('group' => 'General', 'key' => 'RoadAssistance', 'access' => 'protected',
							    'posValues' => array('01234556789','544242124','3535462356','Geen','06-42HELPME')));
$curPro = array();
$curUser = array('id'=>'stefan@test.nl','pass'=>'stefan', 'me' => '19');
$createdSuppliers = array();

$createdResourceItems = array();
$createdResourceGroups = array();

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//main
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$curPro = $providers['0'];
setProvider($B, $curPro);

setProviderSA($B, $curPro);

$createdContractTypes = createNRandomContractTypes($B, $curPro, 2);

logg();
$createdOfferTypes = createNRandomOfferTypes($B, $curPro, 2);
logg();

$createdServices = createNRandomServices($B, $curPro, $createdContractTypes, $createdOfferTypes, 1000);

setProvider($B, $curPro);

$createdPersons = createNRandomSubscribers($B, $curPro, 1);

foreach($createdPersons as $key => $person) {
	$response = loginUser($B, $curPro, $person);
	if($response['result'] == 0  || $response['result'] == '0') {
		logg('login succesful, ' . $response['me_person_id']);
	} else {
		logg('LOGIN FAILED FOR PERSON: ' . $person['id']);
	}
	$response = fillPersonAttributes($B, $curPro, $person, $personAttributes);
	//logvar($response);
	if(rand(1,3) == 2 || true) {
		$response = createSupplier($B, $curPro, $person);
		//logvar( $response);
		if($response['result'] == 0  || $response['result'] == '0') {
			logg('Supplier('.$person['me'].') created.');
			$createdSuppliers[] = $person;
		}
	}	
}

logvar($createdSuppliers);


//Hier verder, Resources aanmaken e.d.
foreach($createdSuppliers as $key => $supplier) {
	//Either be a mass Resource provider, or a singular one.
	if(rand(1,2) == 1) { //mass provider
		//$response = createResourceGroup($B, $curPro, $supplier['me'],  $Group, $ResourceType, $Code, $Info
	}
}

logg("\n");


/*$curPro = $providers['0'];
setProvider($B, $curPro);
$response = setProviderSA($B, $curPro);
//$response = loginUser($B, $curPro, $curUser);
if($response['result'] == 0  || $response['result'] == '0') {
	logvar(fillResource($B, $curPro, $curUser, 4, $carAttributes));
	logvar(fillResource($B, $curPro, $curUser, 5, $carAttributes));
	logvar(fillResource($B, $curPro, $curUser, 6, $carAttributes));
	logvar(fillResource($B, $curPro, $curUser, 7, $carAttributes));
}

*/



?>
