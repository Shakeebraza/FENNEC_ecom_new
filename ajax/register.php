<?php
require_once("../global.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and trim input values
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    // Retrieve role from POST; default to 0 if not provided
    $role     = isset($_POST['role']) && $_POST['role'] !== '' ? (int)$_POST['role'] : 0;

    // Configuration for username and password length
    $minUsernameLength = $fun->getFieldData('username_length'); 
    $maxUsernameLength = 50; 
    $minPasswordLength = $fun->getFieldData('password_length'); 
    $maxPasswordLength = 128; 

    // --- Username Validation ---
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

    // --- Email Validation ---
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

    // --- Password Validation ---
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

    // --- Prepare Data for Insertion ---
    $hashedPassword    = password_hash($password, PASSWORD_DEFAULT);
    $verificationToken = bin2hex(random_bytes(16)); 

    $data = [
        'username'           => $username,
        'email'              => $email,
        'password'           => $hashedPassword,
        'role'               => $role,  // Use the selected role from the form
        'verification_token' => $verificationToken
    ];

    $response = $dbFunctions->setData('users', $data);

    if (!$response['success']) {
        echo json_encode(['status' => 'error', 'errors' => $response['message']]);
        exit();
    }

    // --- Send Verification Email ---
    $verificationLink = $urlval . "verify_email.php?token=$verificationToken&email=$email&role=none";
    $emailTemplate    = $emialTemp->getVerificationTemplate($verificationLink);
    $mailResponse     = smtp_mailer($email, 'Email Verification', $emailTemplate);

    if ($mailResponse == 'sent') {
        echo json_encode(['status' => 'success', 'message' => 'Registration successful! Verification email sent.']);
    } else {
        echo json_encode(['status' => 'error', 'errors' => 'Registration successful, but failed to send verification email.']);
    }
    exit();
}
?>
