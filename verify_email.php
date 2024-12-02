<?php
require_once("global.php");

if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];

    $user = $dbFunctions->getDatanotenc('users', "email = '$email' AND verification_token = '$token'");

    if ($user && is_array($user) && isset($user[0]['id'])) {
        $userId = $user[0]['id']; 

        $data = [
            'email_verified_at' => $currentDateTime,
            'verification_token' => 0 
        ];

        $updateQuery = $dbFunctions->updateData('users', $data, $userId);

        if ($updateQuery['success'] === true) {
          
            echo "
            <script>
                alert('Email verified successfully! Redirecting to login page...');
                window.location.href = '".$urlval."LoginRegister.php'; // Redirect to login page
            </script>
            ";
            exit(); 
        } else {
            echo "
            <script>
                alert('Something went wrong, please contact the administrator.');
                window.location.href = '".$urlval."LoginRegister.php'; // Redirect to login page
            </script>
            ";
            exit(); 
        }

    } else {
        echo "
        <script>
            alert('Invalid verification token or email!');
            window.location.href = '".$urlval."LoginRegister.php'; // Redirect to login page
        </script>
        ";
        exit(); 
    }
} else {
    echo "
    <script>
        alert('No token or email provided!');
        window.location.href = '".$urlval."LoginRegister.php'; // Redirect to login page
    </script>
    ";
    exit(); 
}
?>