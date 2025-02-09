<?php
require_once("../../global.php");

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve the email verification and member approval settings from approval_parameters
    $emailVerification = strtolower($fun->getData('approval_parameters', 'email_verification', 1));
    $memberApproval    = strtolower($fun->getData('approval_parameters', 'member_approval', 1));

    // Basic data from POST
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? '3'; // default to '3' if not set

    $errors = [];
    $minUsernameLength = $fun->getFieldData('username_length'); // e.g. 3
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

    // Validate role => allowed values are [1, 3, 4]
    if (!in_array($role, ['1', '3', '4'])) {
        $role = '4'; // fallback to '4' (Moderator)
    }

    // If there are errors, return them as JSON and exit
    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit;
    }

    // --- Decide admin_verified based on member_approval ---
    // If member_approval is 'auto', then admin_verified = 1, else 0.
    $adminVerified = ($memberApproval === 'auto') ? 1 : 0;

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Build the data array for insertion
    $data = [
        'username'       => $username,
        'email'          => $email,
        'password'       => $hashedPassword,
        'role'           => $role,
        'admin_verified' => $adminVerified
    ];

    // --- Email Verification Flow ---
    if ($emailVerification === 'enabled') {
        // Generate a token for email verification
        $verificationToken = bin2hex(random_bytes(16));
        $data['verification_token'] = $verificationToken;
        // 'email_verified_at' remains NULL until the user verifies
    } else {
        // If email verification is disabled, mark as verified
        $data['verification_token'] = '0';
        $data['email_verified_at']  = date('Y-m-d H:i:s');
    }

    // Insert the new admin into the 'admins' table
    $response = $dbFunctions->setData('admins', $data);

    // Logging the registration attempt
    $logMsg = sprintf(
        "New Admin Registration: username=%s, email=%s, role=%s, time=%s\n",
        $username,
        $email,
        $role,
        date('Y-m-d H:i:s')
    );
    error_log($logMsg, 3, __DIR__ . '/registration.log'); // Logs to "admins/ajax/registration.log"

    if ($response['success']) {
        // If email verification is enabled, send the verification email
        if ($emailVerification === 'enabled') {
            $verificationLink = $urlval . "verify_email.php?token=" . $data['verification_token'] . "&email=" . urlencode($email) . "&role=admin";

            // Use the registration_verification template as requested
            $templateData = $fun->getTemplate('registration_verification');

            if (!$templateData) {
                // Fallback default if the template is not found
                $subject = 'Email Verification';
                $body    = "<p>Hello {$username},</p>
                            <p>Please verify your email by clicking the link below:</p>
                            <p><a href='{$verificationLink}'>Verify Email</a></p>
                            <p>Thank you!</p>";
            } else {
                // Use the template from the DB. Assume it returns an array with keys 'subject' and 'body'.
                $subject = $templateData['subject'];
                $body    = $templateData['body'];

                // Replace the placeholders with dynamic content.
                $subject = str_replace(['{{username}}', '{{verification_link}}'], [$username, $verificationLink], $subject);
                $body    = str_replace(['{{username}}', '{{verification_link}}'], [$username, $verificationLink], $body);
            }

            // Send the verification email using smtp_mailer()
            $mailResponse = smtp_mailer($email, $subject, $body);
            if ($mailResponse === 'sent') {
                echo json_encode([
                    'status'  => 'success',
                    'message' => 'Registration successful! Verification email sent.'
                ]);
            } else {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Registration successful, but failed to send verification email: ' . $mailResponse
                ]);
            }
        } else {
            // If email verification is disabled, no email is sent
            echo json_encode([
                'status'  => 'success',
                'message' => 'Registration successful! No email verification needed.'
            ]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => $response['message']]);
    }
}
?>
