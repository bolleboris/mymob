<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class BMU_Auth {

   public function __construct() {

   }

   public function CheckRefreshToken($applicationCode, $refreshToken) {
	  BMUCore::b()->push("CheckRefreshToken('$applicationCode','$refreshToken')");
   }
   public function RequestAuthCode($applicationCode, $accessToken) {
	  BMUCore::b()->push("RequestAuthCode('$applicationCode','$accessToken')");
   }
   public function LoginUser($username, $password) {
	  BMUCore::b()->push("LoginUser('$username','$password')");
   }
   public function ConfirmAuthCode($authCode, $appCode, $appKey = '') {
	  BMUCore::b()->push("ConfirmAuthCode('$authCode','$appCode','$appKey')");
   }
   public function SubscriptionComplete($appCode, $subKey, $auth) {
	  BMUCore::b()->push("SubscriptionComplete('$appCode','$subKey','$auth')");
   }

}

?>
