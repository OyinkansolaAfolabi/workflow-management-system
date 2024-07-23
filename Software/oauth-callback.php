<?php
include 'config.php';
require 'vendor/autoload.php';

use League\OAuth2\Client\Provider\Google;

$provider = new Google([
    'clientId'                => $clientId,
    'clientSecret'            => $clientSecret,
    'redirectUri'             => $redirectUri
]);

session_start();

if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
}

if (!isset($_GET['code'])) {
    header('Location: login.php');
    exit;
}

try {
 
   $accessToken = $provider->getAccessToken('authorization_code', [
    'code' => $_GET['code'],
]);


$resourceOwner = $provider->getResourceOwner($accessToken);
$userData = $resourceOwner->toArray();


$_SESSION['user'] = $userData;


header('Location: dashboard.php');
exit;

} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
    echo 'Error: ' . $e->getMessage();
}