<?php
session_start();

$userName = $_REQUEST['user'];

if (strlen($userName) > 0) {
	setcookie('loginCookie',"",time() - 3600);
	session_destroy();
	unset($_SESSION['username']);
	print "{success : true}";
}else{
	print "{success : false}";
}

?>
