<?php

require_once('includes/BMU_Application.php');
require_once('includes/BMU_Attributes.php');
require_once('includes/BMU_Auth.php');
require_once('includes/BMU_Booking.php');
require_once('includes/BMU_Consumer.php');
require_once('includes/BMU_Contract.php');
require_once('includes/BMU_ContractType.php');
require_once('includes/BMU_Customer.php');
require_once('includes/BMU_Legal.php');
require_once('includes/BMU_Me.php');
require_once('includes/BMU_Offer.php');
require_once('includes/BMU_OfferType.php');
require_once('includes/BMU_Person.php');
require_once('includes/BMU_ProviderSA.php');
require_once('includes/BMU_ProviderUI.php');
require_once('includes/BMU_Resource.php');
require_once('includes/BMU_ResourceItem.php');
require_once('includes/BMU_ResourceGroup.php');
require_once('includes/BMU_Service.php');
require_once('includes/BMU_Supplier.php');
require_once('includes/BMU_Schedule.php');
require_once('includes/BMU_Calendar.php');
require_once('includes/BMU_Location.php');

class BMUCore {

    private static $instance;
	private $bmu_rmi, $namespaces, $jsonArray, $callStack, $curl_obj, $sessionID = '';
	private $application_key, $application_code, $access_token;
	private $returnAsString = false;
	private $applicationConnected = false;

	/**
	* Construct the BMUCore Class.
	*/
	private function __construct($bmu_rmi, $namespaces) {
	   $this->bmu_rmi = $bmu_rmi;
	   $this->namespaces = $namespaces;
	   $this->curl_obj = curl_init("https://www.mymobility.org/index.php");
	   $this->flush();
	}
	/**
	* Get singleton instance of BMUCore.
	*/
	public static function singleton($bmu_rmi = '0.0', $namespaces = 'Core') {
	  if (!isset(self::$instance)) {
		 //Creates new instance
		 $className = __CLASS__;
		 self::$instance = new $className($bmu_rmi,$namespaces);
	  }
	  return self::$instance;
	}

	/**
	* Short-hand for singleton function.
	*/
	public static function b() {
	   return self::singleton();
	}
	//cURL doesn't allow for class methods to be called (using the CURLOPT_HEADERFUNC option)
    //Therefore, this static method has been introduced,
    public static function  getHeader($resource, $string) {
      //Expected header input string <Header name>: <value>
	  $exploded = explode(': ',$string);
	  if($exploded[0] == 'Set-Cookie') {
		 //If Set-Cookie, Expected string is Set-Cookie: Cookiename1=Cookievalue1;Cookiename2=Cookievalue2;etc..
		 $exploded2 = explode(";",$exploded[1]);
		 foreach($exploded2 as $key => $value) {
			$exploded3 = explode("=",$value);
			if($exploded3[0] == 'PHPSESSID') {
			   BMUCore::b()->setSessionID($value);
			}
		 }
	  }
	  return strlen($string);
   }
   /**
   * Reset current jsonArray.
   *
   * Used in the sendRequest function, but also available to public use.
   */
	public function flush() {
	   $this->jsonArray = array();
	   $this->jsonArray['bmu_rmi'] = $this->bmu_rmi;
	   $this->jsonArray['namespace'] = $this->namespaces;
	   $this->emptyStack();
	   if(isset($this->access_token)) {
	   	$this->jsonArray['access_token'] = $this->access_token;
	   }
	   if(isset($this->application_key)) {
	   	$this->jsonArray['application_key'] = $this->application_key;
	   }
	   if(isset($this->application_code)) {
	   	$this->jsonArray['application_code'] = $this->application_code;
	   }
	}
	/**
	* Add a new key - value pair to the jsonArray.
	*
	* $key String containg name of key
	* $value can be anything.
	*/
	public function addToArray($key, $value) {
	   $this->jsonArray[$key] = $value;
	}

	/**
	* Sets whether you want sendRequest's output to be returned as a String or as an Associative Array.
	*
	* $bool = true, return as String
	* $bool = false, return as associative array
	*/
	public function setReturnAsString($bool) {
	   $this->returnAsString = $bool;
	}
	/**
	* Returns a String representation of current Request.
	*
	* @return String
	*/
	public function __toString() {
	    $this->jsonArray['invocation'] = $this->buildInvocation();
		$output = json_encode($this->jsonArray);
		return $output;
	}

