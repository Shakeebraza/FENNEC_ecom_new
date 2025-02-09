<?php
require_once '../global.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    // Check if the email exists in the database
    $user = $dbFunctions->getDatanotenc('users', "email = '$email'");

    if ($user) {
        $userId = $user[0]['id'];
        $username = $user[0]['username']; // Assuming username is fetched here
        $token = bin2hex(random_bytes(16)); // Generate a secure token
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour

        // Update the user's record with the reset token and expiry time
        $req = $dbFunctions->updateData('users', [
            'reset_token' => $token,
            'reset_token_expiry' => $expires,
        ], $userId);

        if ($req['success']) {
            $verificationLink = $urlval . "resetpassword.php?token=$token&email=$email";

            // Fetch the email template for password reset
            $templateData = $fun->getTemplate('password_reset_notification');

            if (!$templateData) {
                echo json_encode(['success' => false, 'message' => 'Email template not found.']);
                exit;
            }

            // Replace placeholders in the template
            $subject = str_replace('{{username}}', $username, $templateData['subject']);
            $body = str_replace(
                ['{{username}}', '{{reset_link}}'],
                [$username, $verificationLink],
                $templateData['body']
            );

            // Send the reset password email
            $mailResponse = smtp_mailer($email, $subject, $body);

            if ($mailResponse === 'sent') {
                echo json_encode(['success' => true, 'message' => 'Password reset email sent successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Something went wrong while updating the user record.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
