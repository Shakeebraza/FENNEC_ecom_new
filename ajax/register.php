<?php
require_once("../global.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve approval parameters from the DB using your getData() function
    $memberApproval = $fun->getData('approval_parameters', 'member_approval', 1);
    $emailVerification = $fun->getData('approval_parameters', 'email_verification', 1);
    // Convert them to lowercase for easy comparison
    $memberApproval = strtolower($memberApproval);
    $emailVerification = strtolower($emailVerification);

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

    // --- Decide admin_verified based on member_approval ---
    // If member_approval == 'auto', admin_verified = 1, else 0
    $adminVerified = ($memberApproval === 'auto') ? 1 : 0;

    // --- Prepare Data for Insertion ---
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Build user data array
    $userData = [
        'username'       => $username,
        'email'          => $email,
        'password'       => $hashedPassword,
        'role'           => $role,
        'admin_verified' => $adminVerified
    ];

    // --- Email Verification Flow ---
    if ($emailVerification === 'enabled') {
        // Normal token-based verification
        $verificationToken = bin2hex(random_bytes(16)); 
        $userData['verification_token'] = $verificationToken;
        // 'email_verified_at' remains NULL until user verifies
    } else {
        // email_verification is disabled => skip emailing, auto-verify
        $userData['verification_token'] = '0';
        $userData['email_verified_at'] = date('Y-m-d H:i:s');
    }

    // --- Insert the User ---
    $response = $dbFunctions->setData('users', $userData);
    if (!$response['success']) {
        echo json_encode(['status' => 'error', 'errors' => $response['message']]);
        exit();
    }

    // If email verification is enabled, send the verification email
    if ($emailVerification === 'enabled') {
        $verificationLink = $urlval . "verify_email.php?token=" . $userData['verification_token'] . "&email=$email&role=none";
        $emailTemplate    = $emialTemp->getVerificationTemplate($verificationLink);
        $mailResponse     = smtp_mailer($email, 'Email Verification', $emailTemplate);

        if ($mailResponse == 'sent') {
            echo json_encode(['status' => 'success', 'message' => 'Registration successful! Verification email sent.']);
        } else {
            echo json_encode(['status' => 'error', 'errors' => 'Registration successful, but failed to send verification email.']);
        }
    } else {
        // Email verification is disabled, so user is fully registered instantly
        echo json_encode(['status' => 'success', 'message' => 'Registration successful! No email verification needed.']);
    }
    exit();
}
?>
