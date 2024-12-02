<?php
require_once("../global.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

 
    $minUsernameLength = $fun->getFieldData('username_length'); 
    $maxUsernameLength = 50; 

    $minPasswordLength = $fun->getFieldData('password_length'); 
    $maxPasswordLength = 128; 

    // Validate username
    if (empty($username)) {
        echo json_encode(['status' => 'error', 'errors' => 'Username is required.']);
        exit();
    } elseif (strlen($username) < $minUsernameLength) {
        echo json_encode(['status' => 'error', 'errors' => "Username must be at least $minUsernameLength characters long."]);
        exit();
    } elseif (strlen($username) > $maxUsernameLength) {
        echo json_encode(['status' => 'error', 'errors' => "Username must be no longer than $maxUsernameLength characters."]);
        exit();
    } else {
        $usernameCount = $dbFunctions->getCount('users', 'username', "username = '$username'");
        if ($usernameCount > 0) {
            echo json_encode(['status' => 'error', 'errors' => 'Username is already taken']);
            exit();
        }
    }

    // Validate email
    if (empty($email)) {
        echo json_encode(['status' => 'error', 'errors' => 'Email is required.']);
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'errors' => 'Invalid email format.']);
        exit();
    } else {
        $emailCount = $dbFunctions->getCount('users', 'email', "email = '$email'");
        if ($emailCount === false) {
            echo json_encode(['status' => 'error', 'errors' => 'Error checking email count. Database query failed.']);
            exit();
        }
        if ($emailCount > 0) {
            echo json_encode(['status' => 'error', 'errors' => 'Email is already registered']);
            exit();
        }
    }


    if (empty($password)) {
        echo json_encode(['status' => 'error', 'errors' => 'Password is required.']);
        exit();
    } elseif (strlen($password) < $minPasswordLength) {
        echo json_encode(['status' => 'error', 'errors' => "Password must be at least $minPasswordLength characters long."]);
        exit();
    } elseif (strlen($password) > $maxPasswordLength) {
        echo json_encode(['status' => 'error', 'errors' => "Password must be no longer than $maxPasswordLength characters."]);
        exit();
    }

 
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $verificationToken = bin2hex(random_bytes(16)); 


    $data = [
        'username' => $username,
        'email' => $email,
        'password' => $hashedPassword,
        'role' => 0,
        'verification_token' => $verificationToken
    ];

  
    $response = $dbFunctions->setData('users', $data);

    if (!$response['success']) {
        echo json_encode(['status' => 'error', 'errors' => $response['message']]);
        exit();
    }


    $verificationLink = $urlval . "verify_email.php?token=$verificationToken&email=$email";
    $temp = $emialTemp->getVerificationTemplate($verificationLink);
    $mailResponse = smtp_mailer($email, 'Email Verification', $temp);


    if ($mailResponse == 'Sent') {
        echo json_encode(['status' => 'success', 'message' => 'Registration successful! Verification email sent.']);
    } else {
        echo json_encode(['status' => 'error', 'errors' => 'Registration successful, but failed to send verification email.']);
    }
    exit();
}