	/**
	* builds an invocation using the current invocation stack (does not destroy the stack).
	*
	* @return invocation String
	*/
	public function buildInvocation() {
	   return implode('.',$this->callStack);
	}
	/**
	* Empties the CallStack
	*
	*/
	public function emptyStack() {
	   $this->callStack = array();
	}

	/**
	* Pushes $callString on the Call Stack
	*
	* $callString : String
	*/
	public function push($callString) {
	   $this->callStack[] = $callString;
	}
	/**
	* Convenience function for pushing a function with multiple arguments on the stack
	*
	* $funcName : String containing function name
	* $arguments : array of arguments (usually strings)
	*/
	public function pushFunc($funcName,$arguments) {
	   $this->push($funcName."('".implode("','",$arguments)."')");
	}

	public function pushFuncWithAlias($funcName, $AliasArray, $AliasName = 'alias') {
	  $this->push($funcName.'('.$AliasName.')');
	  $this->addToArray($AliasName,$AttributesArray);
   }
	/**
	* Sets current session ID to $session
	*
	* Used for establishing a session with the BMU Server, set by cURL when no session ID is known (== '')
	*
	* $session : String of form 'PHPSESSID=<string containing sessionKey>'
	*/
	public function setSessionID($session) {
	  $this->sessionID = $session;
	}
	/**
	* Unsets current Session ID
	*
	* Basically resets the session, called on calling the function Application.Disconnect().
	*
	*/
	public function unsetSessionID() {
		$this->sessionID = '';
	}

	/**
	* Sends the current request to the BMU server and returns BMU's response
	*
	* SendRequest uses cURL to start a HTTP connection between itself and the BMU server.
	* SessionID's are set if not yet present and used as long as the instance of BMUCore exists, unless Application.Disconnect is called.
	* It then waits for BMU to respond and returns the response.
	* @return is a String if returnAsString == true, associative array if returnAsString = false (its false by default)
	*/
    public function sendRequest() {
	  //Send initialized request to mobility4all, using cURL
	  //var_dump($this->__toString());
	  if(isset($this->jsonArray['application_code'])) {
	  	$this->application_code = $this->jsonArray['application_code'];
	  }
	  if(isset($this->jsonArray['access_token'])) {
	  	$this->access_token = $this->jsonArray['access_token'];
	  }
	  if(isset($this->jsonArray['application_key'])) {
	  	$this->application_key = $this->jsonArray['application_key'];
	  }
      $this->curl_obj = curl_init("https://www.mymobility.org/index.php");
	  $curlOptions = array(
	      CURLOPT_CAINFO      => "/home/websites/www.mymobility.org/https/www/auth/lib/cacert.pem",
		  CURLOPT_POST		  =>	1,
		  CURLOPT_POSTFIELDS  =>	'request='.urlencode($this->__toString()),
		  CURLOPT_HEADER	  =>	0,
		  CURLOPT_RETURNTRANSFER => 1,
		  CURLOPT_SSL_VERIFYHOST => 0, // moet weg, maakt man-in-the-middle-attack theoretisch mogelijk
		  CURLOPT_SSL_VERIFYPEER => 0 // moet weg, maakt man-in-the-middle-attack theoretisch mogelijk

	  );
	  if($this->sessionID != '') {
		 $curlOptions[CURLOPT_COOKIE] =  $this->sessionID;
	  } else {
		 $curlOptions[CURLOPT_HEADERFUNCTION] =	'BMUCore::getHeader';
	  }
	  curl_setopt_array($this->curl_obj,$curlOptions);
//	  echo "1:".curl_error($this->curl_obj);
	  $response = curl_exec($this->curl_obj);
	  
	  //echo "2:".curl_error($this->curl_obj);
	  $decodedResponse = json_decode($response,true);

	  $this->closeCURL();
	  //Return html output
	  $this->flush();
	  //$jsonbla = extractJson($output);
	  if($this->returnAsString) {
		 return $response;
	  } else {
		 //Decoding
		 return $decodedResponse;
	  }

	}
	/**
	* Sets the bool applicationConnect. Called when calling functions Application.Connect (set to true)
	* and application.Disconnect (set to false).
	*
	*/
	public function setApplicationConnected($bool) {
	   $this->applicationConnected = $bool;
	}
	/**
	* Gets the bool applicationConnect
	*
	* @return True if app has connect, false if not connect.
	*/
	public function getApplicationConnect() {
	   return $this->applicationConnect;
	}

