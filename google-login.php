<?php
require_once("global.php");
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('599470363677-igrb9daksarvf22aj4bfd59ugbia12dd.apps.googleusercontent.com');
$client->setClientSecret('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');
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
