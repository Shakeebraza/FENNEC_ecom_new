<?php
require_once("global.php");
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
$client->setRedirectUri('https://fennec.digicomet.net/google-login.php');
$client->addScope(['email', 'profile']);

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        // Handle error
        exit('Error fetching access token');
    }

    $client->setAccessToken($token['access_token']);
    $google_service = new Google_Service_Oauth2($client);
    $user_info = $google_service->userinfo->get();

    $email = $user_info->email;
    $name = $user_info->name;

    $where = "email = '" . $email . "'";
    $user = $dbFunctions->getDatanotenc('users', $where);

    if ($user) {
        // Existing user, set session and redirect
        $user = $user[0];
        $sessionSet = $fun->sessionSet($email);
        header("Location: index.php");
        exit();
    } else {
        // New user, store temp data and redirect to role selection
        $_SESSION['temp_user'] = [
            'email' => $email,
            'name' => $name
        ];
        header("Location: select-role.php");
        exit();
    }
}
?>
