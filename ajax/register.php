<?php
require_once("../global.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve approval parameters from the DB
    $memberApproval    = strtolower($fun->getData('approval_parameters', 'member_approval', 1));
    $emailVerification = strtolower($fun->getData('approval_parameters', 'email_verification', 1));

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
    // If member_approval == 'auto', then admin_verified = 1, else 0.
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
        // Generate token-based verification if enabled
        $verificationToken = bin2hex(random_bytes(16)); 
        $userData['verification_token'] = $verificationToken;
        // 'email_verified_at' remains NULL until user verifies
    } else {
        // Email verification is disabled => auto-verify
        $userData['verification_token'] = '0';
        $userData['email_verified_at'] = date('Y-m-d H:i:s');
    }

    // --- Insert the User ---
    $response = $dbFunctions->setData('users', $userData);
    if (!$response['success']) {
        echo json_encode(['status' => 'error', 'errors' => $response['message']]);
        exit();
    }

    // --- Sending Verification Email (if enabled) ---
    if ($emailVerification === 'enabled') {
        // Create the verification link
        $verificationLink = $urlval . "verify_email.php?token=" . $userData['verification_token'] . "&email=" . urlencode($email) . "&role=none";

        // Retrieve the dynamic email template from the database using the template key.
        // You should have a function (e.g., $fun->getTemplate($template_key)) that queries the email_templates table.
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

        // Send the email using your smtp_mailer() function
        $mailResponse = smtp_mailer($email, $subject, $body);

        if ($mailResponse == 'sent') {
            echo json_encode(['status' => 'success', 'message' => 'Registration successful! Verification email sent.']);
        } else {
            echo json_encode(['status' => 'error', 'errors' => 'Registration successful, but failed to send verification email.']);
        }
    } 
    
    if ($memberApproval === 'admin') {
        $approvalToken = bin2hex(random_bytes(16));
        $dbFunctions->updateData('users', ['approval_token' => $approvalToken], $userId);

        $approvalLink = $urlval . "approve_user.php?token=$approvalToken&user_id=$userId";

        $templateData = $fun->getTemplate('signup_awaiting_approval');

        if (!$templateData) {
            $subject = 'New User Signup: Approval Needed';
            $body = "<p>A new user has signed up and requires your approval:</p>
                    <p>Username: {$username}</p>
                    <p>Email: {$email}</p>
                    <p><a href='{$approvalLink}'>Approve User</a></p>";
        } else {
            $subject = str_replace(['{{username}}', '{{email}}'], [$username, $email], $templateData['subject']);
            $body = str_replace(
                ['{{username}}', '{{email}}', '{{approval_link}}'],
                [$username, $email, $approvalLink],
                $templateData['body']
            );
        }

        $adminEmail = $fun->getData('site_settings', 'value', 4); // Assuming the admin email is stored in settings
        smtp_mailer($adminEmail, $subject, $body);
    }
    echo json_encode(['status' => 'success', 'message' => 'Registration successful! No email verification needed.']);
    exit();
}
?>
