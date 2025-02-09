<?php
require_once('../../../global.php');

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD'], 3, __DIR__ . '/status_update_errors.log');
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Validate required POST parameters
if (!isset($_POST['id']) || !isset($_POST['status'])) {
    error_log("Missing POST parameters: " . json_encode($_POST), 3, __DIR__ . '/status_update_errors.log');
    echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
    exit;
}

$userId = $security->decrypt($_POST['id']);
$status = $_POST['status'];

// Check if userId is valid
if (!$userId) {
    error_log("Invalid user ID after decryption. Encrypted ID: " . $_POST['id'], 3, __DIR__ . '/status_update_errors.log');
    echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    exit;
}

// Update the status in the database
$data = ['status' => $status];
$updateStatus = $dbFunctions->updateData('users', $data, $userId);

if ($updateStatus) {
    // Fetch user details (username and email)
    $userData = $dbFunctions->getData('users', "id = '$userId'");
    if (empty($userData)) {
        error_log("User not found in database for ID: $userId", 3, __DIR__ . '/status_update_errors.log');
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit;
    }

    // Extract user details
    $user = $userData[0]; // Assuming `getData` returns an array
    $encryptedUsername = $user['username'] ?? null;
    $encryptedEmail = $user['email'] ?? null;

    // Decrypt the username and email
    $username = $security->decrypt($encryptedUsername);
    $email = $security->decrypt($encryptedEmail);

    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("Invalid email address for user ID: $userId. Email: " . ($email ?? 'NULL'), 3, __DIR__ . '/status_update_errors.log');
        echo json_encode([
            'success' => false,
            'message' => 'User email address is invalid or missing.'
        ]);
        exit;
    }

    // Map the status to a readable value
    $statusText = $status == '1' ? 'Activated' : 'Blocked';

    // Log email sending preparation
    error_log("Preparing to send email to recipient: $email", 3, __DIR__ . '/email_logs.log');

    // Fetch the email template from the database
    $templateData = $fun->getTemplate('account_status_change_notification_member');

    if (!$templateData) {
        // Fallback template if not found in DB
        error_log("Template not found for key: account_status_change_notification_member", 3, __DIR__ . '/email_logs.log');
        echo json_encode(['success' => true, 'message' => 'Status updated, no email sent due to missing template.']);
        exit;
        // $subject = 'Your Account Status Has Changed';
        // $body = "<p>Hello {$username},</p>
        //          <p>Your account status has been updated to: <strong>{$statusText}</strong>.</p>
        //          <p>If you did not request this change, please contact our support team immediately.</p>
        //          <p>Thank you,<br>Your Company Name</p>";
    } else {
        // Use template and replace placeholders
        $subject = $templateData['subject'];
        $body = $templateData['body'];

        // Replace placeholders like {{username}} and {{status}}
        $subject = str_replace(['{{username}}', '{{status}}'], [$username, $statusText], $subject);
        $body = str_replace(['{{username}}', '{{status}}'], [$username, $statusText], $body);
    }

    // Attempt to send email
    $mailResponse = smtp_mailer($email, $subject, $body);

    if ($mailResponse === 'sent') {
        error_log("Email successfully sent with subject: $subject to: $email", 3, __DIR__ . '/email_logs.log');
        echo json_encode(['success' => true, 'message' => 'Status updated and email sent.']);
    } else {
        error_log("Mailer Error for $email: $mailResponse", 3, __DIR__ . '/email_logs.log');
        echo json_encode(['success' => false, 'message' => 'Status updated but email not sent: ' . $mailResponse]);
    }
} else {
    error_log("Failed to update status for user ID: $userId", 3, __DIR__ . '/status_update_errors.log');
    echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
}
?>
