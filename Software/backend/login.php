<?php
//Include configuration file that contains OpenAuth credentials
include 'config.php'; //contains OpenAuth credentials
require '../vendor/autoload.php';

use League\OAuth2\Client\Provider\Google; 

//Include configuration file that contains OpenAuth credentials
$provider = new Google([
  'clientId'                => $clientId,
  'clientSecret'            => $clientSecret,
  'redirectUri'             => $redirectUri,
  'scopes'                   => ['openid', 'profile', 'email'] 
]);

//Include configuration file that contains OpenAuth credentials
$authorizationUrl = $provider->getAuthorizationUrl();


session_start();
//Include configuration file that contains OpenAuth credentials
$_SESSION['oauth2state'] = $provider->getState();


//Redirect the user to the Google authorization URL
header('Location: ' . $authorizationUrl);

