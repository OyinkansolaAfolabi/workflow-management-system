<?php
//Include configuration file that contains OpenAuth credentials
include 'backend/config.php'; // Contains OpenAuth credentials and database connection
require 'vendor/autoload.php';

use League\OAuth2\Client\Provider\Google;
// Create a new Google OAuth2 provider instance with the necessary credentials
$provider = new Google([
    'clientId'                => $clientId,
    'clientSecret'            => $clientSecret,
    'redirectUri'             => $redirectUri,
    'scopes'                  => ['openid', 'profile', 'email']
]);

//Start the session to manage OAuth2 state and user data
session_start();

if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
}

//Start the session to manage OAuth2 state and user data
if (!isset($_GET['code'])) {
    header('Location: index.php');
    exit;
}

try {
 //Get the access token using the authorization code

    $accessToken = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code'],
    ]);
//Get owner's information using the access token
    $resourceOwner = $provider->getResourceOwner($accessToken);
    $userData = $resourceOwner->toArray();
    $_SESSION['user'] = $userData;

    // Include YHROCU database connection file
    include('backend/db.php');
    
    // Prepare a statement to check if the user exists and fetch the account_type
    $stmt = $mysqli->prepare("SELECT id, account_type FROM users WHERE email = ?");
    $stmt->bind_param('s', $userData['email']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        // User does not exist, create a new user record
        $stmt->close();
        $stmt = $mysqli->prepare("INSERT INTO users (name, email, account_type) VALUES (?, ?, 'user')");
        $stmt->bind_param('ss', $userData['name'], $userData['email']);
        $stmt->execute();
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['account_type'] = 'user';
    } else {
        // User exists, fetch the user id and account_type
        $stmt->bind_result($userId, $accountType);
        $stmt->fetch();
        $_SESSION['user_id'] = $userId;
        $_SESSION['account_type'] = $accountType;
    }

    $stmt->close();
    $mysqli->close();

    header('Location: dashboard/index.php');
    exit;

} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
