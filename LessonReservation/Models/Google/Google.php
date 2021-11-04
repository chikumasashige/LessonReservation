<?php
//Include Google Configuration File
require(ROOT_PATH .'Models/Google/Gconfig.php');

//This $_GET["code"] variable value received after user has login into their Google Account redirct to PHP script then this variable value has been received
if(isset($_GET["code"])){
   //It will Attempt to exchange a code for an valid authentication token.
   $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);
   //This condition will check there is any error occur during geting authentication token. If there is no any error occur then it will execute if block of code/
 if(!isset($token['error'])){
   //Set the access token used for requests
   $google_client->setAccessToken($token['access_token']);
   //Store "access_token" value in $_SESSION variable for future use.
   $_SESSION['access_token'] = $token['access_token'];
   //Create Object of Google Service OAuth 2 class
   $google_service = new Google_Service_Oauth2($google_client);
   //Get user profile data from google
   $data = $google_service->userinfo->get();
   //Below you can find Get profile data and store into $_SESSION variable
   if(!empty($data['given_name'])){
    $_SESSION['first_name'] = $data['given_name'];
   }
   if(!empty($data['family_name'])){
    $_SESSION['last_name'] = $data['family_name'];
   }
   if(!empty($data['email'])){
    $_SESSION['email'] = $data['email'];
   }
   if(!empty($data['gender'])){
    $_SESSION['user_gender'] = $data['gender'];
   }
   if(!empty($data['picture'])){
    $_SESSION['image'] = $data['picture'];
   }
  }
}
?>