	private function closeCURL() {
	   curl_close($this->curl_obj);
	}
	private function BMUSessionExists() {
	   return isset($this->sessionID);
	}

	//Namespaces

	public function Wheels4All() {
	   $this->jsonArray['namespace'] = 'Wheels4All';
	   return $this;
	}

	//BMUCore roles.

	public function Application() {
	   $this->push('Application');
	   return new BMU_Application();
	}
	public function Attributes() {
	   $this->push('Attributes');
	   return new BMU_Attributes();
	}
	public function Auth($auth_id) {
	   $this->push("Auth($auth_id)");
	   return new BMU_Auth();
	}

	public function ProviderUI($provider_id = '') {
	   $this->push('ProviderUI('.$provider_id.')');
	   return new BMU_ProviderUI();
	}
	public function Person($person_id = '') {
	   $this->push('Person('.$person_id.')');
	   return new BMU_Person();
	}
	public function Legal($legal_id = '') {
	   $this->push('Legal('.$legal_id.')');
	   return new BMU_Legal();
	}
	public function Contract($contract_id = '') {
	   if($contract_id == '') {
		  $this->push('Contract');
	   } else {
		  $this->push('Contract('.$contract_id.')');
	   }
	   return new BMU_Contract();
	}
	public function ContractType($contractType_id = '') {
	   if($contractType_id == '') {
		  $this->push('ContractType');
	   } else {
		  $this->push('ContractType('.$contractType_id.')');
	   }
	   return new BMU_ContractType();
	}
	public function Customer($customer_id) {
	   $this->push('Customer('.$customer_id.')');
	   return new BMU_Customer();
	}
	public function Supplier($supplier_id = null) {
	    if($supplier_id == null) {
		  $this->push('Supplier');
	   } else {
		  $this->push('Supplier('.$supplier_id.')');
	   }
	   return new BMU_Supplier();
	}
	public function Offer($offer_id = '') {
	   if($offer_id == '') {
		  $this->push('Offer');
	   } else {
		  $this->push('Offer('.$offer_id.')');
	   }
	   return new BMU_Offer();
	}
	public function Resource($resource_id = '') {
	   if($resource_id == '') {
		  $this->push('Resource');
	   } else {
		  $this->push('Resource('.$resource_id.')');
	   }
	   return new BMU_Resource();
	}
	public function Consumer() {
	   $this->push('Consumer');
	   return new BMU_Consumer();
	}
	public function OfferType($offerType_id= '') {
	   if($offerType_id == '') {
		  $this->push('OfferType');
	   } else {
		  $this->push('OfferType('.$offerType_id.')');
	   }
	   return new BMU_OfferType();
	}
	public function Me() {
	   $this->push('Me');
	   return new BMU_Me();
	}
	public function ProviderSA($ProviderSA_id) {
	   $this->push('ProviderSA('.$ProviderSA_id.')');
	   return new BMU_ProviderSA();
	}
	public function Service($Service_id) {
	   $this->push('Service('.$Service_id.')');
	   return new BMU_Service();
	}
	public function ResourceItem($resourceItem_id) {
	   $this->push('ResourceItem('.$resourceItem_id.')');
	   return new BMU_ResourceItem();
	}
	public function ResourceGroup($resourceGroup_id) {
	   $this->push('ResourceGroup('.$resourceItem_id.')');
	   return new BMU_ResourceGroup();
	}
	public function Booking($Booking_id) {
	   $this->push('Booking('.$Booking_id,')');
	   return new BMU_Booking();
	}
	public function Schedule() {
	   $this->push('Schedule');
	   return new BMU_Schedule();
	}
	public function Calendar($Calendar_id) {
	   $this->push('Calendar('.$Calendar_id.')');
	   return new BMU_Calendar();
	}
	public function Location($Location_id) {
	   $this->push('Location('.$Location_id.')');
	   return new BMU_Location();
	}

}
?>