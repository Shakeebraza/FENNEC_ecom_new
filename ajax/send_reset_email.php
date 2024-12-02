<?php
require_once '../global.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }


    $user = $dbFunctions->getDatanotenc('users', "email = '$email'");

    if ($user) {
        $userId = $user[0]['id'];
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $req=$dbFunctions->updateData('users', [
            'reset_token' => $token,
            'reset_token_expiry' => $expires,
        ], $userId);

        if($req['success']){
            $verificationLink = $urlval . "resetpassword.php?token=$token&email=$email";
            $temp = $emialTemp->getResetpasswordTemplate($verificationLink);
            $mailResponse = smtp_mailer($email, 'Email Verification', $temp);
            if ($mailResponse == 'Sent') {
                echo json_encode(['success' => true, 'message' => 'Password reset email sent successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
            }
        }else{
            echo json_encode(['success' => false, 'message' => 'Somethink went wrong']);
        }




    } else {
        echo json_encode(['success' => false, 'message' => 'Email not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
