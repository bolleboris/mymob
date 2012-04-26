<?php
require_once('adodb5/adodb.inc.php');
require_once('BMUMigration/lib/BMUCore.php');
$username = $_REQUEST['user'];
$password = $_REQUEST['password'];


//Voor lange sessies is het belangrijk dat de garbage collector niet de cookie op eet. (Om nom nom!)
ini_set('session.gc_maxlifetime',12*60*60);		//Zet de max lifetime van de garbage collector op 12 uur
ini_set('session.gc_probability',1);			
ini_set('session.gc_divisor',1);

session_start();

function sendErrorJSON($errorMsg){
	$jsondata['success'] = false;
	$jsondata['msg'] = $errorMsg;

	die(json_encode($jsondata));
}

function validateLogin($username,$password){
	global $sql;
	
	if(!strstr($username,'@')){
		$query = "SELECT UID FROM users WHERE Login = ? AND Password = ? AND Active = 1;";
		if(!$rs = $sql->Execute($query,array($username,$password))) sendErrorJSON("Fout in query: ".$sql->ErrorMsg());
		if($rs->EOF){ 	//Login failed
			return 0;
		}else{	
			return $rs->fields['UID'];
		}
	}else{			//Username bevat @ teken, dus komt uit Personentabel (gebruiker is coordinator)
		$query = "
			SELECT
				p.PersoonNr,
				p.LoginWachtwoordMD5,
				p.Niveau,
				ad.Latitude,
				ad.Longitude
			FROM w4a.W4APersonen p
			LEFT JOIN w4a.W4AAdressen ad ON ad.AdresId = p.AdresId
			WHERE p.Email = ? AND p.Niveau >= 2 AND (opgezegdPer IS NULL OR opgezegdPer >= NOW());";
		if(!$rs = $sql->Execute($query,array($username))) sendErrorJSON("Fout in query: ".$sql->ErrorMsg());
		if($rs->EOF){ 	//User not found
			sendErrorJSON("Je hebt een verkeerde inlognaam opgegeven, of je hebt onvoldoende rechten om in te loggen.");
			return 0;
		}else{	
			$password_pt = $password;
			$password_db = $rs->fields['LoginWachtwoordMD5'];
			$password_array = explode(":",$password_db);
			$password_md5 = $password_array[0];
			$password_salt = isset($password_array[1]) ? $password_array[1] : '';
			$password_enc = ($password_salt) ? md5($password_pt.$password_salt).':'.$password_salt : md5($password_pt);
			if($password_enc == $password_db){
				$_SESSION['userLevel'] = $rs->fields['Niveau'];
				$_SESSION['baseLat'] = $rs->fields['Latitude'];
				$_SESSION['baseLon'] = $rs->fields['Longitude'];
				return $rs->fields['PersoonNr'];			
			}else{		//Password verkeerd
				return 0;
			}
		}
	}
}

$sql = ADONewConnection('mysql');
if(!@$sql->Connect(
	'localhost', 
	'backoffice', 
	't97c6Tsj2sqRdcdz', 
	'backoffice')
) sendErrorJSON("Kan niet verbinden met de database");

if($output = validateLogin($username,$password) != 0) {
	$query = "INSERT INTO log VALUES(null,NOW(),?,?,1);";
	if(!$rs = $sql->execute($query,array($username,$_SERVER['REMOTE_ADDR']))) die("SQL error: ".$sql->ErrorMsg());
	session_start();
	setcookie('loginCookie',$username);
	$_SESSION['username'] = $username;
	$return['username'] = $username;
	$return['success'] = true;
	if(strstr($username,'@'))  $return['userlevel'] = (int)$_SESSION['userLevel'];
	echo json_encode($return);
} else {
	$query = "INSERT INTO log VALUES(null,NOW(),?,?,0);";
	if(!$rs = $sql->execute($query,array($username,$_SERVER['REMOTE_ADDR']))) die("SQL error: ".$sql->ErrorMsg());
	unset($_SESSION['username']);
	session_destroy();
	$return['success'] = false;
	$return['msg'] = "Verkeerde gebruikersnaam en/of password";
	echo json_encode($return);
}
?>
