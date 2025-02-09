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

$productId = $_POST['id'];
$status = $_POST['status'];

// Map the status to a readable value
$statusText = ($status == '1') ? 'Approved' : (($status == '0') ? 'Unapproved' : 'Pending');

// Update the product status in the database
$data = ['is_enable' => $status];
$updateStatus = $dbFunctions->updateData('products', $data, $productId);

if ($updateStatus) {
    // Fetch product details (including encrypted user_id)
    $productData = $dbFunctions->getData('products', "id = '$productId'");
    if (empty($productData)) {
        error_log("Product not found in database for ID: $productId", 3, __DIR__ . '/status_update_errors.log');
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit;
    }

    $product = $productData[0]; // Assuming `getData` returns an array
    $encryptedProductName = $product['name'] ?? null;
    $encryptedUserId = $product['user_id'] ?? null;

    if (!$encryptedUserId) {
        error_log("No user associated with product ID: $productId", 3, __DIR__ . '/status_update_errors.log');
        echo json_encode(['success' => false, 'message' => 'No user associated with this product.']);
        exit;
    }

    // Decrypt the product name and user_id
    $productName = $security->decrypt($encryptedProductName);
    if (!$productName) {
        $productName = 'Product'; // Fallback if decryption fails
    }

    $userId = $security->decrypt($encryptedUserId);
    if (!$userId) {
        error_log("Failed to decrypt user_id for product ID: $productId", 3, __DIR__ . '/status_update_errors.log');
        echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
        exit;
    }

    // Fetch user details (including encrypted email) using decrypted user_id
    $userData = $dbFunctions->getData('users', "id = '$userId'");
    if (empty($userData)) {
        error_log("User not found for decrypted user_id: $userId", 3, __DIR__ . '/status_update_errors.log');
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit;
    }

    $user = $userData[0]; // Assuming `getData` returns an array
    $encryptedUsername = $user['username'] ?? null;
    $encryptedEmail = $user['email'] ?? null;

    // Decrypt the username and email
    $username = $security->decrypt($encryptedUsername);
    if (!$username) {
        $username = 'User'; // Fallback if decryption fails
    }

    $email = $security->decrypt($encryptedEmail);
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("Invalid email address for user_id: $userId. Email: " . ($email ?? 'NULL'), 3, __DIR__ . '/status_update_errors.log');
        echo json_encode(['success' => true, 'message' => 'Product status updated, but no valid email found for the user.']);
        exit;
    }

    // Fetch the email template from the database
    $templateData = $fun->getTemplate('product_status_change_notification');

    if (!$templateData) {
        // If no template is found, log and return success without sending an email
        error_log("Template not found for key: product_status_change_notification", 3, __DIR__ . '/email_logs.log');
        echo json_encode(['success' => true, 'message' => 'Product status updated, no email sent due to missing template.']);
        exit;
    }

    // Use the template and replace placeholders
    $subject = $templateData['subject'];
    $body = $templateData['body'];

    // Replace placeholders like {{username}}, {{product_name}}, and {{status}}
    $subject = str_replace(['{{username}}', '{{product_name}}', '{{status}}'], [$username, $productName, $statusText], $subject);
    $body = str_replace(['{{username}}', '{{product_name}}', '{{status}}'], [$username, $productName, $statusText], $body);

    // Attempt to send email
    $mailResponse = smtp_mailer($email, $subject, $body);

    if ($mailResponse === 'sent') {
        error_log("Email successfully sent with subject: $subject to: $email", 3, __DIR__ . '/email_logs.log');
        echo json_encode(['success' => true, 'message' => 'Product status updated and email sent.']);
    } else {
        error_log("Mailer Error for $email: $mailResponse", 3, __DIR__ . '/email_logs.log');
        echo json_encode(['success' => false, 'message' => 'Product status updated, but email not sent: ' . $mailResponse]);
    }
} else {
    error_log("Failed to update product status for product ID: $productId", 3, __DIR__ . '/status_update_errors.log');
    echo json_encode(['success' => false, 'message' => 'Failed to update product status.']);
}
?>
