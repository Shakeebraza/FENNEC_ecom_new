<?php
require_once("global.php");
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('599470363677-igrb9daksarvf22aj4bfd59ugbia12dd.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://fennec.digicomet.net/google-login.php');
$client->addScope('email');
$client->addScope('profile');
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    $client->setAccessToken($token['access_token']);

    $google_service = new Google_Service_Oauth2($client);
    $user_info = $google_service->userinfo->get();

    $email = $user_info->email;
    $name = $user_info->name;

    $where = "email = '" . $email . "'";
    $user = $dbFunctions->getDatanotenc('users', $where);

    if ($user) {
        $user = $user[0];
        $sessionSet = $fun->sessionSet($email);
    } else {
        $data = [
            'email' => $email,
            'username' => $name,
            'password' => password_hash(bin2hex(random_bytes(10)), PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
            'role' => 0,
            'verification_token' => 0,
            'email_verified_at' => date('Y-m-d H:i:s')
        ];
        $dbFunctions->setData('users', $data);
        $sessionSet = $fun->sessionSet($email);
    }
    // Server-side redirection
    header("Location: index.php");
    exit();
}
?>
