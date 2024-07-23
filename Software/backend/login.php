<?php
include 'config.php';
require '../vendor/autoload.php';

use League\OAuth2\Client\Provider\Google; 


$provider = new Google([
  'clientId'                => $clientId,
  'clientSecret'            => $clientSecret,
  'redirectUri'             => $redirectUri,
  'scopes'                   => ['openid', 'profile', 'email'] 
]);

$authorizationUrl = $provider->getAuthorizationUrl();


session_start();
$_SESSION['oauth2state'] = $provider->getState();


header('Location: ' . $authorizationUrl);

