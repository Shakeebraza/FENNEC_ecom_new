<?php
require_once("../../global.php");

// Only process POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic data
    $username = $_POST['username'] ?? '';
    $email    = $_POST['email']    ?? '';
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role']     ?? '3'; // default to '3' if not set

    $errors = [];
    $minUsernameLength = $fun->getFieldData('username_length'); // e.g. 3 or so
    $maxUsernameLength = 50;

    // Validate username
    if (empty($username)) {
        $errors[] = 'Username is required.';
    } elseif (strlen($username) < $minUsernameLength) {
        $errors[] = "Username must be at least $minUsernameLength characters long.";
    } elseif (strlen($username) > $maxUsernameLength) {
        $errors[] = "Username must be less than $maxUsernameLength characters long.";
    } else {
        $usernameCount = $dbFunctions->getCount('admins', 'username', "username = '$username'");
        if ($usernameCount > 0) {
            $errors[] = 'Username is already taken.';
        }
    }

    // Validate email
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    } else {
        $emailCount = $dbFunctions->getCount('admins', 'email', "email = '$email'");
        if ($emailCount > 0) {
            $errors[] = 'Email is already registered.';
        }
    }

    // Validate password
    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < $fun->getFieldData('password_length')) {
        $errors[] = 'Password must be at least ' . $fun->getFieldData('password_length') . ' characters long.';
    } elseif (strlen($password) > 125) {
        $errors[] = 'Password must be less than 125 characters long.';
    }

    // Validate role => [1,3,4]
    if (!in_array($role, ['1','3','4'])) {
        $role = '4'; // fallback to Moderator
    }

    // If errors, return them
    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit;
    }

    // No errors => proceed
    $hashedPassword     = password_hash($password, PASSWORD_DEFAULT);
    $verificationToken  = bin2hex(random_bytes(16));

    $data = [
        'username'           => $username,
        'email'              => $email,
        'password'           => $hashedPassword,
        'role'               => $role,
        'verification_token' => $verificationToken
    ];

    $response = $dbFunctions->setData('admins', $data);

    // Logging
    $logMsg = sprintf(
        "New Registration: username=%s, email=%s, role=%s, time=%s\n",
        $username, $email, $role, date('Y-m-d H:i:s')
    );
    error_log($logMsg, 3, __DIR__ . '/registration.log'); // logs to "admins/ajax/registration.log"

    if ($response['success']) {
        // Construct email verification link
        $verificationLink = $urlval . "verify_email.php?token=$verificationToken&email=$email";
        $mailSubject = 'Email Verification';
        $mailBody    = "Please click the link below to verify your email address:\n$verificationLink";

        $mailResponse = smtp_mailer($email, $mailSubject, $mailBody);

        if ($mailResponse === 'sent') {
            echo json_encode([
                'status'  => 'success',
                'message' => 'Registration successful! Verification email sent.'
            ]);
        } else {
            // If $mailResponse is not 'sent', assume it’s an error string
            echo json_encode([
                'status'  => 'error',
                'message' => 'Registration successful, but failed to send verification email: ' . $mailResponse
            ]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => $response['message']]);
    }
}